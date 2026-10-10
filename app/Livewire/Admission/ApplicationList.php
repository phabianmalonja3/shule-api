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

protected $rules=[
   
        'search' => 'nullable|string|max:255', // Allow null, ensure it's a string, and limit the length

];

// public function loadMore(): void
//     {
//         $this->amount += 5;
//     }

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

public function render()
{
    $user = Auth::user();
    $schoolId = $user->school_id;

    // Base query for applications scoped to the user's school
    $baseQuery = Admission::whereHas('examCenter.school', function ($q) use ($schoolId) {
        $q->where('schools.id', $schoolId);
    });

    // Get a plain array of distinct status names
    $statuses = (clone $baseQuery)
        ->distinct()
        ->pluck('status')
        ->toArray(); // e.g., ['in progress', 'submitted', 'completed']

    // Filtered Applications Query
    $applicationsQuery = (clone $baseQuery)->with(['applicant', 'examCenter.school']);
dd($applicationsQuery[0]->applicant->parents());
    // Apply Search Filter
    if (!empty($this->search)) {
        $applicationsQuery->whereHas('applicant', function ($q) {
            $q->where('first_name', 'like', '%' . $this->search . '%')
              ->orWhere('last_name', 'like', '%' . $this->search . '%')
              ->orWhere('phone', 'like', '%' . $this->search . '%')
              ->orWhere('parent_phone', 'like', '%' . $this->search . '%');
        });
    }

    // Apply Status Filter
    if (!empty($this->status)) {
        $applicationsQuery->where('status', $this->status);
    }

    $applications = $applicationsQuery->paginate(10);

    return view('livewire.admission.application-list', [
        'applications' => $applications,
        'statuses' => $statuses,
    ]);
}
}
