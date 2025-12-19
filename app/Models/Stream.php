<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Stream extends Model implements Auditable
{

    use \OwenIt\Auditing\Auditable;
    protected $fillable = ['school_class_id', 'name','teacher_id','alias'];

    public function classTeacher()
{
    return $this->belongsTo(User::class, 'teacher_id');
}

public function homeworks()
    {
        return $this->hasMany(Homework::class);
    }
    // public function subjectTeachers()
    // {
    //     return $this->hasMany(StreamSubjectTeacher::class);
    // }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
    
   

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }

    public function subjectsWithTeachers()
{
    return $this->belongsToMany(Subject::class, 'stream_subjects', 'stream_id', 'subject_id')
                ->withPivot('teacher_id')
                ->with('teacher');
}
public function streamTeacher()
{
    return $this->belongsTo(User::class, 'stream_teacher_id');
}

public function teachers()
{
    return $this->belongsToMany(Teacher::class, 'teacher_streams');
}



public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function subjectTeachers()
{
    return $this->belongsToMany(User::class, 'stream_subject_teacher', 'stream_id', 'teacher_id')
                ->withPivot('subject_id')
                ->withTimestamps();
}
    public function class()
{
    return $this->belongsToMany(User::class, 'stream_subjects', 'stream_id', 'teacher_id')
                ->withPivot('subject_id')
                ->withTimestamps();
}
// public function class()
//     {
//         return $this->belongsTo(SchoolClass::class, 'school_class_id');
//     }


public function students()
{
    return $this->hasMany(Student::class);
}
    

public function classTeachers()
{
    return $this->hasMany(User::class, 'class_teacher_id'); // Adjust column as per your database design
}

public function subjectTeacher()
{
    return $this->subjects()->wherePivotNotNull('teacher_id');
}

public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'stream_subject_teacher'); 
    }
    
}
