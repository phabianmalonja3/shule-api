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
        Schema::create('payments', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('parent_id'); // Foreign key for parent making the payment
            $table->unsignedBigInteger('student_id'); // Foreign key for the student
            $table->decimal('amount', 10, 2); // Payment amount
            $table->string('method')->nullable(); // Payment method (e.g., credit card, PayPal)
            $table->enum('status', ['Paid', 'Pending', 'Failed'])->default('Pending'); // Payment status
            $table->string('transaction_id')->nullable(); // Transaction ID from payment gateway
            $table->date('subscription_start')->nullable(); // Subscription start date
            $table->date('subscription_end')->nullable(); // Subscription end date
            $table->timestamps(); // Created at and Updated at
            $table->foreign('parent_id')->references('id')->on('parents')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
