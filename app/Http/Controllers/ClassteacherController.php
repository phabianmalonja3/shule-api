<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Announcement;
use App\Models\Assignment;
use Illuminate\Http\Request;
use App\Models\StreamSubject;
use Illuminate\Support\Facades\DB;
use App\Models\StreamSubjectTeacher;
use Illuminate\Support\Facades\Auth;
use App\Models\AcademicYear;
use App\Models\Stream;
use Livewire\Component;
use App\Models\SchoolClass;

class ClassteacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $teacher = auth()->user();   

        $announcements = Announcement::where('school_id', $teacher->school_id)->whereIn('type',['internal','both'])
                                        ->orderByDesc('created_at')->take(10)->get(); // Replace with actual query for announcement count
    
        $data = $this->getTeacherAssignments(auth()->user()->id);
        $schoolId = $teacher->school_id;
        $search = $request->input('search');

        $students = DB::table('students')
            ->select(
                'students.*',
                'schools.name as school_name',
                'school_classes.name as class_name'
            )
            ->join('schools', 'students.school_id', '=', 'schools.id')
            ->join('school_classes', 'students.school_class_id', '=', 'school_classes.id')
            ->where('students.school_id', $schoolId)
            ->when($search, function ($query, $search) {
                $query->where('students.reg_number', 'like', "%$search%");
            })
            ->paginate(10);

        $academicYear = AcademicYear::where('school_id', $teacher->school->id)
                                    ->where('is_active', true)
                                    ->first();

        $teacherStreamsCount = User::with('streams')->count();
        $streamSubjects = StreamSubjectTeacher::with(['subject', 'stream.schoolClass'])->where('teacher_id', $teacher->id)->get();
    
        $subjects = $streamSubjects->pluck('subject')->filter()->unique('id')->values();
        $subjectIds =  $subjects->pluck('id')->values()->unique()->toArray();
        $streams = $streamSubjects->pluck('stream')->unique('id'); 
        $streamIds = $streams->pluck('id')->values()->unique()->toArray();
        $classes = $streamSubjects->pluck('stream.schoolClass')->unique('id');   

        $resultStatus = Student::whereHas('marks', function($q) use($subjectIds, $academicYear){$q->whereIn('subject_id',$subjectIds)
                                                         ->where('academic_year_id',$academicYear->id);})
                      ->whereIn('stream_id',$streamIds)->first();

        return view('teacher.panel',compact('data','students','teacherStreamsCount','subjects','streams','classes','announcements','resultStatus'));
    }

    /**
     * Show the form for creating a new resource.
     */

     

    protected function getTeacherAssignments($teacherId)
    {
        $assignments =StreamSubjectTeacher::with(['subject', 'stream.schoolClass',])
            ->where('teacher_id', $teacherId)
            ->get();

            $data = [
                'class_count' => $assignments->pluck('stream.class.id')->unique()->count(),
                'stream_count' => $assignments->pluck('stream.id')->count(),
                'subject_count' => $assignments->pluck('subject.id')->unique()->count(),
                'students_count' => $this->countStudents($assignments), // Optional: Count students in streams
                'assignments' => $assignments, // Detailed assignments
            ];

            return $data;
    }
    private function countStudents($assignments)
    {
        $streamIds = $assignments->pluck('stream.id')->unique();
    
        return Student::whereIn('stream_id', $streamIds)->count();
    }
    
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
