<?php

namespace App\Http\Controllers;

use App\Models\BalanceSheetAccount;
use App\Models\BalanceSheetDetail;
use App\Models\TransactionCode;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class BalanceSheetAccountController extends Controller
{
    public function index()
    {
        $title = 'Akun Neraca';
        $transaction_code = TransactionCode::where('account_id', 3)->orderBy('code')->get();
        $years = BalanceSheetDetail::query()->distinct()->orderByDesc('year')->pluck('year');

        return view('admin.balance_sheet_account.index', compact('title', 'transaction_code', 'years'));
    }

    public function get_balance_sheet_account_index(Request $request)
    {
        if ($request->ajax()) {
            $counters = 1;
            $categories = [
                'asset' => 'Aset',
                'liability' => 'Liabilitas',
                'net_asset' => 'Aset Neto',
            ];

            $accounts = BalanceSheetAccount::with(['transaction_code', 'transaction_code_sub']);

            return DataTables::of($accounts)
                ->addIndexColumn()
                ->addColumn('number', function () use (&$counters) {
                    return $counters++;
                })
                ->addColumn('transaction_code', function ($v) {
                    return $v->transaction_code
                        ? $v->transaction_code->code.' - '.$v->transaction_code->name
                        : '-';
                })
                ->addColumn('transaction_code_sub', function ($v) {
                    return $v->transaction_code_sub
                        ? $v->transaction_code_sub->code.' - '.$v->transaction_code_sub->name
                        : '-';
                })
                ->addColumn('display_category', function ($v) use ($categories) {
                    return $categories[$v->category] ?? $v->category;
                })
                ->addColumn('display_is_active', function ($v) {
                    return $v->is_active
                        ? '<span class="badge badge-success">Aktif</span>'
                        : '<span class="badge badge-secondary">Tidak Aktif</span>';
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
                ->rawColumns(['display_is_active', 'action'])
                ->make(true);
        }
    }

    public function validate(Request $request, $action = null)
    {
        if ($request->ajax()) {
            $attributes = [
                'transaction_code_id' => 'Kode Transaksi',
                'name' => 'Nama Akun Neraca',
                'category' => 'Kategori',
                'is_active' => 'Status',
            ];

            $rules = [
                'transaction_code_id' => ['required', 'integer', Rule::exists('transaction_codes', 'id')],
                'name' => 'required|max:255',
                'category' => ['required', Rule::in(['asset', 'liability', 'net_asset'])],
                'is_active' => 'required|boolean',
            ];

            $request->validate($rules, [], $attributes);

            return response()->json(['success' => true]);
        }
    }

    // # Save Balance Sheet Account
    public function store(Request $request)
    {
        if ($request->ajax()) {

            $balance_sheet_account = new BalanceSheetAccount;
            $balance_sheet_account->transaction_code_id = $request->transaction_code_id;
            $balance_sheet_account->name = $request->name;
            $balance_sheet_account->category = $request->category;
            $balance_sheet_account->is_active = $request->is_active;
            $balance_sheet_account->save();

            activity()->log('Create Balance Sheet Account Data');

            return response()->json(['success' => true, 'message' => 'Tambah Data Berhasil']);
        }
    }

    // # Get Balance Sheet Account
    public function edit(Request $request, BalanceSheetAccount $balance_sheet_account)
    {
        if ($request->ajax()) {
            return response()->json(['success' => true, 'data' => $balance_sheet_account]);
        }
    }

    // # Edit Balance Sheet Account
    public function update(Request $request, BalanceSheetAccount $balance_sheet_account)
    {
        if ($request->ajax()) {

            $balance_sheet_account->transaction_code_id = $request->transaction_code_id;
            $balance_sheet_account->name = $request->name;
            $balance_sheet_account->category = $request->category;
            $balance_sheet_account->is_active = $request->is_active;
            $balance_sheet_account->save();

            activity()->log('Edit Balance Sheet Account Data With ID = '.$balance_sheet_account->id);

            return response()->json(['success' => true, 'message' => 'Ubah Data Berhasil']);
        }
    }

    // # Delete Balance Sheet Account
    public function delete(Request $request, BalanceSheetAccount $balance_sheet_account)
    {
        if ($request->ajax()) {
            $balance_sheet_account->delete();
            activity()->log('Delete Balance Sheet Account Data With ID = '.$balance_sheet_account->id);

            return response()->json(['success' => true, 'message' => 'Hapus Data Berhasil']);
        }
    }
}
