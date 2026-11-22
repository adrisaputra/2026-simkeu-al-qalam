<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Models\AcademicYear;
use App\Models\Account;
use App\Models\Income;
use App\Models\Student;
use App\Models\Transaction;
use App\Models\TransactionBank;
use App\Models\TransactionCode;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as SpreadsheetReaderException;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Yajra\DataTables\DataTables;

class TransactionBankController extends Controller
{
    // # Show Data
    public function index(Request $request): View
    {
        $title = 'Pemasukan Bank Siswa';
        
        $account = Account::get();
        $transaction_code = TransactionCode::where('account_id',1)->get();

        $filters = $request->validate([
            'academic_year_id' => ['nullable', 'integer', 'exists:academic_years,id'],
            'status' => ['nullable', Rule::in(['unmatched', 'matched', 'partial'])],
        ]);

        $academicYears = AcademicYear::query()->orderByDesc('start_date')->get();
        $activeYear = $academicYears->firstWhere('is_active', true);
        if (! array_key_exists('academic_year_id', $filters)) {
            $filters['academic_year_id'] = $activeYear?->id;
        }

        $selectedYear = $academicYears->firstWhere('id', $filters['academic_year_id']);
        $bankTransactionDetails = static fn (Builder $query): Builder => $query->where('account_id', 1);
        $transactionsQuery = Transaction::query()
            ->whereHas('transaction_banks', $bankTransactionDetails)
            ->when(
                $selectedYear,
                fn (Builder $query, AcademicYear $academicYear): Builder => $query->whereBetween(
                    'date',
                    [$academicYear->start_date, $academicYear->end_date]
                )
            )
            ->with(['transaction_banks' => static function (Relation $relation): void {
                $relation->where('account_id', 1);
            }])
            ->orderByDesc('id');

        $transaction_bank_value = (clone $transactionsQuery)->get();
        $filteredTransactionsQuery = clone $transactionsQuery;

        $unmatchedDetails = static fn (Builder $query): Builder => $query
            ->where('account_id', 1)
            ->whereNull('student_id');
        $matchedDetails = static fn (Builder $query): Builder => $query
            ->where('account_id', 1)
            ->whereNotNull('student_id');

        if (($filters['status'] ?? null) === 'unmatched') {
            $filteredTransactionsQuery
                ->whereHas('transaction_banks', $unmatchedDetails)
                ->whereDoesntHave('transaction_banks', $matchedDetails);
        } elseif (($filters['status'] ?? null) === 'matched') {
            $filteredTransactionsQuery
                ->whereHas('transaction_banks', $matchedDetails)
                ->whereDoesntHave('transaction_banks', $unmatchedDetails);
        } elseif (($filters['status'] ?? null) === 'partial') {
            $filteredTransactionsQuery
                ->whereHas('transaction_banks', $matchedDetails)
                ->whereHas('transaction_banks', $unmatchedDetails);
        }

        $transaction_bank = $filteredTransactionsQuery->paginate(10)->withQueryString();

        return view('admin.transaction_bank.index', compact(
            'title',
            'transaction_bank',
            'transaction_bank_value',
            'academicYears',
            'filters',
            'selectedYear',
            'account',
            'transaction_code'
        ));
    }

    public function show(Request $request, Transaction $transaction): View
    {
        $title = 'Detail Pemasukan Bank Siswa';
        
        $filters = $request->validate([
            'status' => ['nullable', Rule::in(['unmatched', 'matched'])],
        ]);

        $student = Student::limit(10)->get();
        $query = TransactionBank::query();

        if (($filters['status'] ?? null) === 'unmatched') {
            $query->whereNull('student_id');
        } elseif (($filters['status'] ?? null) === 'matched') {
            $query->whereNotNull('student_id');
        } 

        $transaction_bank = $query->where('transaction_id', $transaction->id)->paginate(10)->withQueryString();

        return view('admin.transaction_bank.show', compact(
            'title',
            'filters',
            'transaction',
            'transaction_bank',
            'student'
        ));
    }
    public function validate(Request $request, $action = null)
    {
        if ($request->ajax()) {

            $attributes = [
                'student_id' => 'Siswa',
            ];

            if ($action === 'Simpan') {
                $rules = [
                    'student_id' => 'required|max:255',
                ];
            } else {
                $rules = [
                    'student_id' => 'required|max:255',
                ];
            }

            $request->validate($rules, [], $attributes);

            return response()->json(['success' => true]);
        }
    }

    // # Get Data
    public function edit(Request $request, TransactionBank $transaction_bank)
    {
        if ($request->ajax()) {
            $student = Student::where('name','like', '%' . $transaction_bank->student_name . '%')->get();
            return response()->json(['success' => true, 'data' => $transaction_bank, 'student' => $student]);
        }
    }

    // # Edit Student
    public function update(Request $request, TransactionBank $transaction_bank)
    {
        if ($request->ajax()) {

            $transaction_bank->student_id = $request->student_id;
            $transaction_bank->status = 'matched';
            $transaction_bank->save();

            activity()->log('Edit Transaction Bank Data With ID = '.$transaction_bank->id);

            return response()->json(['success' => true, 'message' => 'Ubah Data Berhasil']);
        }
    }

    // # Delete Student
    public function delete(Request $request, TransactionBank $transaction_bank)
    {
        if ($request->ajax()) {
            $transaction_bank->delete();
            activity()->log('Delete Transaction Bank Data With ID = '.$transaction_bank->id);

            return response()->json(['success' => true, 'message' => 'Hapus Data Berhasil']);
        }
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
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
            $result = DB::transaction(function () use ($rows, $validated, $request) {

                $bankTransactions = [];
                $totalAmount = 0;

                // Buat transaction utama terlebih dahulu
                $transaction = Transaction::create([
                    'date' => now()->toDateString(),
                    'type' => 'income',
                    'account_id' => 1,
                    'transaction_code_id' => $request->transaction_code_id,
                    'transaction_code_sub_id' => $request->transaction_code_sub_id,
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
                    $amount = str_replace('.', '', $row[6] ?? 0);
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

                    $studentId = null;

                    if ($nis !== null && trim((string) $nis) !== '') {
                        $nis = trim((string) $nis);

                        $studentId = Student::where(function ($query) use ($nis) {
                            $query->where('nis', $nis)
                                ->orWhere('nisn', $nis)
                                ->orWhere('nipd', $nis);
                        })->value('id');
                    }

                    // Status pencocokan siswa
                    $matchStatus = $studentId ? 'matched' : 'unmatched';

                    // =========================
                    // DETAIL TRANSACTION
                    // =========================
                    $bankTransactions[] = [
                        'transaction_id' => $transaction->id,
                        'account_id' => 1,
                        'transaction_code_id' => $request->transaction_code_id,
                        'transaction_code_sub_id' => $request->transaction_code_sub_id,
                        'student_id' => $studentId,
                        'transaction_number' => $row[1] ?? null,
                        'student_name' => $row[4] ?? null,
                        'classes' => $row[5] ?? null,
                        'date' => $date,
                        'time' => $time,
                        'import_date' => now()->toDateString(),
                        'amount' => $amount,
                        'user_id' => Auth::id(),
                        'status' => $matchStatus
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
