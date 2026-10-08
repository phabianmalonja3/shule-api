<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Admission extends Model
{
    protected $table = 'applications';

    protected $fillable = [
        'applicant_id', 
        'center_id', 
        'status',
        'is_notified',
        'is_confirmed',
        'is_paid',
        'school_attended',
        'preferred_language',
    ];

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    public function examCenter(): BelongsTo
    {
        // Fixed class casing and explicitly provided 'center_id' foreign key
        return $this->belongsTo(ExamCenter::class, 'center_id');
    }

    public function school(): HasOneThrough
    {
        return $this->hasOneThrough(
            School::class,      // Target model
            ExamCenter::class,  // Intermediate model
            'id',               // Foreign key on exam_centers table (exam_centers.id)
            'id',               // Foreign key on schools table (schools.id)
            'center_id',        // Local key on applications table (applications.center_id)
            'school_id'         // Local key on exam_centers table (exam_centers.school_id)
        );
    }
}