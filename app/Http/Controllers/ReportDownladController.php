<?php

namespace App\Http\Controllers;
use App\Models\Mark;
use App\Models\User;
use App\Models\Student;
use App\Models\GradeScale;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportDownladController extends Controller
{

    protected $student;
    protected $teacherName;
    protected $headTeacher;
    protected $totalStudents;

    
    
    public function __invoke(Student $student)
    {


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
                        ->whereHas('examType',function ($query)  {
                            $query->where('name', 'Annual');
                        
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
