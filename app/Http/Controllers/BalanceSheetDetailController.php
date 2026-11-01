<?php

namespace App\Http\Controllers;

use App\Models\BalanceSheetAccount;
use App\Models\BalanceSheetDetail;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\DataTables;

class BalanceSheetDetailController extends Controller
{
    public function index(): View
    {
        $title = 'Detail Saldo Neraca';
        $balance_sheet_accounts = BalanceSheetAccount::orderBy('name')->get();
        $years = BalanceSheetDetail::query()->distinct()->orderByDesc('year')->pluck('year');

        return view('admin.balance_sheet_details.index', compact('title', 'balance_sheet_accounts', 'years'));
    }

    public function create(Request $request): View
    {
        $year = filter_var(
            $request->query('year', now()->year),
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1900, 'max_range' => 9999]]
        );

        abort_if($year === false, 422, 'Tahun harus berupa angka antara 1900 dan 9999.');

        $title = 'Input Detail Saldo Neraca';
        $balance_sheet_accounts = BalanceSheetAccount::where('is_active', true)
            ->with(['balance_sheet_details' => function ($query) use ($year) {
                $query->where('year', $year);
            }])
            ->orderBy('name')
            ->get();

        return view('admin.balance_sheet_details.input', compact('title', 'year', 'balance_sheet_accounts'));
    }

    public function get_balance_sheet_detail_index(Request $request)
    {
        if ($request->ajax()) {
            $counters = 1;
            $validated = $request->validate([
                'year' => ['nullable', 'integer', 'between:1900,9999'],
            ]);

            $balance_sheet_details = BalanceSheetDetail::with('balance_sheet_account');

            if (isset($validated['year'])) {
                $balance_sheet_details->where('year', $validated['year']);
            }

            return DataTables::of($balance_sheet_details)
                ->addIndexColumn()
                ->addColumn('number', function () use (&$counters) {
                    return $counters++;
                })
                ->addColumn('account_name', function ($v) {
                    return $v->balance_sheet_account?->name ?? '-';
                })
                ->addColumn('display_amount', function ($v) {
                    return number_format((float) $v->amount, 2, ',', '.');
                })
                ->addColumn('action', function ($v) {
                    $btn = '<a href="#" onClick="getData('.$v->id.')" id="'.$v->id.'" title="Edit" data-toggle="modal" data-target="#exampleModal">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                            </a>';
                    $btn .= '<a href="#" onclick="deleteData('.$v->id.')" id="'.$v->id.'" class="warning confirm" data-toggle="tooltip" data-placement="top" title="Hapus">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                            </a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        abort(404);
    }

    public function validate(Request $request, $action = null)
    {
        if ($request->ajax()) {
            $attributes = [
                'balance_sheet_account_id' => 'Akun Neraca',
                'year' => 'Tahun',
                'amount' => 'Jumlah',
                'notes' => 'Catatan',
            ];

            $uniqueYear = Rule::unique('balance_sheet_details', 'year')
                ->where('balance_sheet_account_id', $request->input('balance_sheet_account_id'));

            if ($action !== 'Simpan' && $request->filled('id')) {
                $uniqueYear->ignore($request->integer('id'));
            }

            $rules = [
                'balance_sheet_account_id' => 'required|integer|exists:balance_sheet_accounts,id',
                'year' => ['required', 'integer', 'min:1900', 'max:9999', $uniqueYear],
                'amount' => ['required', 'numeric', 'between:-9999999999999.99,9999999999999.99', 'decimal:0,2'],
                'notes' => 'nullable|string',
            ];

            $request->validate($rules, [], $attributes);

            return response()->json(['success' => true]);
        }
    }

    // # Save Balance Sheet Detail
    public function store(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->validate([
                'balance_sheet_account_id' => 'required|integer|exists:balance_sheet_accounts,id',
                'year' => [
                    'required',
                    'integer',
                    'min:1900',
                    'max:9999',
                    Rule::unique('balance_sheet_details', 'year')
                        ->where('balance_sheet_account_id', $request->input('balance_sheet_account_id')),
                ],
                'amount' => ['required', 'numeric', 'between:-9999999999999.99,9999999999999.99', 'decimal:0,2'],
                'notes' => 'nullable|string',
            ], [], [
                'balance_sheet_account_id' => 'Akun Neraca',
                'year' => 'Tahun',
                'amount' => 'Jumlah',
                'notes' => 'Catatan',
            ]);

            $balance_sheet_detail = new BalanceSheetDetail;
            $balance_sheet_detail->balance_sheet_account_id = $data['balance_sheet_account_id'];
            $balance_sheet_detail->year = $data['year'];
            $balance_sheet_detail->amount = $data['amount'];
            $balance_sheet_detail->notes = $data['notes'] ?? null;
            $balance_sheet_detail->save();
            activity()->log('Create Balance Sheet Detail Data');

            return response()->json(['success' => true, 'message' => 'Tambah Data Berhasil']);
        }
    }

    public function storeAll(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'year' => ['required', 'integer', 'min:1900', 'max:9999'],
            'amounts' => ['required', 'array', 'min:1'],
            'amounts.*' => ['required', 'numeric', 'between:-9999999999999.99,9999999999999.99', 'decimal:0,2'],
        ], [], [
            'year' => 'Tahun',
            'amounts' => 'Nominal saldo',
            'amounts.*' => 'Nominal saldo',
        ]);

        DB::transaction(function () use ($data) {
            $activeAccountIds = BalanceSheetAccount::where('is_active', true)
                ->orderBy('id')
                ->lockForUpdate()
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();
            $submittedAccountIds = array_map('intval', array_keys($data['amounts']));
            sort($submittedAccountIds);

            if ($activeAccountIds === []) {
                throw ValidationException::withMessages([
                    'amounts' => 'Belum ada akun neraca aktif untuk diinput.',
                ]);
            }

            if ($submittedAccountIds !== $activeAccountIds) {
                throw ValidationException::withMessages([
                    'amounts' => 'Daftar saldo harus memuat seluruh akun neraca yang aktif.',
                ]);
            }

            foreach ($data['amounts'] as $accountId => $amount) {
                BalanceSheetDetail::updateOrCreate(
                    [
                        'balance_sheet_account_id' => (int) $accountId,
                        'year' => $data['year'],
                    ],
                    ['amount' => $amount]
                );
            }
        });

        activity()->log('Create or Update Balance Sheet Details for Year = '.$data['year']);

        return redirect()
            ->route('balance_sheet_details.index')
            ->with('success', 'Data detail saldo neraca berhasil disimpan.');
    }

    // # Get Balance Sheet Detail
    public function edit(Request $request, BalanceSheetDetail $balance_sheet_detail)
    {
        if ($request->ajax()) {
            return response()->json(['success' => true, 'data' => $balance_sheet_detail]);
        }
    }

    // # Edit Balance Sheet Detail
    public function update(Request $request, BalanceSheetDetail $balance_sheet_detail)
    {
        if ($request->ajax()) {
            $data = $request->validate([
                'balance_sheet_account_id' => 'required|integer|exists:balance_sheet_accounts,id',
                'year' => [
                    'required',
                    'integer',
                    'min:1900',
                    'max:9999',
                    Rule::unique('balance_sheet_details', 'year')
                        ->where('balance_sheet_account_id', $request->input('balance_sheet_account_id'))
                        ->ignore($balance_sheet_detail->id),
                ],
                'amount' => ['required', 'numeric', 'between:-9999999999999.99,9999999999999.99', 'decimal:0,2'],
                'notes' => 'nullable|string',
            ], [], [
                'balance_sheet_account_id' => 'Akun Neraca',
                'year' => 'Tahun',
                'amount' => 'Jumlah',
                'notes' => 'Catatan',
            ]);

            $balance_sheet_detail->balance_sheet_account_id = $data['balance_sheet_account_id'];
            $balance_sheet_detail->year = $data['year'];
            $balance_sheet_detail->amount = $data['amount'];
            $balance_sheet_detail->notes = $data['notes'] ?? null;
            $balance_sheet_detail->save();

            activity()->log('Edit Balance Sheet Detail Data With ID = '.$balance_sheet_detail->id);

            return response()->json(['success' => true, 'message' => 'Ubah Data Berhasil']);
        }
    }

    // # Delete Balance Sheet Detail
    public function delete(Request $request, BalanceSheetDetail $balance_sheet_detail)
    {
        if ($request->ajax()) {
            $balance_sheet_detail->delete();
            activity()->log('Delete Balance Sheet Detail Data With ID = '.$balance_sheet_detail->id);

            return response()->json(['success' => true, 'message' => 'Hapus Data Berhasil']);
        }
    }
}
