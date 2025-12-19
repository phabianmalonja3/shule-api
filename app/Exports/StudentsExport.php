<?php

namespace App\Exports;

use App\Models\Student;

use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StudentsExport implements FromCollection, WithHeadings, WithMapping,ShouldAutoSize
{
    protected $students;
    public $rowNumber = 0;

    public function __construct($students)
    {
        $this->students = $students;
    }

    public function collection()
    {
        return $this->students;
    }

    public function headings(): array
    {
        return [
            '#',
            'Registration #',
            'First Name',
            'Middlename',
            'Surname',
            'Gender',
            'Stream',
            'Parent Phone #',
            'Payment Status',
            'Status',
        ];
    }

    public function map($student): array
    {
        $this->rowNumber++;
        $nameParts = explode(' ', $student->user->name);
        $firstName = $nameParts[0] ?? '';
        $middleName = $nameParts[1] ?? '';
        $surname = $nameParts[2] ?? '';

        $gender = trim($student->user->gender);

        if ($gender == 'female') {
            $gender = 'F';
        }else {
            $gender = 'M';
        };

        $parent_phones = $student->parents->pluck('phone')->take(2)->join(', ');

        if(empty($parent_phones)){
            $parent_phones = $student->user->phone?? 'N/A';
        }

        return [
            $this->rowNumber,
            $student->reg_number,
            $firstName,
            $middleName,
            $surname,
            $gender,
            $student->stream->schoolClass->name.' '.$student->stream->name ?? 'N/A',
            $parent_phones,
            $student->payment_status,
            $student->status ? 'Active' : 'Inactive',
        ];
    }
}
