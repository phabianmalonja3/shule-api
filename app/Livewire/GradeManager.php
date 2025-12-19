<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\GradeScale;
use App\Models\GenericGrade;

class GradeManager extends Component
{
    public $grades = [];
    public $confirmedGrades = [];
    public $teacher;

    public function mount()
    {
        $this->teacher = auth()->user();
        $schoolTypes = json_decode($this->teacher->school->school_type);

        // Retrieve all the grades for the teacher's school types
        $this->grades = GenericGrade::whereIn('school_type', $schoolTypes)
            ->get()
            ->groupBy('school_type')
            ->map(fn ($group) => $group->toArray())
            ->toArray();

        // Retrieve confirmed grades based on school ID
        $this->confirmedGrades = GradeScale::where('school_id', $this->teacher->school_id)
            ->latest()
            ->get()
            ->groupBy('school_type')
            ->map(fn ($group) => $group->toArray())
            ->toArray();
    }

    public function confirmAll($schoolType)
    {
        $confirmed_grades = GradeScale::where('school_type', $schoolType)
                                      ->where('school_id', $this->teacher->school_id)
                                    //   ->where('academic_year_id',);
                                      ->get();

        // Retrieve unconfirmed generic grades for the specific school type
        $genericGradesToConfirm = GenericGrade::where('school_type', $schoolType)->get();

        // Loop through and confirm each grade
        foreach ($genericGradesToConfirm as $grade) {
            GradeScale::create([
                'school_id' => $this->teacher->school_id,
                'school_type' => $schoolType,
                'grade' => $grade->grade,
                'min_marks' => $grade->min_marks,
                'max_marks' => $grade->max_marks,
                'remarks' => $grade->remarks,
                'generic_grade_id' => $grade->id,
            ]);
        }

        // Refresh the component state to show the confirmed grades and hide the "Confirm All" button
        $this->mount();

        // Dispatch a success message
       flash()->option('position', 'bottom-right')->success('All grades confirmed successfully.');
    }

    public function confirmGrade($gradeId, $schoolType)
    {
        // Find the specific grade from the GenericGrade model
        $grade = GenericGrade::findOrFail($gradeId);

        // Create a new confirmed grade entry in the GradeScale model
        $confirmedGrade = GradeScale::create([
            'school_id' => $this->teacher->school_id,
            'school_type' => $schoolType,
            'grade' => $grade->grade,
            'min_marks' => $grade->min_marks,
            'max_marks' => $grade->max_marks,
            'remarks' => $grade->remarks,
            'generic_grade_id' => $grade->id,  // Store the ID of the generic grade
        ]);

        // Remove the grade from the generic grades list for the specific school type
        // $this->grades[$schoolType] = array_filter(
        //     $this->grades[$schoolType],
        //     fn ($g) => $g['id'] !== $gradeId
        // );

        // Add the confirmed grade to the confirmed grades list for the school type
        $this->confirmedGrades[$schoolType][] = $confirmedGrade->toArray();

        // Dispatch event for the success message
        //$this->dispatch('gradeConfirmed');
        flash()->option('position', 'bottom-right')->success('A grade has been confirmed successfully.');
    }

    public function render()
    {
        return view('livewire.grade-manager');
    }
}
