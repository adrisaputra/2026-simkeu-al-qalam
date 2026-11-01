<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class IncomeDateFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_income_list_can_be_filtered_by_month_and_year(): void
    {
        $userId = DB::table('users')->insertGetId([
            'name' => 'Pengguna Uji',
            'email' => 'income-filter@example.test',
            'password' => 'password',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $categoryId = DB::table('desc_categories')->insertGetId([
            'name' => 'Kategori Uji',
            'entered_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $matchingTransactionId = DB::table('transactions')->insertGetId([
            'date' => '2026-05-10',
            'type' => 'income',
            'desc_category_id' => $categoryId,
            'desc' => 'Pemasukan yang cocok',
            'amount' => 100000,
            'user_id' => $userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('transactions')->insert([
            [
                'date' => '2026-04-10',
                'type' => 'income',
                'desc_category_id' => $categoryId,
                'desc' => 'Bulan berbeda',
                'amount' => 100000,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date' => '2025-05-10',
                'type' => 'income',
                'desc_category_id' => $categoryId,
                'desc' => 'Tahun berbeda',
                'amount' => 100000,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'date' => '2026-05-11',
                'type' => 'expense',
                'desc_category_id' => $categoryId,
                'desc' => 'Bukan pemasukan',
                'amount' => 100000,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->withoutMiddleware()
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->getJson(route('income.list', [
                'month' => 5,
                'year' => 2026,
                'draw' => 1,
                'start' => 0,
                'length' => 50,
            ]))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $matchingTransactionId);

        $this->getJson(route('income.list', [
            'draw' => 2,
            'start' => 0,
            'length' => 50,
        ]))
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }
}
