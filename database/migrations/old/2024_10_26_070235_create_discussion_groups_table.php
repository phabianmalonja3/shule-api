<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('discussion_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('students')->onDelete('cascade'); // student who created the group
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('discussion_group_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discussion_group_id')->constrained('discussion_groups')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('discussion_group_student');
        Schema::dropIfExists('discussion_groups');
    }

    /**
     * Reverse the migrations.
     */
    
};
