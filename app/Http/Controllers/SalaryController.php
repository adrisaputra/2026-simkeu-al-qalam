<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Models\Employee;
use App\Models\EmployeeKpiBonus;
use App\Models\Salary;
use App\Models\WorkUnit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class SalaryController extends Controller
{
    
    ## Show Data
    public function index()
    {
        $title = "Gaji Pegawai";
        $work_unit = WorkUnit::get();
        $employees = Employee::get();

        // Periode bulan ini
        $currentDate = now();
        $month = $currentDate->format('m');
        $year = $currentDate->format('Y');

        // Periode bulan sebelumnya
        $previousDate = $currentDate->copy()->subMonth();
        $previousMonth = $previousDate->format('m');
        $previousYear = $previousDate->format('Y');

        // Ambil seluruh data gaji bulan sebelumnya
        $previousSalaries = Salary::where('month', $previousMonth)
            ->where('year', $previousYear)
            ->get()
            ->keyBy('employee_id');

        // Komponen gaji yang akan disalin
        $salaryFields = [
            'salary',
            'allowance1',
            'allowance2',
            'allowance3',
            'ttq',
            'salary_adjustment',
            'bos',
            'jp',
            'meal_allowance',
            'rumbay',
            'salary_amount',
            'deduction',
            'amount_received',
        ];

        foreach ($employees as $employee) {

            // Cari gaji bulan sebelumnya milik pegawai
            $previousSalary = $previousSalaries->get($employee->id);

            // Siapkan data gaji
            $salaryData = [];

            foreach ($salaryFields as $field) {
                $salaryData[$field] = $previousSalary
                    ? ($previousSalary->$field ?? 0)
                    : 0;
            }

            // Buat data bulan ini jika belum tersedia
            Salary::firstOrCreate(
                [
                    'employee_id' => $employee->id,
                    'month' => $month,
                    'year' => $year,
                ],
                $salaryData
            );
        }

        return view('admin.salary.index', compact('title', 'work_unit'));
    }


    ## Get Data
    public function get_salary_index(Request $request)
    {

        if ($request->ajax()) {
            $counter = 1;

            $month = $request->input('get_month') ?? date('m');
            $year = $request->input('get_year') ?? date('Y');

            $salary_employee_ids = Salary::where('month', $month)
                ->where('year', $year)
                ->pluck('employee_id');

            $query = Employee::query()
                        ->leftJoin(
                            'work_units',
                            'work_units.id',
                            '=',
                            'employees.work_unit_id'
                        )
                        ->select(
                            'employees.*',
                            'work_units.name as work_unit_name'
                        )
                        ->whereIn('employees.id', $salary_employee_ids);
                
                        if ($request->has('get_work_unit') && !empty($request->input('get_work_unit'))) {
                            $get_work_unit = $request->input('get_work_unit');
                            $query->whereHas('work_unit', function($q) use ($get_work_unit) {
                                $q->where('id', $get_work_unit);
                            });
                        }

            $employee = $query->limit(10);
            
            return DataTables::of($employee)
                ->addIndexColumn()
                ->addColumn('number', function () use (&$counter) {
                    return $counter++;
                })
                ->addColumn('name_display', function ($v) {
                    return 'NIK : ' . $v->nik . '<br><b>' . $v->name . '</b>';
                })
                ->addColumn('display_work_unit_name', function ($v) {
                    return $v->work_unit->name;
                })
                ->addColumn('tmt_display', function ($v) use ($month,$year) {
                    return Helpers::date($v->tmt).'<br><b>'.Helpers::length_of_service($v->tmt, $month, $year);
                })
                ->addColumn('display_status', function ($v) use ($month,$year){
                    $salary = Salary::where('employee_id', $v->id)->where('month',$month)->where('year',$year)->first();
                    $salary_allowance = Helpers::salary_allowance($v->tmt, $v->niy, $month, $year);
                    return Helpers::format_number($salary->salary + $salary_allowance);
                })
                ->addColumn('action', function ($v) use ($month,$year){
                    $salary = Salary::where('employee_id', $v->id)->where('month',$month)->where('year',$year)->first();
                    if($salary){ 
                        $btn = '<a href="#" onClick="getData(' . $salary->id . ')" id="' . $salary->id . '" title="Edit" data-toggle="modal" data-target="#exampleModal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                        </a>';
                    } else {
                        $btn = '-';
                    }
                    return $btn;
                })
                ->filterColumn('name', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('employees.name', 'like', "%{$keyword}%")
                            ->orWhere('nik', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('tmt', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('tmt', 'like', "%{$keyword}%");
                    });
                })
                ->rawColumns(['name_display', 'tmt_display', 'display_status', 'action'])->make(true);
        }
    }

    
    public function validate(Request $request, $action = null)
    {
        if ($request->ajax()) {

            // $attributes = [
            //     'salary' => 'No. Induk Siswa',
            // ];

            // $rules = [
            //     'salary' => 'required|max:255',
            // ];
            
            // $request->validate($rules, [], $attributes);

            return response()->json(['success' => true]);
        }
    }


    // # Get Salary
    public function edit(Request $request, Salary $salary)
    {
        if ($request->ajax()) {
            $salary->load('employee');
            return response()->json(['success' => true, 'data' => $salary]);
        }
    }

    // # Edit Salary
    public function update(Request $request, Salary $salary)
    {
        if ($request->ajax()) {

            $salary->salary = $request->salary ? str_replace('.', '', $request->salary) : 0;
            $salary->allowance1 = $request->allowance1 ? str_replace('.', '', $request->allowance1) : 0;
            $salary->allowance2 = $request->allowance2 ? str_replace('.', '', $request->allowance2) : 0;
            $salary->allowance3 = $request->allowance3;
            $salary->ttq = $request->ttq ? str_replace('.', '', $request->ttq) : 0;
            $salary->salary_adjustment = $request->salary_adjustment ? str_replace('.', '', $request->salary_adjustment) : 0;
            $salary->bos = $request->bos ? str_replace('.', '', $request->bos) : 0;
            $salary->jp = $request->jp ? str_replace('.', '', $request->jp) : 0;
            $salary->meal_allowance = $request->meal_allowance ? str_replace('.', '', $request->meal_allowance) : 0;
            $salary->rumbay = $request->rumbay ? str_replace('.', '', $request->rumbay) : 0;
            $salary->status = 1;
            $salary->save();

            activity()->log('Edit Salary Data With ID = '.$salary->id);

            return response()->json(['success' => true, 'message' => 'Ubah Data Berhasil']);
        }
    }

    // # Delete Salary
    public function delete(Request $request, Salary $salary)
    {
        if ($request->ajax()) {
            $salary->delete();
            activity()->log('Delete Salary Data With ID = '.$salary->id);

            return response()->json(['success' => true, 'message' => 'Hapus Data Berhasil']);
        }
    }

    public function sync(Request $request)
    {
        if ($request->ajax()) {
            $employees = Employee::get();

            // Periode yang dipilih
            $currentDate = Carbon::create(
                $request->year,
                $request->month,
                1
            );

            $month = $currentDate->format('m');
            $year = $currentDate->format('Y');

            // Periode bulan sebelumnya
            $previousDate = $currentDate->copy()->subMonth();
            $previousMonth = $previousDate->format('m');
            $previousYear = $previousDate->format('Y');

            // Ambil seluruh data gaji bulan sebelumnya
            $previousSalaries = Salary::where('month', $previousMonth)
                ->where('year', $previousYear)
                ->get()
                ->keyBy('employee_id');

            // Komponen gaji yang akan disalin
            $salaryFields = [
                'salary',
                'allowance1',
                'allowance2',
                'allowance3',
                'ttq',
                'salary_adjustment',
                'bos',
                'jp',
                'meal_allowance',
                'rumbay',
                'salary_amount',
                'deduction',
                'amount_received',
            ];

            foreach ($employees as $employee) {

                // Cari gaji bulan sebelumnya milik pegawai
                $previousSalary = $previousSalaries->get($employee->id);

                // Siapkan data gaji
                $salaryData = [];

                foreach ($salaryFields as $field) {
                    $salaryData[$field] = $previousSalary
                        ? ($previousSalary->$field ?? 0)
                        : 0;
                }

                // Buat data bulan ini jika belum tersedia
                Salary::firstOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'month' => $month,
                        'year' => $year,
                    ],
                    $salaryData
                );
            }

            return response()->json(['success' => true, 'message' => 'Data Gaji Berhasil Disinkronisasi']);
        }
       
    }

}
