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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Linking to users table
            $table->foreignId('school_id')->constrained('schools'); // School ID from authenticated user
            $table->foreignId('school_class_id')->constrained('school_classes'); // Class ID
            $table->foreignId('stream_id')->constrained('streams'); // Stream ID
            $table->boolean('is_active')->default(true); // To activate/deactivate student
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
