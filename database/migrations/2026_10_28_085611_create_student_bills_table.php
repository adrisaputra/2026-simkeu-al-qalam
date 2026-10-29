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
        Schema::create('student_bills', function (Blueprint $table) {
             $table->increments('id');

            // Relasi siswa
            $table->unsignedInteger('student_id');
            $table->foreign('student_id')
                ->references('id')
                ->on('students')
                ->restrictOnDelete();

            // Relasi jenis tagihan
            $table->unsignedInteger('student_bill_type_id');
            $table->foreign('student_bill_type_id')
                ->references('id')
                ->on('student_bill_types')
                ->restrictOnDelete();

            // Relasi tahun ajaran
            $table->unsignedInteger('academic_year_id');
            $table->foreign('academic_year_id')
                ->references('id')
                ->on('academic_years')
                ->restrictOnDelete();

            // Periode tagihan di dalam tahun ajaran
            $table->date('period_start');
            $table->date('period_end');

            // Nominal disimpan sebagai snapshot saat tagihan diterbitkan
            $table->decimal('amount', 15, 2);

            // Batas waktu pembayaran
            $table->date('due_date')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_bills');
    }
};
