<?php

namespace App\Livewire\Subjects;

use App\Models\Subject;
use Livewire\Component;
use Illuminate\Validation\Rule;

class SubjectCreate extends Component
{

    public $name;
    public $subject;
    public $id;


    
    
    
    
    
    
protected $rules=[
    'name' => 'required|string|unique:subjects,name'
];

public function mount($subject = null)
{
    $this->subject = $subject;

    if ($this->subject) {
        $this->name = $this->subject->name;
    }
}

public function render()
{


 
    return view('livewire.subjects.subject-create');
}


public function update()
    {

        $schoolId = auth()->user()->school_id;
    
        // Validate the request data
        $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // Ensure the name is unique within the same school, except for the current subject
                Rule::unique('subjects')->where(function ($query) use ($schoolId) {
                    return $query->where('school_id', $schoolId)
                                 ->where('id', '!=', $this->subject->id); // Exclude current subject
                }),
            ],
        ]);
    
        // Find the subject by ID and update
        
        $this->subject->name = $this->name;
        $this->subject->save();
    
        flash()->option('position', 'bottom-right')->success('Subject updated successfully!');

        // Redirect back to the subjects index with a success message
        return redirect()->route('subjects.index');
      

       
    }


    public function store(){
        $this->validate();
     Subject::create(['name' => $this->name,'school_id'=>auth()->user()->school_id]);
     flash()->option('position', 'bottom-right')->success('Subject updated successfully!');


        return redirect()->route('subjects.index');
    }
}
