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
        Schema::create('incomes', function (Blueprint $table) {
            $table->increments('id',11);

            $table->unsignedInteger('desc_category_id');
            $table->foreign("desc_category_id")->references('id')->on("desc_categories");
            
            $table->unsignedInteger('student_id');
            $table->foreign("student_id")->references('id')->on("students");
            
            $table->string('transaction_number')->nullable();
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->double('amount')->nullable();
            
            $table->unsignedBigInteger('user_id');
            $table->foreign("user_id")->references('id')->on("users");
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
