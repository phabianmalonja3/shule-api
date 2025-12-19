<?php

namespace App\Livewire\Marks;

use App\Models\Mark;
use App\Models\User;
use App\Models\Stream;
use App\Models\Student;
use Livewire\Component;
use App\Models\GradeScale;
use App\Models\SchoolClass;
use App\Models\ExaminationType;
use App\Models\StudentResult;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\AcademicYear;
use App\Models\StreamSubjectTeacher;
use Carbon\Carbon;

class MarksList extends Component
{
    public $search = '';
    public $selectedSubject = '';
    public $selectedStream = '';
 
    protected $student;
    protected $teacherName;
    protected $headTeacher;
    protected $totalStudents;

    public $selectedClass = null;
    public $streams = [];
    public $selectedexaminationsType = null;
    public $grades = ['A', 'B', 'C', 'D', 'E', 'F'];
    public $selectedGrade = '';
    public $classes = '';
    public $class_result_flag = '';
    public $showStreams = true;
    public $streamIds = [];
    public $months = [];
    public $showNoMarksAlert = false;
    public $accordionKey;

    public function mount($class_result_flag, $editStatus, $selectedexaminationsType, $selectedClass, $selectedSubject, $selectedStream, $search, $accordionKey)
    {
        $teacher = auth()->user();

        if ($teacher->hasAnyRole(['header teacher', 'academic teacher'])) {
            $classes = SchoolClass::with('streams')
                ->latest()
                ->where('school_id', $teacher->school_id)
                ->get();
        } elseif ($teacher->hasRole('class teacher')) {
            $classes = SchoolClass::with('streams')
                ->where(function ($q) use ($teacher) {
                    $q->whereHas('streams', function ($query) use ($teacher) {
                        $query->where('stream_teacher_id', $teacher->id);
                    })->orWhere('teacher_class_id', $teacher->id);
                })
                ->latest()
                ->get();
        } elseif ($teacher->hasRole('teacher')) {
            $assignments = StreamSubjectTeacher::with('stream.schoolClass')
                ->where('teacher_id', $teacher->id)
                ->get();
            $classIds = $assignments->pluck('stream.school_class_id')->unique()->all();
            $streamIds = $assignments->pluck('stream.id')->unique()->all();
            $classes = SchoolClass::whereIn('id', $classIds)
                ->with('streams', function ($q) use ($streamIds) {
                    $q->whereIn('id', $streamIds);
                })
                ->latest()
                ->get();
            $this->streamIds = $streamIds;

        } else {
            $classes = collect();
        }

        $this->class_result_flag = $class_result_flag;
        $this->classes = $classes;

        if ($this->selectedClass) {
            $this->updateStreamVisibility($this->selectedClass);
        }

        $currentMonth = Carbon::now()->month; // Get the current month
        for ($i = 1; $i <= $currentMonth; $i++) {
            $this->months[$i] = date("F", mktime(0, 0, 0, $i, 1));
        }

        if($editStatus > 0){
            $this->selectedexaminationsType = $selectedexaminationsType;
            $this->selectedClass = $selectedClass;
            $this->selectedSubject = $selectedSubject;
            $this->selectedStream = $selectedStream;
            $this->search = $search;
            $this->accordionKey = $accordionKey;
        }
    }

    protected function updateStreamVisibility($classId)
    {
        $selectedClassModel = SchoolClass::find($classId);
        if ($selectedClassModel && $selectedClassModel->teacher_class_id !== null) {
            $this->showStreams = false;
            $this->selectedStream = null; 
        } else {
            $this->showStreams = true;
        }
    }

    public function updatedSelectedClass($classId)
    {
        //$this->selectedStream = null;
        $this->streams = []; 
        $teacher = auth()->user();

        $academicYear = AcademicYear::where('school_id', auth()->user()->school->id)
                                    ->where('is_active', true)
                                    ->first();

        if ($classId) {
            $this->updateStreamVisibility($classId);

            $assignedSubjects = StreamSubjectTeacher::with('subject')
                ->where('teacher_id', $teacher->id)
                ->whereHas('stream', fn($q) => $q->where('school_class_id', $this->selectedClass))
                ->get();
            
            $assignedSubjects = $assignedSubjects->pluck('subject')
                ->unique('id')
                ->sortBy('name');

            // Find which of these subjects actually have marks entered for this class.
            $subjectIdsWithMarks = Mark::where('academic_year_id', $academicYear->id)
                ->whereHas('student', function ($query) {
                    $query->where('school_class_id', $this->selectedClass);
                })
                ->distinct('subject_id')
                ->pluck('subject_id');

            // Filter the assigned subjects to only show those with existing marks.
            $subjects = $assignedSubjects->whereIn('id', $subjectIdsWithMarks);
            
            // Pre-select the first subject if one isn't already selected
            if (empty($this->selectedSubject) && $subjects->count() > 0) {
                $this->selectedSubject = $subjects->first()->id;
            }
                
            if ($this->showStreams) {
                $selectedClassModel = $this->classes->firstWhere('id', $classId);
                if ($selectedClassModel) {
                    $this->streams = $selectedClassModel->streams;
                } else {

                    $this->streams = Stream::where('school_class_id', $classId)->get();
                }

                // if (empty($this->selectedStream) && count($this->streams) > 0) {
                //     $this->selectedStream = $this->streams[0]->id;
                // }
            }
        } else {
            $this->showStreams = true; 
        }
    }

    public function resetFilters()
    {
        $this->reset([
            'search',
            'selectedClass',
            'selectedStream',
            'selectedSubject',
            'selectedexaminationsType',
            'selectedGrade',
        ]);
        
        $this->class_result_flag = '';

        $this->streams = [];
    }
    
    public function render()
    {
        $teacher = auth()->user();
        $teacherId = $teacher->id;
        $subjects = $teacher->subjects->unique();

        $academicYear = AcademicYear::where('school_id', auth()->user()->school->id)
            ->where('is_active', true)
            ->first();

        $schoolId = auth()->user()->school_id;
        $examinationsTypes = ExaminationType::whereHas('marks', function ($query) use ($academicYear) {     // Boresho: Iwe kwa marks za darasa pia
            $query->where('academic_year_id', $academicYear->id);
        })->get();

        $grades = $this->grades;
        $examTypeId = 0;

        if (empty($this->class_result_flag)) {

            if ($teacher->hasAnyRole(['header teacher', 'academic teacher'])) {

                if (empty($this->selectedClass)) {
                    $this->selectedClass = $this->classes[0]->id;
                }

                $this->streams = Stream::where('school_class_id', $this->selectedClass)->get();

                $studentsMarks = Student::with(['user', 'marks' => function($q){
                    $q->when($this->selectedexaminationsType, fn($q) => $q->where('exam_type_id', $this->selectedexaminationsType));
                }])
                ->whereHas('marks', function($q) use($academicYear){$q->where('academic_year_id', $academicYear->id)
                    ->when($this->selectedexaminationsType, fn($q) => $q->where('exam_type_id', $this->selectedexaminationsType));})
                ->when($this->selectedClass, fn($q) => $q->where('school_class_id', $this->selectedClass))
                ->when($this->selectedStream, fn($q) => $q->where('stream_id', $this->selectedStream)); 

            } elseif ($teacher->hasRole('class teacher')) {

                $classes = SchoolClass::with(['streams' => function ($query) use ($teacher) {
                    $query->where('teacher_id', $teacher->id);
                }])->where('academic_year_id', $academicYear->id)
                    ->latest()
                    ->get();

                $streamIds = $classes->flatMap->streams->pluck('id')->unique()->values()->toArray();
                
                if (empty($this->selectedClass) && count($classes) > 0) {
                    $this->selectedClass = $classes[0]->id;
                }

                $this->streams = Stream::where('school_class_id', $this->selectedClass)->whereIn('id', $streamIds)->get();

                $studentsMarks = Student::with(['user', 'marks' => function($q){
                    $q->when($this->selectedexaminationsType, fn($q) => $q->where('exam_type_id', $this->selectedexaminationsType))
                    ->when($this->selectedSubject, fn($q) => $q->where('subject_id', $this->selectedSubject))
                    ->when($this->selectedGrade, fn($q) => $q->where('grade', $this->selectedGrade));
                }])
                ->whereHas('marks', function($q) use($academicYear){$q->where('academic_year_id', $academicYear->id)
                    ->when($this->selectedexaminationsType, fn($q) => $q->where('exam_type_id', $this->selectedexaminationsType))
                    ->when($this->selectedSubject, fn($q) => $q->where('subject_id', $this->selectedSubject))
                    ->when($this->selectedGrade, fn($q) => $q->where('grade', $this->selectedGrade));})
                ->whereIn('stream_id', $streamIds)
                ->when($this->selectedClass, fn($q) => $q->where('school_class_id', $this->selectedClass))
                ->when($this->selectedStream, fn($q) => $q->where('stream_id', $this->selectedStream)); 
                
            } elseif ($teacher->hasRole('teacher')) {

                $assignedClassStreams = StreamSubjectTeacher::with('stream.schoolClass', 'subject')
                    ->where('teacher_id', $teacher->id)
                    ->get();
                
                $streamsIds = $assignedClassStreams->pluck('stream_id')->unique()->values()->toArray();
                $classes = $assignedClassStreams->map(function ($record) {
                    return $record->stream->schoolClass;
                })->unique('id');
                
                if (empty($this->selectedClass) && $classes->isNotEmpty()) {
                    $this->selectedClass = $classes->first()->id;
                }

                $subjectIdsWithMarks = Mark::where('academic_year_id', $academicYear->id)
                    ->whereHas('student', function ($query) {
                        $query->where('school_class_id', $this->selectedClass)
                            ->when($this->selectedStream, fn($q) => $q->where('stream_id', $this->selectedStream));
                    })
                    ->distinct('subject_id')
                    ->pluck('subject_id');

                $subjectsWithMarks = $assignedClassStreams->pluck('subject')
                    ->whereIn('id', $subjectIdsWithMarks)
                    ->unique('id')
                    ->sortBy('name');

                if (empty($this->selectedSubject) && $subjectsWithMarks->count() > 0) {
                    $this->selectedSubject = $subjectsWithMarks->first()->id;
                }

                if (empty($this->selectedexaminationsType) && count($examinationsTypes) > 0) {
                    $this->selectedexaminationsType = $examinationsTypes[0]->id;
                }

                $this->streams = Stream::where('school_class_id', $this->selectedClass)
                    ->whereIn('id', $streamsIds)
                    ->get();

                $studentsMarks = Student::with(['user', 'marks' => function($q){
                    $q->when($this->selectedexaminationsType, fn($q) => $q->where('exam_type_id', $this->selectedexaminationsType))
                   ->when($this->selectedSubject, fn($q) => $q->where('subject_id', $this->selectedSubject))
                   ->when($this->selectedGrade, fn($q) => $q->where('grade', $this->selectedGrade));
                }])
                ->whereHas('marks', function($q) use($academicYear){$q->where('academic_year_id', $academicYear->id)
                   ->when($this->selectedexaminationsType, fn($q) => $q->where('exam_type_id', $this->selectedexaminationsType))
                   ->when($this->selectedSubject, fn($q) => $q->where('subject_id', $this->selectedSubject))
                   ->when($this->selectedGrade, fn($q) => $q->where('grade', $this->selectedGrade));})
                ->whereIn('stream_id', $streamsIds)
                ->when($this->selectedClass, fn($q) => $q->where('school_class_id', $this->selectedClass))
                ->when($this->selectedStream, fn($q) => $q->where('stream_id', $this->selectedStream)); 

            }

        } else {

            $flag = explode('/', $this->class_result_flag);
            $classId = $flag[0];
            $examTypeId = $flag[1];
            if (empty($this->selectedClass)) {
                $this->selectedClass = $classId;
            }
            
            $this->streams = Stream::where('school_class_id', $this->selectedClass)->get();
            //Pre-select first stream if only one exists for the teacher
            if (empty($this->selectedStream) && !empty($this->streams)) {
                $this->selectedStream = $this->streams[0]->id;
            }

            $studentsMarks = Student::with(['user', 'marks' => function ($query) use ($examTypeId) {
                $query->where('exam_type_id', $examTypeId)
                      ->when($this->selectedSubject, fn($q) => $q->where('subject_id', $this->selectedSubject));
            }])->whereHas('marks', function($q) use($academicYear){$q->where('academic_year_id', $academicYear->id);})
            ->where('school_class_id', $classId);
        
        }

        if (empty($this->selectedexaminationsType) && count($examinationsTypes) > 0) {
            $this->selectedexaminationsType = $examinationsTypes->first()->id;
        }else{
            
        }

        $filtersActive = !empty($this->selectedClass) || !empty($this->selectedStream) || !empty($this->search) || !empty($this->selectedexaminationsType);

        if (!$filtersActive) {
            $studentsMarks = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
            $subjectsWithMarks = collect();
        } else {
            $studentsMarks->when($this->search, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('username', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedClass, function ($query) {
                $query->where('school_class_id', $this->selectedClass);
            })
            ->when($this->selectedStream, function ($query) {
                $query->where('stream_id', $this->selectedStream);
            });

            $studentsRanks = [];
            $tiesPerPosition = [];
            $position = 1;
            if ($studentsMarks->isNotEmpty()) {

                $sortedStudents = $studentsMarks->sortByDesc(function ($student) {
                    $mark = $student->marks->first();
                    return $mark ? $mark->obtained_marks : -1;
                });

                $standardRank = $denseRank = 0; 
                $previousScore = null;
                $tieCounter = 0;

                foreach ($sortedStudents as $student) {
                    $currentMark = $student->marks->first()?->obtained_marks;

                    if ($currentMark !== $previousScore) {
                        $standardRank = $position;
                        $denseRank += 1;
                        if ($previousScore !== null) {
                            $tiesPerPosition[$standardRank - $tieCounter] = $tieCounter;
                        }
                        $tieCounter = 1;
                    } else {
                        $tieCounter++;
                    }

                    $studentsRanks[$student->id] = [
                        'standardRank' => $standardRank,
                        'tiesPerPosition' => $tiesPerPosition,
                        'denseRank' => $denseRank
                    ];

                    $position++;
                    $previousScore = $currentMark;
                }
                
                if ($previousScore !== null) {
                    $tiesPerPosition[$standardRank] = $tieCounter;
                }
            }

            $studentsMarks = $studentsMarks->paginate(10);

            $allStudentsHaveNoMarks = $studentsMarks->every(fn($student) => $student->marks->isEmpty());

            if ($studentsMarks->isEmpty() || $allStudentsHaveNoMarks) {
                $this->showNoMarksAlert = true;
            } else {
                $this->showNoMarksAlert = false;
            }

            if($teacher->hasAnyRole(['academic teacher','header master','class teacher'])){
                $subjectsWithMarks = collect();
            foreach ($studentsMarks as $student) {
                $subjectsWithMarks = $subjectsWithMarks->merge($student->marks->pluck('subject'));
            }
                $subjectsWithMarks = $subjectsWithMarks->unique('id')->sortBy('name');

            }
        }

        if ($this->accordionKey > 0) {
            $this->dispatch('open-accordion-panel', monthNumber: $this->accordionKey);
        }

        if($teacher->hasAnyRole(['academic teacher','header master','class teacher'])){
            return view('livewire.marks.marks-list', compact('studentsMarks', 'subjectsWithMarks', 'examinationsTypes', 'grades','examTypeId','studentsRanks'));
        }elseif($teacher->hasRole('teacher')){
            return view('livewire.marks.subject-marks-list', ['studentsMarks' => $studentsMarks, 'classes' => $classes, 'examinationsTypes' => $examinationsTypes, 'subjects' => $subjectsWithMarks]);
        }
    }

    public function ReportDownload ($id)
    {
        $student = Student::findorfail($id);

        $student = $student->load(['marks' => function ($query) {
            $query->with('subject'); 
        }]); 
        $this->student = $student;

        $school = $student->school;
        $class = $student->schoolClass;

        $year = $class->academicYear->year;

        $scales = GradeScale::where('school_id', $school->id)
        ->get();

        $marks =  $this->student->marks->map(function ($mark) {
            $stream = $this->student->stream;
            $fullname = explode(' ',$mark->teacher->name);
        
        $firstName = $fullname[0];
        $lastName = $fullname[2];

        $this->teacherName = $firstName .' '.$lastName;
            
        $position = Mark::where('subject_id', $mark->subject_id)
                    ->whereHas('student', function ($query) use ($stream) {
                        $query->where('stream_id', $stream->id); 
                    })
                    ->where('obtained_marks', '>', $mark->obtained_marks)
                    ->count() + 1;
                        
                        // Add 1 to get the a
        $this->totalStudents = Mark::where('subject_id', $mark->subject_id)
                        ->whereHas('student', function ($query) use ($stream) {
                            $query->where('stream_id', $stream->id); 
                        })
                        ->distinct('student_id') 
                        ->count(); 
                     
         return  [
                'subject' => $mark->subject->name,
                'marks' => $mark->obtained_marks,
                'grade' => $mark->grade,
                'remarks' => $mark->remark,
                'position' => $position,
                'teacher' => $firstName .' '.$lastName,
                'exam_type' => $mark->examType->name ?? 'N/A',
        
            ];
            
        });

        $teacherName =$this->teacherName;
        $headTeacher = User::role('header teacher')
        ->where('school_id', auth()->user()->school->id)
        ->first();
        $totalStudents = $this->totalStudents; 

        $pdf = Pdf::loadView('pdf.report', compact('student','school','scales','marks','year','teacherName','headTeacher','totalStudents'));
        return $pdf->download('Report_for_'. $this->student->user->name.'.pdf');

    }
}
