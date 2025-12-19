<?php

namespace App\Livewire;

use App\Models\ExaminationType;
use App\Models\StudentResult;
use Carbon\Carbon;
use Livewire\Component;

class StudentResults extends Component
{
    public $student;
    public $students;
    public $examType = 'Monthly'; // Default filter
    public $examTypes = [];
    public $examTypeName;
    public $academic_year;
    public $months = [];
    public $results = [];
    public $currentMonth;
    public $student_id;
    protected $queryString = ['examType'];

    public function mount()
    {
        $user = auth()->user();
        $this->students = $user->parent->students;

        if ($this->students->isNotEmpty()) {
            $this->student = $this->students->first();
            $this->student_id =  $this->students->first()->id;
          
        }

        $this->currentMonth = Carbon::now()->month;

        // Define months based on term
        if ($this->currentMonth >= 1 && $this->currentMonth <= 6) {
            for ($i = 1; $i <= $this->currentMonth; $i++) {
                $this->months[$i] = date("F", mktime(0, 0, 0, $i, 1));
            }
        } else {
            for ($i = 7; $i <= $this->currentMonth; $i++) {
                $this->months[$i] = date("F", mktime(0, 0, 0, $i, 1));
            }
        }

        $this->examTypes = ExaminationType::whereIn('name',['Monthly', 'Midterm', 'Terminal', 'Annual'] )
            ->pluck('name', 'id');

            
            $this->examType = ExaminationType::where('name', 'Monthly')->value('id') ?? 'all';
          



        $this->fetchResults();
    }

    public function fetchResults()
    {

      
        // if (!$this->student_id) {
        //     $this->results = collect(); // Empty collection if no student is selected
        //     return;
        // }

        $this->examTypeName = ExaminationType::find($this->examType)->name;

        $this->results = StudentResult::where('student_id', $this->student_id)
            // ->when($this->academic_year, fn($query) => $query->where('academic_year_id', $this->academic_year))
            // ->when($this->examType && $this->examType !== 'all', fn($query) => $query->where('exam_type_id', $this->examType))
           
            ->get();

            // dd(     $this->results);

            // dd($this->results);
             // Group results by month
    }

    public function render()
    {
        return view('livewire.student-result', [
            'students' => $this->students,
        ]);
    }
}
