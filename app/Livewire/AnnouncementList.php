<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Announcement;
use Livewire\WithPagination;

class AnnouncementList extends Component
{


    use WithPagination;

    public $amount = 3;

    public $expandedAnnouncement = [];

  
    public function toggleDetails($id)
    {
        if (isset($this->expandedAnnouncement[$id])) {
            unset($this->expandedAnnouncement[$id]);
        } else {
            $this->expandedAnnouncement[$id] = true;
        }
    }
    public function loadMore(): void
    {
        $this->amount += 3;
    }

    public function render()
    {
        $user = auth()->user(); 
        // $students = $user->parent->students; 

        $students = $user->parent->students;
        $school= $students->first()->school;

        $announcements = $school->annoucements()->take($this->amount)->get();
        return view('livewire.announcement-list',compact('announcements'));
    }
}
