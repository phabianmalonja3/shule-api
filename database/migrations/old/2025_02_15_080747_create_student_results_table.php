<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Drop the `student_results` table if it exists
        Schema::dropIfExists('student_results');
        
        // Create the new `student_results` table
        Schema::create('student_results', function (Blueprint $table) {
            $table->id();  // Primary key
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');  // Foreign key to `students`
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');  // Foreign key to `academic_years`
            $table->foreignId('exam_type_id')->constrained('examination_types')->onDelete('cascade');  // Foreign key to `examination_types`
            $table->integer('total_marks')->default(0);  // Total marks obtained
            $table->decimal('average_marks', 5, 2)->default(0);  // Average marks (rounded to two decimal places)
            $table->string('position')->nullable();  // Position in the class
            $table->timestamps();  // For created_at and updated_at
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_results');
    }
};
