<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentResult extends Model
{
    protected $table = 'student_results';  // This is optional if the table name follows Laravel's naming convention

    // Define the fillable attributes (columns you can mass-assign)
    protected $fillable = [
        'student_id',
        'academic_year_id',
        'exam_type_id',
        'total_marks',
        'average_marks',
        'position',
        'month',
        'week'
    ];


    // protected static function boot()
    // {
    //     parent::boot();

    //     // Automatically set the position when creating a new result
    //     static::creating(function ($studentResult) {
    //         $studentResult->position = self::calculatePosition($studentResult->exam_type_id, $studentResult->total_marks);
    //     });

    //     // Automatically set the position when updating an existing result
    //     static::updating(function ($studentResult) {
    //         $studentResult->position = self::calculatePosition($studentResult->exam_type_id, $studentResult->total_marks);
    //     });
    // }

    // // Function to calculate the position
    // public static function calculatePosition($examTypeId, $totalMarks)
    // {
    //     // Fetch all student results for the given exam type, ordered by total marks
    //     $results = self::where('exam_type_id', $examTypeId)
    //                    ->orderByDesc('total_marks')
    //                    ->get();

    //     $position = 1;
    //     $previousMarks = null;

    //     // Calculate position based on total_marks
    //     foreach ($results as $result) {
    //         if ($result->total_marks != $previousMarks) {
    //             // Update the position if the marks are different
    //             $position = $position + 1;
    //         }

    //         // If the marks are the same as the previous one, they share the same position
    //         $previousMarks = $result->total_marks;
    //     }

    //     return $position;
    // }


    // public static function updatePositions($examTypeId)
    // {
    //     // Fetch all student results for the given exam type, ordered by total marks in descending order
    //     $results = self::where('exam_type_id', $examTypeId)
    //                    ->orderByDesc('total_marks')
    //                    ->get();

    //     $position = 1;
    //     $previousMarks = null;
    //     $sharedPosition = 1;

    //     foreach ($results as $result) {
    //         if ($result->total_marks != $previousMarks) {
    //             // If the total marks are different, update the position
    //             $position = $sharedPosition;
    //         }

    //         // Update the position based on the rank (order of total_marks)
    //         $result->update(['position' => $position]);

    //         // If the marks are the same as the previous one, they share the same position
    //         $previousMarks = $result->total_marks;
    //         $sharedPosition++; // Increase shared position for the next record
    //     }
    // }

    // Define relationships with other models

    // A result belongs to a student
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // A result belongs to an academic year
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    // A result belongs to an exam type
    public function examType()
    {
        return $this->belongsTo(ExaminationType::class, 'exam_type_id');
    }

    // A result can have many marks associated with it
    public function marks()
    {
        return $this->hasMany(Mark::class, 'exam_result_id');  // Assumes the `marks` table has an `exam_result_id` column
    }

    // public function calculateTotalMarks()
    // {
    //     $totalMarks = $this->marks->sum('obtained_marks');
    //     return $totalMarks;
    // }

    // // You can calculate the average marks from the related marks table
    // public function calculateAverage()
    // {
    //     $totalMarks = $this->calculateTotalMarks();
    //     $totalSubjects = $this->marks->count();
    //     return $totalMarks / $totalSubjects;
    // }

}
