<?php

namespace App\Livewire;

use App\Models\Announcement;
use Livewire\Component;
use Livewire\WithPagination;

class Announcements extends Component
{

    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $userAnnouncements = false; // To toggle 'My Announcements' tab

    protected $updatesQueryString = ['search', 'statusFilter'];

    public function render()
    {
        $announcements = Announcement::query()
    ->when($this->search, function ($query) {
        $query->where('title', 'like', '%'.$this->search.'%');
    })
    ->when($this->statusFilter, function ($query) {
        $query->where('status', $this->statusFilter);
    })
    ->when($this->userAnnouncements, function ($query) {
        $query->where('user_id', auth()->id());
    })
    ->when(auth()->user()->hasAnyRole(['teacher', 'class teacher']), function ($query) {
        $query->where('type', 'internal');
    })
    ->paginate(10);

        return view('livewire.announcements', [
            'announcements' => $announcements
        ]);
    }

    public function toggleUserAnnouncements()
    {
        $this->userAnnouncements = !$this->userAnnouncements;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

  
}
