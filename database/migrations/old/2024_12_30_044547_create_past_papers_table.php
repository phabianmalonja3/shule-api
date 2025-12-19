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
        Schema::create('past_papers', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('subject_id')->references('id')->on('subjects')->onDelete('CASCADE');
            $table->foreignId('teacher_id')->references('id')->on('users')->onDelete('CASCADE');
            $table->string('title');
            $table->text('description');
            $table->string('year');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('past_papers');
    }
};
