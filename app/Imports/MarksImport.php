<?php

namespace App\Imports;

use App\Models\Mark;
use App\Models\Student;
use App\Models\GradeScale;
use App\Models\AcademicYear;
use App\Models\StreamSubjectTeacher;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;

class MarksImport implements ToCollection, WithHeadingRow
{
    use Importable;

    protected $subjectId;
    protected $streamId;
    protected $exam_type;
    protected $selectedMonth;
    protected $selectedWeek;
    protected $classId;
    protected $academicYearId;
    protected $schoolId;
    protected $gradeScales;
    protected $subjectsInClass;
    protected $classStatus;

    public $examUploadId;
    public $studentsToUpdateResults = [];

    public function __construct($subjectId, $streamId, $exam_type, $selectedMonth, $selectedWeek, $classId, $classStatus)
    {
        $this->streamId = $streamId;
        $this->selectedMonth = $selectedMonth;
        $this->selectedWeek = $selectedWeek;
        $this->exam_type = $exam_type;
        $this->classId = $classId;
        $this->schoolId = auth()->user()->school->id;
        $this->classStatus = $classStatus;

        if ($this->classStatus > 0) {

            $this->subjectsInClass = StreamSubjectTeacher::with('subject')
                ->whereHas('stream', fn ($query) => $query->where('school_class_id', $this->classId))
                ->get()
                ->pluck('subject.name', 'subject.id')
                ->mapWithKeys(function ($subjectName, $subjectId) {
                    return [strtolower($subjectName) => $subjectId];
                });
                
        } else {
            $this->subjectId = $subjectId;
        }

        $academicYear = AcademicYear::where('school_id', $this->schoolId)
            ->where('is_active', true)
            ->firstOrFail();
        $this->academicYearId = $academicYear->id;

        $this->gradeScales = GradeScale::where('school_id', $this->schoolId)
            ->orderBy('min_marks', 'desc')
            ->get();
    }

    public function collection(Collection $rows)
    { 
        if ($rows->isEmpty()) {
            flash()->option('position','bottom-right')->error("The uploaded file is empty or contains no records.");
            return redirect()->route('livewire.marks-upload');
        }

        $header = $rows->first()->keys()->toArray();

        $cleanedHeader = array_map(function($value) {
            return strtolower(trim($value ?? ''));
        }, $header);

        $filteredHeader = array_filter($cleanedHeader, function($value) {
            return $value !== null && $value !== '';
        });

        $studentIdRegNo = array_search('registration_number', array_map('strtolower', $filteredHeader));
        $studentName = array_search('student_name', array_map('strtolower', $filteredHeader));
        $marksIndex = array_search('marks', array_map('strtolower', $filteredHeader));
        
        if ($this->classStatus == 0 && ($marksIndex === false || $studentIdRegNo === false || $studentName === false)) {
            flash()->option('position', 'bottom-right')->error("The uploaded file is missing 'registration number', 'student name', or 'marks' column.");
            return redirect()->route('livewire.marks-upload');
        }

        $nonSubjectColumns = ['registration_number', 'student_name'];
        if ($this->classStatus > 0) {
            $nonSubjectColumns[] = 'stream';

            $subjectColumns = array_diff($filteredHeader, $nonSubjectColumns);

            if(empty($subjectColumns) || $studentIdRegNo === false || $studentName === false){
                flash()->option('position', 'bottom-right')->error("The uploaded file is missing 'registration number', 'student name', or 'subjects' column.");
                return redirect()->route('livewire.marks-upload');
                
            }elseif (count($subjectColumns) > 0 && count($subjectColumns) != count($this->subjectsInClass)) {
                flash()->option('position', 'bottom-right')->error("Number of subjects in the file does not match the subjects assigned to the class. Expected " . count($this->subjectsInClass) . ", got " . count($subjectColumns) . ".");
                return redirect()->route('livewire.marks-upload');
                //throw new \Exception("Number of subjects in the file does not match the subjects assigned to the class. Expected " . count($this->subjectsInClass) . ", got " . count($subjectColumns) . ".");
            }

        } else {
            $nonSubjectColumns[] = 'marks'; // Only for single-subject uploads
        
        }
        
        $students = $this->classStatus > 0 || $this->streamId == 0? Student::where('school_class_id', $this->classId)->get()->keyBy('reg_number') : Student::where('stream_id', $this->streamId)->get()->keyBy('reg_number');

        foreach ($rows as $index => $row) {

            $regNo = $row['registration_number'] ?? null;
            $name = $row['student_name'] ?? null;
            $index += 2;

            if ($this->classStatus == 0) {
                $marks = $row['marks'] ?? null; 

                if($regNo === null || $name === null || $marks === null){
                    flash()->option('position','bottom-right')->error("Registration number, student name or marks is missing in row " . $index);
                    return redirect()->route('livewire.marks-upload');
                }

                if (!is_numeric($marks) || $marks < 0 || $marks > 100) {
                    flash()->option('position','bottom-right')->error('Row ' . $index . " , marks must be a number between 0 and 100.");
                    return redirect()->route('livewire.marks-upload');
                }

                $last_slash_position = strrpos($row['registration_number'], '/');
                $registration_number = substr($row['registration_number'], 0, $last_slash_position);
                $regNumber = $registration_number;
                $subjectId = substr($row['registration_number'], $last_slash_position + 1);

            } else {
                $rowHasMarks = false;
                foreach ($subjectColumns as $subjectName) {
                    $marks = $row[$subjectName] ?? null;

                    if (!is_null($marks) && $marks !== '') {

                        if (!is_numeric($marks) || $marks < 0 || $marks > 100) {
                            flash()->option('position','bottom-right')->error('Row ' . $index . " , marks for '{$subjectName}' must be a number between 0 and 100.");
                            return redirect()->route('livewire.marks-upload');

                        } else {
                            $subjectId = $this->subjectsInClass->get(strtolower($subjectName));
                            if ($subjectId) {
                                $rowHasMarks = true;
                            }
                        }
                    }
                }

                if (!$rowHasMarks) {
                    flash()->option('position','bottom-right')->error("Row ". $index . " does not have marks for any subject.");
                    return redirect()->route('livewire.marks-upload');

                }
                $regNumber = $row['registration_number'];
            }

            $student = $students[$regNumber] ?? null;

            if ($student == null) {
                flash()->option('position','bottom-right')->error("Student with registration number {$regNumber} does not exist. Please crosscheck the selected class/stream.");
                return redirect()->route('livewire.marks-upload');
            }

            if ($this->classStatus == 0) {
                
                if ($subjectId != $this->subjectId) {
                    flash()->option('position','bottom-right')->error("Marks uploaded are not for the selected subject. Please crosscheck the selected subject.");
                    return redirect()->route('livewire.marks-upload');

                }

                $marks = (int) $row['marks'];
                $this->saveMark($student, $this->subjectId, $marks);

            } else {
                
                foreach ($subjectColumns as $subjectName) {
                    $marks = $row[$subjectName];
                    if (is_numeric($marks)) {
                        $subjectId = $this->subjectsInClass->get(strtolower($subjectName));
                        if ($subjectId) {
                           $this->saveMark($student, $subjectId, $marks);
                        }
                    }
                }
            }
        }
    }
    
    protected function saveMark($student, $subjectId, $marks)
    {
        $gradeInfo = $this->getGradeFromMarks($marks);

        Mark::updateOrCreate(
            [
                'student_id' => $student->id,
                'subject_id' => $subjectId,
                'exam_type_id' => $this->exam_type,
                'academic_year_id' => $this->academicYearId,
                'month' => $this->selectedMonth,
                'week' => $this->selectedWeek,
            ],
            [
                'obtained_marks' => (int) $marks,
                'grade' => $gradeInfo['grade'],
                'remark' => $gradeInfo['remark'],
                'teacher_id' => auth()->id(),
                'exam_upload_id' => (int) $this->examUploadId,
            ]
        );
    }
    
    protected function getGradeFromMarks($marks)
    {
        foreach ($this->gradeScales as $scale) {
            if ($marks >= $scale->min_marks && $marks <= $scale->max_marks) {
                return ['grade' => $scale->grade, 'remark' => $scale->remarks];
            }
        }
        return ['grade' => 'F', 'remark' => 'Fail'];
    }

    public function rules(): array
    {
        if ($this->classId > 0) {
            return [
                'registration_number' => 'required|string',
            ];
        }

        return [
            'registration_number' => 'required|string',
            'marks' => 'required|numeric|min:0|max:100',
        ];
    }
}