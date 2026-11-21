<?php

namespace Tests\Feature;

use App\Http\Controllers\TransactionBankController;
use App\Models\AcademicYear;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TransactionBankIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_filters_transactions_by_academic_year_and_matching_status(): void
    {
        Schema::table('transaction_banks', function (Blueprint $table): void {
            $table->unsignedInteger('transaction_id')->nullable();
            $table->unsignedInteger('account_id')->nullable();
        });

        $academicYear = AcademicYear::query()->create([
            'name' => '2025/2026',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
            'is_active' => true,
        ]);
        AcademicYear::query()->create([
            'name' => '2026/2027',
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
            'is_active' => false,
        ]);

        $descriptionCategoryId = DB::table('desc_categories')->insertGetId([
            'name' => 'Transaksi Uji',
            'entered_by' => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $userId = DB::table('users')->insertGetId([
            'name' => 'Pengguna Uji',
            'email' => 'transaction-bank-test@example.test',
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

        $unmatchedTransactionId = $this->createTransaction(
            '2025-07-01',
            'Transaksi belum cocok',
            $descriptionCategoryId,
            $userId,
            [null]
        );
        $matchedTransactionId = $this->createTransaction(
            '2026-06-30',
            'Transaksi cocok',
            $descriptionCategoryId,
            $userId,
            [$studentId]
        );
        $partialTransactionId = $this->createTransaction(
            '2026-01-15',
            'Transaksi sebagian cocok',
            $descriptionCategoryId,
            $userId,
            [null, $studentId]
        );
        $otherYearTransactionId = $this->createTransaction(
            '2026-07-01',
            'Transaksi tahun berikutnya',
            $descriptionCategoryId,
            $userId,
            [null]
        );

        foreach ([
            'unmatched' => $unmatchedTransactionId,
            'matched' => $matchedTransactionId,
            'partial' => $partialTransactionId,
        ] as $status => $expectedTransactionId) {
            $view = app(TransactionBankController::class)->index(Request::create('/transaction_bank', 'GET', [
                'academic_year_id' => $academicYear->id,
                'status' => $status,
            ]));

            $this->assertSame(
                [$expectedTransactionId],
                $view->getData()['transaction_bank']->getCollection()->modelKeys()
            );
        }

        $view = app(TransactionBankController::class)->index(Request::create('/transaction_bank', 'GET', [
            'academic_year_id' => '',
        ]));

        $this->assertContains(
            $otherYearTransactionId,
            $view->getData()['transaction_bank']->getCollection()->modelKeys()
        );
    }

    /**
     * @param  array<int, int|null>  $studentIds
     */
    private function createTransaction(
        string $date,
        string $description,
        int $descriptionCategoryId,
        int $userId,
        array $studentIds
    ): int {
        $transactionId = DB::table('transactions')->insertGetId([
            'date' => $date,
            'type' => 'income',
            'desc_category_id' => $descriptionCategoryId,
            'desc' => $description,
            'amount' => 100,
            'user_id' => $userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ($studentIds as $studentId) {
            DB::table('transaction_banks')->insert([
                'transaction_id' => $transactionId,
                'account_id' => 1,
                'desc_category_id' => $descriptionCategoryId,
                'student_id' => $studentId,
                'transaction_number' => 'TRX-'.$transactionId,
                'date' => $date,
                'time' => '10:00:00',
                'amount' => 100,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $transactionId;
    }
}
