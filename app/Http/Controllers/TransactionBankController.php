<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Models\DescCategory;
use App\Models\Income;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Yajra\DataTables\DataTables;

class IncomeController extends Controller
{
    ## Show Data
    public function index()
    {
        $title = "Penerimaan";
        $desc_category = DescCategory::get();
        return view('admin.income.index', compact('title', 'desc_category'));
    }

    ## Get Data
    public function get_income_index(Request $request)
    {

        if ($request->ajax()) {
            $counter = 1;

            $income = Income::with('desc_category', 'student')->limit(10);

            return DataTables::of($income)
                ->addIndexColumn()
                ->addColumn('number', function () use (&$counter) {
                    return $counter++;
                })
                ->addColumn('display_date', function ($v) {
                    return Helpers::date($v->date).'<br>'.$v->time;
                })
                ->addColumn('display_classes', function ($v) {
                    return $v->student?->classes->name;
                })
                ->addColumn('display_amount', function ($v) {
                    return Helpers::format_number($v->amount);
                })
                ->addColumn('action', function ($v) {
                    $btn = '<a href="#" onClick="getData(' . $v->id . ')" id="' . $v->id . '" title="Edit" data-toggle="modal" data-target="#exampleModal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                        </a>';
                    $btn .= '<a href="#" onclick="deleteData(' . $v->id . ')" id="' . $v->id . '" class="warning confirm" data-toggle="tooltip" data-placement="top" title="Hapus">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                        </a>';
                    return $btn;
                })
                ->rawColumns(['display_date', 'action'])->make(true);
        }
    }

    public function validate(Request $request, $action)
    {

        if ($request->ajax()) {

            $attributes = [
                'name' => 'Nama Kategori Uraian',
                'entered_by' => 'Penginput',
            ];

            if ($action === "Simpan") {
                $rules = [
                    'name' => 'required|max:255',
                    'entered_by' => 'required|max:255'
                ];
            } else {
                $rules = [
                    'name' => 'required|max:255',
                    'entered_by' => 'required|max:255'
                ];
            }

            $request->validate($rules, [], $attributes);

            return response()->json(['success' => true]);
        }
    }

    ## Save Data
    public function store(Request $request)
    {
        if ($request->ajax()) {
            $income = new Income();
            $income->desc_category_id = $request->desc_category_id;
            $income->student_id = $request->student_id;
            $income->transaction_number = $request->transaction_number;
            $income->time = $request->time;
            $income->date = $request->date;
            $income->amount = $request->amount;
            $income->save();

            activity()->log('Create Data Income');
            return response()->json(['success' => true, 'message' => 'Tambah Data Berhasil']);
        }
    }

    ## Get Data
    public function edit(Request $request, Income $income)
    {
        if ($request->ajax()) {
            return response()->json(['success' => true, 'data' => $income]);
        }
    }

    ## Edit Data
    public function update(Request $request, Income $income)
    {
        if ($request->ajax()) {
            $income->desc_category_id = $request->desc_category_id;
            $income->student_id = $request->student_id;
            $income->transaction_number = $request->transaction_number;
            $income->time = $request->time;
            $income->date = $request->date;
            $income->amount = $request->amount;
            $income->save();

            activity()->log('Edit Data Income With ID = ' . $income->id);
            return response()->json(['success' => true, 'message' => 'Ubah Data Berhasil']);
        }
    }

    ## Delete Data
    public function delete(Request $request, Income $income)
    {
        if ($request->ajax()) {
            $income->delete();
            activity()->log('Delete Data Income With ID = ' . $income->id);
            return response()->json(['success' => true, 'message' => 'Hapus Data Berhasil']);
        }
    }


    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        DB::beginTransaction();

        try {

            $reader = new Xlsx();
            $spreadsheet = $reader->load(
                $request->file('file')->getRealPath()
            );

            $StudentImported = 0;

            $data = $spreadsheet->getSheet(0)->toArray();

            foreach ($data as $index => $row) {

                if ($index == 0) {
                    continue;
                }

                if (empty(array_filter($row))) {
                    continue;
                }

                ## Date and Time
                $dateTime = $row[2] ?? null;

                $date = null;
                $time = null;

                if ($dateTime !== null && $dateTime !== '') {

                    $dateTime = trim((string) $dateTime);

                    $dateTime = preg_replace('/\s+/', ' ', $dateTime);

                    $parts = explode(' ', $dateTime, 2);

                    $datePart = $parts[0] ?? null;
                    $timePart = $parts[1] ?? null;

                    // Tanggal
                    if ($datePart) {

                        $dateParts = explode('/', $datePart);

                        if (count($dateParts) === 3) {

                            $date = $dateParts[2] . '-' .
                                str_pad($dateParts[1], 2, '0', STR_PAD_LEFT) . '-' .
                                str_pad($dateParts[0], 2, '0', STR_PAD_LEFT);
                        }
                    }

                    // Waktu
                    if ($timePart) {

                        $time = str_replace('.', ':', $timePart);

                        if (substr_count($time, ':') === 1) {
                            $time .= ':00';
                        }
                    }
                }

                ## NIS
                $nis = $row[3] ?? null;

                $student = Student::where('nis', $nis)->first();

                ## Amount
                $amount = $row[7] ?? null;

                if ($amount !== null && $amount !== '') {
                    $amount = str_replace('.', '', $amount);
                    $amount = str_replace(',', '.', $amount);
                }

                Income::create([
                    'desc_category_id' => $request->desc_category_id,
                    'transaction_number' => $row[1] ?? null,
                    'date' => $date,
                    'time' => $time,
                    'student_id' => $student?->id,
                    'amount' => $amount,
                    'user_id' => Auth::id(),
                ]);

                $StudentImported++;
            }

            // Semua berhasil
            DB::commit();

            return back()->with(
                'success',
                "Income: {$StudentImported} data berhasil diimport."
            );
        } catch (\Throwable $e) {

            // Ada satu saja yang gagal,
            // batalkan SEMUA data yang sudah masuk
            DB::rollBack();

            return back()->with(
                'error',
                'Import gagal. Tidak ada data yang disimpan. Error: ' . $e->getMessage()
            );
        }
    }
}
