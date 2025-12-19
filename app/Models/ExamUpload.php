<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamUpload extends Model
{
    protected $fillable = [
        'school_class_id',
        'exam_type_id',
        'academic_year_id',
        'month',
        'week',
        'uploaded_by'
    ];

    
    public function schoolClass(){

	return $this->belongsTo(SchoolClass::class, 'school_class_id');

    }

    public function examinationType(){

    	return $this->belongsTo(ExaminationType::class,'exam_type_id');

    }

    public function academicYear(){

	return $this->belongsTo(AcademicYear::class,'academic_year_id');

    }

    public function user(){

	return $this->belongsTo(User::class, 'uploaded_by');

    }

    public function marks(){

	return $this->hasMany(Mark::class, 'exam_upload_id');

    }
}
