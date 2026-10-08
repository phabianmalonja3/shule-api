<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamCenter extends Model
{
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }
}
