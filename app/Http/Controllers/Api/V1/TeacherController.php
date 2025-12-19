<?php

namespace App\Http\Controllers\Api\V1;
use App\Models\Mark;
use App\Models\Note;
use App\Models\User;
use App\Models\Media;
use App\Models\Stream;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Homework;
use App\Models\Attendance;
use App\Models\SchoolClass;
use Illuminate\Support\Str;
use App\Imports\UsersImport;
use App\Models\Announcement;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Mail\NewTeacherCreatedMail;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\StreamSubjectTeacher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\Http;


class TeacherController extends Controller
{

    public function toggleStatus(User $teacher)
{
    $teacher->is_verified = !$teacher->is_verified; // Toggle status
    $teacher->save();
    flash()->option('position', 'bottom-right')->success('Teacher status updated successfully!');
    return back();
}

public function removeStreamSubject($teacherId, $classId)
{
    $teacher = User::findOrFail($teacherId);

    $teacher->teachingStreams()->where('school_class_id', $classId)->delete();
    flash()->option('position', 'bottom-right')->success('Stream-subjects for the teacher have been removed successfully.');

    return redirect()->back();
}

public function subjectTeacher($student)
{
    $student = Student::with('stream')->findOrFail($student);

    $teachers = StreamSubjectTeacher::with(['teacher', 'subject'])
    ->where('stream_id', $student->stream->id)
    ->get();

    return view('student.subjectTeacher.index',compact('teachers','student'));
}

public function search(Request $request)
{
    $search = $request->input('search');
    $schoolId = Auth::user()->school_id;

    $roles = ['teacher', 'class teacher', 'academic teacher'];

    $teachers = User::latest()->where('school_id', $schoolId)
        ->whereHas('roles', function ($query) use ($roles) {
            $query->whereIn('name', $roles);
        })
        ->where(function($query) use ($search) {
            $query->where('fullname', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%');
        })
        ->paginate(5);

    return view('components.teacher.home', compact('teachers'));
}


public function updateTeacher(Teacher $teacher)
{

    {
        return view('teachers.manage', compact('teacher'));
    }
    // Return the filtered teachers' table as a partial view
    return view('components.teacher.home', compact('teachers'));
}


    public function index(Request $request)
    {
        $user = Auth::user(); // Get the authenticated user
        $schoolId = $user->school_id; // Get the school ID

        $roles = ['teacher', 'class teacher', 'academic teacher','header teacher','assistant headteacher'];
        $announcementsQuery = Announcement::where('school_id', $schoolId)->latest();

       // Role-based filtering
        if ($user->hasRole('header teacher')) {
            $announcementCount = $announcementsQuery->where('user_id','!=',$user->id)->where(function ($query) use ($user) {
                $query->WhereHas('user.roles', function ($subQuery) {
                        $subQuery->whereIn('name', ['assistant headteacher','academic teacher']);
                    });
            })->count();
        } elseif ($user->hasRole('academic teacher')) {
            // Academic teacher sees their own + headmaster's announcements
            $announcementCount = $announcementsQuery->where('user_id','!=',$user->id)->where(function ($query){
                $query->WhereHas('user.roles', function ($subQuery) {
                        $subQuery->whereIn('name', ['header teacher','assistant headteacher','academic teacher']);
                    });
            })->count();
        } elseif ($user->hasRole('class teacher')) {
            $announcementCount = $announcementsQuery->where('user_id','!=',$user->id)->count();
        } elseif ($user->hasRole('teacher')) {
            $announcementCount = $announcementsQuery->where('user_id','!=',$user->id)->where(function ($query) use ($user) {
                $query->whereIn('type', ['internal','both']);
            })->count();
        }else{
            $announcementCount = 0;
        }

        $teachers = User::latest()->where('school_id', $schoolId)
                                ->whereHas('roles', function ($query) use ($roles) {
                                    $query->whereIn('name', $roles);
                                })
                                ->get();
                                // dd(  $teachers);


        $students= Student::withCount('user')->where('school_id', $schoolId)->get();

                              $teachersCount = $teachers->count();
                              $studentsCount = $students->count();

        $classCount = SchoolClass::where('school_id', $user->school_id)->count();

        return view('components.teacher.home', compact('teachersCount','studentsCount','classCount','announcementCount',

        ));


    }

    public function edit($id)
    {
        $teacher = User::find($id);

        $academicTeacherCount = User::where('school_id', auth()->user()->school_id)
        ->whereHas('roles', function ($query) {
            $query->where('name', 'academic teacher');
        })
        ->count();
        $HeaderTeacherAssistCount = User::where('school_id', auth()->user()->school_id)
        ->whereHas('roles', function ($query) {
            $query->where('name', 'assistant headteacher');
        })
        ->count();
        return view('components.teacher.edit',['teacher'=>$teacher,'academicTeacherCount'=>$academicTeacherCount,'HeaderTeacherAssistCount'=>$HeaderTeacherAssistCount]);
    }



    public function editAssigment($id)
    {
        $schoolId = Auth::user()->school_id; // Get the current user's school_id
        $roles = ['teacher', 'class teacher', 'academic teacher'];

        // Get the teachers in the same school and with specific roles
        $subjects = Subject::with('school_id', $schoolId)->get();

        $teachers = User::latest()->where('school_id', $schoolId)
                                ->whereHas('roles', function ($query) use ($roles) {
                                    $query->whereIn('name', $roles);
                                })
                                ->get();

        $assignment = StreamSubjectTeacher::findorfail($id);

        return response()->json([
            'success'=>true,
            'teachers'=>$teachers,
            'subjects'=>$subjects,
            'assignment'=>$assignment
        ]);
    }

    public function create()
    {
        $excludedRoles = ['header teacher','head teacher', 'administrator', 'parent', 'student','class teacher'];

        $schoolId = Auth::user()->school_id;

        // Count academic teachers for the current school
        $academicTeacherCount = \App\Models\User::whereHas('roles', function ($query) {
            $query->where('name', 'academic teacher');
        })->where('school_id', $schoolId)->count();


        $roles = Role::whereNotIn('name', values: $excludedRoles)->get();


            return view('components.teacher.upload',compact('roles','academicTeacherCount'));
    }

     /**
     * Generate and download the Excel sample file with descriptive headers for the add teachers.
     *
     * @return \Illuminate\Http\Response
     */


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate request data
        $validatedData = $this->validateRequest($request);

        $role = '';

        if($request->role){
            $role = $request->role;
        }

        $headTeacher = Auth::user();

          return $this->createTeacher($validatedData,$role, $headTeacher);



    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $teacher= User::with(['subjects','streams'])->findOrFail($id);

        // $teacher= User::with('subjects')->findOrFail($id);
        // $teacher = User::with('schoolClass')->findOrFail($id);
        $classes = $teacher->streams->pluck('school_class_id')->unique();
        $classes = SchoolClass::whereIn('id',$classes);
        $classes = $classes->pluck('name')->unique();

        return view('components.teacher.view',compact('teacher', 'classes'));

    }

    /**
     * Update the specified resource in storage.



     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $teacher = User::find($id);
        if (!$teacher) {
            abort(404);
        }
        $teacher->delete();
        flash()->option('position', 'bottom-right')->success('You have deleted the staff');

        return back();
    }

    /**
     * Validate the request data.
     */
    private function validateRequest(Request $request)
    {
        return $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => ['nullable', 'string','max:255'],
            'sur_name' => 'required|string|max:255',
            'email' => ['nullable', 'email', 'unique:users,email'],
           'role' => ['nullable', 'exists:roles,name'],
            'phone' => ['required', 'regex:/^0[0-9]{9}$/', 'unique:users,phone'],
            'gender' => 'required|in:male,female',

        ]);
    }


    public function assignSubjectTeacher(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => [
                'required',
                'exists:users,id',
            ],
            'streams.*' => 'exists:streams,id',
        ]);

        $schoolClassId = $request->school_class_id;
        $schoolClass = SchoolClass::find($schoolClassId);
        $schoolClass->subjects()->attach($request->subject_id);

        // Get streams based on the request
        $streams = $request->all_streams === 'yes'
            ? Stream::where('school_class_id', $schoolClassId)->get()
            : Stream::whereIn('id', $request->streams)
                  ->where('school_class_id', $schoolClassId)
                  ->get();

        // Track duplicate assignments and conflicting assignments
        $duplicateStreams = [];
        $conflictingStreams = [];

        try {
            DB::beginTransaction();

            foreach ($streams as $stream) {
                // Check if the subject is already assigned to any teacher in the same stream
                $conflictExists = StreamSubjectTeacher::where('subject_id', $request->subject_id)
                    ->where('stream_id', $stream->id)
                    ->exists();

                if ($conflictExists) {
                    // Track conflicting streams
                    $conflictingStreams[] = $stream->name;
                } else {
                    // Check if the specific teacher is already assigned this subject in the stream
                    $alreadyAssigned = StreamSubjectTeacher::where('subject_id', $request->subject_id)
                        ->where('teacher_id', $request->teacher_id)
                        ->where('stream_id', $stream->id)
                        ->exists();

                    if (!$alreadyAssigned) {
                        StreamSubjectTeacher::create([
                            'stream_id' => $stream->id,
                            'school_class_id' => $schoolClassId,
                            'subject_id' => $request->subject_id,
                            'teacher_id' => $request->teacher_id,
                        ]);
                    } else {
                        $duplicateStreams[] = $stream->name;
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            // Log the error or handle it appropriately
            Log::error($e->getMessage());
            flash()->option('position', 'bottom-right')->error('An error occurred while assigning the subject teacher.');
            return redirect()->back();
        }

        // Handle conflicting assignments
        if (!empty($conflictingStreams)) {
            $conflictStreamNames = implode(', ', $conflictingStreams);
            flash()->option('position', 'bottom-right')
                ->warning("The subject is already assigned to another teacher in the following streams: {$conflictStreamNames}.");
        }

        // Handle duplicate assignments
        if (!empty($duplicateStreams)) {
            $duplicateStreamNames = implode(', ', $duplicateStreams);
            flash()->option('position', 'bottom-right')
                ->warning("The subject is already assigned to this teacher in the following streams: {$duplicateStreamNames}.");
        }

        // Success message if new assignments were made
        if (count($duplicateStreams) + count($conflictingStreams) < count($streams)) {
            flash()->option('position', 'bottom-right')->success('Subject teacher assigned successfully.');
        }

        return redirect()->back();
    }


    public function detachTeacherFromStreamSubject($teacherId, $streamSubjectId)
    {

        $teacher = User::findOrFail($teacherId);

    // Find the specific stream subject (using the stream subject ID)
    $streamSubject = $teacher->streamSubjects()->where('id', $streamSubjectId)->first();

    // Check if the stream subject exists and remove it
    if ($streamSubject) {
        $streamSubject->delete();  // This removes the relationship (if StreamSubject is a separate model)
    }

    // $schoolClass->subjects()->detach($subjectId);
    flash()->option('position', 'bottom-right')->success('Teacher removed from stream and subject.');

        // Redirect back with success message
        return redirect()->back();
    }

    private function createTeacher(array $validatedData,$role, $headTeacher)
    {
        $school = $headTeacher->school;
       $generatedPassword = Str::random(8);

        $teacher = User::create([
            'name' => $validatedData['first_name'].' '.($validatedData['middle_name'] ?? '').' '.$validatedData['sur_name'],
            'email' => $validatedData['email'],
            'gender' => $validatedData['gender'],
            'phone' => $validatedData['phone'],
            'username' => $validatedData['phone'],
            'role' => ['required','exists:roles,name'],
            'password' => $generatedPassword,
            'school_id' => $school->id,
            'is_verified' => true,
            'created_by' => auth()->user()->id
        ]);

        if($role){
            $teacher->assignRole($role);
        }else{

            $teacher->assignRole('teacher');
        }

 $smsMessage = "Hongera! Username yako ni {$teacher->username} na password ni {$generatedPassword}. Tafadhali tembelea www.shulemis.ac.tz ili kuanza.";

$response = Http::withToken(config('sms.SMS_API_KEY')) // API Key from .env
    ->acceptJson()
    ->post('https://sms.webline.co.tz/api/v3/sms/send', [
        'recipient' => $teacher->phone,
        'sender_id' => config('sms.SENDER_ID'), // e.g., TAARIFA
        'type'       => 'plain',
        'message'    => $smsMessage,
    ]);


      flash()->option('position','bottom-right')->success('Teacher successfully added and SMS sent!');

        return redirect()->route('teachers.index');
    }

    public function getSubjects()
    {
        $teacher = Auth::user();
        $subjects = Subject::where('teacher_id', $teacher->id)->get();

        return response()->json(['data' => $subjects], 200);
    }

    public function update(Request $request,$id)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => ['nullable', 'string','max:255'],
            'sur_name' => 'required|string|max:255',
            'email' => ['nullable', 'email'],
            'role' => ['nullable', 'exists:roles,name'],
            'phone' => ['required', 'regex:/^0[0-9]{9}$/'],
            'gender' => 'required|in:male,female',

        ]);

        try{
            DB::beginTransaction();

            $teacher = User::findorfail($id);
            $teacher->name=$request->first_name .' ' .($request['middle_name'] ?? '').' ' .$request->sur_name;
            $teacher->email=$request->email;
            $teacher->phone=$request->phone;
            $teacher->username=$request->phone;
            $teacher->gender=$request->gender;
            $teacher->update();
            if($request->has('role')){
                $teacher->syncRoles([$request->role]);
            }

            DB::commit();

            flash()->option('position','bottom-right')->success('succesfull update teacher');
            return redirect()->route('teachers.index');

        }catch(\Exception $e){
            DB::rollBack();
            flash()->option('position','bottom-right')->error('Error during Updating Due to :'.$e->getMessage());
            return back();
        }
    }

    public function storeMarks(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'mark' => 'required|integer|min:0|max:100',
            'comments' => 'nullable|string',
        ]);

        $teacher = Auth::user();

        $mark = Mark::updateOrCreate(
            ['student_id' => $validated['student_id'], 'subject_id' => $validated['subject_id']],
            ['teacher_id' => $teacher->id, 'mark' => $validated['mark'], 'comments' => $validated['comments']]
        );

        // return response()->json(['success' => true, 'data' => new MarkResource($mark)], 201);
    }

    public function getStudentMarks($student_id)
    {
        $teacher = Auth::user();
        $marks = Mark::where('student_id', $student_id)->where('teacher_id', $teacher->id)->get();

        return response()->json(['data' => $marks], 200);
    }

    public function commentOnPerformance(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'comments' => 'required|string',
        ]);

        $mark = Mark::where('student_id', $validated['student_id'])
                    ->where('subject_id', $validated['subject_id'])
                    ->first();

        if ($mark) {
            $mark->comments = $validated['comments'];
            $mark->save();

            return response()->json(['success' => true, 'message' => 'Comment added successfully']);
        }

        return response()->json(['error' => 'Marks not found for the student and subject'], 404);
    }

    public function getAnnouncements()
    {
        $announcements = Announcement::where('school_id', Auth::user()->school_id)->get();

        return response()->json(['data' => $announcements], 200);
    }

    public function searchForm(Request $request)
    {
        $schoolId = Auth::user()->school_id;
        $teacherRoles = ['teacher', 'class teacher', 'academic teacher'];

        $teachers = User::where('school_id', $schoolId)
            ->whereHas('roles', function ($query) use ($teacherRoles) {
                $query->whereIn('name', $teacherRoles);
            });

        if ($request->has('search')) {
            $search = $request->input('search');
            $teachers = $teachers->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $teachers = $teachers->paginate(10);

        return response()->json([
            'html' => view('components.teacher.teacher_list', compact('teachers'))->render(),
        ]);
    }

    public function getAttendanceReport($student_id)
    {
        $attendance = Attendance::where('student_id', $student_id)->get();

        return response()->json(['data' => $attendance], 200);
    }

    public function setHomework(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'details' => 'required|string',
            'due_date' => 'required|date',
        ]);

        Homework::create([
            'teacher_id' => Auth::user()->id,
            'subject_id' => $validated['subject_id'],
            'details' => $validated['details'],
            'due_date' => $validated['due_date'],
        ]);

        return response()->json(['success' => true, 'message' => 'Homework created successfully'], 201);
    }

    public function getHomework($subject_id)
    {
        $teacher = Auth::user();
        $homework = Homework::where('subject_id', $subject_id)->where('teacher_id', $teacher->id)->get();

        return response()->json(['data' => $homework], 200);
    }

    public function uploadNotes(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'file' => 'required|file|mimes:pdf,docx,txt',
        ]);

        $path = $request->file('file')->store('notes');

        Note::create([
            'teacher_id' => Auth::user()->id,
            'subject_id' => $validated['subject_id'],
            'file_path' => $path,
        ]);

        return response()->json(['success' => true, 'message' => 'Note uploaded successfully'], 201);
    }

    public function uploadMedia(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'file' => 'required|file|mimes:mp4,mp3',
        ]);

        $path = $request->file('file')->store('media');

        Media::create([
            'teacher_id' => Auth::user()->id,
            'subject_id' => $validated['subject_id'],
            'file_path' => $path,
        ]);

        return response()->json(['success' => true, 'message' => 'Media uploaded successfully'], 201);
    }

    public function teacherSubject()
    {
        $defaultColors =
        [
            '#8BC34A', '#FF5722', '#9C27B0', '#FFEB3B', '#E91E63', '#2196F3',
            '#03A9F4', '#795548', '#673AB7', '#009688', '#FF9800', '#F44336',
            '#607D8B', '#FF6F00', '#6A1B9A'
        ];

        $schoolId = auth()->user()->school_id;

        $subjects = Subject::where('school_id', $schoolId)
        ->with('schoolClasses')
        ->get()
        ->map(function ($subject, $index) use ($defaultColors) {
            $subject->color = $defaultColors[$index % count($defaultColors)];
            return $subject;
        });

        return view('subjects.teacher_subject', compact('subjects'));
    }

}
