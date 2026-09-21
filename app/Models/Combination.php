<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Combination extends Model
{
    protected $fillable = ['name', 'description'];
    protected $casts = ['subject_id' => 'array'];
	public $timestamps = false;

    public function students()
    {
        return $this->belongsToMany(
            Student::class,         
            'student_combination',  
            'combination_id',       
            'student_id'            
        )->withTimestamps()->withPivot('created_by');
    }

    public function studentsCurrentYear()
    {
        $currentYear = Carbon::now()->year;
        
        return $this->students()
            ->wherePivot(DB::raw('YEAR(student_combination.created_at)'), $currentYear);
    }  
    
    public function schools()
    {
        return $this->belongsToMany(
            School::class,
            'combination_school', 
            'combination_id',     
            'school_id'           
        )->withTimestamps()->withPivot('created_by');
    }
    
    public function subjects()
    {
        return $this->belongsToMany(
            Subject::class,
            'combination_subject', 
            'combination_id',     
            'subject_id'           
        );
    } 

    public function getSubjectsAttribute()
    {
        // Get the array of IDs from your JSON column (adjust relation/property path as needed)
        $subjectIds = $this->subject_id ?? []; 

        if (empty($subjectIds)) {
            return collect(); // Return an empty collection if no IDs exist
        }

        // Fetch and return the actual Subject models
        return Subject::whereIn('id', $subjectIds)->get();
    }
}
