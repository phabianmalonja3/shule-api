<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Casts\Attribute;
class User extends Authenticatable  implements Auditable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use  HasApiTokens, HasFactory, Notifiable, HasRoles,\OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'is_verified',
        'gender',
        'phone',
        'password',
        'school_id',
        'username',
        'created_by'
    ];
    public function student()
{
    return $this->hasOne(Student::class,'user_id');
}


// public function setNameAttribute($value)
// {
//     $this->attributes['name'] = ucwords(strtolower($value));
// }

// Accessor

protected function name(): Attribute
{
    return Attribute::make(
        get: fn (string $value) => ucwords($value),
        set: fn (string $value) => ucwords($value)
    );
}



public function streams()
{

    return $this->hasMany(Stream::class, 'teacher_id', 'id'); // Ensure 'teacher_id' is used
}
public function announcements()
{

    return $this->hasMany(Announcement::class, 'user_id', 'id'); // Ensure 'teacher_id' is used
}

// public function student()
// {
//     return $this->hasOne(Student::class); // Assuming a user can only have one student record
// }

public function parent()
{
    return $this->hasOne(ParentModel::class,'user_id');
}



    public function school()
    {
        return $this->belongsTo(School::class);
    }
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'stream_subject_teacher', 'teacher_id', 'subject_id');
    }

    public function streamsTeaches()
    {
        return $this->belongsToMany(Stream::class, 'stream_subject_teacher', 'teacher_id', 'stream_id');
    }

    public function details()
    {
        return $this->hasOne(UserDetail::class);
    }

    public function schools()
    {
        return $this->belongsToMany(School::class, 'school_teacher', 'teacher_id', 'school_id');
    }

//     public function schoolClass()
// {
//     return $this->belongsTo(SchoolClass::class,'teacher_class_id','id');
// }

    public function classTeacher()
{

    return $this->hasOne(SchoolClass::class);

}

public function notes()
{
    return $this->hasMany(Note::class,'teacher_id');
}

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class,'teacher_id', 'id'); // 'school_class_id' is assumed as the foreign key in the 'teachers' table
    }

    public function teachingStreams()
{
    return $this->belongsToMany(Stream::class, 'stream_subjects', 'teacher_id', 'stream_id')
                ->withPivot('subject_id')
                ->withTimestamps();
}
// Teacher can have many streams
// public function streams()
// {
//     return $this->belongsToMany(Stream::class, 'stream_teacher', 'teacher_id', 'stream_id');
// }


public function classstreams()
{
    return $this->hasMany(Stream::class, 'teacher_id');
}

public function streamSubjects()
{
    return $this->hasMany(StreamSubjectTeacher::class,'teacher_id');
}
public function assignments()
{
    return $this->hasMany(Assignment::class,'teacher_id');
}


public function classes()
    {
        return $this->belongsToMany(SchoolClass::class, 'teacher_class', 'teacher_id', 'class_id');
    }
public function schoolClasses()
    {
        return $this->hasManyThrough(
            SchoolClass::class,
            StreamSubject::class,
            'teacher_id',
            'id',
            'id',
            'stream_id'
        );
    }


public function subjectstream()
    {
        return $this->hasManyThrough(
            Subject::class,       // Final model to access
            StreamSubject::class, // Intermediate table
            'teacher_id',         // Foreign key on stream_subjects
            'id',                 // Foreign key on subjects
            'id',                 // Local key on users
            'subject_id'          // Local key on stream_subjects
        );
    }

    public function examUpload(){

    	return $this->belongsToMany(ExamUpload::class, 'uploaded_by','id');
    }

}
