<?php

namespace App\Livewire;

use App\Models\Mark;
use Livewire\Component;

class RecentStudentMark extends Component
{

    public function render()
    {

        $marks = Mark::where('student_id',auth()->user()->student->id)->get()->unique();

        return view('livewire.recent-student-mark',compact('marks'));
    }
}
