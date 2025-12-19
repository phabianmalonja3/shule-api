<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Resource;

class RecentResource extends Component
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

   
        public function render()
        {

            $resources = Resource::latest()->take( $this->amount)->get();

        // $recentAssignments = Assignment::take($this->amount)->latest()->where('stream_id', $this->streamId)->get();
        return view('livewire.recent-resource',['resources'=>$resources]);
    }
}
