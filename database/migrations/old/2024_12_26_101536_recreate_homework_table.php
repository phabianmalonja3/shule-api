<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
   
    public function up()
    {
        // Drop the existing homework table if it exists
        Schema::dropIfExists('homework');

        // Recreate the homework table
        Schema::create('homework', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teacher_id'); // Reference to the teacher
            $table->unsignedBigInteger('subject_id'); // Reference to the subject
            $table->unsignedBigInteger('class_id'); // Reference to the class
            $table->unsignedBigInteger('stream_id'); // Reference to the stream
            $table->string('title'); // Title of the homework
            $table->text('description')->nullable(); // Optional description
            $table->string('file_path')->nullable(); // File path for uploaded PDF or other files
            $table->date('due_date'); // Due date for the homework
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('school_classes')->onDelete('cascade');
            $table->foreign('stream_id')->references('id')->on('streams')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('homework');
    }
};
