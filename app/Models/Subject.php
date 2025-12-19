<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'name',
        'teacher_id',
        'school_id',
        'created_by_system',
		'school_level'

    ];

    public function resources()
    {
        return $this->hasMany(Resource::class); 
    }

	public function teacher()
	{
		return $this->belongsTo(User::class, 'teacher_id');
	}

	public function streams()
	{
		return $this->belongsToMany(Stream::class, 'stream_subject_teacher');
	}
   
    public function marks()
    {
        return $this->hasMany(Mark::class);
    }

    public function homeworks()
    {
        return $this->hasMany(Homework::class);
    }

	public function teachers()
	{
		return $this->belongsToMany(User::class, 'stream_subject_teacher', 'subject_id', 'teacher_id');
	}

	public function schoolClasses()
	{
		return $this->belongsToMany(SchoolClass::class, 'subject_school_class');
	}

	public function streamSubjects()
	{
		return $this->hasMany(StreamSubject::class);
	}
	
	public function school()
	{
		return $this->belongTO(School::class);
	}
}
