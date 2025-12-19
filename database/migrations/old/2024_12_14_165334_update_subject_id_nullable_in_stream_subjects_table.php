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
        Schema::table('stream_subjects', function (Blueprint $table) {
            $table->unsignedBigInteger('subject_id')->nullable()->change(); // Make `subject_id` nullable
        });
    }

    public function down()
    {
        Schema::table('stream_subjects', function (Blueprint $table) {
            $table->unsignedBigInteger('subject_id')->nullable(false)->change(); // Revert `subject_id` to NOT NULL
        });
    }
};
