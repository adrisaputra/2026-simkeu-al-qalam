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
        Schema::create('student_bill_types', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('work_unit_id');
            $table->string('name');
            $table->enum('frequency', [
                'weekly',
                'monthly',
                'semester',
                'yearly',
            ]);

            $table->decimal('default_amount', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['work_unit_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_bill_types');
    }
};
