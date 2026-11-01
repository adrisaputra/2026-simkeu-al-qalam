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
        Schema::create('balance_sheet_accounts', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('transaction_code_id');
            $table->foreign('transaction_code_id')->references('id')->on('transaction_codes');

            $table->unsignedInteger('transaction_code_sub_id');
            $table->foreign('transaction_code_sub_id')->references('id')->on('transaction_code_subs');

            $table->string('name');

            $table->enum('category', [
                'asset',
                'liability',
                'net_asset',
            ]);

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balance_sheet_accounts');
    }
};
