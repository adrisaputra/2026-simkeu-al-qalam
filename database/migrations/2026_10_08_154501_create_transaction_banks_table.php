<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_banks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('desc_category_id');
            $table->foreign('desc_category_id')->references('id')->on('desc_categories');
            $table->unsignedInteger('student_id')->nullable();
            $table->foreign('student_id')->references('id')->on('students');
            $table->string('transaction_number')->nullable();
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->decimal('amount', 15, 2);
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_banks');
    }
};
