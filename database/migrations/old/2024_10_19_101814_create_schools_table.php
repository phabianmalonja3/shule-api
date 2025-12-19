<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
{
    Schema::create('schools', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('motto')->nullable();
        $table->string('color')->default('#000000');
        $table->string('postal_code')->nullable();
        $table->string('city')->nullable();
        $table->json('school_type')->nullable();
        $table->timestamps();
    });
}


    public function down()
    {
        Schema::dropIfExists('schools');
    }
};
