<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentParent extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'student_id', 'is_subscribed'];
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }
}
