<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Resource;
use Livewire\WithPagination;

class SubjectResources extends Component
{


    use WithPagination;

    public $subject;
    public $search = '';
    public $resources = [];
    public $amount = 5;
    public function mount($subject){


        // dd($subject);
        $this->subject=$subject;
    
    }
    // Listen for query string changes
    protected $queryString = ['search'];


 

    public function loadMore(): void
    {
        $this->amount += 5;
    }


    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $this->resources = Resource::latest()->where('subject_id', $this->subject->id)

        ->take($this->amount)
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%');
            })
            ->get();

        

        return view('livewire.subject-resources');
    }
}


