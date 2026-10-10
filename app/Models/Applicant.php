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

     
protected $casts = [
        'parent_id' => 'array',
    ];

    /**
     * Get all admissions/applications submitted by this applicant.
     */

    public function admissions(): HasMany
    {
        return $this->hasMany(Admission::class, 'applicant_id');
    }


    /**
     * Get multiple parents/guardians based on the JSON array of IDs.
     */
    public function parents()
    {
        if (empty($this->parent_id) || !is_array($this->parent_id)) {
            return ParentModel::whereRaw('0 = 1'); // Return an empty query builder if no parents exist
        }

        // Replace ParentModel with your actual parent model name (e.g., Guardian::class)
        return ParentModel::whereIn('id', $this->parent_id);
    }   
    /**
     * Get the latest admission for this applicant (useful if applicants re-apply).
     */
    public function latestAdmission(): HasOne
    {
        return $this->hasOne(Admission::class, 'applicant_id')->latestOfMany();
    }
}