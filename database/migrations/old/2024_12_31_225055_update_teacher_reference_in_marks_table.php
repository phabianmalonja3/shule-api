<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('marks', function (Blueprint $table) {
            // Drop the existing foreign key and column
            $table->dropForeign(['teacher_id']);
            
            // Add the new foreign key reference
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::table('marks', function (Blueprint $table) {
            // Rollback to the previous foreign key
            $table->dropForeign(['teacher_id']);
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
        });
    }
};
