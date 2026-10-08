<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Models\DescCategory;
use App\Models\Transaction;
use App\Models\TransactionCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

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
                'date' => 'Tanggal'
            ];

            if ($action === "Simpan") {
                $rules = [
                    'date' => 'required|date'
                ];
            } else {
                $rules = [
                    'date' => 'required|date'
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
            $income->amount = str_replace('.', '', $request->amount);
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
            $income->amount = str_replace('.', '', $request->amount);
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
