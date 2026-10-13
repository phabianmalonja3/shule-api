<?php

namespace App\Livewire\Admission;

use App\Models\User;
use App\Models\Admission;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class ApplicationList extends Component
{

use WithPagination;

public $search;
public $amount;
public $status;
public $exam_center_id = '';
public $examCenters;

protected $rules=[
   
        'search' => 'nullable|string|max:255', // Allow null, ensure it's a string, and limit the length

];

// public function loadMore(): void
//     {
//         $this->amount += 5;
//     }

public function mount()
{
    // Fetch unique exam centers for the dropdown filter
    $this->examCenters = \App\Models\ExamCenter::orderBy('name')->get();
}

    public function toggleVerification($teacherId, $isVerified)
    {
        // Find the teacher
        $teacher = \App\Models\User::findOrFail($teacherId);

        if($isVerified == 1){
            $newStatus = 0;
        }else{
            $newStatus = 1;
        }
        // Add role check safeguard if needed, but it's already in the Blade template

        // Toggle the status
        $teacher->is_verified = $newStatus;
        $teacher->save();

        session()->flash('message', 'Teacher status updated successfully.');
    }

    public function getHasSelectedOrNotSelectedProperty()
{
    return Admission::whereIn('status', ['selected', 'not selected'])->exists();
}

public function getHasNotifiedProperty()
{
    return Admission::where('is_notified', 1)->exists();
}

public function render()
{
    $applications = Admission::query()
        ->with(['applicant', 'examCenter'])
        ->when($this->search, function ($query) {
            $query->whereHas('applicant', function ($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('surname', 'like', '%' . $this->search . '%');
            });
        })
        ->when($this->status, function ($query) {
            $query->where('status', $this->status);
        })
        ->when($this->exam_center_id, function ($query) {
            $query->where('center_id', $this->exam_center_id);
        })
        ->paginate(10);

    return view('livewire.admission.application-list', [
        'applications' => $applications,
    ]);
}
}
