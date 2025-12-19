<?php

namespace App\Imports;

use App\Models\Timetable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class TimetableImport implements ToModel,WithHeadingRow,WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
public $classId;
public function __construct($classId){
$this->classId = $classId;
}

    public function model(array $row)
    {

        
        $start_time = $this->excelTimeToTimeString($row['start_time']);
        $end_time = $this->excelTimeToTimeString($row['end_time']);


      
        return new TimeTable([
            'stream_id' => $row['stream_id'],
            'subject_id' => $row['subject_id'],
            'teacher_id' => $row['teacher_id'],
            'day' => $row['day_of_week'],
            'school_class_id'=>$this->classId,
            'start_time' => $start_time,
            'end_time' => $end_time,
        ]);
    }


    private function excelTimeToTimeString($excelTime) {
        $hours = floor($excelTime * 24); // Get the whole hours
        $minutes = round(($excelTime * 24 - $hours) * 60); // Get the minutes part
        return sprintf('%02d:%02d', $hours, $minutes); // Format as HH:MM
    }
    
    public function rules(): array
    {
        return [
            'day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday', // Ensure day is selected and valid
            'stream_id' => 'required|exists:streams,id', // Ensure the stream exists in the database
            'subject_id' => 'required|exists:subjects,id', // Ensure the subject exists in the database
            'teacher_id' => 'required|exists:users,id', // Ensure the teacher exists in the database
        ];
    }
    
    public function messages(): array
    {
        return [
            'day_of_week.required' => 'The day field is required.',
            'day_of_week.in' => 'The selected day is invalid. Please select a valid day from Monday to Friday.',
            
            'stream_id.required' => 'The stream is required.',
            'stream_id.exists' => 'The selected stream does not exist in our records.',
            
            'subject_id.required' => 'The subject is required.',
            'subject_id.exists' => 'The selected subject does not exist in our records.',
            
            'teacher_id.required' => 'The teacher is required.',
            'teacher_id.exists' => 'The selected teacher does not exist in our records.',
        ];
    }
    

}
