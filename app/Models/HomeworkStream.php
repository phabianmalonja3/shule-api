<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeworkStream extends Model
{
    protected $table = 'homework_streams';

    protected $fillable = [
     
        'homework_id',
        'stream_id',
        
    ];
}
