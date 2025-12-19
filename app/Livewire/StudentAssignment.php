<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Stream;
use App\Models\Student;
use App\Models\Assignment;

class StudentAssignment extends Component
{
    public $student_id;
    public $student;
    public $students = [];
    public $assignments = [];

    public function mount()
    {
        $user = auth()->user();


        $this->student_id = request('student');

        if ($user && $user->parent) {
            $this->students = $user->parent->students;
        }

        $this->fetchAssignments();
    }

    public function fetchAssignments()
    {
        if (!$this->student_id) {
            $this->assignments = collect(); // Ensure it is a Collection
            return;
        }
    
        $this->student = Student::find($this->student_id);
    
        if ($this->student) {
            $this->assignments = Assignment::where('stream_id', $this->student->stream_id)->get();
        } else {
            $this->assignments = collect(); // Convert to Collection
        }
    }
    

    public function render()
    {
        return view('livewire.student-assignment', [
            'students' => $this->students,
            'assignments' => $this->assignments,
        ]);
    }
}
