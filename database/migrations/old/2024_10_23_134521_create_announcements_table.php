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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();  // Primary key
            $table->foreignId('school_id')->constrained()->onDelete('cascade');  // Foreign key to 'schools' table
            $table->string('title');  // Title of the announcement
            $table->text('content');  // Content of the announcement
            $table->boolean('is_active')->default(true);  // Status of the announcement (active/inactive)
            $table->timestamps();  // Created at and updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
