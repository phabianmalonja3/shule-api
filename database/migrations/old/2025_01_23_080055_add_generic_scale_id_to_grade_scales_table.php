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
        Schema::table('grade_scales', function (Blueprint $table) {
            $table->foreignId('generic_grade_id')
            ->nullable()
            ->references('id')
            ->on('generic_grades')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grade_scales', function (Blueprint $table) {
            //
        });
    }
};
