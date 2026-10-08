<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\WorkUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\DataTables;

class ClassesController extends Controller
{
    public function index($work_unit = null)
    {
        $title = "Kelas";
        $work_unit = Crypt::decrypt($work_unit);
        $work_unit = WorkUnit::where('id', $work_unit)->first();
        return view('admin.classes.index', compact('title', 'work_unit'));
    }

    public function get_classes_index(Request $request, $work_unit = null)
    {
        if ($request->ajax()) {
            $counters = 1;

            $work_unit = Crypt::decrypt($work_unit);
            $work_unit = WorkUnit::where('id', $work_unit)->first();

            $classes = Classes::where('work_unit_id', $work_unit->id)->limit(10);

            return DataTables::of($classes)
            ->addIndexColumn()
            ->addColumn('number', function () use (&$counters) {
                return $counters++;
            })
            
            ->addColumn('student', function ($v) {
                $url = url('student', Crypt::encrypt($v->id));
                $btn = '<a href="' . $url . '" class="btn btn-info btn-sm position-relative me-5" data-toggle="tooltip" data-placement="top" title="Data">
                            <span>Lihat Siswa</span>';

                    if ($v->students->count() > 0) {
                        $btn .= '<span class="badge badge-danger counter">'.$v->students->count().'</span>';

                    }

                    $btn .= '</a>';
                    
                return $btn;
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
            ->rawColumns(['student','action'])
            ->make(true);
        }
    }

    public function validate(Request $request, $action = null)
    {
        if ($request->ajax()) {

            $attributes = [
                'name' => 'Nama Kelas'
            ];

            if ($action === "Simpan") {
                $rules = [
                    'name' => 'required|max:255'
                ];
            } else {
                $rules = [
                    'name' => 'required|max:255'
                ];
            }

            $request->validate($rules, [], $attributes);

            return response()->json(['success' => true]);
        }
    }

    ## Save Classes 
    public function store(Request $request)
    {
        if ($request->ajax()) {

            $classes = new Classes();
            $classes->work_unit_id = $request->work_unit_id;
            $classes->name = $request->name;
            $classes->save();
            activity()->log('Create Classes Data');
            return response()->json(['success' => true, 'message' => 'Tambah Data Berhasil']);
        }
    }

    ## Get Classes
    public function edit(Request $request, Classes $classes)
    {
        if ($request->ajax()) {
            return response()->json(['success' => true, 'data' => $classes]);
        }
    }

    ## Edit Classes
    public function update(Request $request, Classes $classes)
    {
        if ($request->ajax()) {

            $classes->work_unit_id = $request->work_unit_id;
            $classes->name = $request->name;
            $classes->save();

            activity()->log('Edit Classes Data With ID = ' . $classes->id);
            return response()->json(['success' => true, 'message' => 'Ubah Data Berhasil']);
        }
    }

    ## Delete Classes
    public function delete(Request $request, Classes $classes)
    {
        if ($request->ajax()) {
            $classes->delete();
            activity()->log('Delete Classes Data With ID = ' . $classes->id);
            return response()->json(['success' => true, 'message' => 'Hapus Data Berhasil']);
        }
    }

}
