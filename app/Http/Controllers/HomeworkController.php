<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Subject;
use App\Models\Homework;
use Illuminate\Http\Request;
use App\Models\StreamSubject;
use App\Models\StreamSubjectTeacher;
use Illuminate\Support\Facades\Auth;

class HomeworkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('homework.index');
    }

    public function create()
{
  
    // dd($streams->schoolClass);
    // Pass the data to the view, including classes, subjects, and streams
    return view('homework.create');
}
    public function show()
{

    return view('homework.view');
}




public function completionRate($homeworkId)
{
    $homework = Homework::with('submissions')->find($homeworkId);

    $totalStudents = Student::count(); // Adjust based on your system
    $completed = $homework->submissions->where('status', 'completed')->count();

    $completionRate = ($completed / $totalStudents) * 100;

    return view('homeworks.completion_rate', compact('homework', 'completionRate'));
}

}
