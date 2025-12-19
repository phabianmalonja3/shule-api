<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'school_id',
        'school_class_id',
        'academic_year_id',
        'stream_id',
        'is_active',
        'payment_status',
        'reg_number',
        'created_by',
        'combination_id'
    ];
    public function attendances()
    {
        return $this->hasMany(Attendance::class); 
    }
    public function user() {
        return $this->belongsTo(User::class,'user_id');
    }

    public function schoolClass() {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }

    public function stream() {
        return $this->belongsTo(Stream::class);
    }

    public function school() {
        return $this->belongsTo(School::class);
    }

    public function marks()
    {
        return $this->hasMany(Mark::class); // Assuming a student can have many marks
    }

    public function assignments()
    {
        return $this->hasManyThrough(Assignment::class, Stream::class, 'id', 'stream_id', 'stream_id', 'id');
    }

    public function getCompletionRateAttribute()
    {
        $totalAssignments = $this->assignments()->count();
        $totalHomeworks = $this->homeworks()->count();

        $completedAssignments = $this->assignments()->whereIn('submissions.status', ['submitted', 'graded'])->count();
        $completedHomeworks = $this->homeworks()->whereIn('submissions.status', ['submitted', 'graded'])->count();

        $total = $totalAssignments + $totalHomeworks;
        $completed = $completedAssignments + $completedHomeworks;

        if ($total === 0) {
            return 0; // Avoid division by zero
        }

        return ($completed / $total) * 100;
    }

    public function announcements()
    {
        return $this->belongsToMany(Announcement::class)->whereHas('school', function ($query) {
            $query->where('id', $this->school_id); 
        });
    }

    public function parents()
    {
        return $this->belongsToMany(ParentModel::class, 'student_parent', 'student_id', 'parent_id');
    }

    public function payments()
    {
        return $this->belongsToMany(Payment::class, 'payment_student');
    }

    public function combinations() {
        return $this->belongsToMany(
                Combination::class,    
                'student_combination', 
                'student_id',          
                'combination_id'       
            );
    }
}
