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
        if(Auth::user()->group->name == 'Admin Finance'){
            $income = Transaction::where('type', 'income')->sum('amount');
            $expense = Transaction::where('type', 'expense')->sum('amount');
            return view('admin.home', compact('title','income','expense'));
        } 
    }
}