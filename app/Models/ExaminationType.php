<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExaminationType extends Model
{
    protected $fillable = [
        'name'
    ];

    public function school()
    {
        return $this->belongsTo(Mark::class);
    }

    public function marks()
    {
        return $this->hasMany(Mark::class, 'exam_type_id');
    }

//     public function marks()
// {
//     return $this->hasMany(Mark::class, 'student_id', 'student_id');
// }

    

}
