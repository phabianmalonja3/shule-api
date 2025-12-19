<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeworkSubmission extends Model
{
    protected $table = 'homework_submissions';

    protected $fillable = [
        'homework_id',
        'teacher_id',  // Taken from the homeworks table
        'student_id',  // Taken from the homeworks table
        'stream_id', 
        'file_path',  // The stream the homework belongs to
        'submission_status', // Enum: 'submitted', 'pending', 'late', 'graded'
        'submission_date',
    ];

    public function homework()
    {
        return $this->belongsTo(Homework::class);
    }
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
