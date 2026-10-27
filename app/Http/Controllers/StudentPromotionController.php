<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Student;
use App\Models\StudentClassHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StudentPromotionController extends Controller
{
    public function index(): View
    {
        $title = 'Kenaikan Kelas';
        $activeYear = AcademicYear::query()->where('is_active', true)->first();
        $academicYears = AcademicYear::query()
            ->where('is_active', false)
            ->orderByDesc('start_date')
            ->orderByDesc('id')
            ->get();
        $sourceClasses = Classes::query()
            ->with('work_unit')
            ->withCount('students')
            ->whereHas('students')
            ->orderBy('work_unit_id')
            ->orderBy('name')
            ->get();
        $destinationClasses = Classes::query()
            ->orderBy('work_unit_id')
            ->orderBy('name')
            ->get();

        return view('admin.student_promotion.index', compact(
            'title',
            'activeYear',
            'academicYears',
            'sourceClasses',
            'destinationClasses',
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'target_academic_year_id' => ['required', 'integer', 'exists:academic_years,id'],
            'mappings' => ['required', 'array'],
            'mappings.*' => ['nullable', 'integer', 'exists:classes,id'],
        ]);

        $classMappings = collect($validated['mappings'])
            ->filter(fn ($classId): bool => $classId !== null && $classId !== '')
            ->map(fn ($classId): int => (int) $classId)
            ->all();

        if ($classMappings === []) {
            throw ValidationException::withMessages([
                'mappings' => 'Pilih minimal satu pemetaan kelas tujuan.',
            ]);
        }

        $promotedStudentCount = DB::transaction(function () use ($validated, $classMappings): int {
            $sourceYear = AcademicYear::query()
                ->where('is_active', true)
                ->lockForUpdate()
                ->first();

            if ($sourceYear === null) {
                throw ValidationException::withMessages([
                    'target_academic_year_id' => 'Aktifkan tahun ajaran sumber terlebih dahulu.',
                ]);
            }

            $targetYear = AcademicYear::query()
                ->lockForUpdate()
                ->findOrFail($validated['target_academic_year_id']);

            if ($targetYear->is_active || $targetYear->is($sourceYear)) {
                throw ValidationException::withMessages([
                    'target_academic_year_id' => 'Tahun ajaran tujuan harus berbeda dan belum aktif.',
                ]);
            }

            $sourceClassIds = array_map('intval', array_keys($classMappings));
            $allClassIds = array_unique(array_merge($sourceClassIds, array_values($classMappings)));
            $classes = Classes::query()->whereIn('id', $allClassIds)->get()->keyBy('id');

            if ($classes->count() !== count($allClassIds)) {
                throw ValidationException::withMessages([
                    'mappings' => 'Salah satu kelas yang dipilih tidak ditemukan.',
                ]);
            }

            foreach ($classMappings as $sourceClassId => $targetClassId) {
                if ((int) $classes[$sourceClassId]->work_unit_id !== (int) $classes[$targetClassId]->work_unit_id) {
                    throw ValidationException::withMessages([
                        'mappings' => 'Kelas tujuan harus berada pada jenjang yang sama dengan kelas asal.',
                    ]);
                }
            }

            $studentsByClass = Student::query()
                ->whereIn('classes_id', $sourceClassIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->groupBy('classes_id');

            foreach ($sourceClassIds as $sourceClassId) {
                if ($studentsByClass->get($sourceClassId, collect())->isEmpty()) {
                    throw ValidationException::withMessages([
                        'mappings' => 'Kelas asal yang dipilih tidak memiliki siswa.',
                    ]);
                }
            }

            $students = $studentsByClass->flatten();
            $studentIds = $students->pluck('id')->all();

            if (StudentClassHistory::query()
                ->where('academic_year_id', $targetYear->id)
                ->whereIn('student_id', $studentIds)
                ->exists()) {
                throw ValidationException::withMessages([
                    'target_academic_year_id' => 'Sebagian siswa sudah memiliki riwayat pada tahun ajaran tujuan.',
                ]);
            }

            $sourceHistories = StudentClassHistory::query()
                ->where('academic_year_id', $sourceYear->id)
                ->whereIn('student_id', $studentIds)
                ->get()
                ->groupBy('student_id');

            foreach ($students as $student) {
                $histories = $sourceHistories->get($student->id, collect());

                if ($histories->count() > 1 || ($histories->isNotEmpty() && (int) $histories->first()->class_id !== (int) $student->classes_id)) {
                    throw ValidationException::withMessages([
                        'mappings' => 'Riwayat kelas tahun ajaran aktif tidak sesuai dengan kelas siswa saat ini.',
                    ]);
                }

                $sourceHistory = $histories->first();

                if ($sourceHistory === null) {
                    StudentClassHistory::create([
                        'student_id' => $student->id,
                        'class_id' => $student->classes_id,
                        'academic_year_id' => $sourceYear->id,
                        'status' => 'selesai',
                    ]);
                } else {
                    $sourceHistory->update(['status' => 'selesai']);
                }

                $targetClassId = $classMappings[$student->classes_id];

                StudentClassHistory::create([
                    'student_id' => $student->id,
                    'class_id' => $targetClassId,
                    'academic_year_id' => $targetYear->id,
                    'status' => 'aktif',
                ]);

                $student->update(['classes_id' => $targetClassId]);
            }

            activity()->log(
                'Promote '.$students->count().' Student(s) From Academic Year '.$sourceYear->name.' To '.$targetYear->name
            );

            return $students->count();
        });

        return redirect()->route('student_promotions.index')->with(
            'success',
            $promotedStudentCount.' siswa berhasil dipindahkan ke tahun ajaran tujuan.'
        );
    }
}
