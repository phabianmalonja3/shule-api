<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = ['student_parent_id', 'start_date', 'end_date', 'is_active'];

    public function parent()
    {
        return $this->belongsTo(StudentParent::class);
    }
}
