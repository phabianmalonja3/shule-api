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
        Schema::table('school_applications', function (Blueprint $table) {
            // Set the default value of the status column to 'pending'
            $table->string('status')->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('school_applications', function (Blueprint $table) {
            // Remove the default value of the status column
            $table->string('status')->default(null)->change();
        });
    }
};
