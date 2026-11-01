<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BalanceSheetYearFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_balance_sheet_details_can_be_filtered_by_year(): void
    {
        $accountId = $this->createBalanceSheetAccount();
        $matchingDetailId = $this->createBalanceSheetDetail($accountId, 2025);
        $this->createBalanceSheetDetail($accountId, 2026);

        $this->withoutMiddleware()
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->getJson(route('balance_sheet_details.list', [
                'year' => 2025,
                'draw' => 1,
                'start' => 0,
                'length' => 50,
            ]))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $matchingDetailId);
    }

    public function test_balance_sheet_accounts_can_be_filtered_by_detail_year(): void
    {
        $matchingAccountId = $this->createBalanceSheetAccount();
        $otherAccountId = $this->createBalanceSheetAccount();
        $this->createBalanceSheetDetail($matchingAccountId, 2025);
        $this->createBalanceSheetDetail($otherAccountId, 2026);

        $this->withoutMiddleware()
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->getJson(route('balance_sheet_account.list', [
                'year' => 2025,
                'draw' => 1,
                'start' => 0,
                'length' => 50,
            ]))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $matchingAccountId);
    }

    private function createBalanceSheetAccount(): int
    {
        $number = DB::table('transaction_codes')->count() + 1;
        $codeId = DB::table('transaction_codes')->insertGetId([
            'code' => 'TEST-'.$number,
            'name' => 'Kode Uji',
            'type' => 'income',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $subCodeId = DB::table('transaction_code_subs')->insertGetId([
            'transaction_code_id' => $codeId,
            'code' => 'SUB-'.$number,
            'name' => 'Subkode Uji',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return DB::table('balance_sheet_accounts')->insertGetId([
            'transaction_code_id' => $codeId,
            'transaction_code_sub_id' => $subCodeId,
            'name' => 'Akun Neraca Uji',
            'category' => 'asset',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createBalanceSheetDetail(int $accountId, int $year): int
    {
        return DB::table('balance_sheet_details')->insertGetId([
            'balance_sheet_account_id' => $accountId,
            'year' => $year,
            'amount' => 1000,
            'notes' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
