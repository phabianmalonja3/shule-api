<?php

namespace Database\Seeders;

use App\Models\ExaminationType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ExamTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $examinationTypes = ['Weekly','Monthly','Midterm','Annual'];

        foreach($examinationTypes as $examType){
            ExaminationType::create(['name'=>$examType]);
        }
    }
}
