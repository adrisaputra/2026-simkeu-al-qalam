<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Employee;
use App\Models\WorkUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\Facades\DataTables;

class WorkUnitController extends Controller
{
    ## Show Data
    public function index()
    {
        $title = "Unit Kerja";
        return view('admin.work_unit.index',compact('title'));
    }

    ## Get Data
    public function get_work_unit_index(Request $request)
    {

        if ($request->ajax()) {
            $counter = 1;

            $work_unit = WorkUnit::whereIn('id', [3, 4, 5, 6])->limit(10);

            return DataTables::of($work_unit)
            ->addIndexColumn()
            ->addColumn('number', function () use (&$counter) {
                return $counter++;
            })
            ->addColumn('action', function ($v) {
                $url = url('classes', Crypt::encrypt($v->id));
                
                $btn = '<a href="' . $url . '" class="btn btn-info btn-sm position-relative me-5" data-toggle="tooltip" data-placement="top" title="Data">
                            <span>Lihat Kelas</span>';

                    if ($v->classess->count() > 0) {
                        $btn .= '<span class="badge badge-danger counter">'.$v->classess->count().'</span>';

                    }

                    $btn .= '</a>';
                    
                // $btn = '<a href="#" onClick="getData('.$v->id.')" id="'.$v->id.'" class="btn btn-sm mb-2 mr-1 btn-info" title="Edit">Lihat Kelas</a>';
                return $btn;
            })
            ->rawColumns(['image','action'])->make(true);
        }
        
    }


}
