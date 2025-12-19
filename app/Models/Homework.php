<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Homework extends Model implements Auditable
{


    use \OwenIt\Auditing\Auditable;



    protected $table = 'homeworks';


    protected $fillable = [
        'teacher_id',
        'subject_id',
        'class_id',
        'stream_id',
        'title',
        'description',
        'file_path',
        'due_date',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class); // Replace with your actual class model name
    }

    public function stream()
    {
        return $this->belongsTo(Stream::class);
    }
    public function streams()
    {
        return $this->belongsToMany(Stream::class,'homework_streams');
    }

    public function getFormattedDueDateAttribute()
{
    return \Illuminate\Support\Carbon::parse($this->due_date)->format('Y-m-d\TH:i');
}
public function submissions()
{
    return $this->hasMany(HomeworkSubmission::class, 'homework_id');
}


}
