<?php


namespace App\Livewire\Examinations;

use Carbon\Carbon;
use App\Models\Stream;
use App\Models\Subject;
use Livewire\Component;
use App\Models\SchoolClass;
use App\Models\AcademicYear;
use App\Models\StudentResult;
use App\Models\ExaminationType;
use App\Models\ExaminationResult;
use App\Models\StreamSubjectTeacher;
use App\Exports\StudentClassResultsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ExaminationResults extends Component
{
    public $selectedSubject;
    public $selectedStream;
    public $selectedAcademicYear;
    public $subjects = [];
    public $streams = [];
    public $classes = [];
    public $academic_year;
    public $exam_types = [];
    public $results = [];
    public $academic_years = [];

    public $selectedClass;
    public $selectedExaminationType;
    public $examType;
    public $sort_by = 'first'; 
    public $search;
    public $currentWeek, $availableWeeks = [];

    public $months = [];
    public $currentMonth;
    public $selectedMonth;
    public $weeks = [1, 2, 3, 4];
    public $showStreams = true;
    public $showNoMarksAlert = false;
    public $tiesPerPositionByClass = [];
    public $tiesPerPositionByStream = [];

    public function mount()
    {
        $this->currentMonth = Carbon::now()->month; // Get the current month
        for ($i = 1; $i <= $this->currentMonth; $i++) {
            $this->months[$i] = date("F", mktime(0, 0, 0, $i, 1));
        }
        // if ($this->currentMonth >= 1 && $this->currentMonth <= 6) {
        //     // First Term: Allow January to the current month
        //     $this->months = [];
        //     for ($i = 1; $i <= $this->currentMonth; $i++) {
        //         $this->months[$i] = date("F", mktime(0, 0, 0, $i, 1));
        //     }
        // } else {
        //     // Second Term: Allow July to the current month
        //     $this->months = [];
        //     for ($i = 7; $i <= $this->currentMonth; $i++) {
        //         $this->months[$i] = date("F", mktime(0, 0, 0, $i, 1));
        //     }
        // }

        $teacher = auth()->user();
        $this->academic_years = AcademicYear::where('school_id',$teacher->school_id)->latest()->get(); 
        $teacher->hasRole('administrator')? $this->classes = [] : $this->classes = SchoolClass::where('school_id', $teacher->school->id)->get();

        if(empty($this->selectedAcademicYear) && count($this->academic_years) > 0){ 
            $this->selectedAcademicYear = $this->academic_years[0]->id;
        }

        if(empty($this->selectedClass) && count($this->classes) >= 0){
            $this->selectedClass = $this->classes[0]->id;
        }

        if(empty($this->selectedAcademicYear) && count($this->academic_years) >= 0){
            $this->selectedAcademicYear = $this->academic_years[0]->id;
        }

        $this->exam_types = ExaminationType::select('examination_types.*')
            ->join('student_results', 'examination_types.id', '=', 'student_results.exam_type_id')
            ->where('student_results.academic_year_id', $this->selectedAcademicYear)
            ->distinct()
            ->orderBy('examination_types.id')
            ->get();
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
        $this->streams = []; 

        if ($classId) {
            $this->updateStreamVisibility($classId);

            if ($this->showStreams) {
                $selectedClassModel = $this->classes->firstWhere('id', $classId);
                if ($selectedClassModel) {
                    $this->streams = $selectedClassModel->streams;
                } else {

                    $this->streams = Stream::where('school_class_id', $classId)->get();
                }
            }
        } else {
            $this->showStreams = true; 
        }

        $this->fetchResults();
    }

    public function setAvailableWeeks()
    {
        $this->availableWeeks = range(1, 4);
    }

    public function fetchResults()
    { 
        $query = StudentResult::where('academic_year_id',$this->selectedAcademicYear)->where('exam_type_id',$this->selectedExaminationType)
                              ->whereHas('student', function($q) {$q->where('school_class_id',$this->selectedClass)
                              ->when($this->selectedStream, fn($q) => $q->where('stream_id', $this->selectedStream));});

        if ($this->selectedClass) {
            $query->whereHas('student', function ($query) {
                $query->where('school_class_id', $this->selectedClass);
            });
        }

        if ($this->search) {
            $query->whereHas('student', function ($q) {
                // Group the search conditions to ensure the OR logic is correct
                $q->where(function ($q2) {
                    $q2->where('reg_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($q3) {
                        $q3->where('name', 'like', '%' . $this->search . '%');
                    });
                });
            });
        }

        if ($this->sort_by == 'last') {
            $query->orderByDesc('average_marks');
        } elseif ($this->sort_by == 'first') {
            $query->orderBy('average_marks');
        }
    
        $this->results = $query->get();

    }

    public function render()
    {
        $teacher = auth()->user();

        if(empty($this->selectedExaminationType) && count($this->exam_types) > 0){
            $this->selectedExaminationType = $this->exam_types[0]->id;
        }

        if($teacher->hasAnyRole(['header teacher','assistant headteacher','academic teacher'])){
                        
            $streams = $teacher->streamsTeaches;
            $streamIds = $streams->pluck('id')->unique();
            $classIds = $streams->pluck('school_class_id')->unique();
            $classes = SchoolClass::whereIn('id',$classIds)->latest()->get();
            $this->classes = $classes;
            $this->streams = $streams->isEmpty() ? collect() : $streams->where('school_class_id',$this->selectedClass);

        }elseif($teacher->hasRole('class teacher')) {
            $streams = $teacher->streams;
            $classIds = $streams->pluck('school_class_id')->unique();
            $streamIds = $streams->pluck('id')->unique();
            $this->classes = SchoolClass::whereIn('id', $classIds)->get();
            $this->streams = $streams->isEmpty() ? collect() : $streams->where('school_class_id',$this->selectedClass);

            if(empty($this->selectedStream) && count($this->streams) > 0){
                $this->selectedStream = $this->streams[0]->id;
            }
     
        }

        if($this->exam_types->where('id', $this->selectedExaminationType)->whereIn('name', ['Monthly','Midterm'])->isNotEmpty()){
            $this->results = StudentResult::with(['student' => function($q) use($streamIds){$q->whereIn('stream_id',$streamIds)
                ->when($this->selectedStream, fn($q) => $q->where('stream_id', $this->selectedStream))
                ->when($this->selectedClass, fn($q) => $q->where('school_class_id', $this->selectedClass));}, 
                                                'examType' => function($q){$q->when($this->selectedExaminationType, fn($q) => $q->where('id', $this->selectedExaminationType));}])
                    ->whereHas('student', function($query) use ($streamIds) {$query->whereIn('stream_id', $streamIds)
                    ->when($this->selectedStream, fn($q) => $q->where('stream_id', $this->selectedStream))
                    ->when($this->selectedClass, fn($q) => $q->where('school_class_id', $this->selectedClass));})
                    ->where('academic_year_id', $this->selectedAcademicYear)
                    ->when($this->selectedExaminationType, fn($q) => $q->where('exam_type_id', $this->selectedExaminationType))
                    ->get();

        }else{

            $examTypeIdsToExclude = $this->exam_types->whereIn('name', ['Monthly', 'Midterm'])->pluck('id');

            if($this->exam_types->where('id', $this->selectedExaminationType)->where('name', 'Terminal')->isNotEmpty()){
                $this->results = StudentResult::with(['student' => function($q) use($streamIds){$q->whereIn('stream_id',$streamIds)
                    ->when($this->selectedStream, fn($q) => $q->where('stream_id', $this->selectedStream))
                    ->when($this->selectedClass, fn($q) => $q->where('school_class_id', $this->selectedClass));}, 
                                                    'examType' => function($q){$q->when($this->selectedExaminationType, fn($q) => $q->where('id', $this->selectedExaminationType));}])
                        ->whereHas('student', function($query) use ($streamIds) {$query->whereIn('stream_id', $streamIds)
                        ->when($this->selectedStream, fn($q) => $q->where('stream_id', $this->selectedStream))
                        ->when($this->selectedClass, fn($q) => $q->where('school_class_id', $this->selectedClass));})
                        ->where('academic_year_id', $this->selectedAcademicYear)
                        ->when($this->selectedExaminationType, fn($q) => $q->where('exam_type_id', $this->selectedExaminationType))
                        ->get();
            }else{
                $this->results = StudentResult::with(['student' => function($q) use($streamIds){$q->whereIn('stream_id',$streamIds)
                    ->when($this->selectedStream, fn($q) => $q->where('stream_id', $this->selectedStream))
                    ->when($this->selectedClass, fn($q) => $q->where('school_class_id', $this->selectedClass));}, 
                                                    'examType' => function($q) use($examTypeIdsToExclude){$q->when($this->selectedExaminationType, fn($q) => $q->whereNotIn('id', $examTypeIdsToExclude));}])
                        ->whereHas('student', function($query) use ($streamIds) {$query->whereIn('stream_id', $streamIds)
                        ->when($this->selectedStream, fn($q) => $q->where('stream_id', $this->selectedStream))
                        ->when($this->selectedClass, fn($q) => $q->where('school_class_id', $this->selectedClass));})
                        ->where('academic_year_id', $this->selectedAcademicYear)
                        ->when($this->selectedExaminationType, fn($q) => $q->whereNotIn('exam_type_id', $examTypeIdsToExclude))
                        ->get();
            }
        }

        $tiesPerPositionByClass = $tiesPerPositionByStream = [];
        $allResults = $this->results->where('exam_type_id', $this->selectedExaminationType)->flatMap(function ($result) {
            $result->school_class_id = $result->student->school_class_id;
            $result->stream_id = $result->student->stream_id;
            return collect([$result]);
        });

        if ($allResults->isNotEmpty()) {

            $groupedByStream = $allResults->groupBy(function ($result) {
                return $result->stream_id . '-' . $result->month . '-' . $result->stream_position;
            });

            foreach ($groupedByStream as $group) {
                $firstResult = $group->first();
                $streamId = $firstResult->stream_id;
                $month = $firstResult->month;
                $position = $firstResult->stream_position;
                $tieCount = $group->count();

                if (!isset($tiesPerPositionByStream[$streamId])) {
                    $tiesPerPositionByStream[$streamId] = [];
                }
                if (!isset($tiesPerPositionByStream[$streamId][$month])) {
                    $tiesPerPositionByStream[$streamId][$month] = [];
                }
                $tiesPerPositionByStream[$streamId][$month][$position] = $tieCount;
            }

            $groupedByClass = $allResults->groupBy(function ($result) {
                return $result->school_class_id . '-' . $result->month . '-' . $result->position;
            });

            foreach ($groupedByClass as $group) {
                $firstResult = $group->first();
                $classId = $firstResult->school_class_id;
                $month = $firstResult->month;
                $position = $firstResult->position;
                $tieCount = $group->count();

                if (!isset($tiesPerPositionByClass[$classId])) {
                    $tiesPerPositionByClass[$classId] = [];
                }
                if (!isset($tiesPerPositionByClass[$classId][$month])) {
                    $tiesPerPositionByClass[$classId][$month] = [];
                }
                $tiesPerPositionByClass[$classId][$month][$position] = $tieCount;
            }
        }

        $this->tiesPerPositionByStream = $tiesPerPositionByStream;
        $this->tiesPerPositionByClass = $tiesPerPositionByClass;
        $allResults = $this->results->count();

        if ($allResults == 0) {
            $this->showNoMarksAlert = true;
        } else {
            $this->showNoMarksAlert = false;
        }

        if ($this->selectedExaminationType) {
            $this->examType =ExaminationType::find($this->selectedExaminationType)->name;
        }

        if($this->selectedAcademicYear){
            $acYear = $this->academic_years->where('id',$this->selectedAcademicYear)->first();
            $this->academic_year = $acYear->year;
        }

        return view('livewire.examinations.examination-results',);
    }

    public function downloadReport()
    {   
        $results = $this->results->where('exam_type_id',$this->selectedExaminationType)->sortBy('position');               
        $transformedData = $results->map(function ($result) {
            $position = empty($this->selectedStream)? $result->position : $result->stream_position;
            return [
                    'Student Name' => $result->student->user->name,
                    'Stream' => $result->student->stream->alias ?? 'N/A',
                    'Total Marks' => $result->total_marks ?? 'N/A',
                    'Average' => $result->average_marks ?? 'N/A',
                    'Grade' => $result->grade ?? 'N/A',
                    'Remark' => $result->remark ?? 'N/A',
                    'Position' => $position,
                ];
        })->values();

        $transformedData = $transformedData->map(function ($item, $key) {
            return array_merge(['S/N' => $key + 1], $item);
        });
        
        $schoolName = auth()->user()->school->name ?? 'School Report';

        if(!empty($this->selectedStream)){
           
            $stream = $results->first()->student->stream->first();
            $className = SchoolClass::where('id',$this->selectedClass)->pluck('name')->values()->first().' '.$stream->alias;
        }else{
            $className = SchoolClass::where('id',$this->selectedClass)->pluck('name')->values()->first();
        }

        $academicYear = AcademicYear::find($this->selectedAcademicYear)->year ?? 'All Years';

        return Excel::download(
            new StudentClassResultsExport($transformedData, $schoolName, $this->examType, $className, $academicYear),
            $className. ' results_report_' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}
