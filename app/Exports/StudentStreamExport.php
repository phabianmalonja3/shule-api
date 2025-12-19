<?php
namespace App\Exports;

use App\Models\Student;
use App\Models\StreamSubject;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StudentStreamExport implements FromCollection, WithHeadings,ShouldAutoSize
{
    protected $teacherId;

    // Constructor to accept teacher ID
    public function __construct($teacherId)
    {
        $this->teacherId = $teacherId;
    }

    public function collection()
    {
        // Get streams assigned to the teacher
        $streams = StreamSubject::with('stream', 'stream.schoolClass', 'subject')
            ->where('teacher_id', $this->teacherId)
            ->get();

        // Get the students assigned to the streams
        $students = Student::whereIn('school_class_id', $streams->pluck('stream.school_class_id'))
            ->get();

        // Prepare the data for export
        $data = $students->map(function ($student) use ($streams) {
            // Get the stream name the student belongs to
            $stream = $streams->firstWhere('stream.school_class_id', $student->school_class_id);

            return [
                'registration_number' => $student->reg_number,
                'student_name' => $student->user->name,
                'subject_name' => $stream ? $stream->subject->name : 'No subject assigned',
                'marks'
            ];
        });

        return $data;
    }

    // Define the column headings
    public function headings(): array
    {
        return [
            'Registration Number',
            'Student Name',
            'Subject Name',
            'Marks',
        ];
    }
}
