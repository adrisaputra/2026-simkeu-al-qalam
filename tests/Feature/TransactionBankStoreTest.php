<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TransactionBankStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_saves_bank_transaction_and_recalculates_parent_amount(): void
    {
        Schema::table('transactions', function (Blueprint $table): void {
            $table->unsignedInteger('transaction_code_sub_id')->nullable();
            $table->softDeletes();
        });
        Schema::table('students', function (Blueprint $table): void {
            $table->softDeletes();
        });
        Schema::table('transaction_banks', function (Blueprint $table): void {
            $table->unsignedInteger('transaction_id')->nullable();
            $table->unsignedInteger('account_id')->nullable();
            $table->unsignedInteger('transaction_code_id')->nullable();
            $table->unsignedInteger('transaction_code_sub_id')->nullable();
            $table->string('student_name')->nullable();
            $table->string('classes')->nullable();
            $table->date('import_date')->nullable();
            $table->string('status')->nullable();
        });

        $descriptionCategoryId = DB::table('desc_categories')->insertGetId([
            'name' => 'Transaksi Uji',
            'entered_by' => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $userId = DB::table('users')->insertGetId([
            'name' => 'Pengguna Uji',
            'email' => 'transaction-bank-store-test@example.test',
            'password' => 'password',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $classId = DB::table('classes')->insertGetId([
            'work_unit_id' => 3,
            'name' => 'Kelas Uji',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $studentId = DB::table('students')->insertGetId([
            'classes_id' => $classId,
            'id_student' => 'SISWA-UJI',
            'name' => 'Siswa Uji',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $transactionId = DB::table('transactions')->insertGetId([
            'date' => '2026-01-15',
            'type' => 'income',
            'desc_category_id' => $descriptionCategoryId,
            'desc' => 'Transaksi uji',
            'amount' => 25,
            'user_id' => $userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('transaction_banks')->insert([
            'transaction_id' => $transactionId,
            'desc_category_id' => $descriptionCategoryId,
            'student_id' => $studentId,
            'transaction_number' => 'TRX-SEBELUMNYA',
            'amount' => 2500,
            'user_id' => $userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user = (new User)->forceFill(['id' => $userId]);
        $user->setRelation('group', (new Group)->forceFill(['name' => 'Accountant']));

        config(['database.connections.mysql' => config('database.connections.sqlite')]);
        DB::purge('mysql');
        DB::connection('mysql')->setPdo(DB::connection('sqlite')->getPdo());

        $this->actingAs($user)
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->postJson(route('transaction_bank.store'), [
                'id' => $transactionId,
                'student_id2' => $studentId,
                'transaction_number' => 'TRX-BARU',
                'amount' => '2.250',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertSame(
            2250.0,
            (float) DB::table('transaction_banks')
                ->where('transaction_id', $transactionId)
                ->where('transaction_number', 'TRX-BARU')
                ->value('amount')
        );
        $this->assertSame(
            4750.0,
            (float) DB::table('transactions')->where('id', $transactionId)->value('amount')
        );
    }
}
