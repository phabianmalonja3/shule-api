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
        Schema::table('stream_subject_teacher', function (Blueprint $table) {
            $table->foreignId('school_class_id')->after('teacher_id')->constrained('school_classes')->onDelete('cascade');
        });
    }

   
};
