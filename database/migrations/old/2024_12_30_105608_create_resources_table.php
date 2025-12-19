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
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subject_id');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('resource_type'); // 'notes', 'past_paper', 'video', 'audio', 'link' // For uploads
            $table->string('url')->nullable(); // For external links
            $table->unsignedBigInteger('created_by'); // ID of the teacher who uploaded
            $table->foreign('created_by')->references('id')->on('users'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
