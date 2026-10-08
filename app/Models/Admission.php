<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'preferred_language'
    ];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }

    public function examCenter()
    {
        return $this->belongsTo(examCenter::class);
    }

    public function school(): HasOneThrough
    {
        return $this->hasOneThrough(
            School::class,     // Target model you want to access
            ExamCenter::class, // Intermediate model
            'id',              // Foreign key on 'exam_centers' table (referenced by center_id on applications)
            'id',              // Foreign key on 'schools' table (referenced by school_id on exam_centers)
            'center_id',        // Local key on 'applications' table
            'school_id'        // Local key on 'exam_centers' table
        );
    }
}
