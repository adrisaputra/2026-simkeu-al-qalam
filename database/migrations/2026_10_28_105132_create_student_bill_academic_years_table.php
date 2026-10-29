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
        Schema::create('student_bill_academic_years', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('work_unit_id');

            // Relasi tahun ajaran
            $table->unsignedInteger('academic_year_id');
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->restrictOnDelete();

            // Relasi jenis tagihan
            $table->unsignedInteger('student_bill_type_id');
            $table->foreign('student_bill_type_id')->references('id')->on('student_bill_types')->restrictOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_bill_academic_years');
    }
};
