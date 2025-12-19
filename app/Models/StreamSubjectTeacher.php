<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class StreamSubjectTeacher extends Model implements Auditable
{

    use \OwenIt\Auditing\Auditable;



    protected $table = 'stream_subject_teacher';
    protected $guarded = [];

    public function streams()
{
    return $this->belongsToMany(Stream::class, 'stream_subject_teacher', 'subject_teacher_id', 'stream_id');
}

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
}
