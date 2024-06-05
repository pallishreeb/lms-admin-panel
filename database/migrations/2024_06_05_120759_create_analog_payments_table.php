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
        Schema::create('analog_payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number');
            $table->string('payment_screenshot')->nullable();
            $table->string('division')->nullable();
            $table->string('district')->nullable();
            $table->string('upazilla')->nullable();
            $table->string('school_name')->nullable();
            $table->string('class');
            $table->string('student_name');
            $table->string('mobile_number');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('category_id');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('amount')->nullable();
            $table->string('trans_id')->nullable();
            $table->date('valid_until')->default('2024-12-31');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analog_payments');
    }
};
