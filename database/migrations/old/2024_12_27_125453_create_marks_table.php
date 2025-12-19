<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('marks');

        // Create the marks table with the updated structure
        Schema::create('marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained();  // Assuming you have a 'students' table
            $table->foreignId('subject_id')->constrained();  // Assuming you have a 'subjects' table
            $table->decimal('obtained_marks', 5, 2);  // Marks obtained by the student
            $table->string('grade', 2);  // Grade (e.g. A, B, C)
            $table->foreign('grade')->references('grade')->on('grade_scale');  // Reference grade from grade_scale
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marks');
    }
};
