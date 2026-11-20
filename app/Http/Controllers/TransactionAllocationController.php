<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Models\Transaction;
use App\Models\TransactionAllocation;
use App\Models\TransactionCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\DataTables;

class TransactionAllocationController extends Controller
{
    ## Show Data
    public function index()
    {
        $title = "Alokasi Dana";
        $year = date('Y');

        $income = Transaction::whereYear('date', $year)->where('type', 'income')->sum('amount');
        $used = TransactionAllocation::whereYear('date', $year)->sum('amount');
        $remaining = $income - $used; 
        $percentage = $income > 0 ? ($used / $income) * 100 : 0;      

        $income = Helpers::format_number($income);
        $used = Helpers::format_number($used);
        $remaining = Helpers::format_number($remaining);
        $percentage = number_format($percentage, 2) . '%';
        return view('admin.transaction_allocation.index',compact('title','year','income','used','remaining','percentage'));
    }

    ## Get Data
    public function get_transaction_allocation_index(Request $request)
    {

        if ($request->ajax()) {
            $counter = 1;

            $year = $request->input('year') ?? date('Y');
            $transaction_code = TransactionCode::where('account_id',1)->get();

            return DataTables::of($transaction_code)
            ->addIndexColumn()
            ->addColumn('number', function () use (&$counter) {
                return $counter++;
            })
            ->addColumn('received', function ($v) use ($year){
                $income = Transaction::whereYear('date', $year)->where('type', 'income')->where('transaction_code_id', $v->id)->sum('amount');
                return Helpers::format_number($income);
            })
            ->addColumn('used', function ($v) use ($year){
                $used = TransactionAllocation::whereYear('date', $year)->where('transaction_code_id', $v->id)->sum('amount');
                return Helpers::format_number($used);
            })
            ->addColumn('remaining', function ($v) use ($year){
                $income = Transaction::whereYear('date', $year)->where('type', 'income')->where('transaction_code_id', $v->id)->sum('amount');
                $used = TransactionAllocation::whereYear('date', $year)->where('transaction_code_id', $v->id)->sum('amount');
                return Helpers::format_number($income - $used);
            })
            ->addColumn('action', function ($v) {
                $url = url('transaction_allocation/detail', Crypt::encrypt($v->id));
                $btn = '<a href="' . $url . '" class="btn btn-info btn-sm position-relative me-5" data-toggle="tooltip" data-placement="top" title="Detail">
                            <span>Detail</span></a>';
                return $btn;
            })
            ->rawColumns(['image','action'])->make(true);
        }
        
    }

    public function detail($transaction_code = null)
    {
        $title = 'Detail';
        $transaction_code = Crypt::decrypt($transaction_code);
        $transaction_code = TransactionCode::where('id', $transaction_code)->first();

        $year = date('Y');
        $income = Transaction::where('transaction_code_id', $transaction_code->id)->whereYear('date', $year)->where('type', 'income')->sum('amount');
        $used = TransactionAllocation::where('transaction_code_id', $transaction_code->id)->whereYear('date', $year)->sum('amount');
        $remaining = $income - $used;   
        $percentage = $income > 0 ? ($used / $income) * 100 : 0;      

        $income = Helpers::format_number($income);
        $used = Helpers::format_number($used);
        $remaining = Helpers::format_number($remaining);
        $percentage = number_format($percentage, 2) . '%';

        return view('admin.transaction_allocation.detail',compact('title','transaction_code','income','used','remaining','percentage'));
    }

    
    ## Get Data
    public function get_transaction_allocation_detail(Request $request, $transaction_code = null)
    {

        if ($request->ajax()) {
            $counter = 1;

            $transaction_code = Crypt::decrypt($transaction_code);
            $transaction_code = TransactionCode::where('id', $transaction_code)->first();

            $transaction_allocation = TransactionAllocation::where('transaction_code_id',$transaction_code->id)->limit(10);

            return DataTables::of($transaction_allocation)
            ->addIndexColumn()
            ->addColumn('number', function () use (&$counter) {
                return $counter++;
            })
                ->addColumn('display_date', function ($v) {
                    return Helpers::date($v->date);
                })
                ->addColumn('display_desc', function ($v) {
                    return $v->desc;
                })
                ->addColumn('display_amount', function ($v) {
                    return Helpers::format_number($v->amount);
                })
            ->rawColumns([''])->make(true);
        }
        
    }

}
