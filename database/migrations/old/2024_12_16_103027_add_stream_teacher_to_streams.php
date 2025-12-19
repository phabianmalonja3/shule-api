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
        Schema::table('streams', function (Blueprint $table) {
            $table->unsignedBigInteger('stream_teacher_id')->nullable()->after('school_class_id');
            $table->foreign('stream_teacher_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('streams', function (Blueprint $table) {
            $table->dropForeign(['stream_teacher_id']);
            $table->dropColumn('stream_teacher_id');
        });
    }
};
