<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Models\AcademicYear;
use App\Models\StudentBillAcademicYear;
use App\Models\StudentBillType;
use App\Models\TransactionCode;
use App\Models\WorkUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\DataTables;

class StudentBillTypeController extends Controller
{
    ## Show Data
    public function index()
    {
        $title = "Kategori Tagihan Siswa";
        $work_unit = WorkUnit::whereIn('id', [3, 4, 5, 6])->get();
        $transaction_code = TransactionCode::where('account_id',1)->get();
        return view('admin.student_bill_type.index',compact('title','work_unit','transaction_code'));
    }

    ## Get Data
    public function get_student_bill_type_index(Request $request)
    {

        if ($request->ajax()) {
            $counter = 1;

            $query = StudentBillType::query();

            // if ($request->filled('get_work_unit')) {
                $get_work_unit = $request->input('get_work_unit');
                $query->where('work_unit_id', $get_work_unit);
            // }

            $student_bill_type = $query->limit(10);

            return DataTables::of($student_bill_type)
            ->addIndexColumn()
            ->addColumn('number', function () use (&$counter) {
                return $counter++;
            })
            ->addColumn('display_work_unit', function ($v){
                return $v->work_unit->name;
            })
            ->addColumn('display_transaction_code', function ($v) {
                return $v->transaction_code->name;
            })
            ->addColumn('display_frequency', function ($v) {
                return match ($v->frequency) {
                    'weekly'   => 'Pekanan',
                    'monthly'  => 'Bulanan',
                    'semester' => 'Semesteran',
                    'yearly'   => 'Tahunan',
                    default    => 'Tidak diketahui',
                };
            })
            ->addColumn('display_default_amount', function ($v) {
                return Helpers::format_number($v->default_amount);
            })
            ->addColumn('student_bill_academic_year', function ($v) {
                $academic_year = AcademicYear::where('is_active',true)->first();
                $url = url('student_bill/store', Crypt::encrypt($v->id));

                $student_bill_academic_year = StudentBillAcademicYear::
                                                where('academic_year_id', $academic_year->id)
                                                ->where('student_bill_type_id', $v->id)
                                                ->first();
                if($student_bill_academic_year){
                    return 'Tagihan Periode '.$academic_year->name.' Sudah Diterbitkan';
                } else {
                    $btn = '<a href="#" onClick="StudentBillStore('.$v->id.')" class="btn btn-info btn-sm position-relative me-5" data-toggle="tooltip" data-placement="top" title="Data">
                                <span>Terbitkan Tagihan Periode '.$academic_year->name.'</span></a>';
                    return $btn;
                }
            })
            ->addColumn('action', function ($v) {
                $btn = '<a href="#" onClick="getData('.$v->id.')" id="'.$v->id.'" title="Edit" data-toggle="modal" data-target="#exampleModal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                        </a>';
                $btn = '<a href="#" onClick="getData('.$v->id.')" id="'.$v->id.'" title="Edit" data-toggle="modal" data-target="#exampleModal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                        </a>';
                $btn .= '<a href="#" onclick="deleteData('.$v->id.')" id="'.$v->id.'" class="warning confirm" data-toggle="tooltip" data-placement="top" title="Hapus">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                        </a>';
                return $btn;
            })
            ->rawColumns(['student_bill_academic_year','action'])->make(true);
        }
        
    }

    public function validate(Request $request, $action)
    {

        if ($request->ajax()) {

            $attributes = [
                'work_unit_id' => 'Jenjang',
                'transaction_code_id' => 'Kode Transaksi',
                'frequency' => 'Frekuensi Tagihan',
            ];

            if($action==="Simpan"){
                $rules = [
                    'work_unit_id' => 'required',
                    'transaction_code_id' => 'required',
                    'frequency' => 'required'
                ];
            } else {
                $rules = [
                    'work_unit_id' => 'required',
                    'transaction_code_id' => 'required',
                    'frequency' => 'required'
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
            $student_bill_type = New StudentBillType();
            $student_bill_type->work_unit_id = $request->work_unit_id;
            $student_bill_type->transaction_code_id = $request->transaction_code_id;
            $student_bill_type->frequency = $request->frequency;
            $student_bill_type->default_amount = str_replace('.', '', $request->default_amount) ?? 0;
            $student_bill_type->save();
            
            activity()->log('Create Data Student Bill Type');
            return response()->json(['success' => true,'message' => 'Tambah Data Berhasil']);
        }
    }

    ## Get Data
    public function edit(Request $request, StudentBillType $student_bill_type)
    {
        if ($request->ajax()) {
            return response()->json(['success' => true,'data' => $student_bill_type]);
        }
    }

    ## Edit Data
    public function update(Request $request, StudentBillType $student_bill_type)
    {
        if ($request->ajax()) {
            $student_bill_type->work_unit_id = $request->work_unit_id;
            $student_bill_type->transaction_code_id = $request->transaction_code_id;
            $student_bill_type->frequency = $request->frequency;
            $student_bill_type->default_amount = str_replace('.', '', $request->default_amount) ?? 0;
            $student_bill_type->save();
    
            activity()->log('Edit Data Student Bill Type With ID = '.$student_bill_type->id);
            return response()->json(['success' => true,'message' => 'Ubah Data Berhasil']);
        }
    }

    ## Delete Data
    public function delete(Request $request, StudentBillType $student_bill_type)
    {
        if ($request->ajax()) {
            $student_bill_type->delete();
            activity()->log('Delete Data Student Bill Type With ID = '.$student_bill_type->id);
            return response()->json(['success' => true,'message' => 'Hapus Data Berhasil']);
        }
    }

}
