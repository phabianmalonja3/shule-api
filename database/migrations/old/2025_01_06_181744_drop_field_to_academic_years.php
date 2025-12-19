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
       

        Schema::table('academic_years', function (Blueprint $table) {

            $table->dropColumn('start_date');
            $table->dropColumn('end_date');
            $table->dropColumn('midterm_end_date');
            $table->dropColumn('midterm_start_date');
            $table->dropColumn('annual_end_date');
            $table->dropColumn('annual_start_date');

        

            // $table->id();
            // $table->string('year');
            // $table->foreignId('school_id')
            // ->constrained()
            // ->onDelete('cascade');
            // $table->boolean('is_active')->default(false);
            // $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_years', function (Blueprint $table) {
            //
        });
    }
};
