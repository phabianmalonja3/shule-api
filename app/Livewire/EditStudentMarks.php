<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Mark;
use App\Models\Student;

class EditStudentMarks extends Component
{
    public $studentId;
    public $studentName;
    public $selectedSubject;
    public $obtainedMarks;
    public $grade;
    public $remark;

    protected $listeners = ['setStudentAndSubject'];

    protected $rules = [
        'obtainedMarks' => 'required|numeric|min:0|max:100',
        'grade' => 'nullable|string',
        'remark' => 'nullable|string',
    ];

    public function setStudentAndSubject($studentId, $subjectId)
    {
        $this->studentId = $studentId;
        $this->selectedSubject = $subjectId;

        $student = Student::find($studentId);
        $this->studentName = $student->user->name ?? 'Unknown';

        $marks = Mark::where('student_id', $studentId)
            ->where('subject_id', $subjectId)
            ->first();

        if ($marks) {
            $this->obtainedMarks = $marks->obtained_marks;
            $this->grade = $marks->grade;
            $this->remark = $marks->remark;
        } else {
            $this->obtainedMarks = '';
            $this->grade = '';
            $this->remark = '';
        }

        // Emit an event to open the modal
        $this->dispatch('openEditModal');
    }

    public function updateMarks()
    {
        $this->validate();

        Mark::updateOrCreate(
            ['student_id' => $this->studentId, 'subject_id' => $this->selectedSubject],
            [
                'obtained_marks' => $this->obtainedMarks,
                'grade' => $this->grade,
                'remark' => $this->remark,
            ]
        );

        session()->flash('message', 'Marks updated successfully.');
        $this->dispatch('marksUpdated'); // Notify parent component
    }

    public function render()
    {
        return view('livewire.edit-student-marks');
    }
}
