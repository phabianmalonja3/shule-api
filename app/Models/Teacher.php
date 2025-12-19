<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable =[
        'user_id',
    ];

    public function streams()
    {
        
        return $this->belongsToMany(Stream::class, 'teacher_streams'); 
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'stream_subject_teacher');
    }
    
}
