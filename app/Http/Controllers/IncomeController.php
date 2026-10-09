<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Models\DescCategory;
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
        $transaction_code = TransactionCode::get();
        $desc_category = DescCategory::get();
        return view('admin.income.index', compact('title', 'transaction_code', 'desc_category'));
    }

    ## Get Data
    public function get_income_index(Request $request)
    {

        if ($request->ajax()) {
            $counter = 1;

            $income = Transaction::where('type', 'income')->with('desc_category', 'transaction_code');

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
            $bankTransactions = TransactionBank::with('student')
                ->where('transaction_id', $income->id)
                ->select('transaction_banks.*');

            return DataTables::of($bankTransactions)
                ->addIndexColumn()
                ->addColumn('student_name', fn($transaction) => $transaction->student?->name ?? '-')
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
                'amount' => 'Jumlah'
            ];

            if ($action === "Simpan") {
                $rules = [
                    'date' => 'required|date',
                    'amount' => 'required|numeric'
                ];
            } else {
                $rules = [
                    'date' => 'required|date',
                    'amount' => 'required|numeric'
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
            $income->transaction_code_id = $request->transaction_code_id;
            $income->desc_category_id = $request->desc_category_id;
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
            $income->transaction_code_id = $request->transaction_code_id;
            $income->desc_category_id = $request->desc_category_id;
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

    public function import(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
            'desc_category_id' => 'required|exists:desc_categories,id',
            'desc' => 'required|string|max:1000',
        ]);

        try {
            $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
        } catch (SpreadsheetReaderException $exception) {
            throw ValidationException::withMessages([
                'file' => 'File Excel tidak dapat dibaca. Pastikan file tidak rusak.',
            ]);
        }

        $rows = $spreadsheet->getSheet(0)->toArray();

        try {
            $result = DB::transaction(function () use ($rows, $validated) {

                $bankTransactions = [];
                $totalAmount = 0;

                // Buat transaction utama terlebih dahulu
                $transaction = Transaction::create([
                    'date' => now()->toDateString(),
                    'type' => 'income',
                    'transaction_code_id' => 2,
                    'desc_category_id' => $validated['desc_category_id'],
                    'desc' => $validated['desc'],
                    'amount' => 0,
                    'user_id' => Auth::id(),
                ]);

                foreach ($rows as $index => $row) {

                    // Skip header / baris kosong
                    if (
                        $index === 0 ||
                        empty(array_filter(
                            $row,
                            fn($value) => $value !== null && $value !== ''
                        ))
                    ) {
                        continue;
                    }

                    // =========================
                    // AMOUNT
                    // =========================
                    $amount = str_replace('.', '', $row[7] ?? 0);
                    $amount = (float) $amount;

                    // =========================
                    // DATE & TIME
                    // =========================
                    $date = null;
                    $time = null;

                    $dateTime = trim((string) ($row[2] ?? ''));

                    if ($dateTime !== '') {

                        $parts = preg_split('/\s+/', $dateTime, 2);

                        $dateParts = explode('/', $parts[0] ?? '');

                        if (count($dateParts) === 3) {

                            $parsedDate = \DateTime::createFromFormat(
                                '!j/n/Y',
                                $dateParts[0] . '/' .
                                    $dateParts[1] . '/' .
                                    $dateParts[2]
                            );

                            $dateErrors = \DateTime::getLastErrors();

                            if (
                                $parsedDate === false ||
                                (
                                    $dateErrors !== false &&
                                    (
                                        $dateErrors['warning_count'] > 0 ||
                                        $dateErrors['error_count'] > 0
                                    )
                                )
                            ) {
                                throw ValidationException::withMessages([
                                    'file' => 'Tanggal pada baris ' . ($index + 1) . ' tidak valid.',
                                ]);
                            }

                            $date = $parsedDate->format('Y-m-d');
                        }

                        if (!empty($parts[1])) {

                            $time = str_replace('.', ':', $parts[1]);

                            if (substr_count($time, ':') === 1) {
                                $time .= ':00';
                            }
                        }
                    }

                    // =========================
                    // STUDENT
                    // =========================
                    $nis = $row[3] ?? null;

                    $studentId = ($nis === null || $nis === '')
                        ? null
                        : Student::where('nis', $nis)->value('id');

                    // =========================
                    // DETAIL TRANSACTION
                    // =========================
                    $bankTransactions[] = [
                        'transaction_id' => $transaction->id,
                        'desc_category_id' => $validated['desc_category_id'],
                        'student_id' => $studentId,
                        'transaction_number' => $row[1] ?? null,
                        'date' => $date,
                        'time' => $time,
                        'amount' => $amount,
                        'user_id' => Auth::id(),
                    ];

                    $totalAmount += $amount;
                }

                // Tidak ada data
                if ($bankTransactions === []) {
                    throw ValidationException::withMessages([
                        'file' => 'File Excel tidak berisi data transaksi untuk diimpor.',
                    ]);
                }

                // Update total transaction utama
                $transaction->update([
                    'amount' => $totalAmount,
                ]);

                // Insert transaksi bank
                foreach ($bankTransactions as $bankTransaction) {
                    $transaction->transaction_banks()->create($bankTransaction);
                }

                return [
                    'count' => count($bankTransactions),
                    'total' => $totalAmount,
                ];
            });
        } catch (ValidationException $e) {
            throw $e;
        }

        activity()->log('Import Data Transaction');

        return back()->with(
            'success',
            $result['count'] .
                ' transaksi bank berhasil diimpor. Total pemasukan: ' .
                Helpers::format_number($result['total'])
        );
    }
}
