<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransactionAllocation;
use App\Models\TransactionCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class ExpenseController extends Controller
{
    ## Show Data
    public function index()
    {
        $title = "Pengeluaran";
        $account = Account::whereIn('id', ['2','3','4','5'])->get();
        $payment_accounts = TransactionCode::whereIn('code', ['100', '101'])->get();
        $receipt_account = TransactionCode::where('account_id', 1)->get();
        return view('admin.expense.index', compact('title', 'account','payment_accounts','receipt_account'));
    }

    ## Get Data
    public function get_expense_index(Request $request)
    {

        if ($request->ajax()) {
            $counter = 1;

            $query = Transaction::query();

            if ($request->has('month')) {
                $month = $request->input('month') ?? date('m');
                $query->whereMonth('date', $month);
            }
            if ($request->has('year')) {
                $year = $request->input('year') ?? date('Y');
                $query->whereYear('date', $year);
            }
            
            $expense = $query->where('type', 'expense')->with('desc_category', 'transaction_code')->limit(10);

            return DataTables::of($expense)
                ->addIndexColumn()
                ->addColumn('number', function () use (&$counter) {
                    return $counter++;
                })
                ->addColumn('display_date', function ($v) {
                    return Helpers::date($v->date);
                })
                ->addColumn('display_transaction_code', function ($v) {
                    return $v->transaction_code?->code ?? '-';
                })
                ->addColumn('display_classes', function ($v) {
                    return $v->student?->classes->name;
                })
                ->addColumn('display_amount', function ($v) {
                    return Helpers::format_number($v->amount);
                })
                ->addColumn('action', function ($v) {
                    $btn = '<a href="#" onClick="getData(' . $v->id . ')" id="' . $v->id . '" title="Edit" data-toggle="modal" data-target="#exampleModal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                        </a>';
                    $btn .= '<a href="#" onclick="deleteData(' . $v->id . ')" id="' . $v->id . '" class="warning confirm" data-toggle="tooltip" data-placement="top" title="Hapus">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                        </a>';
                    return $btn;
                })
                ->rawColumns(['display_date', 'action'])->make(true);
        }
    }

    public function validate(Request $request, $action)
    {

        if ($request->ajax()) {

            $attributes = [
                'date' => 'Tanggal',
                'amount' => 'Jumlah',
                'account_id' => 'Akun',
                'transaction_code_id' => 'Kode Transaksi',
                'payment_account_id' => 'Akun Pembayaran'
            ];

            if ($action === "Simpan") {
                $rules = [
                    'date' => 'required|date',
                    'amount' => 'required',
                    'account_id' => 'required',
                    'transaction_code_id' => 'required',
                    'payment_account_id' => 'required'
                ];
            } else {
                $rules = [
                    'date' => 'required|date',
                    'amount' => 'required',
                    'account_id' => 'required',
                    'transaction_code_id' => 'required',
                    'payment_account_id' => 'required'
                ];
            }

            $request->validate($rules, [], $attributes);

            return response()->json(['success' => true]);
        }
    }

    ## Save Data
    public function store(Request $request)
    {
        if ($request->ajax()) {
            DB::transaction(function () use ($request) {
                $expense = new Transaction();
                $expense->date = $request->date;
                $expense->type = 'expense';
                $expense->account_id = $request->account_id;
                $expense->transaction_code_id = $request->transaction_code_id;
                $expense->transaction_code_sub_id = $request->transaction_code_sub_id;
                $expense->desc = $request->desc;
                $expense->amount = str_replace('.', '', $request->amount) ?? 0;
                $expense->user_id = Auth::user()->id;
                $expense->save();

                if ($request->filled('receipt_account_id')) {
                    $transaction_allocation = new TransactionAllocation();
                    $transaction_allocation->transaction_id = $expense->id;
                    $transaction_allocation->account_id = 1;
                    $transaction_allocation->transaction_code_id = $request->receipt_account_id;
                    $transaction_allocation->date = $request->date;
                    $transaction_allocation->desc = $request->desc;
                    $transaction_allocation->amount = str_replace('.', '', $request->amount) ?? 0;
                    $transaction_allocation->user_id = Auth::user()->id;
                    $transaction_allocation->save();
                }

            });

            activity()->log('Create Data Transaction');
            return response()->json(['success' => true, 'message' => 'Tambah Data Berhasil']);
        }
    }

    ## Get Data
    public function edit(Request $request, Transaction $expense)
    {
        if ($request->ajax()) {
            return response()->json(['success' => true, 'data' => $expense]);
        }
    }

    ## Edit Data
    public function update(Request $request, Transaction $expense)
    {
        if ($request->ajax()) {
            $expense->date = $request->date;
            $expense->type = 'expense';
            $expense->account_id = $request->account_id;
            $expense->transaction_code_id = $request->transaction_code_id;
            $expense->transaction_code_sub_id = $request->transaction_code_sub_id;
            $expense->desc = $request->desc;
            $expense->amount = str_replace('.', '', $request->amount) ?? 0;
            $expense->user_id = Auth::user()->id;
            $expense->save();

            activity()->log('Edit Data Transaction With ID = ' . $expense->id);
            return response()->json(['success' => true, 'message' => 'Ubah Data Berhasil']);
        }
    }

    ## Delete Data
    public function delete(Request $request, Transaction $expense)
    {
        if ($request->ajax()) {
            $expense->delete();
            activity()->log('Delete Data Transaction With ID = ' . $expense->id);
            return response()->json(['success' => true, 'message' => 'Hapus Data Berhasil']);
        }
    }
}
