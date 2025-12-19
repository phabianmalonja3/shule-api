<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class StreamSubject extends Model
{
    use HasFactory;

    protected $fillable = ['stream_id', 'subject_id', 'teacher_id'];

    public function stream()
    {
        return $this->belongsTo(Stream::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class);
    }
    // public function schoolClass()
    // {
    //     return $this->belongsTo(SchoolClass::class, 'stream_id'); // Update 'stream_id' to match your column
    // }

    // public function subject1()
    // {
    //     return $this->belongsTo(Subject::class, 'subject_id'); // Update 'subject_id' to match your column
    // }
   

}

