<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Models\Account;
use App\Models\Student;
use App\Models\Transaction;
use App\Models\TransactionBank;
use App\Models\TransactionCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\DataTables;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as SpreadsheetReaderException;

class IncomeController extends Controller
{
    ## Show Data
    public function index()
    {
        $title = "pemasukan";
        $account = Account::get();
        $transaction_code = TransactionCode::where('account_id',1)->get();
        $payment_accounts = TransactionCode::whereIn('code', ['100', '101'])->get();
        return view('admin.income.index', compact('title','account','transaction_code','payment_accounts'));
    }

    ## Get Data
    public function get_income_index(Request $request)
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
            
            $income = $query->where('type', 'income')->with('desc_category', 'transaction_code')->limit(10);

            return DataTables::of($income)
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
                ->addColumn('display_amount', function ($v) {
                    return Helpers::format_number($v->amount);
                })
                ->addColumn('action', function ($v) {
                    $transaction_bank = TransactionBank::where('transaction_id', $v->id)->count();
                    if($transaction_bank > 0){
                        $btn = '<button type="button" class="btn btn-sm btn-info" onclick="showBankTransactions(' . $v->id . ')">Transaksi Bank</button> ';
                    } else {
                        $btn = null;
                    }
                    $btn .= '<a href="#" onClick="getData(' . $v->id . ')" id="' . $v->id . '" title="Edit" data-toggle="modal" data-target="#exampleModal">
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

    public function get_bank_transactions(Request $request, Transaction $income)
    {
        abort_unless($income->type === 'income', 404);

        if ($request->ajax()) {
            $bankTransactions = TransactionBank::with('student.classes.work_unit')
                ->where('transaction_id', $income->id)
                ->select('transaction_banks.*');

            return DataTables::of($bankTransactions)
                ->addIndexColumn()
                ->addColumn('student_name', fn($transaction) => $transaction->student?->name ?? '-')
                ->addColumn('work_unit_name', fn($transaction) => $transaction->student?->classes?->work_unit?->name ?? '-')
                ->addColumn('class_name', fn($transaction) => $transaction->student?->classes?->name ?? '-')
                ->addColumn('display_date', fn($transaction) => $transaction->date ? Helpers::date($transaction->date) : '-')
                ->addColumn('display_amount', fn($transaction) => Helpers::format_number($transaction->amount))
                ->make(true);
        }

        abort(404);
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
            $income = new Transaction();
            $income->date = $request->date;
            $income->type = 'income';
            $income->account_id = 1;
            $income->transaction_code_id = $request->transaction_code_id;
            $income->transaction_code_sub_id = $request->transaction_code_sub_id ?? null;
            $income->payment_account_id = $request->payment_account_id;
            $income->desc = $request->desc;
            $income->amount = str_replace('.', '', $request->amount) ?? 0;
            $income->user_id = Auth::user()->id;
            $income->save();

            activity()->log('Create Data Transaction');
            return response()->json(['success' => true, 'message' => 'Tambah Data Berhasil']);
        }
    }

    ## Get Data
    public function edit(Request $request, Transaction $income)
    {
        if ($request->ajax()) {
            return response()->json(['success' => true, 'data' => $income]);
        }
    }

    ## Edit Data
    public function update(Request $request, Transaction $income)
    {
        if ($request->ajax()) {
            $income->date = $request->date;
            $income->type = 'income';
            $income->account_id = $request->account_id;
            $income->transaction_code_id = $request->transaction_code_id;
            $income->transaction_code_sub_id = $request->transaction_code_sub_id ?? null;
            $income->payment_account_id = $request->payment_account_id;
            $income->desc = $request->desc;
            $income->amount = str_replace('.', '', $request->amount) ?? 0;
            $income->user_id = Auth::user()->id;
            $income->save();

            activity()->log('Edit Data Transaction With ID = ' . $income->id);
            return response()->json(['success' => true, 'message' => 'Ubah Data Berhasil']);
        }
    }

    ## Delete Data
    public function delete(Request $request, Transaction $income)
    {
        if ($request->ajax()) {
            $income->delete();
            activity()->log('Delete Data Transaction With ID = ' . $income->id);
            return response()->json(['success' => true, 'message' => 'Hapus Data Berhasil']);
        }
    }

}
