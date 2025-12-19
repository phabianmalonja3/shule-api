<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

	protected $casts = [
			'school_type' => 'array',
	];
	
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
		$levels = $this->school_type ?? []; 

		return Subject::where('school_id', $schoolId)
					  ->orWhere(function ($query) use ($levels) {
						$query->whereNull('school_id')
							->where(function ($q) use ($levels) {
								foreach ($levels as $level) {
									$q->orWhereJsonContains('school_level', $level);
								}
							});
					});
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
