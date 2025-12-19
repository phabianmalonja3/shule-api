<?php
use Livewire\Component;
use App\Models\Assignment;
use App\Models\Student;
use App\Models\Stream;

class AssignmentList extends Component
{
    public $student_id;
    public $stream_id;
    public $student;
    public $students;
    public $streams;
    public $assignments = [];

    public function mount()
    {

         $user = auth()->user();
        $this->students = $user->parent->students;

        $this->students = Student::with('user')->get();
        $this->streams = Stream::all();
        $this->student = Stream::all();
    }

    public function fetchAssignments()
    {

        $user = auth()->user();
        $this->students = $user->parent->students;

        if ($this->students->isNotEmpty()) {
            $this->student = $this->students->first();
            $this->student_id =  $this->students->first()->id;
          
        }

        if ($this->student_id && $this->stream_id) {
            $this->assignments = Assignment::whereHas('student', function ($query) {
                $query->where('id', $this->student_id)->where('stream_id', $this->stream_id);
            })->get();
        } else {
            $this->assignments = [];
        }
    }

    public function render()
    {
        return view('livewire.assignment-list');
    }
}

