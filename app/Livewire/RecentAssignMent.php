<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Homework;
use App\Models\Assignment;
use App\Models\StreamSubjectTeacher;

class RecentAssignMent extends Component
{


    public $amount = 3;
    public $streamId;


    public function mount(){
        $user = auth()->user();
        $this->streamId = $user->student->stream_id;


    //    dd($streamSubjects);
    }

    public function loadMore(): void
    {
        $this->amount += 3;
    }

    public function render()
    {

       $streamSubjects= StreamSubjectTeacher::take($this->amount)->latest()->where('stream_id',$this->streamId)->with(['subject'])->get();

        $recentAssignments = Assignment::take($this->amount)->latest()->where('stream_id', $this->streamId)->get();
                                          // Adjust limit as needed
         
        return view('livewire.recent-assign-ment',['recentAssignments'=>$recentAssignments,'streamSubjects'=>$streamSubjects]);
    }
}
