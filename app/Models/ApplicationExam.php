<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationExam extends Model
{
protected $casts = [
        'date' => 'array',
    ];

    public function getFormattedExamDateAttribute()
    {
        // Extract the actual date string from the JSON structure
        // (e.g., if your JSON looks like {"date": "2026-05-12"} or is a simple array)
        $rawDate = $this->date['date'] ?? $this->date[0] ?? null;

        if (!$rawDate) {
            return null;
        }

        return Carbon::parse($rawDate)->format('d/m/Y');
    }
}
