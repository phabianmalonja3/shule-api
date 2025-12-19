<?php

namespace App\Livewire\Teacher;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class TeacherList extends Component
{

use WithPagination;

public $search;
public $amount;

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

        $this->validate();


        $headTeacher = Auth::user(); // Get the authenticated user

        // ... (rest of your code for getting counts)

        $schoolId = $headTeacher->school_id; // Get the school ID

        // Roles to filter teachers
        $roles = ['teacher', 'class teacher', 'academic teacher','header teacher','head teacher','assistant headteacher'];
    

        
        
        $teachersQuery = User::take($this->amount)->latest()->where('school_id', $schoolId)
            ->whereHas('roles', function ($query) use ($roles) {
                $query->whereIn('name', $roles);
            });

        if (!empty($this->search)) {
            $search = $this->search;
            $teachersQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('phone', 'like', "%{$this->search}%");
            });
        }

        // Paginate the teachers list
        $teachers = $teachersQuery->paginate(10);

        if(auth()->user()->hasAnyRole(['class teacher','teacher'])){
            $teachers = $teachersQuery->where('is_verified',true)->get();
        }

        // dd($teachers);

        return view('livewire.teacher.teacher-list',[
            'teachers'=>$teachers
        ]);
    }
}
