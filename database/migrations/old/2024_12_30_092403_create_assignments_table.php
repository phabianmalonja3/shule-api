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
    { Schema::create('assignments', function (Blueprint $table) {
        $table->id(); 
        $table->foreignId('subject_id')->references('id')->on('subjects')->onDelete('cascade'); 
        $table->foreignId('stream_id')->references('id')->on('streams')->onDelete('cascade'); 
        $table->foreignId('teacher_id')->references('id')->on('users')->onDelete('cascade'); 
        $table->string('title');
        $table->text('description')->nullable();
        $table->dateTime('due_date');
        $table->timestamps(); 
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
