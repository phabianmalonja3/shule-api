<?php

namespace App\Livewire\Homeworks;

use Livewire\Component;
use App\Models\Homework;
use App\Models\StreamSubjectTeacher;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
class HomeList extends Component
{

    use WithPagination;

public $selectedStream = '';
public $selectedSubject = '';
public $searchTerm = '';


public function mount()
{

    $teacher = Auth::user();

    $streamSubjects = StreamSubjectTeacher::with(['subject', 'stream'])

    ->where('teacher_id', $teacher->id)
    ->get();

$subjects = $streamSubjects->pluck('subject')->filter()->unique('id')->values(); // Ensure no duplicate subjects
$streams = $streamSubjects->pluck('stream')->unique('id'); // Ensure no duplicate streams

    $this->selectedSubject = $subjects->count() > 1 ? null : $this->subjects->last()->id;
    $this->selectedStream = $streams->count() > 1 ? null : $this->streams->first()->id;
}

    public function render()
    {

        $teacher = Auth::user();

        // Fetch streamSubjects related to the teacher, including relationships for 'subject' and 'stream'
        $streamSubjects = StreamSubjectTeacher::with(['subject', 'stream'])
            ->where('teacher_id', $teacher->id)
            ->get();

        $subjects = $streamSubjects->pluck('subject')->filter()->unique('id')->values(); // Ensure no duplicate subjects
        $streams = $streamSubjects->pluck('stream')->unique('id'); // Ensure no duplicate streams

        
        // Apply filters to the homework query
        $homeworks = Homework::latest()->with('subject')
            ->where('teacher_id', $teacher->id)
            ->when($this->selectedStream, function ($query) {
                $query->whereHas('streams', function ($streamQuery) {
                    $streamQuery->where('streams.id', $this->selectedStream); // Explicitly specify 'streams.id'
                });
            })

            ->when($this->selectedSubject, function ($query) {
                $query->where('subject_id', $this->selectedSubject);
            })
            ->when($this->searchTerm, function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('title', 'like', '%' . $this->searchTerm . '%')
                             ->orWhere('description', 'like', '%' . $this->searchTerm . '%');
                });
            })
            ->paginate(10);
            
        return view('livewire.homeworks.home-list',compact('homeworks','subjects','streams'));
    }
}
