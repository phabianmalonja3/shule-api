<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{

    protected $fillable = ['phone', 'physical_address', 'postal_address', 'profile_picture', 'postcode'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
