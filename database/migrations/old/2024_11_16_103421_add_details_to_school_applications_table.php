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
        Schema::table('school_applications', function (Blueprint $table) {
            $table->string('first_name');
            $table->string('middle_name')->nullable();  // middle_name can be nullable

            // Adding the location column as JSON type
            $table->json('location')->nullable();  // JSON column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_applications', function (Blueprint $table) {
            //
        });
    }
};
