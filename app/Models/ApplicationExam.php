<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationExam extends Model
{
protected $casts = [
        'exam_date' => 'array',
    ];

    // Accessor to easily grab and format the date from the JSON structure
    public function getFormattedDateAttribute()
    {
        // Adjust the key ('date' or index [0]) depending on how your JSON is structured
        $dateValue = $this->exam_date['date'] ?? $this->exam_date[0] ?? $this->exam_date ?? null;

        return $dateValue ? Carbon::parse($dateValue)->format('d/m/Y') : null;
    }
}
