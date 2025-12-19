<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'parent_id',
        'amount',
        'method',
        'status',
        'transaction_id',
        'subscription_start',
        'subscription_end',
    ];

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }
    // public function student()
    // {
    //     return $this->belongsTo(Student::class); // For one-to-one
    // }
    
    // Or, for many-to-many:
    public function students()
    {
        return $this->belongsToMany(Student::class, 'payment_student');
    }
}
