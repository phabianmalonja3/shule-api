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

public $isOpen = false;

// Form fields
public $applicant_name;
public $first_name;
public $middlename;
public $surname;
public $center_id;
public $exam_date;
public $exam_language;
public $form_status = 'in progress';

protected $rules=[
   
        'search' => 'nullable|string|max:255', // Allow null, ensure it's a string, and limit the length

];

protected $listeners = [
    'open-add-application-modal' => 'createApplication'
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


    public function createApplication()
{dd('Nsuuu');
    $this->resetValidation();
    $this->reset(['first_name', 'middlename', 'surname', 'center_id', 'exam_date', 'exam_language', 'form_status']);
    
    // Open the modal form
    $this->isOpen = true;
}

public function storeApplication()
{
    $this->validate([
        'first_name' => 'required|string|max:40',
        'middlename' => 'required|string|max:40',
        'surname' => 'required|string|max:40',                
        'center_id' => 'required|integer',
        'exam_date' => 'required|date',
        'exam_language' => 'required|string|max:40',        
        'form_status' => 'required|string|max:20',
    ]);

    Admission::create([
        'first_name' => $this->first_name,
        'middlename' => $this->middlename,
        'surname' => $this->surname,    
        'center_id' => $this->center_id,    
        'exam_date' => $this->exam_date,
        'exam_language' => $this->exam_language,
        'status' => $this->form_status,
    ]);

    $this->isOpen = false;
    
    // Optional: send a success flash message
    session()->flash('message', 'Application created successfully.');
}

public function render()
{
    $user = Auth::user();
    $schoolId = $user->school_id;

    // Base query for applications scoped to the user's school
    $baseQuery = Admission::with(['applicant', 'examCenter.applicationExams'])->whereHas('examCenter.school', function ($q) use ($schoolId) {
        $q->where('schools.id', $schoolId);
    });

    // Get a plain array of distinct status names
    $statuses = (clone $baseQuery)
        ->distinct()
        ->pluck('status')
        ->toArray(); // e.g., ['in progress', 'submitted', 'completed']

    // Filtered Applications Query
    $applicationsQuery = (clone $baseQuery)->with(['applicant', 'examCenter.school']);

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

    if (!empty($this->exam_center_id)) {
        $applicationsQuery->where('center_id', $this->exam_center_id);
    }


    $applications = $applicationsQuery->paginate(10);

    return view('livewire.admission.application-list', [
        'applications' => $applications,
        'statuses' => $statuses,
    ]);
}
}
