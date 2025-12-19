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
        Schema::create('generic_grades', function (Blueprint $table) {
            $table->id();
            $table->char('grade');
            $table->decimal('min_marks',5,2);
            $table->decimal('max_marks',5,2);
            $table->string('remarks');
            $table->string('school_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generic_grades');
    }
};
