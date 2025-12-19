<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 'grade' ,
        'min_marks',
        'max_marks',
        'remarks',
        'school_id',
     */
    public function up(): void
    {
        Schema::create('grade_scales', function (Blueprint $table) {
            $table->id();
            $table->char('grade');
            $table->decimal('min_marks',5,2);
            $table->decimal('max_marks',5,2);
            $table->string('remarks');
            $table->foreignId('school_id')->constrained('schools')->onDelete('CASCADE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_scales');
    }
};
