<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // School name
            $table->string('address'); // School address
            $table->string('postal_code')->nullable(); // Postal code
            $table->string('phone_number')->nullable(); // Phone number
            $table->foreignId('head_teacher_id')->constrained('users')->onDelete('cascade'); // Foreign key to users table
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('schools');
    }
};
