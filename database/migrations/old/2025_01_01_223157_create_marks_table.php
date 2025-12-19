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
        Schema::dropIfExists('marks'); // Drop the table if it exists

        Schema::create('marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')
            ->constrained('students')
            ->onDelete('CASCADE');
            $table->foreignId('teacher_id')
            ->constrained('users')
            ->onDelete('CASCADE');
            $table->foreignId('subject_id')
            ->constrained()
            ->onDelete('CASCADE');
            $table->integer('obtained_marks');
            $table->string('grade');
            $table->string('remark')->nullable(); // Allow remark to be null
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marks', function (Blueprint $table) {
            //
        });
    }
};
