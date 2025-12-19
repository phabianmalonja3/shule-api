<?php

namespace App\Livewire;

use App\Models\Payment;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentHistory extends Component
{
    use WithPagination;

    public $search = ''; // Search query
    public $sortField = 'created_at'; // Default sort field
    public $sortDirection = 'desc'; // Default sort direction
    public $isPolling = true;

    public function mount()
    {
        $this->isPolling = true; 
    }

    public function dehydrate()
    {
        $this->isPolling = false; 
    }

    protected $queryString = ['search', 'sortField', 'sortDirection'];

    public function updated()
    {
        $this->isPolling = true;
    
        // Simulate polling delay
        sleep(1);
        // dd('ok');
    
        $this->isPolling = false;
    }
    

    public function sortBy($field, $direction = 'asc')
    {
        $this->sortField = $field;
        $this->sortDirection = $direction;
    }

    public function render()
    {
        $payments = Payment::query()
        ->when($this->search, function ($query) {
            $query->whereHas('parent', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })->orWhereHas('students', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        })
        ->with(['parent', 'students']) // Load parent and students relationships
        ->orderBy($this->sortField, $this->sortDirection)
        ->paginate(10);

        // dd($payments);
        return view('livewire.payment-history', [
            'payments' => $payments,
        ]);
    }
}
