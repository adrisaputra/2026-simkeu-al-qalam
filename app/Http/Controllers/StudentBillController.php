<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\StudentBill;
use App\Models\StudentBillAcademicYear;
use App\Models\StudentBillType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class StudentBillController extends Controller
{

    ## Show Data
    public function index($student)
    {
        $title = "Detail Tagihan Siswa";
        $student = Crypt::decrypt($student);
        $student = Student::where('id', $student)->first();
        $academic_year = AcademicYear::get();
        $academic_year_active = AcademicYear::where('is_active', true)->firstOrFail();
        return view('admin.student_bill.index',compact('title','student','academic_year','academic_year_active'));
    }

    public function get_student_bill_index(Request $request, $student = null)
    {
        if ($request->ajax()) {
            $counters = 1;

            $student = Crypt::decrypt($student);

            $query = StudentBill::query();
                    
            $get_academic_year = $request->input('get_academic_year');
            $query->where('academic_year_id', $get_academic_year);
            
            $student = $query->where('student_id',$student)->limit(10);

            return DataTables::of($student)
                ->addIndexColumn()
                ->addColumn('number', function () use (&$counters) {
                    return $counters++;
                })
                ->addColumn('display_student_bill_type', function ($v){
                    return $v->student_bill_type?->transaction_code?->name;
                })
                ->addColumn('display_periode', function ($v){
                    if($v->student_bill_type->frequency == 'monthly'){
                        // return '<b>'.Helpers::month_name(date('m', strtotime($v->period_start))).'</b><br>'.Helpers::date($v->period_start).' - '.Helpers::date($v->period_end) ;
                        return '<b>'.Helpers::month_name(date('m', strtotime($v->period_start))).'</b>' ;
                    }
                })
                ->addColumn('display_nominal', function ($v){
                    return Helpers::format_number($v->amount);
                })
                ->addColumn('display_status', function ($v) {
                    // if($v->status=='Active'){
                        $status ='<span class="badge badge-success">Lunas</span>';
                    // }else{
                    //     $status ='<span class="badge badge-danger">Tidak Aktif</span>';
                    // }
                    return $status;
                })
                ->addColumn('action', function ($v) {
                    $btn = '<a href="#" onClick="getData('.$v->id.')" id="'.$v->id.'" class="btn btn-info btn-sm position-relative me-5" title="Pembayaran" data-toggle="modal" data-target="#exampleModal">
                            Pembayaran
                        </a>';

                    return $btn;
                })
                ->rawColumns(['display_periode','display_status', 'action'])
                ->make(true);
        }
    }

    public function store(Request $request, $student_bill_type = null)
    {

        if ($request->ajax()) {
            // $studentBillTypeId = Crypt::decrypt($student_bill_type);

            $studentBillType = StudentBillType::query()
                ->where('is_active', true)
                ->findOrFail($student_bill_type);

            $academicYear = AcademicYear::where('is_active', true)
                ->firstOrFail();

            $startDate = Carbon::parse($academicYear->start_date)->startOfDay();
            $endDate = Carbon::parse($academicYear->end_date)->startOfDay();

            if ($startDate->gt($endDate)) {
                return redirect()->back()->with(
                    'error',
                    'Tanggal mulai tahun ajaran tidak boleh melebihi tanggal berakhir.'
                );
            }

            // Bentuk seluruh periode berdasarkan frekuensi tagihan.
            $periods = [];

            switch ($studentBillType->frequency) {
                case 'weekly':
                    $periodStart = $startDate->copy();

                    while ($periodStart->lte($endDate)) {
                        $periodEnd = $periodStart->copy()
                            ->addDays(6);

                        if ($periodEnd->gt($endDate)) {
                            $periodEnd = $endDate->copy();
                        }

                        $periods[] = [
                            'period_start' => $periodStart->toDateString(),
                            'period_end' => $periodEnd->toDateString(),
                        ];

                        $periodStart = $periodEnd->copy()->addDay();
                    }
                    break;

                case 'monthly':
                    $periodStart = $startDate->copy();

                    while ($periodStart->lte($endDate)) {
                        $periodEnd = $periodStart->copy()->endOfMonth();

                        if ($periodEnd->gt($endDate)) {
                            $periodEnd = $endDate->copy();
                        }

                        $periods[] = [
                            'period_start' => $periodStart->toDateString(),
                            'period_end' => $periodEnd->toDateString(),
                        ];

                        $periodStart = $periodEnd->copy()->addDay();
                    }
                    break;

                case 'semester':
                    // Semester pertama: mulai tahun ajaran sampai 6 bulan,
                    // atau sampai akhir tahun ajaran jika lebih pendek.
                    $semesterStart = $startDate->copy();

                    while ($semesterStart->lte($endDate)) {
                        $semesterEnd = $semesterStart->copy()
                            ->addMonthsNoOverflow(6)
                            ->subDay();

                        if ($semesterEnd->gt($endDate)) {
                            $semesterEnd = $endDate->copy();
                        }

                        $periods[] = [
                            'period_start' => $semesterStart->toDateString(),
                            'period_end' => $semesterEnd->toDateString(),
                        ];

                        $semesterStart = $semesterEnd->copy()->addDay();
                    }
                    break;

                case 'yearly':
                    $periods[] = [
                        'period_start' => $startDate->toDateString(),
                        'period_end' => $endDate->toDateString(),
                    ];
                    break;

                default:
                    return redirect()->back()->with(
                        'error',
                        'Frekuensi tagihan tidak dikenali.'
                    );
            }

            $result = DB::transaction(function () use (
                $studentBillType,
                $academicYear,
                $periods
            ) {
                // Simpan relasi jenis tagihan dengan tahun ajaran.
                $relation = StudentBillAcademicYear::query()
                    ->where('work_unit_id', $studentBillType->work_unit_id)
                    ->where('academic_year_id', $academicYear->id)
                    ->where('student_bill_type_id', $studentBillType->id)
                    ->first();

                if (!$relation) {
                    $relation = new StudentBillAcademicYear();
                    $relation->work_unit_id = $studentBillType->work_unit_id;
                    $relation->academic_year_id = $academicYear->id;
                    $relation->student_bill_type_id = $studentBillType->id;
                    $relation->save();
                }

                // Ambil siswa pada unit kerja yang sesuai.
                $students = Student::query()
                    ->whereHas('classes', function ($query) use ($studentBillType) {
                        $query->where(
                            'work_unit_id',
                            $studentBillType->work_unit_id
                        );
                    })
                    ->get(['id']);

                $created = 0;
                $skipped = 0;

                foreach ($students as $student) {
                    foreach ($periods as $period) {
                        $bill = StudentBill::firstOrCreate(
                            [
                                'student_id' => $student->id,
                                'student_bill_type_id' => $studentBillType->id,
                                'academic_year_id' => $academicYear->id,
                                'period_start' => $period['period_start'],
                            ],
                            [
                                'period_end' => $period['period_end'],
                                'amount' => $studentBillType->default_amount,
                            ]
                        );

                        if ($bill->wasRecentlyCreated) {
                            $created++;
                        } else {
                            $skipped++;
                        }
                    }
                }

                return [
                    'students' => $students->count(),
                    'periods' => count($periods),
                    'created' => $created,
                    'skipped' => $skipped,
                ];
            });

            return response()->json(['success' => true,'message' => "Penerbitan selesai. Siswa: {$result['students']}, "
                    . "periode per siswa: {$result['periods']}, "
                    . "tagihan baru: {$result['created']}, "
                    . "tagihan yang sudah ada: {$result['skipped']}."]);
        }
    }
}
