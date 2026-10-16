<?php

namespace App\Http\Controllers;

use App\Models\TransactionCode;
use App\Models\TransactionCodeSub;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class TransactionCodeSubController extends Controller
{
    public function index(string $transaction_code): View
    {
        $transactionCode = $this->findTransactionCode($transaction_code);
        $title = 'Sub Kode Transaksi';

        return view('admin.transaction_code_sub.index', compact('title', 'transactionCode'));
    }

    public function get_transaction_code_sub_index(Request $request, string $transaction_code): JsonResponse
    {
        abort_unless($request->ajax(), 404);

        $parent = $this->findTransactionCode($transaction_code);
        $counter = 1;

        return DataTables::of($parent->transaction_code_subs())
            ->addIndexColumn()
            ->addColumn('number', function () use (&$counter) {
                return $counter++;
            })
            ->addColumn('action', function (TransactionCodeSub $transactionCodeSub) {
                $id = $transactionCodeSub->id;

                return '<a href="#" onclick="getData('.$id.')" title="Edit" data-toggle="modal" data-target="#exampleModal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                        </a>
                        <a href="#" onclick="deleteData('.$id.')" class="warning confirm" data-toggle="tooltip" data-placement="top" title="Hapus">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                        </a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function validate(Request $request, string $transaction_code, string $action): JsonResponse
    {
        abort_unless($request->ajax(), 404);
        $this->findTransactionCode($transaction_code);

        $request->validate([
            'code' => ['required', 'string', 'max:20'],
            'name' => ['required', 'string', 'max:255'],
        ], [], [
            'code' => 'Kode Sub Transaksi',
            'name' => 'Nama Sub Kode Transaksi',
        ]);

        return response()->json(['success' => true]);
    }

    public function store(Request $request, string $transaction_code): JsonResponse
    {
        abort_unless($request->ajax(), 404);

        $parent = $this->findTransactionCode($transaction_code);
        $data = $request->validate([
            'code' => ['required', 'string', 'max:20'],
            'name' => ['required', 'string', 'max:255'],
        ], [], [
            'code' => 'Kode Sub Transaksi',
            'name' => 'Nama Sub Kode Transaksi',
        ]);

        $parent->transaction_code_subs()->create($data);

        activity()->log('Create Transaction Code Sub Data');

        return response()->json(['success' => true, 'message' => 'Tambah Data Berhasil']);
    }

    public function edit(Request $request, string $transaction_code, TransactionCodeSub $transaction_code_sub): JsonResponse
    {
        abort_unless($request->ajax(), 404);

        $parent = $this->findTransactionCode($transaction_code);
        $transactionCodeSub = $parent->transaction_code_subs()->findOrFail($transaction_code_sub->id);

        return response()->json(['success' => true, 'data' => $transactionCodeSub]);
    }

    public function update(Request $request, string $transaction_code, TransactionCodeSub $transaction_code_sub): JsonResponse
    {
        abort_unless($request->ajax(), 404);

        $parent = $this->findTransactionCode($transaction_code);
        $transactionCodeSub = $parent->transaction_code_subs()->findOrFail($transaction_code_sub->id);
        $data = $request->validate([
            'code' => ['required', 'string', 'max:20'],
            'name' => ['required', 'string', 'max:255'],
        ], [], [
            'code' => 'Kode Sub Transaksi',
            'name' => 'Nama Sub Kode Transaksi',
        ]);

        $transactionCodeSub->update($data);

        activity()->log('Edit Transaction Code Sub Data With ID = '.$transactionCodeSub->id);

        return response()->json(['success' => true, 'message' => 'Ubah Data Berhasil']);
    }

    public function delete(Request $request, string $transaction_code, TransactionCodeSub $transaction_code_sub): JsonResponse
    {
        abort_unless($request->ajax(), 404);

        $parent = $this->findTransactionCode($transaction_code);
        $transactionCodeSub = $parent->transaction_code_subs()->findOrFail($transaction_code_sub->id);
        $transactionCodeSub->delete();

        activity()->log('Delete Transaction Code Sub Data With ID = '.$transactionCodeSub->id);

        return response()->json(['success' => true, 'message' => 'Hapus Data Berhasil']);
    }

    private function findTransactionCode(string $encryptedId): TransactionCode
    {
        return TransactionCode::findOrFail(Crypt::decrypt($encryptedId));
    }
}
