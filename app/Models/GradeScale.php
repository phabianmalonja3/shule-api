<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeScale extends Model
{

    protected $fillable = [
        'grade' ,
        'min_marks',
        'max_marks',
        'remarks',
        'school_id',
        'school_type',
        'generic_grade_id'
    ];
}
