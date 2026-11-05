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
        Schema::create('salaries', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('employee_id');
            
            $table->string('month')->nullable();
            $table->year('year')->nullable();
            $table->double('salary')->nullable();
            $table->double('allowance1')->nullable();
            $table->double('allowance2')->nullable();
            $table->double('allowance3')->nullable();
            $table->double('allowance4')->nullable();
            $table->double('allowance5')->nullable();
            $table->double('allowance6')->nullable();
            $table->double('salary_adjustment')->nullable();
            $table->double('bos')->nullable();
            $table->double('meal_allowance')->nullable();
            $table->double('rumbay')->nullable();
            $table->double('salary_amount')->nullable();
            $table->double('deduction')->nullable();
            $table->double('amount_received')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};
