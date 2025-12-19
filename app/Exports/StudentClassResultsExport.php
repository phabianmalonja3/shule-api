<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentClassResultsExport implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    protected $data;
    protected $schoolName;
    protected $examType;
    protected $className;
    protected $academicYear;
    protected $subjects;

    public function __construct($data, $schoolName, $examType, $className, $academicYear)
    {
        $this->data = $data;
        $this->schoolName = $schoolName;
        $this->examType = $examType;
        $this->className = $className;
        $this->academicYear = $academicYear;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            [$this->schoolName],
            ['Report for ' . $this->className . ' ' . $this->examType. ' Results '],
            [$this->academicYear.' Academic Year'],
            [''], // An empty row for spacing
            [
                'S/N',
                'Student Name',
                'Stream',
                'Total Marks',
                'Average',
                'Grade',
                'Remark',
                'Position'
            ]
        ];
    }
    
    public function startCell(): string
    {
        return 'A1';
    }

    public function title(): string
    {
        return 'Student Report';
    }

    public function styles(Worksheet $sheet)
    {
        // Merge cells for the main report titles
        $sheet->mergeCells('A1:' . $sheet->getHighestColumn() . '1');
        $sheet->mergeCells('A2:' . $sheet->getHighestColumn() . '2');
        $sheet->mergeCells('A3:' . $sheet->getHighestColumn() . '3');

        return [
            1 => [
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => 'center'],
            ],
            2 => [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => 'center'],
            ],
            3 => [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => 'center'],
            ],
            5 => ['font' => ['bold' => true]], // Style the main headings row
        ];
    }
}