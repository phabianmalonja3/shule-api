<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToSchoolApplicationsTable extends Migration
{
    public function up()
    {
        Schema::table('school_applications', function (Blueprint $table) {
            $table->string('address')->nullable(); // Add the address field
            $table->string('postal_code')->nullable(); // Add the postal code field
            $table->string('city')->nullable(); // Add the city field
            $table->json('school_type')->nullable(); // Add school_type field to store as JSON
        });
    }

    public function down()
    {
        Schema::table('school_applications', function (Blueprint $table) {
            $table->dropColumn(['address', 'postal_code', 'city', 'school_type']); // Drop columns if rolling back
        });
    }
}
