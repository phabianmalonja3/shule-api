<?php

namespace App\Exports;

use App\Models\StreamSubjectTeacher;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TimeTableTemplate implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public $classId;

    public function __construct($classId)
    {
        $this->classId = $classId;
    }

    public function collection()
    {
        return StreamSubjectTeacher::whereHas('stream', function ($query) {
            $query->where('school_class_id', $this->classId);
        })
        ->with(['stream.class', 'subject', 'teacher'])
        ->get();
    }

    public function headings(): array
    {
        return [
            // Helps prevent mix-up
            'Stream ID', // Helps prevent mix-up
            'Teacher ID', // Helps prevent mix-up
            'Subject Id', // Helps prevent mix-up
            'Subject Name',
            'Stream Name',
            'Teacher Name',
            'Day of Week',
            'Start Time',
            'End Time',
        ];
    }

    public function map($entry): array
    {
        
            return [
               
                $entry->stream->id, // Stream ID
                $entry->teacher->id, // Stream ID
                $entry->subject->id, // Subject Name
                $entry->subject->name, // Subject Name
                $entry->stream->name, // Stream Name
                $entry->teacher->name, // Teacher Name
                '', // Placeholder for Day
                '', // Placeholder for Start Time
                '', // Placeholder for End Time
            ];
        
        
    }
}
