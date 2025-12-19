<?php

namespace App\Livewire;

use App\Models\Assignment;
use App\Models\StreamSubjectTeacher;
use Livewire\Component;
use Livewire\WithPagination;

class AssignmentList extends Component
{

    use WithPagination;

   
    public $streams;
    public $subjects;
    public $search = '';
    public $selectedSubject = null;
    public $selectedStream = null;
    public $teacher = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedSubject' => ['except' => null],
        'selectedStream' => ['except' => null],
    ];

    
    public function mount()
    {

       $this->teacher= auth()->user();

       
       $streamSubjects = StreamSubjectTeacher::with(['subject', 'stream'])

       ->where('teacher_id', $this->teacher->id)

       ->get();
   
       $this->subjects = $streamSubjects

       ->pluck('subject')

       ->filter()

       ->unique('id')

       ->values();

       // Ensure no duplicate subjects
       $this->streams = $streamSubjects->pluck('stream')->unique('id');  
        // Fetch all subjects
    }

    public function render()
    {


        $assignments = Assignment::where('teacher_id', $this->teacher->id)
        ->when($this->search, function ($query) {
            $query->where('title', 'like', '%' . $this->search . '%');
        })
        ->when($this->selectedStream, function ($query) {
            $query->whereHas('streams', function ($streamQuery) {
                $streamQuery->where('streams.id', $this->selectedStream); // Explicitly specify 'streams.id'
            });
        })
        ->when($this->selectedSubject, function ($query) {
            $query->where('subject_id', $this->selectedSubject);
        })
        ->with(['subject', 'streams'])
        ->latest()
        ->paginate(10);
    


        return view('livewire.assignment-list',compact('assignments'));
    }
}
