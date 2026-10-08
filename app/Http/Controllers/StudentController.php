<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Yajra\DataTables\DataTables;

class StudentController extends Controller
{
    public function index($classes = null)
    {
        $title = "Siswa";
        $classes = Crypt::decrypt($classes);
        $classes = Classes::where('id', $classes)->first();
        return view('admin.student.index', compact('title', 'classes'));
    }

    public function get_student_index(Request $request, $classes = null)
    {
        if ($request->ajax()) {
            $counters = 1;

            $classes = Crypt::decrypt($classes);
            $classes = Classes::where('id', $classes)->first();

            $student = Student::where('classes_id', $classes->id)->limit(10);

            return DataTables::of($student)
            ->addIndexColumn()
            ->addColumn('number', function () use (&$counters) {
                return $counters++;
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
            ->rawColumns(['category','action'])
            ->make(true);
        }
    }

    public function validate(Request $request, $action = null)
    {
        if ($request->ajax()) {

            $attributes = [
                'nis' => 'No. Induk Siswa',
                'name' => 'Nama Siswa'
            ];

            if ($action === "Simpan") {
                $rules = [
                    'nis' => 'required|max:255',
                    'name' => 'required|max:255'
                ];
            } else {
                $rules = [
                    'nis' => 'required|max:255',
                    'name' => 'required|max:255'
                ];
            }

            $request->validate($rules, [], $attributes);

            return response()->json(['success' => true]);
        }
    }

    ## Save Student 
    public function store(Request $request)
    {
        if ($request->ajax()) {

            $student = new Student();
            $student->classes_id = $request->classes_id;
            $student->nis = $request->nis;
            $student->name = $request->name;
            $student->save();
            activity()->log('Create Student Data');
            return response()->json(['success' => true, 'message' => 'Tambah Data Berhasil']);
        }
    }

    ## Get Student
    public function edit(Request $request, Student $student)
    {
        if ($request->ajax()) {
            return response()->json(['success' => true, 'data' => $student]);
        }
    }

    ## Edit Student
    public function update(Request $request, Student $student)
    {
        if ($request->ajax()) {

            $student->classes_id = $request->classes_id;
            $student->nis = $request->nis;
            $student->name = $request->name;
            $student->save();

            activity()->log('Edit Student Data With ID = ' . $student->id);
            return response()->json(['success' => true, 'message' => 'Ubah Data Berhasil']);
        }
    }

    ## Delete Student
    public function delete(Request $request, Student $student)
    {
        if ($request->ajax()) {
            $student->delete();
            activity()->log('Delete Student Data With ID = ' . $student->id);
            return response()->json(['success' => true, 'message' => 'Hapus Data Berhasil']);
        }
    }

    
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $reader = new Xlsx();
        $spreadsheet = $reader->load($request->file('file')->getRealPath());

        $StudentImported = 0;

        $data = $spreadsheet->getSheet(0)->toArray();

        foreach ($data as $index => $row) {

            if ($index == 0) continue;

            if (empty(array_filter($row))) {
                continue;
            }

            Student::Create(
                [
                    'classes_id'          => $request->classes_id,
                    'nis'       => $row[1] ?? null,
                    'name'          => $row[2] ?? null
                ]
            );

            $StudentImported++;
        }

        return back()->with(
            'success',
            "Student: {$StudentImported} data berhasil diimport."
        );
    }

}
