<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class School extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'logo',
        'motto',
        'address',        
        'color',        
        'postal_code',    
        'city',           
        'school_type',
        'sponsorship_type',
        'district',
        'ward',
        'region',
        'phone',
        'contract_number',
        'generic_school_id',
        'registration_number'
    ];


     /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'school_type' => 'array',
        ];
    }

	
    public function teachers()
	{
		return $this->hasMany(User::class)
					->where(function ($query) {
						$query->whereHas('roles', function ($query) {
							$query->whereIn('name', ['header teacher', 'academic teacher', 'class teacher', 'teacher']);
						});
					});
	}


    public function headerTeacher()
    {
        return $this->hasOne(User::class)->whereHas('roles', function ($query) {
            $query->where('name', 'header teacher');
        });
    }
    public function parents()
    {
        return $this->hasOne(User::class)->whereHas('roles', function ($query) {
            $query->where('name', 'parent');
        });
    }
    public function classes()
	{
		return $this->hasMany(SchoolClass::class);
	}

public function subjects()
{
    $schoolId = $this->id;
    $levels = Arr::wrap($this->school_type);

    $query = Subject::query()->where(function ($query) use ($schoolId, $levels) {
        
        // Part 1: Global subjects (school_id is null AND matches levels)
        $query->where(function ($q) use ($levels) {
            $q->whereNull('school_id');

            if (!empty($levels)) {
                $q->where(function ($subQuery) use ($levels) {
                    foreach ($levels as $level) {
                        $subQuery->orWhereJsonContains('school_level', $level);
                    }
                });
            }
        })
        // Part 2: OR extra school-specific subjects
        ->orWhere('school_id', $schoolId);

    });


    // Run this to see the raw SQL output in your browser:
     //dd($query->toSql(), $query->getBindings());

    return $query;
}

	public function annoucements()
	{
		return $this->hasMany(Announcement::class);
	}

	public function combinations()
    {
        return $this->belongsToMany(
            Combination::class,
            'combination_school', 
            'school_id',          
            'combination_id'    
        );
    }
}
