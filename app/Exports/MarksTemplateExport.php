<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\StreamSubjectTeacher;
use App\Models\Stream;
use App\Models\Subject;

class MarksTemplateExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;
    
    protected $classStatus;
    protected $streamId;
    protected $subjectId;
    protected $classId;
    protected $subjects = [];
    protected $teacherAssignments = [];

    public function __construct($classStatus, $streamId, $subjectId, $classId)
    {
        $this->classStatus = $classStatus;
        $this->streamId = $streamId;
        $this->subjectId = $subjectId;
        $this->classId = $classId;

        if ($this->classStatus > 0) {
            $assignments = StreamSubjectTeacher::with('subject')
                ->whereHas('stream', function ($query) {
                    $query->where('school_class_id', $this->classId);
                })
                ->get();

            foreach ($assignments as $ass) {
                $this->teacherAssignments[] = $ass;
            }

            foreach ($this->teacherAssignments as $teacherAssign) {
                if (!in_array($teacherAssign->subject->name, $this->subjects)) {
                    $this->subjects[] = $teacherAssign->subject->name;
                }
            }
        }
    }

    public function headings(): array
    {
        if ($this->classStatus > 0) {
            return array_merge(
                [
                    'Registration Number',
                    'Stream',
                    'Student Name'
                ],
                $this->subjects
            );
        } else {
            return [
                'Registration Number',
                'Student Name',
                'Marks'
            ];
        }
    }

    public function collection()
    {
        if ($this->classStatus > 0) {
            // This is the class teacher logic.
            return Student::with(['user', 'stream'])
                ->where('school_id', auth()->user()->school_id)
                ->where('school_class_id', $this->classId)
                ->orderBy('reg_number')
                ->get();
        } else {
            // This is the subject teacher logic.
            if ($this->streamId == 0) {
                return Student::query()
                    ->where('school_id', auth()->user()->school_id)
                    ->where('school_class_id', $this->classId)
                    ->get();
            } else {
                // Corrected: Use a direct where clause on stream_id
                return Student::query()
                    ->where('school_id', auth()->user()->school_id)
                    ->where('stream_id', $this->streamId)
                    ->get();
            }
        }
    }

    public function map($student): array
    {
        if ($this->classStatus > 0) {
            $rowData = [
                $student->reg_number ?? '',
                $student->stream->name ?? '',
                $student->user->name ?? '',
            ];

            foreach ($this->subjects as $subjectName) {
                $rowData[] = '';
            }

            return $rowData;
        } else {
            return [
                $student->reg_number . '/' . $this->subjectId ?? '',
                $student->user->name ?? '',
                ''
            ];
        } 
    }
}