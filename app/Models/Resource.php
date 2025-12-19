<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;

class Resource extends Model implements HasMedia
{

    use InteractsWithMedia;


    protected $fillable = [
        'subject_id',
        'title',
        'description',
        'resource_type',
        'file_path',
        'url',
        'created_by',
    ];

   

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function createdBy() 
    {
        return $this->belongsTo(User::class, 'created_by'); 
    }

}
