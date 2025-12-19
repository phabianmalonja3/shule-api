<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StreamTeacher extends Model
{
    protected $fillable=[
         'teacher_id', // Optional, must exist in the users table
            'school_class_id'];
}
