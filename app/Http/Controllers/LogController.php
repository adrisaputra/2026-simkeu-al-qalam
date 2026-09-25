<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class LogController extends Controller
{
    ## Show Data
    public function index()
    {
        $title = "Log";
		return view('admin.log.index',compact('title'));
    }

    ## Get Data
    public function get_log_index(Request $request)
    {

        if ($request->ajax()) {
            $counter = 1;

            $log = Log::limit(10);

            return DataTables::of($log)
            ->addIndexColumn()
            ->addColumn('number', function () use (&$counter) {
                return $counter++;
            })
            ->addColumn('name', function ($v) {
                return $v->user ? $v->user->name : '';
            })
            ->addColumn('execution_time', function ($v) {
                return \Carbon\Carbon::parse($v->created_at)->diffForHumans();
            })
            ->addColumn('action', function ($v) {
                $btn = '<a href="#" onClick="getData('.$v->id.')" id="'.$v->id.'" title="Edit" data-toggle="modal" data-target="#exampleModal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-list text-info"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                        </a>';
                return $btn;
            })
            ->rawColumns(['action'])->make(true);
        }
        
    }
    
    ## Get Data
    public function detail(Request $request,$id)
    {
        if ($request->ajax()) {
            $log = Log::where('id',$id)->first();
            return response()->json(['success' => true,'user' =>$log->user->name, 'data' => $log]);
        }
    }

}
