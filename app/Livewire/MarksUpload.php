<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Stream;
use App\Models\Subject;
use Livewire\Component;
use App\Imports\MarksImport;
use Livewire\WithFileUploads;
use App\Models\ExaminationType;
use Illuminate\Support\Facades\Log;
use App\Exports\MarksTemplateExport;
use App\Models\StreamSubjectTeacher;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\SchoolClass;
use App\Models\AcademicYear;
use App\Models\ExamUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Collection;
use App\Models\Mark;
use App\Models\Student;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MarksUpload extends Component
{
    use WithFileUploads;

    public $exam_type;
    public $otherExamType;
    public $subject_id;
    public $stream_id;
    public $showForm = false;
    public $marks_file;
    public $isLoading = false;
    public $currentWeek, $availableWeeks = [];
    public $class_status;
    public $class_id;
    public $exam_uploads;

    public $months = [];
    public $currentMonth;
    public $selectedMonth;
    public $weeks = [1, 2, 3, 4];
    public $selectedWeek;
    public $isMidterm = false;
    public $isMonthly = false;
    public $isWeekly = false;
    public $isOther = false;
    
    // Properties for conditional display
    public $showStreams = true;
    public $showClassNameInStreamDropdown = true; // NEW PROPERTY

    // Use empty collections to prevent "count() on null" errors
    public Collection $subjectsAssigned;
    public Collection $streams;
    public Collection $classes;
    public Collection $exam_types;
    public Collection $allAssignments;
    public $examDataMap = [];
    public $selectedSubject = null;
    public $subjectStatus = null;

    public function mount($classupload,$selectedSubject,$subjectStatus)
    {
        $this->currentMonth = Carbon::now()->month;
        $this->class_status = $classupload;
        $this->selectedSubject = (int) $selectedSubject;
        $teacher = Auth::user();

        $academicYear = AcademicYear::where('school_id', $teacher->school->id)
            ->where('is_active', true)
            ->first();

        $this->months = [];
        for ($i = 1; $i <= $this->currentMonth; $i++) {
            $this->months[$i] = date("F", mktime(0, 0, 0, $i, 1));
        }
        $this->selectedMonth = $this->currentMonth;
        
        if($this->class_status != 1){

            $this->exam_uploads = ExamUpload::with(['academicYear:id,year','schoolClass:id,name','examinationType:id,name'])
                                    ->where('uploaded_by',$teacher->id)
                                    ->where('academic_year_id',$academicYear->id)->orderByDesc('created_at')->get();
            $examUploadedIds = $this->exam_uploads->pluck('id')->unique()->values()->toArray();

            $marks = Mark::with(['subject', 'student.stream'])
                ->whereIn('exam_upload_id', $examUploadedIds)
                ->get();

            foreach ($this->exam_uploads as $upload) {
                $examId = $upload->id;
                $subjects = $marks->where('exam_upload_id', $examId)->pluck('subject.name', 'subject.id')->unique()->toArray();
                $streams = $marks->where('exam_upload_id', $examId)->pluck('student.stream.alias')->unique()->sort()->values()->toArray();

                $this->examDataMap[$examId] = [
                    'subjects' => $subjects,
                    'streams' => $streams,
                ];
            }

        }else{
            $this->exam_uploads = ExamUpload::with(['user:id,name,phone','academicYear:id,year','schoolClass:id,name','examinationType:id,name'])
                                            ->where('academic_year_id',$academicYear->id)->orderByDesc('created_at')->get();
        }

        if ($this->class_status == 1) {
            $this->allAssignments = StreamSubjectTeacher::with(['stream.schoolClass', 'subject'])
                ->whereHas('stream.schoolClass', fn($query) => $query->where('school_id', $teacher->school->id))
                ->get();
            $this->classes = SchoolClass::where('school_id', $teacher->school->id)->get();
    
        } else {
            $this->allAssignments = StreamSubjectTeacher::with(['stream.schoolClass', 'subject'])
                ->where('teacher_id', $teacher->id)
                ->get();
            $this->classes = collect();
        }

        $this->streams = collect();
        $this->subjectsAssigned = $this->allAssignments->pluck('subject')->unique('id');
        $this->exam_types = ExaminationType::all();
        
        $this->showStreams = false;

        if($subjectStatus > 0){
            $this->subject_id = $selectedSubject;
            $this->updatedSubjectId($this->subject_id);
        }

        if ($this->subjectsAssigned->count() === 1) {
            $this->subject_id = $this->subjectsAssigned->first()->id;
            $this->updatedSubjectId($this->subject_id);
        }
        
        // Initially hide class name in stream dropdown if multiple classes exist for the first subject
        if($this->classes->count() > 1) {
            $this->showClassNameInStreamDropdown = false;
        }
    }
    
    public function updatedSubjectId($subjectId)
    {
        $this->class_id = null;
        $this->stream_id = null;
        $this->streams = collect();
        $this->classes = collect();
        $this->showStreams = true;

        if ($subjectId) {
            $subjectAssignments = $this->allAssignments->where('subject.id', (int) $subjectId);
            $this->classes = $subjectAssignments->pluck('stream.schoolClass')->unique('id')->sortBy(fn ($class) => $class->name);
            $this->streams = $subjectAssignments->pluck('stream')->unique('id')->sortBy(fn ($stream) => $stream->schoolClass->name);
        }
        
        // NEW LOGIC: Set flag based on class count
        if ($this->classes->count() > 1) {
            $this->showClassNameInStreamDropdown = false;
        } else {
            $this->showClassNameInStreamDropdown = true;
        }

        if ($this->classes->count() === 1) {
            $this->class_id = $this->classes->first()->id;
            $this->updatedClassId($this->class_id);
        } elseif ($this->classes->count() > 1) {
            $this->class_id = $this->classes->first()->id;
            $this->updatedClassId($this->class_id);
        }
        
        if ($this->streams->count() <= 1 && $this->classes->count() <= 1) {
            $this->showStreams = false;
            if ($this->streams->count() === 1) {
                $this->stream_id = $this->streams->first()->id;
            }
        }

        if($this->subjectStatus > 0 && $this->stream_id == null){
            $this->stream_id = $this->streams->first()->id;
        }
    }

    public function updatedClassId($classId)
    {
        $this->stream_id = null;
        $this->streams = collect();
        $this->showStreams = true;

        if ($this->subject_id && $classId) {
            $this->streams = $this->allAssignments
                ->where('stream.schoolClass.id', (int) $classId)
                ->where('subject.id', (int) $this->subject_id)
                ->pluck('stream')
                ->unique('id')
                ->sortBy(fn ($stream) => $stream->schoolClass->name);
        }
        
        if ($this->streams->count() <= 1) {
            $this->showStreams = false;
            if ($this->streams->count() === 1) {
                $this->stream_id = $this->streams->first()->id;
            }
        }
    }

    public function updateExamType()
    {
        $exam_type = ExaminationType::find($this->exam_type);
        $this->isMidterm = $exam_type?->name == 'Midterm';
        $this->isMonthly = $exam_type?->name == 'Monthly';
        $this->isWeekly = $exam_type?->name == 'Weekly';
        $this->isOther = $exam_type?->name == 'other';
    }

    public function uploadMarks(Request $request)
    {
        $rules = [
            'exam_type'     => 'required|exists:examination_types,id',
            'marks_file'    => 'required|file|mimes:xlsx,xls|max:5120',
            'selectedMonth' => 'nullable|integer|min:1|max:12',
            'selectedWeek'  => 'nullable|integer|min:1|max:4',
        ];

        if ($this->class_status == 1) {
            $rules['class_id'] = 'required|exists:school_classes,id';
        } else {
            $rules['subject_id'] = 'required|exists:subjects,id';
            if ($this->stream_id != 0) {
                $rules['stream_id'] = 'required|exists:streams,id';
            }
        }

        $this->validate($rules);

        $file = $this->marks_file;
        $subjectId = $this->subject_id;
        $streamId = $this->stream_id;
        $exam_type = $this->exam_type;
        $selectedMonth = $this->selectedMonth;
        $selectedWeek = $this->selectedWeek?? 0;
        $classStatus = $this->class_status;
        $academic_teacher = Auth::user();

        $classId = $this->class_status > 0
            ? $this->class_id
            : optional(Stream::find($this->stream_id))->school_class_id;
        
        $academicYear = AcademicYear::firstWhere([
            'school_id' => auth()->user()->school->id,
            'is_active' => true,
        ]);

        $filePath = $file->getRealPath();

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $headers = array_shift($rows);
        $studentIdRegNo = array_search('registration number', array_map('strtolower', $headers));
        $studentName = array_search('student name', array_map('strtolower', $headers));
        $marksIndex = array_search('marks', array_map('strtolower', $headers));

        $failures = $marksFailures = [];

        // if (empty($rows)) {
        //     flash()->option('position', 'bottom-right')->error("The uploaded file is empty or contains no records.");
        //     return redirect()->back();
        // }

        // foreach ($rows as $index => $row) {
        //     $regNo = $row[$studentIdRegNo] ?? null;
        //     $name = $row[$studentName] ?? null;
        //     $marks = $row[$marksIndex] ?? null; 

        //     if($regNo === null || $name === null || $marks === null){
        //         $failures[] = $index + 2;
        //     }

        //     if (!is_numeric($marks) || $marks < 0 || $marks > 100) {
        //         $marksFailures[] = $index + 2;
        //     }
        // }

        // if (!empty($failures)) {
        //     $count = count($failures);
            
        //     if ($count === 1) {
        //         $rowList = $failures[0];
        //     } else {
        //         $lastRow = array_pop($failures);
        //         $rowList = implode(', ', $failures);
        //         $rowList .= " and " . $lastRow;
        //     }
            
        //     $message = "Registration number, name or marks is missing in row " . $rowList;
            
        //     flash()->option('position', 'bottom-right')->error($message);
        //     return redirect()->back();
        // }

        // if (!empty($marksFailures)) {
        //     $count = count($marksFailures);
            
        //     if ($count === 1) {
        //         $rowList = $marksFailures[0];
        //     } else {
        //         $lastRow = array_pop($marksFailures);
        //         $rowList = implode(', ', $marksFailures);
        //         $rowList .= " and " . $lastRow;
        //     }
            
        //     $message = "Marks in row " . $rowList . " must be a number between 0 and 100.";
            
        //     flash()->option('position', 'bottom-right')->error($message);
        //     return redirect()->back();
        // }

        try {
            DB::transaction(function () use ($file, $subjectId, $streamId, $exam_type, $selectedMonth, $selectedWeek, $classId, $academic_teacher, $academicYear, $classStatus) { 

                $examUpload = ExamUpload::where([
                    'uploaded_by' => (int) $academic_teacher->id,
                    'exam_type_id' => (int) $exam_type,
                    'academic_year_id' => (int) $academicYear->id,
                    'school_class_id' => (int) $classId,
                    'month' => $selectedMonth,
                    'week' => $selectedWeek,
                ]);

                if($examUpload){
                    $examUpload->touch();
                }
                                
                $examUpload = ExamUpload::updateOrCreate([
                    'uploaded_by' => (int) $academic_teacher->id,
                    'exam_type_id' => (int) $exam_type,
                    'academic_year_id' => (int) $academicYear->id,
                    'school_class_id' => (int) $classId,
                    'month' => $selectedMonth,
                    'week' => $selectedWeek,
                ]);

                $import = new MarksImport($subjectId, $streamId, $exam_type, $selectedMonth, $selectedWeek, $classId, $classStatus);
                $import->examUploadId = $examUpload->id;

                Excel::import($import, $file);
                
            });

            flash()->option('position','bottom-right')->success('Marks have been successfully uploaded.');
            return redirect()->route('livewire.marks-upload', ['classupload' => $this->class_status]);

        } catch (ValidationException $e) {
            $failures = $e->failures();
            session()->flash('failures', $failures);
            session()->flash('error', 'Import failed due to validation errors.');
            Log::error('Marks import failed due to validation errors: ' . $e->getMessage());
            return redirect()->back();
        } catch (\Exception $e) {
            Log::error('Marks import failed: ' . $e->getMessage());
            session()->flash('error', 'An unexpected error occurred during the import.');
            return redirect()->back();
        }
    }

    public function downloadTemplate()
    { 
        $this->isLoading = true;

        if($this->class_status == 1){
            $this->validate([
            'class_id' => 'required|exists:school_classes,id',
            ]);

            $class = SchoolClass::find($this->class_id)->name;

            return Excel::download(new MarksTemplateExport($this->class_status, 0, 0, $this->class_id), "marks_template_for_{$class}.xlsx", \Maatwebsite\Excel\Excel::XLSX);

        }else{
            $this->validate([
            'subject_id' => 'nullable|exists:subjects,id',
            // 'stream_id' => 'required|exists:streams,id',
            ]);

            if ($this->stream_id != 0) {
                $validationRules['stream_id'] = 'required|exists:streams,id';
            }

            // Logic to handle "All Streams" template download
            $classIdForDownload = $this->class_id ?? optional($this->streams->where('id',$this->stream_id)->first())->school_class_id;
                
            $class = SchoolClass::find($this->class_id)->name;
            $subject = Subject::find($this->subject_id)->name;
            $subjectName = explode(' ', $subject)[0];  
            
            if ($this->stream_id == 0) {
                $fileName = "marks_template_for_{$class}_all_streams_{$subjectName}.xlsx";
            } else {
                $stream = Stream::find($this->stream_id)->name;
                $fileName = "marks_for_{$class}_{$stream}_{$subjectName}.xlsx";
            }
            
            // Pass the class ID to the exporter if "All Streams" is selected
            return Excel::download(new MarksTemplateExport($this->class_status, $this->stream_id, $this->subject_id, $classIdForDownload), $fileName, \Maatwebsite\Excel\Excel::XLSX);
        }

        $this->isLoading = false;
    }

    public function render()
    {
        if ($this->class_status == 1) {
            return view('livewire.class-marks-upload');
        }
        return view('livewire.marks-upload');
    }
}