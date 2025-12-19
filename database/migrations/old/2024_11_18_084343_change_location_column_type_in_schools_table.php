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
        Schema::table('schools', function (Blueprint $table) {
            // Alter the 'location' column to JSON type
            $table->json('location')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('schools', function (Blueprint $table) {
            // Revert the 'location' column back to TEXT
            $table->text('location')->nullable()->change();
        });
    }
};
