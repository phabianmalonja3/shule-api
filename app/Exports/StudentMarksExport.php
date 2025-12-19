<?php

namespace App\Exports;
use App\Models\Mark;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StudentMarksExport implements FromCollection, WithHeadings,ShouldAutoSize
{
 
        protected $student;

        public function __construct(Student $student)
        {
            $this->student = $student;
        }
        public function collection()
        {
            

        
            return $this->student->marks->map(function ($mark) {
                $stream = $this->student->stream;
               $fullname = explode(' ',$mark->teacher->name);

               $firstName = $fullname[0];
               $lastName = $fullname[2];

               $position = Mark::where('subject_id', $mark->subject_id)
                            ->whereHas('student', function ($query) use ($stream) {
                                $query->where('stream_id', $stream->id); 
                            })
                            ->where('obtained_marks', '>', $mark->obtained_marks)
                            ->count() + 1; // Add 1 to get the a
             
                          
                return [
                    'subject' => $mark->subject->name,
                    'marks' => $mark->obtained_marks,
                    'grade' => $mark->grade,
                    'remarks' => $mark->remark,
                    'position' => $position,
                    'teacher' => $firstName .' '.$lastName,
                ];
            });
        }
    
        public function headings(): array
        {
            return [
                'Subject',
                'Marks',
                'Grade',
                "Teacher's Comment",
                'PST',
                'Subject Teacher',
            ];
        }
}
