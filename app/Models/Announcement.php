<?php

namespace App\Models;

use Carbon\Carbon;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    // Define the fillable attributes for mass assignment
    protected $fillable = [
        'title',
        'content',
        'user_id',
        'school_id',
        'is_active',
        'start_date',
        'end_date',
        'type'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Define the relationship to the School model
    public function school()
    {
        return $this->belongsTo(School::class);
    }

   

    public function students()
    {
        return $this->belongsToMany(Student::class); 
    }
    public function user()
    {
        return $this->belongsTo(User::class); 
    }

    public function formatDates()
    {
        return [
            'start_date' => Carbon::parse($this->start_date)->format('Y-m-d'),
            'end_date' => Carbon::parse($this->end_date)->format('Y-m-d'),
        ];
    }

    public function scopeIsActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeIsExpired($query)
    {
        return $query->whereDate('end_date', '<', now());
    }

}
