<?php

namespace App\Livewire\Assignments;

use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\StreamSubjectTeacher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class AssignmentCreate extends Component
{




    use WithFileUploads;


    public $title,$subject_id,$file,$due_date,$description ,$all_streams ;

public $subjects=[];
public $streams=[];
public $streams_ids=[];
public $assignment;


    protected $rules=[

        'title' => 'required|string|max:255',

        'subject_id' => 'required|exists:subjects,id',

        // 'streams_ids' => 'required|array|min:1', // Must be an array and not empty
        // 'streams_ids.*' => 'exists:streams,id', 

        'file' => 'required|file|mimes:pdf,doc,docx|max:2048',
        
        'due_date' => 'required|date',
    ];


    public function mount($assignment = null)
{
    $teacher = Auth::user();

    // Fetch the streams and subjects related to the teacher
    $streamSubjects = StreamSubjectTeacher::with(['subject', 'stream'])
        ->where('teacher_id', $teacher->id)
        ->get();

    // Extract unique subjects and streams
    $subjects = $streamSubjects->pluck('subject')->filter()->unique('id')->values();
    $streams = $streamSubjects->pluck('stream')->unique('id');


    // If editing an existing assignment, populate the fields
    if ($assignment) {

        // dd($assignment->audits);
        $this->assignment = $assignment;
        $this->title = $assignment->title;
        $this->subject_id = $assignment->subject_id;
        $this->streams_ids = $assignment->streams->pluck('id')->toArray(); // Always an array
        $this->description = $assignment->description;
    } else {
        // Initialize streams_ids properly (even when there's only one stream)
        $this->streams_ids = $streams->count() === 1 ? [$streams->first()->id] : [];
        $this->subject_id = $subjects->count() === 1 ? $subjects->first()->id : null;
    }

    $this->subjects = $subjects;
    $this->streams = $streams;
}




    public function storeAssignment()
    {
try
    {

$teacher = Auth::user();



    $streamSubjects = StreamSubjectTeacher::with(['subject', 'stream'])
        ->where('teacher_id', $teacher->id)
        ->get();

    $streams = $streamSubjects->pluck('stream')->unique('id');
    $streamIds = $this->all_streams === 'yes' 
    ? $streams->pluck('id')->toArray() 
    : $this->streams_ids; 

// dd($streamIds);

        $this->validate();


 
        if($this->file){

        $path = $this->file->store('assigments','public');

        }
        
        DB::beginTransaction();

        $assignment = Assignment::create(
            [
            'title' => $this->title,

            'subject_id' => $this->subject_id,

            'description'=>$this->description,
           
            'due_date' => $this->due_date,

            'teacher_id' => auth()->id(),

            'file_path' => $path
        ]
    );

    $assignment->streams()->attach($streamIds);
        
     
        

        flash()->option('position', 'bottom-right')->success('Assigment Created successfully.');
    
     DB::commit();

    }catch(\Exception $e)
    {

     DB::rollBack();

     Log::error('error' .$e->getMessage());

     flash()

     ->option('position', 'bottom-right')

     ->error('Error in creating  Assigments.' .$e->getMessage());
     return back();

    }
    return redirect()->route('assignments.index');
 


    }

    public function render()
    {
        return view('livewire.assignments.assignment-create');
    }
}
