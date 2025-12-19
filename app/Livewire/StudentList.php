<?php

namespace App\Livewire;

use App\Models\Stream;
use App\Models\Student;
use Livewire\Component;
use App\Models\SchoolClass;
use Livewire\WithPagination;
use App\Exports\StudentsExport;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\User;
use App\Models\Combination;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StudentList extends Component
{
    use WithPagination;
    public $classId; 
    public $streamId; 
    public $search = '';
    public $sort = 'desc'; 
    public $streams = []; 
    public $classes = [];
    public $allTeacherStreams = [];

    public function mount()
    {           
        $ac_yr = AcademicYear::where('school_id', auth()->user()->school->id)
                                    ->where('is_active', true)
                                    ->first();

        if(auth()->user()->hasAnyRole(['header teacher','head teacher','assistant headteacher','academic teacher'])){

            $teacher = auth()->user();
            $classes = SchoolClass::where('school_id', $teacher->school->id)->get();
            $streams = Stream::where('school_class_id', $classes->first()->id);

        }else{

            $teacher = User::with([
                'streams',
                'streamSubjects.stream:id,school_class_id'
            ])->whereHas('streamSubjects.stream.schoolClass', function($q) use($ac_yr){$q->where('academic_year_id', $ac_yr->id)->orWhere('teacher_class_id',auth()->user()->id);})
            ->findOrFail(auth()->user()->id);

            $streamSubjectClassIds = $teacher->streamSubjects
                                    ->filter(fn($ss) => $ss->stream)
                                    ->map(function ($streamSubject) {
                                        return [
                                            'stream_id' => $streamSubject->stream->id,
                                            'school_class_id' => $streamSubject->stream->school_class_id
                                        ];
                                    });

            $teacherStreamIds = $teacher->streams->map(function ($stream) {
                                        return [
                                            'stream_id' => $stream->id,
                                            'school_class_id' => $stream->school_class_id,
                                        ];
                                    });

            $allTeacherClassesAndStreams = $teacherStreamIds->concat($streamSubjectClassIds);
            $this->allTeacherStreams = $allTeacherClassesAndStreams->pluck('stream_id')->unique();
            $allTeacherClasses = $allTeacherClassesAndStreams->pluck('school_class_id')->unique();  
            
            $streams = Stream::whereIn('id',$this->allTeacherStreams)->orderBy('school_class_id');
            $classes = SchoolClass::whereIn('id',$allTeacherClasses)->where('school_id', auth()->user()->school->id)->orderBy('name')->get();                      
        }

        $this->classes = $classes;
        $this->classId = $classes->first()->id ?? null;
        $this->streams = $streams->where('school_class_id',$this->classId)->get()?? null;

        // if ($streams->count()  > 1) {

        //     $classIds = $streams->pluck('school_class_id');

        // }


        $this->streamId = null; // $streams->first()->id ?? null;

    }

    public function updatedClassId($value)
    {
        if(auth()->user()->hasAnyRole(['header teacher','head teacher','assistant headteacher','academic teacher'])){
            $this->streams = Stream::where('school_class_id', $value)->get();
        }else{
            $this->streams = Stream::where('school_class_id', $value)->whereIn('id',$this->allTeacherStreams)->get();
        }

        $this->streamId = null;
    }

    public function updatedStreamId($value)
    {
        $stream = Stream::find($value);
        $this->classId = $stream->school_class_id;
    }

    public function updating($field)
    {
        if ($field === 'classId' || $field === 'streamId' || $field === 'search') {
            $this->resetPage();
        }
    }

    public function exportToExcel()
    {
        $timestamp = now()->format('Y-m-d_H-i-s');

        if ($this->streamId) {
            // Export students for the selected stream
            $stream = Stream::findOrFail($this->streamId);
            $students = Student::with('stream', 'schoolClass','parents')
                ->where('stream_id', $this->streamId);

            $filename = "student_list_{$stream->name}_{$timestamp}.xlsx";
        } elseif ($this->classId) {

            $class = SchoolClass::find($this->classId);
            $students = Student::with('parents')->where('school_class_id', $class->id);


            $filename = "student_list_of_{$class->name}_{$timestamp}.xlsx";
        } else {
            $students = Student::with('stream', 'schoolClass','parents')->where('school_id',auth()->user()->school_id);

            $filename = "student_list_{$timestamp}.xlsx";
        }
        $studentsCollection = $students->get();
        return Excel::download(new StudentsExport($studentsCollection), $filename);
    }

    public function render()
    {
        $unassignedStudents = [];
        $currentYear = Carbon::now()->year;
        
        if(auth()->user()->hasAnyRole(['header teacher','head teacher','assistant headteacher','academic teacher'])){

            $query = Student::with(['user', 'stream','parents','combinations'=>function ($query) use ($currentYear) {
                $query->where(DB::raw('YEAR(student_combination.created_at)'), $currentYear)->orderByDesc('updated_at');
            }])
                ->where('school_id', auth()->user()->school_id)
                ->when($this->classId, function ($q) {
                    $q->where('school_class_id', $this->classId);
                })
                ->when($this->streamId, function ($q) {
                    $q->where('stream_id', $this->streamId);
                })
                ->when($this->search, function ($q) {
                    $q->whereHas('user', function ($subQuery) {
                        $subQuery->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('reg_number', 'like', '%' . $this->search . '%');
                    });
                })
                ->orderBy('created_at', $this->sort);

            $unassignedStudents = Student::whereDoesntHave('combinations', function ($query) use ($currentYear) {
                $query->where(DB::raw('YEAR(student_combination.created_at)'), $currentYear);
            })
            ->with('user') 
            ->when($this->classId, function ($q) {$q->where('school_class_id', $this->classId);})
            ->when($this->streamId, function ($q) {$q->where('stream_id', $this->streamId);})
            ->get();            

        }else{

            $ac_yr = AcademicYear::where('school_id', auth()->user()->school->id)
                            ->where('is_active', true)
                            ->first();

            $teacher = User::with([
                'streams',
                'streamSubjects.stream:id,school_class_id'
            ])->whereHas('streamSubjects.stream.schoolClass', function($q) use($ac_yr){$q->where('academic_year_id', $ac_yr->id)->orWhere('teacher_class_id',auth()->user()->id);})
            ->whereHas('streams',function($q){$q->where('school_class_id',$this->classId);})
            ->findOrFail(auth()->user()->id);

            $teacherStreamIds = $teacher->streams->map(function ($stream) {return [
                                                        'stream_id' => $stream->id,
                                                        'school_class_id' => $stream->school_class_id,
                                                    ];});

            $streamSubjectClassIds = $teacher->streamSubjects
                                    ->filter(fn($ss) => $ss->stream)
                                    ->map(function ($streamSubject) {
                                        return [
                                            'stream_id' => $streamSubject->stream->id,
                                            'school_class_id' => $streamSubject->stream->school_class_id,
                                        ];
                                    });

            $allTeacherStreamsAndClasses = $teacherStreamIds->concat($streamSubjectClassIds);

            if($teacherStreamIds->isNotEmpty()){
                $uniqueTeacherClasses = $teacherStreamIds->concat($streamSubjectClassIds)->unique()->values();
            }else{
                $uniqueTeacherClasses = $streamSubjectClassIds->unique()->values();
            }

            $uniqueTeacherClasses = $allTeacherStreamsAndClasses->unique(function ($item) {
                                    return $item['stream_id'] . '-' . $item['school_class_id'];
                                })->values();

            $uniqueStreamIdsForTeacher = $uniqueTeacherClasses->pluck('stream_id')->unique()->values();

            $query = Student::with(['user', 'stream','parents','combinations'=>function ($query) use ($currentYear) {
                $query->where(DB::raw('YEAR(student_combination.created_at)'), $currentYear)->orderByDesc('updated_at');
            }])
                            ->where('school_id', auth()->user()->school_id);

            if ($uniqueStreamIdsForTeacher->isNotEmpty()) {
                $query->whereIn('stream_id', $uniqueStreamIdsForTeacher);
                $unassignedStudents = Student::whereDoesntHave('combinations', function ($query) use ($currentYear) {
                    $query->where(DB::raw('YEAR(student_combination.created_at)'), $currentYear);
                })
                ->with('user') 
                ->whereIn('stream_id', $uniqueStreamIdsForTeacher)
                ->when($this->classId, function ($q) {$q->where('school_class_id', $this->classId);})
                ->when($this->streamId, function ($q) {$q->where('stream_id', $this->streamId);})
                ->get();

            } else {
                $query->whereRaw('1 = 0');
            }

            $query->when($this->classId, function ($q) {$q->where('school_class_id', $this->classId);})
                ->when($this->streamId, function ($q) {$q->where('stream_id', $this->streamId);})
                ->when($this->search, function ($q) {$q->whereHas('user', function ($subQuery) {$subQuery->where('name', 'like', '%' . $this->search . '%')
                                                        ->orWhere('reg_number', 'like', '%' . $this->search . '%');});})
                ->orderBy('created_at', $this->sort);
        }

        $combinations = Combination::all();
        $students = $query->paginate(20);

        return view('livewire.student-list', compact('students','combinations','unassignedStudents'));
    }
}
