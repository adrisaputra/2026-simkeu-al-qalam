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
        Schema::create('transaction_code_subs', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('transaction_code_id');
            $table->foreign('transaction_code_id')->references('id')->on('transaction_codes');

            $table->string('name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_code_subs');
    }
};
