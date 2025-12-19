<?php

namespace App\Livewire\Homeworks;

use App\Models\Student;
use Livewire\Component;
use App\Models\Homework;
use Livewire\WithFileUploads;
use App\Models\HomeworkSubmission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HomeWorkShow extends Component
{


    use WithFileUploads;

    public $homework;
    public $student_id;
    public $completionRate = 0;
    public $file;
    public $hasSubmitted = false;

    public function mount(){

        $this->homework = Homework::find(request('homework'));
        $this->calculateCompletionRate();


        if(auth()->user()->hasRole('student')){
            $this->hasSubmitted = HomeworkSubmission::where('homework_id', $this->homework->id)
            ->where('student_id', auth()->user()->student->id)
            ->exists();
        }
       
        // dd( $this->homework );

    }

    public function calculateCompletionRate()
    {


        // Get students who belong to the same stream as the homework
        $totalStudents = Student::where('stream_id', $this->homework->stream_id)->count();
    
        // Count how many of these students have submitted
        $completed = HomeworkSubmission::where('homework_id', $this->homework->id)
                        ->where('submission_status', 'submitted')
                        ->count();

                        // dd($completed);
    
        // Calculate the completion rate
        $this->completionRate = $totalStudents > 0 ? ($completed / $totalStudents) * 100 : 0;
    }



    public function uploadFile()
    {



        $this->validate([
            'file' => 'required|file|mimes:pdf,doc,docx|max:2048', // Only allow PDF files (max 2MB)
        ]);

        $this->student_id= auth()->user()->student->id;

        // dd($this->student_id);

    
        DB::beginTransaction(); // Start transaction
    
        try {

            $this->hasSubmitted = HomeworkSubmission::where('homework_id', $this->homework->id)
            ->where('student_id', auth()->user()->student->id)
            ->exists();

           

            

            $filePath = $this->file->store('homework_submissions', 'public');
    
            HomeworkSubmission::create([
                'homework_id' => $this->homework->id,
                'student_id' =>  $this->student_id, // Get the logged-in student's ID
                'teacher_id' => $this->homework->teacher_id,
                'stream_id' => auth()->user()->student->stream->id,
                'file_path' => $filePath,
                'submission_status' => 'submitted',
                'submission_date' => now(),
            ]);
    
            DB::commit(); // Commit transaction
            flash()->option('position', 'bottom-right')->success('Homework submitted successfully!');
    

            $this->file = null;
            $this->calculateCompletionRate(); // Refresh progress bar
            return redirect()->route('student.panel');

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction if an error occurs
            Log::error('Homework submission failed: ' . $e->getMessage());
            flash()->option('position', 'bottom-right')->error('Something went wrong! Please try again.'.$e->getMessage());
    
        }
    }


    
    public function render()
    {

    
        return view('livewire.homeworks.home-work-show');
    }
}
