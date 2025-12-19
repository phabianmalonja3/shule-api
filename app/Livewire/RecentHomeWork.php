<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Homework;

class RecentHomeWork extends Component
{

    

    public $amount = 3;
    public $streamId;


    public function mount(){
        $user = auth()->user();
        $this->streamId = $user->student->stream_id;
    }

    public function loadMore(): void
    {
        $this->amount += 3;
    }

    // public function render()
    // {

    //     $recentAssignments = Assignment::take($this->amount)->latest()->where('stream_id', $this->streamId)->get();
    //                                       // Adjust limit as needed
         
    //     return view('livewire.recent-assign-ment',['recentAssignments'=>$recentAssignments]);
    // }
    public function render()
    {

        
        $recentHomeworks = Homework::with('streams')->whereHas('streams',function($q){
          return $q->where('stream_id',$this->streamId);
        })->get();

        // dd( $recentHomeworks);
                                  
        return view('livewire.recent-home-work',['recentHomeworks'=>$recentHomeworks]);
    }
}
