<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class GenericGradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gradeScales = [
            // For Primary School
            [
                'grade' => 'A',
                'min_marks' => 90.00,
                'max_marks' => 100.00,
                'remarks' => 'Excellent',
                'school_type' => 'primary',
            ],
            [
                'grade' => 'B',
                'min_marks' => 75.00,
                'max_marks' => 89.99,
                'remarks' => 'Very Good',
                'school_type' => 'primary',
            ],
            [
                'grade' => 'C',
                'min_marks' => 60.00,
                'max_marks' => 74.99,
                'remarks' => 'Good',
                'school_type' => 'primary',
            ],
            [
                'grade' => 'D',
                'min_marks' => 50.00,
                'max_marks' => 59.99,
                'remarks' => 'average',
                'school_type' => 'primary',
            ],
            [
                'grade' => 'F',
                'min_marks' => 0.00,
                'max_marks' => 49.99,
                'remarks' => 'Fail',
                'school_type' => 'primary',
            ],

           
            [
                'grade' => 'A',
                'min_marks' => 85.00,
                'max_marks' => 100.00,
                'remarks' => 'Excellent',
                'school_type' => 'O-level',
            ],
            [
                'grade' => 'B',
                'min_marks' => 70.00,
                'max_marks' => 84.99,
                'remarks' => 'Very Good',
                'school_type' => 'O-level',
            ],
            [
                'grade' => 'C',
                'min_marks' => 55.00,
                'max_marks' => 69.99,
                'remarks' => 'Average',
                'school_type' => 'O-level',
            ],
            [
                'grade' => 'D',
                'min_marks' => 40.00,
                'max_marks' => 54.99,
                'remarks' => 'Below Average',
                'school_type' => 'O-level',
            ],
            [
                'grade' => 'F',
                'min_marks' => 0.00,
                'max_marks' => 39.99,
                'remarks' => 'Fail',
                'school_type' => 'O-level',
            ],

            // A-level
            [
                'grade' => 'A',
                'min_marks' => 85.00,
                'max_marks' => 100.00,
                'remarks' => 'Excellent',
                'school_type' => 'A-level',
            ],
            [
                'grade' => 'B',
                'min_marks' => 70.00,
                'max_marks' => 84.99,
                'remarks' => 'Very Good',
                'school_type' => 'A-level',
            ],
            [
                'grade' => 'C',
                'min_marks' => 55.00,
                'max_marks' => 69.99,
                'remarks' => 'Average',
                'school_type' => 'A-level',
            ],
            [
                'grade' => 'D',
                'min_marks' => 40.00,
                'max_marks' => 54.99,
                'remarks' => 'Below Average',
                'school_type' => 'A-level',
            ],
            [
                'grade' => 'F',
                'min_marks' => 0.00,
                'max_marks' => 39.99,
                'remarks' => 'Fail',
                'school_type' => 'A-level',
            ],
        ];

        DB::table('generic_grades')->insert($gradeScales);
    }
}
