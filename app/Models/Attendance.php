<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'student_id',
        'date',
        'teacher_id',
        'status',
        'stream_id',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
