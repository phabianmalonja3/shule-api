<?php

namespace App\Livewire\School;

use App\Models\School;
use Livewire\Component;
use Livewire\WithPagination;

class SchoolList extends Component
{

    use WithPagination;

    public $search;
    public $sortOrder ='desc';
    public $sortField = 'name';
    protected $queryString = [
        'search' => ['except' => '']
    ];

    // public function updatedsortBy()
    // {

    //     dd(
    //         'ke'
    //     );
      
    //         $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    //  } 






    public function render()
    {


        $query = School::query();
        // Apply filtering if the 'name' parameter is provided

        if(!empty($this->search)){
            $query->where('name', 'LIKE', '%' . $this->search . '%')
            ->orWhere('registration_number','LIKE','%'.$this->search.'%')
            ->orWhere('address', 'LIKE', '%' . $this->search . '%')
            ->orWhere('is_active',  $this->search);
        }
        

       

        return view('livewire.school.school-list',[
            "schools" => $query->orderBy('created_at', $this->sortOrder)->paginate(10)
    ]);
    }
}
