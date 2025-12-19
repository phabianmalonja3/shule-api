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
            // Make the email column nullable
            $table->string('email')->nullable()->change();
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
            // Revert the email column to not nullable
            $table->string('email')->nullable(false)->change();
        });
    }
};
