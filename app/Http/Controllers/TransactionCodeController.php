<?php

namespace App\Http\Controllers;

use App\Models\TransactionCode;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class TransactionCodeController extends Controller
{
    ## Show Data
    public function index()
    {
        $title = "Kode Transaksi";
        return view('admin.transaction_code.index',compact('title'));
    }

    ## Get Data
    public function get_transaction_code_index(Request $request)
    {

        if ($request->ajax()) {
            $counter = 1;

            $transaction_code = TransactionCode::limit(10);

            return DataTables::of($transaction_code)
            ->addIndexColumn()
            ->addColumn('number', function () use (&$counter) {
                return $counter++;
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
            ->rawColumns(['kpi','action'])->make(true);
        }
        
    }

    public function validate(Request $request, $action)
    {

        if ($request->ajax()) {

            $attributes = [
                'code' => 'Kode Transaksi',
                'name' => 'Nama Transaksi'
            ];

            if($action==="Simpan"){
                $rules = [
                    'code' => 'required|numeric',
                    'name' => 'required|max:255'
                ];
            } else {
                $rules = [
                    'code' => 'required|numeric',
                    'name' => 'required|max:255'
                ];
            }
            
            $request->validate($rules, [],$attributes);
            
            return response()->json(['success' => true]);
        }
    }

    ## Save Data
    public function store(Request $request)
    {
        if ($request->ajax()) {
            $transaction_code = New TransactionCode();
            $transaction_code->code = $request->code;
            $transaction_code->name = $request->name;
            $transaction_code->type = $request->type;
            $transaction_code->description = $request->description;
            $transaction_code->save();
            
            activity()->log('Create Data Transaction Code');
            return response()->json(['success' => true,'message' => 'Tambah Data Berhasil']);
        }
    }

    ## Get Data
    public function edit(Request $request, TransactionCode $transaction_code)
    {
        if ($request->ajax()) {
            return response()->json(['success' => true,'data' => $transaction_code]);
        }
    }

    ## Edit Data
    public function update(Request $request, TransactionCode $transaction_code)
    {
        if ($request->ajax()) {
            $transaction_code->code = $request->code;
            $transaction_code->name = $request->name;
            $transaction_code->type = $request->type;
            $transaction_code->description = $request->description;
            $transaction_code->save();
    
            activity()->log('Edit Data Transaction Code With ID = '.$transaction_code->id);
            return response()->json(['success' => true,'message' => 'Ubah Data Berhasil']);
        }
    }

    ## Delete Data
    public function delete(Request $request, TransactionCode $transaction_code)
    {
        if ($request->ajax()) {
            $transaction_code->delete();
            activity()->log('Delete Data Transaction Code With ID = '.$transaction_code->id);
            return response()->json(['success' => true,'message' => 'Hapus Data Berhasil']);
        }
    }

}
