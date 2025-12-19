<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Student;
use App\Models\SchoolClass;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation, WithEvents
{
    private $streamId;
    private $classId;
    private $fullname;
    private $gender;


    public function __construct($streamId, $classId)
    {
        $this->streamId = $streamId;
        $this->classId = $classId;
    }

    public function model(array $row)
    {
        $schoolId = Auth::user()->school_id; // Authenticated school ID
        $year = now()->year;

        // Full name construction
        $this->fullname = trim($row['first_name'] . ' ' . ($row['middle_name'] ?? '') . ' ' . $row['surname']);
        $this->gender = trim($row['gender']);
        $fullname =  $this->fullname;

        $existingUser = User::where('name', $fullname)
            ->whereHas('student', function ($query) use ($schoolId) {
                $query->where('school_id', $schoolId);
            })
            ->first();

        if ($existingUser) {
            throw new \Exception("Student '{$fullname}' is already registered in the system.");
        }

        $existingStudentInClass = Student::where('school_id', $schoolId)
            ->where('school_class_id', $this->classId)
            ->where('stream_id', $this->streamId)
            ->whereHas('user', function ($query) use ($fullname) {
                $query->where('name', $fullname);
            })
            ->first();

        if ($existingStudentInClass) {
            throw new \Exception("Student '{$fullname}' is already assigned to this class.");
        }

        $lastStudent = Student::where('school_id', $schoolId)->latest('id')->first();
        $nextNumber = $lastStudent ? sprintf('%04d', $lastStudent->id + 1) : '0001';
        $registrationNumber = "{$nextNumber}/{$schoolId}/{$year}";

        // Create User for the Student
        $user = User::create([
            'name' => $fullname,
            'username' => $registrationNumber,
            'gender' => $row['gender'],
            'password' => 'password', // Encrypt default password
        ]);


        $user->student()->create([
            'school_id' => $schoolId,
            'academic_year_id' => SchoolClass::find($this->classId)->academic_year_id,
            'school_class_id' => $this->classId,
            'stream_id' => $this->streamId,
            'reg_number' => $registrationNumber,
            'created_by' =>  auth()->user()->id
        ]);

        $user->assignRole('student');
        return $user;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string',
            'middle_name' => 'required|string',
            'surname' => 'required|string',
            'gender' => 'required',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'first_name.required' => 'First name in row :index is required. Please crosscheck all the values in the column.',
            'middle_name.required' => 'Middlename in row :index is required. Please crosscheck all the values in the column.',
            'sur_name.required' => 'Surname in row :index is required. Please crosscheck all the values in the column.',
            'gender.required' => 'Gender in row :index is required. Please crosscheck all the values in the column.',
        ];
    }

    public function registerEvents(): array
    {
        return [

            BeforeImport::class => function (BeforeImport $event) {
                $sheet = $event->getDelegate()->getActiveSheet();
                $highestRow = $sheet->getHighestDataRow();

//                dd($sheet->toArray());
                if ($highestRow < 2) {
                    throw new \Exception('The uploaded file is empty or contains no records.');
                }
                $schoolId = Auth::user()->school_id;

                $fullname = $this->fullname;

                $existingUser = User::where('name', $fullname)
                    ->whereHas('student', function ($query) use ($schoolId) {
                        $query->where('school_id', $schoolId);
                    })
                    ->first();

                if ($existingUser) {
                    throw new \Exception("Student '{$fullname}' is already registered in the system.");
                }

                $existingStudentInClass = Student::where('school_id', $schoolId)
                    ->where('school_class_id', $this->classId)
                    ->where('stream_id', $this->streamId)
                    ->whereHas('user', function ($query) use ($fullname) {
                        $query->where('name', $fullname);
                    })
                    ->first();

                if ($existingStudentInClass) {
                    throw new \Exception("Student '{$fullname}' is already assigned to this class.");
                }

                // if ($this->gender != 'Female' && $this->gender != 'Male') {
                //     throw new \Exception("Please specify the correct gender (i.e 'Male' or 'Female') for " . $fullname);
                // }
            },
           
        ];
    }
}
