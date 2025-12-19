<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Student;
use App\Models\Attendance;
use Carbon\Carbon;

class StudentAttendanceCalendar extends Component
{

    public $student;
    public $currentDate;
    public $attendanceData = [];
    public $canViewNextMonth = true;

    public function mount($student)
    {
        $this->student = $student;
        $this->currentDate = now(); // Default to the current month
        $this->loadAttendanceData();
        $this->updateNavigationPermissions();
    }

    public function loadAttendanceData()
    {
        $attendanceRecords = Attendance::where('student_id', $this->student->id)
            ->whereYear('date', $this->currentDate->year)
            ->whereMonth('date', $this->currentDate->month)
            ->get()
            ->keyBy(fn($attendance) => Carbon::parse($attendance->date)->format('Y-m-d'));


        $this->attendanceData = $attendanceRecords->toArray();
    }

    public function previousMonth()
    {
        $this->currentDate = $this->currentDate->subMonth();
        $this->loadAttendanceData();
        $this->updateNavigationPermissions();
    }

    public function nextMonth()
    {
        // Prevent going forward if it's already the current month
        if ($this->currentDate->format('Y-m') === now()->format('Y-m')) {
            return;
        }

        $this->currentDate = $this->currentDate->addMonth();
        $this->loadAttendanceData();
        $this->updateNavigationPermissions();
    }

    private function updateNavigationPermissions()
    {
        $nextMonth = $this->currentDate->copy()->addMonth()->format('Y-m');
        $this->canViewNextMonth = Attendance::where('student_id', $this->student->id)
            ->whereYear('date', '=', $this->currentDate->year)
            ->whereMonth('date', '=', $this->currentDate->month)
            ->exists() || $this->currentDate->format('Y-m') === now()->format('Y-m');
    }
    public function render()
    {
        return view('livewire.student-attendance-calendar');
    }
}
