<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date');
            $table->enum('type', ['income', 'expense']);
            $table->unsignedInteger('transaction_code_id')->nullable();
            $table->foreign('transaction_code_id')->references('id')->on('transaction_codes');
            $table->unsignedInteger('desc_category_id');
            $table->foreign('desc_category_id')->references('id')->on('desc_categories');
            $table->text('desc')->nullable();
            $table->decimal('amount', 15, 2);
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
