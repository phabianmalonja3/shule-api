<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('school_applications', function (Blueprint $table) {
            $table->id();
            $table->string('school_name');
            $table->string('fullname');
            $table->string('email')->unique();
            $table->string('phone');
            $table->boolean('is_verified')->default(false); // Admin will verify
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_applications');
    }
};
