<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Models\BalanceSheetDetail;
use App\Models\Employee;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    ## Show Data
    public function index(Request $request)
    {
        $title = "Dashboard";
        if(in_array(Auth::user()->group->name,['Admin Finance','Revenue Treasurer','Expenditure Treasurer'])){
            $income = Transaction::whereMonth('date', now()->month)->where('type', 'income')->sum('amount');
            $expense = Transaction::whereMonth('date', now()->month)->where('type', 'expense')->sum('amount');
            
            $year = now()->year;

            ## Kas Tunai
            $cash_balance = Helpers::cash_balance(18, $year);

            ## Kas Bank
            $bank_balance = Helpers::cash_balance(19, $year);

            ## Total Saldo Kas dan Bank
            $cash_balance = $cash_balance + $bank_balance;

            ## Aset Lancar
            $kas_tunai = Transaction::where('payment_account_id', 18)
                    ->whereYear('date', $year)
                    ->selectRaw("
                        SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) -
                        SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) AS total
                    ")
                    ->value('total') ?? 0;
            
            $kas_bank = Transaction::where('payment_account_id', 19)
                ->whereYear('date', $year)
                ->selectRaw("
                    SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) -
                    SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) AS total
                ")
                ->value('total') ?? 0;

            // Total Aset Lancar = Kas+Bank+Piutang+Aset Lancar Lainnya
            $aset_lancar = $kas_tunai + $kas_bank;

            ## Aset Tetap
            $nilai_aset_tetap = BalanceSheetDetail::whereHas('balance_sheet_account.transaction_code', function ($query) {
                    $query->whereIn('code', [107, 108, 110, 112, 114, 116]);
                })
                ->where('year', $year)
                ->sum('amount');

            // Akumulasi penyusutan
            $akumulasi_penyusutan = BalanceSheetDetail::whereHas('balance_sheet_account.transaction_code', function ($query) {
                    $query->whereIn('code', [109, 111, 113, 115]);
                })
                ->where('year', $year)
                ->sum('amount');

            // Total aset tetap bersih
            $aset_tetap = $nilai_aset_tetap - $akumulasi_penyusutan;

            $total_aset = $aset_lancar + $aset_tetap;

            ##  Grafik Pemasukan dan Pengeluaran Pertahun
            $transactions = Transaction::query()
                            ->selectRaw('MONTH(date) as month')
                            ->selectRaw("
                                SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income
                            ")
                            ->selectRaw("
                                SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense
                            ")
                            ->whereYear('date', $year)
                            ->groupBy(DB::raw('MONTH(date)'))
                            ->get()
                            ->keyBy('month');

            $incomes = [];
            $expenses = [];

            for ($month = 1; $month <= 12; $month++) {
                $incomes[] = (float) ($transactions[$month]->income ?? 0);
                $expenses[] = (float) ($transactions[$month]->expense ?? 0);
            }

            ## Grafik Pie pemasukan berdasarkan kategori 
            $income_by_category = Transaction::query()
                                ->select('transaction_code_id')
                                ->selectRaw('SUM(amount) as total')
                                ->whereYear('date', $year)
                                ->where('type', 'income')
                                ->with('transaction_code:id,name')
                                ->groupBy('transaction_code_id')
                                ->get()
                                ->map(function ($item) {
                                    return [
                                        'name' => $item->transaction_code->name ?? 'Lainnya',
                                        'y' => (float) $item->total,
                                    ];
                                });
            
            ## Grafik Pie pengeluaran berdasarkan kategori 
            $expense_by_category = Transaction::query()
                ->select('transaction_code_id')
                ->selectRaw('SUM(amount) as total')
                ->whereYear('date', $year)
                ->where('type', 'expense')
                ->with('transaction_code:id,name')
                ->groupBy('transaction_code_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'name' => $item->transaction_code->name ?? 'Lainnya',
                        'y' => (float) $item->total,
                    ];
                });

            return view('admin.home', compact('title','income','expense','year','incomes','expenses','income_by_category','expense_by_category','cash_balance','total_aset'));
        } 
    }
}