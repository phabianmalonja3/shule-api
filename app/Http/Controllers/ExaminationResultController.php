<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\StudentResult;
use App\Models\ExaminationResult;
use App\Models\District;

class ExaminationResultController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {   
        return view('results');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function studentResults(Student $student)
    {

        
        $user = auth()->user(); 
        // $students = $user->parent->students; 
        $students = $user->parent->students;
        $student =$students->first();
        $school= $student->school;

        


        return view('student.results.view',compact('student'));
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
    public function show(ExaminationResult $examinationResult)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExaminationResult $examinationResult)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExaminationResult $examinationResult)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExaminationResult $examinationResult)
    {
        //
    }
}
