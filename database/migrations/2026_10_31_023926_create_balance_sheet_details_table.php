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
        Schema::create('balance_sheet_details', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('balance_sheet_account_id');

            $table->foreign('balance_sheet_account_id')
                ->references('id')
                ->on('balance_sheet_accounts')
                ->restrictOnDelete();

            $table->unsignedSmallInteger('year');

            $table->decimal('amount', 15, 2)->default(0);

            $table->text('notes')->nullable();

            $table->timestamps();

            // Satu akun hanya boleh memiliki satu saldo per tahun.
            $table->unique(
                ['balance_sheet_account_id', 'year'],
                'balance_sheet_account_year_unique'
            );

            $table->index('year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balance_sheet_details');
    }
};
