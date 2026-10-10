<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Applicant extends Model
{
    use HasFactory;

    /**
     * Mass-assignable attributes.
     * Adjust these fields to match your applicants table schema.
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'gender',
        'date_of_birth',
    ];

    /**
     * Get all admissions/applications submitted by this applicant.
     */

    public function admissions(): HasMany
    {
        return $this->hasMany(Admission::class, 'applicant_id');
    }
 
    public function parents(): BelongsTo
    {
        return $this->belongsTo(Parent::class, 'parent_id');
    }    
    /**
     * Get the latest admission for this applicant (useful if applicants re-apply).
     */
    public function latestAdmission(): HasOne
    {
        return $this->hasOne(Admission::class, 'applicant_id')->latestOfMany();
    }
}