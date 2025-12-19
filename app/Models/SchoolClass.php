<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    protected $fillable = ['name','school_id','teacher_class_id','created_by_system','academic_year_id'];

    public function streams()
    {
        return $this->hasMany(Stream::class, 'school_class_id', 'id');
    }


    public function teacher(){
    return $this->belongsTo(User::class,'teacher_class_id');

    }
    public function classsTeacher(){
    return $this->belongsTo(User::class,'teacher_class_id','id');

    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function teachers()
    {
        return $this->hasMany(User::class); // Assuming 'school_class_id' is the foreign key in the 'teachers' table
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_school_class');
    }
}
