<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ExaminationResult extends Model implements Auditable
{

    use \OwenIt\Auditing\Auditable;
    
    protected $fillable = ['student_id', 'subject_id', 'exam_type_id', 'marks', 'stream_id','academic_year_id'];


    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function exam()
    {
        return $this->belongsTo(ExaminationType::class);
    }

    public function marks()
    {
        return $this->hasMany(Mark::class);
    }

    public function subject()
{
    return $this->belongsTo(Subject::class);
}

public function stream()
    {
        return $this->belongsTo(Stream::class);
    }
}
