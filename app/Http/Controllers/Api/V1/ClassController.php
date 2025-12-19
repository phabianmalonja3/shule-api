<?php
namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\Stream;
use App\Models\Subject;
use App\Models\TimeTable;
use App\Models\SchoolClass;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\StreamSubjectTeacher;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ClassResource;
use App\Http\Resources\ClassCollection;

class ClassController extends Controller
{
    public function index()
    {
        $classes = SchoolClass::with(['streams.teacher', 'streams.subjects']) 
            ->latest()
            ->where('school_id', auth()->user()->school_id)
            ->paginate(10);

        $missing_streams = [];
        $missing_teachers = [];
        $missing_subject_teachers = [];

        foreach ($classes as $class) {
            if ($class->streams->isEmpty() && $class->teacher_class_id == '') {
                $missing_streams[] = $class->name;
            }

            $assigned_teachers = StreamSubjectTeacher::with(['stream', 'teacher', 'subject'])
                                ->whereHas('stream', function ($query) use($class) {
                                    $query->where('school_class_id', $class->id);
                                })
                                ->get();

            $all_class_streams_count = $class->streams->count();

            if ($class->streams->isNotEmpty()){

                foreach ($class->streams as $stream) {
                    if($stream->teacher_id == ''){
                        $missing_teachers[] = $class->name;
                    }
                    foreach ($stream->subjects as $subject) {

                        $assigned_streams_for_this_subject = $assigned_teachers->where('subject_id', $subject->id)->pluck('stream_id')->unique();
                        $is_subject_fully_assigned_to_class = ($all_class_streams_count > 0 && $assigned_streams_for_this_subject->count() === $all_class_streams_count);

                        if (!$is_subject_fully_assigned_to_class) {
                            $missing_subject_teachers[] = [
                                'class' => $class->name,
                                'stream' => $stream->name,
                                'subject' => $subject->name,
                            ];
                        }
                    }
                }

            }
        }
                  
        return view('class.list', compact('classes', 'missing_streams', 'missing_teachers', 'missing_subject_teachers'));
    }

    public function getClasses()
    {
        $classes = SchoolClass::all();
        return response()->json($classes);
    }

    public function create()
    {
        $schoolId = Auth::user()->school_id; 
        $roles = ['teacher', 'class teacher', 'academic teacher'];

        $teachers = User::latest()->where('school_id', $schoolId)
                        ->whereHas('roles', function ($query) use ($roles) {
                            $query->whereIn('name', $roles);
                        })
                        ->get();

        return view('class.create',compact('teachers'));
    }

    public function store(Request $request)
    {
        try{
            DB::beginTransaction();
                $validatedData = $request->validate([
                    'name' => [
                        'required',
                        'string',
                        'max:255',
                        Rule::unique('school_classes')->where(function ($query) use ($request) {
                            return $query->where('school_id', $request->user()->school_id);
                        }),
                    ],
                    'teacher_class_id' => ['nullable', 'exists:users,id'],
                ]);
        
            $academic_year = AcademicYear::where('school_id',$request->user()->school_id)
            ->where('is_active',true)
            ->first();

            SchoolClass::create([
                'name' => $validatedData['name'],
                'school_id' => $request->user()->school_id,
                'teacher_class_id' => $request->teacher_class_id,
                'academic_year_id'=>$academic_year->id

            ]);

            DB::commit();
            flash()->option('position', 'bottom-right')->success('New class created succesfully.');
            return redirect()->route('classes.index');

        }catch(\Exception $e){

            DB::rollBack(); 
            flash()->option('position', 'bottom-right')->error('Error in creating class: ' . $e->getMessage());
            return back();
        }
    }

    public function show($id)
    {
        $schoolId = Auth::user()->school_id; 
        $roles = ['teacher', 'class teacher'];
    
        $teachers = User::latest()->where('school_id', $schoolId)
                                ->whereHas('roles', function ($query) use ($roles) {
                                    $query->whereIn('name', $roles);
                                })
                                ->get();
    
        $class = SchoolClass::with(['streams.teachers', 'streams.subjects'])->findOrFail($id);
    
        $subjects = Subject::where('school_id', $schoolId)->get();
    
        $assignedTeachers = $class->streams->flatMap(function ($stream) {
            return $stream->teachers;
        })->unique('id'); 
     
        $assignedTeachers1 = User::whereHas('streamSubjects', function ($query) use ($class) {
            $query->whereHas('stream', function ($q) use ($class) {
                $q->where('school_class_id', $class->id);
            })
            ->whereHas('subject'); 
        })->with(['streamSubjects' => function ($query) {
            $query->whereHas('subject'); 
        }, 'streamSubjects.subject', 'streamSubjects.stream'])->get();

        $timetables = TimeTable::where('school_class_id', $id)
                                ->with(['subject', 'teacher', 'stream']) 
                                ->get()
                                ->groupBy('day');
        
        return view('class.view', compact('class', 'teachers', 'subjects', 'assignedTeachers','assignedTeachers1','timetables'));
    }
    
    public function update(Request $request, $id)
    { 
        $validatedData = $request->validate([
            'name' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('school_classes', 'name')
                    ->where('school_id', $request->user()->school_id) 
                    ->ignore($id) 
            ],
            'teacher_class_id' => 'sometimes|exists:users,id',
        ]);

        $class = SchoolClass::findOrFail($id);

        DB::beginTransaction();

        $class->name = $validatedData['name'];

        if($request->teacher_class_id){
            $class->teacher_class_id =  (int)$request->teacher_class_id;
        }

        $class->save();

        if ($request->teacher_class_id) {

            if($request->teacher_class_id != $request->old_teacher_class_id){
                $data = [
                    'name' => 'A',
                    'stream_teacher_id' => (int)$request->teacher_class_id,
                    'teacher_id' => (int)$request->teacher_class_id,  
                    'school_class_id' => $class->id,
                    'alias' => 'A'
                ];

                Stream::where('teacher_id', $request->old_teacher_class_id)
                      ->where('school_class_id', $class->id)->delete();
                Stream::create($data);
                $user = User::findorfail($request->teacher_class_id);

                $user->syncRoles(['class teacher']);
            }

            if(!empty($request->old_teacher_class_id)){
                $class_teacher_status_of_old_teacher = Stream::where('teacher_id',$request->old_teacher_class_id)
                                                ->orWhere('stream_teacher_id',$request->old_teacher_class_id)
                                                ->count();

                $user = User::findorfail($request->old_teacher_class_id);  

                if($class_teacher_status_of_old_teacher == 0 && !$user->hasAnyRole(['academic teacher','head teacher','header teacher','assistant headteacher'])){

                    $user->syncRoles(['teacher']);
                }
            }
        }

        flash()->option('position', 'bottom-right')->success('Changes made succesfully.');
        DB::commit();

        return redirect()->route('classes.index');
    }

    public function destroy($id)
    {
        $class = SchoolClass::findOrFail($id);
        
        $class->delete();
        flash()->option('position', 'bottom-right')->success('A class deleted succesfully.');


        return back();
    }

    public function edit($id)
    {
        $roles = ['teacher', 'class teacher', 'academic teacher'];

        $schoolId = Auth::user()->school_id; // 
        $teachers = User::latest()->where('school_id', $schoolId)
        ->whereHas('roles', function ($query) use ($roles) {
            $query->whereIn('name', $roles);
        })
        ->get();
        
        $class = SchoolClass::findOrFail($id);

        return view('class.create',compact('class','teachers'));
    }
}
