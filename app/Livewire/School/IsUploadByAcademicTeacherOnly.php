<?php

namespace App\Livewire\School;

use Livewire\Component;

class IsUploadByAcademicTeacherOnly extends Component
{
    public function render()
    {

        dd(request()->id);
        // return view('livewire.school.is-upload-by-academic-teacher-only')->layout('layout');
    }
}
