<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Assignment extends Model implements HasMedia,Auditable
{

   
    use InteractsWithMedia,\OwenIt\Auditing\Auditable;


    protected $guarded = [];
   
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    // Relationship with Stream
    public function stream()
    {
        return $this->belongsTo(Stream::class);
    }
    public function streams()
    {
        return $this->belongsToMany(Stream::class,'assignment_stream');
    }

    // Relationship with Teacher
    public function teacher()
    {
        return $this->belongsTo(User::class);
    }
    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('assigmnets_files')
            ->useDisk('public'); // Or your desired disk
    }
}
