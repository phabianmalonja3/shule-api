<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('grade_scale', function (Blueprint $table) {
            $table->char('grade', 1)->primary();  // Grade: A, B, C, etc.
            $table->decimal('min_marks', 5, 2);   // Minimum marks for the grade
            $table->decimal('max_marks', 5, 2);   // Maximum marks for the grade
            $table->timestamps();
        });

        // Optional: Seed initial grade ranges into grade_scale
        DB::table('grade_scale')->insert([
            ['grade' => 'A', 'min_marks' => 90.00, 'max_marks' => 100.00],
            ['grade' => 'B', 'min_marks' => 80.00, 'max_marks' => 89.99],
            ['grade' => 'C', 'min_marks' => 70.00, 'max_marks' => 79.99],
            ['grade' => 'D', 'min_marks' => 60.00, 'max_marks' => 69.99],
            ['grade' => 'E', 'min_marks' => 50.00, 'max_marks' => 59.99],
            ['grade' => 'F', 'min_marks' => 0.00, 'max_marks' => 49.99],
        ]);
    }
};
