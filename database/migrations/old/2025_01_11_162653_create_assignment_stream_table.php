<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assignment_stream', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')
            ->constrained()
            ->onDelete('cascade');
            $table->foreignId('stream_id')
            ->constrained()
            ->onDelete('cascade');
        });
    }


    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_stream');
    }
};
