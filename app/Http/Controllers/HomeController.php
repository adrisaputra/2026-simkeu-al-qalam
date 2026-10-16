<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    ## Show Data
    public function index(Request $request)
    {
        $title = "Dashboard";
        if(in_array(Auth::user()->group->name,['Admin Finance','Revenue Treasurer','Expenditure Treasurer'])){
            $income = Transaction::whereMonth('date', now()->month)->where('type', 'income')->sum('amount');
            $expense = Transaction::whereMonth('date', now()->month)->where('type', 'expense')->sum('amount');
            return view('admin.home', compact('title','income','expense'));
        } 
    }
}