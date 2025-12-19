<?php

namespace App\Livewire\Homeworks;

use App\Models\Stream;
use Livewire\Component;
use App\Models\Homework;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use App\Models\StreamSubjectTeacher;
use Illuminate\Support\Facades\Auth;

class HomeWorkCreate extends Component
{
    use WithFileUploads;

    public $subject_id,$class_id,$description,$stream_id,$title,$file,$due_date,$subjects,$streams,$all_streams,$streams_ids=[];



    public function mount(){
        $teacher = Auth::user();

        // Fetch streamSubjects related to the teacher, including the relationships for 'subject' and 'stream'
        $streamSubjects = StreamSubjectTeacher::with(['subject', 'stream'])->where('teacher_id', $teacher->id)->get();
    
    
        $this->subjects = $streamSubjects->pluck('subject')->filter()->unique('id')->values();
        // Ensure no duplicate subjects
        $this->streams = $streamSubjects->pluck('stream')->unique('id');   // Ensure no duplicate streams
    
    }

protected $rules =[
    'subject_id' => 'required|exists:subjects,id',
    // 'stream_id' => 'required|exists:streams,id',
    'title' => 'required|string|max:255',
    'file' => 'required|file|mimes:pdf,doc,docx|max:2048', // Accept only PDF files
    'due_date' => 'required|date|after:today',
];
    public function store()
{


    
    $this->validate();
    
    $filePath = null;
    if ($this->file) {
        $filePath = $this->file->store('homeworks', 'public');
    }

    $teacher = Auth::user();



$streamSubjects = StreamSubjectTeacher::with(['subject', 'stream'])
    ->where('teacher_id', $teacher->id)
    ->get();

$streams = $streamSubjects->pluck('stream')->unique('id');
$streamIds = $this->all_streams === 'yes' 
? $streams->pluck('id')->toArray() 
: $this->streams_ids; 






    try{

        DB::beginTransaction();

       $homework = Homework::create([
            'teacher_id' => Auth::id(),
            'subject_id' => $this->subject_id,
            // 'class_id' => $class_id,
            // 'stream_id' => $this->stream_id,
            'title' => $this->title,
            'description' => $this->description,
            'file_path' => $filePath,
            'due_date' => $this->due_date,
        ]);

        $homework->streams()->attach($streamIds);

        DB::commit();
    
        flash()

        ->option('position', 'bottom-right')

        ->success('Homework assigned successfully.');
        return redirect()->route('homeworks.index');


    }catch(\Exception $e){

        DB::rollback();
        flash()

        ->option('position', 'bottom-right')

        ->error('Error in creatin Homewoks  ' .$e->getMessage());
        return back();

    }

   
}

// public function storeAssignment()
// {
// try
// {


// // dd($streamIds);

//     $this->validate();



//     if($this->file){

//     $path = $this->file->store('assigments','public');

//     }
    
//     DB::beginTransaction();

//     $assignment = Assignment::create(
//         [
//         'title' => $this->title,

//         'subject_id' => $this->subject_id,

//         'description'=>$this->description,
       
//         'due_date' => $this->due_date,

//         'teacher_id' => auth()->id(),

//         'file_path' => $path
//     ]
// );

// $assignment->streams()->attach($streamIds);
    
 
    

//     flash()->option('position', 'bottom-right')->success('Assigment Created successfully.');

//  DB::commit();

// }catch(\Exception $e)
// {

//  DB::rollBack();

//  Log::error('error' .$e->getMessage());

//  flash()

//  ->option('position', 'bottom-right')

//  ->error('Error in creating  Assigments.' .$e->getMessage());
//  return back();

// }
// return redirect()->route('assignments.index');



    public function render()
    {
        return view('livewire.homeworks.home-work-create');
    }
}
