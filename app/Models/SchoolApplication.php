<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolApplication extends Model
{
    protected $fillable = [
        'school_name',
        'email',
        'phone',
        'address',        // Add address to fillable fields
        'postal_code',    // Add postal code to fillable fields
        'city',           // Add city to fillable fields
        'school_type',
        'fullname',
        'registration_number',
        'first_name',
        'middle_name',
        'location',
        'status',
        'district',
        'ward',
        'region',
        'sponsorship_type',
        'generic_school_id'
    ];

    protected $casts = [
        'location' => 'array',
    ];
}
