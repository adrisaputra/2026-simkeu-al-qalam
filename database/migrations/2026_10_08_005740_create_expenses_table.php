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
        Schema::create('expenses', function (Blueprint $table) {
            $table->increments('id',11);

            $table->date('date')->nullable();

            $table->unsignedInteger('transaction_code_id');
            $table->foreign("transaction_code_id")->references('id')->on("transaction_codes");
            
            $table->unsignedInteger('desc_category_id');
            $table->foreign("desc_category_id")->references('id')->on("desc_categories");
            
            $table->text('desc')->nullable();
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
        Schema::dropIfExists('expenses');
    }
};
