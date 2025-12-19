<?php

namespace App\Imports;

use App\Models\Mark;
use App\Models\Student;
use App\Models\StudentMark;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MarksImport implements ToModel, WithHeadingRow
{
    protected $subjectId;

    public function __construct($subjectId)
    {
        $this->subjectId = $subjectId;
    }

    public function model(array $row)
    {
        // Assuming your Excel sheet has columns: registration_number, marks, comment
        // Adjust column names accordingly

        $student = Student::where('registration_number', $row['registration_number'])->first(); 

        if (!$student) {
            // Handle missing student scenario (e.g., log error, display message)
            // For now, let's skip the row
            return null; 
        }

        return new Mark([
            'student_id' => $student->id,
            'teacher_id' => auth()->user()->id,
            'subject_id' => $this->subjectId, 
            'mark' => $row['marks'],
            'comments' => $row['comment'],
        ]);
    }
}