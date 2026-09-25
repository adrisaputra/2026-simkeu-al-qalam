<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    ## Show Data
    public function index(Request $request)
    {
        $title = "Dashboard";
        if(Auth::user()->group->name == 'Admin Simkeu'){
            $employee = Employee::count();
            $employee_l = Employee::where('gender','Male')->count();
            $employee_p = Employee::where('gender','Female')->count();
            return view('admin.home', compact('title','employee','employee_l','employee_p'));
        } 
    }
}