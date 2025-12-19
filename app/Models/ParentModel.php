<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentModel extends Model
{
    protected $table = 'parents';

    protected $fillable =
    [
        'user_id',
        'first_name',
        'middle_name',
        'sur_name',
        'relationship',
        'phone',
        'gender'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

   
    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_parent', 'parent_id', 'student_id');

    }
    
   
}
