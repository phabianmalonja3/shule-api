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
        Schema::table('school_classes', function (Blueprint $table) {
            $table->unsignedBigInteger('subject_id')->after('id')->nullable();
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::table('school_classes', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
            $table->dropColumn('subject_id');
        });
    }
};
