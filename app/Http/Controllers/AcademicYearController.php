<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AcademicYearController extends Controller
{
    public function index(): View
    {
        $title = 'Tahun Ajaran';
        $academicYears = AcademicYear::query()
            ->orderByDesc('start_date')
            ->orderByDesc('id')
            ->get();

        return view('admin.academic_year.index', compact('title', 'academicYears'));
    }

    public function store(Request $request): RedirectResponse
    {
        AcademicYear::create($this->validatedData($request));
        activity()->log('Create Academic Year Data');

        return redirect()->route('academic_years.index')->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    public function update(Request $request, AcademicYear $academicYear): RedirectResponse
    {
        $academicYear->update($this->validatedData($request, $academicYear));
        activity()->log('Edit Academic Year Data With ID = '.$academicYear->id);

        return redirect()->route('academic_years.index')->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    public function activate(AcademicYear $academicYear): RedirectResponse
    {
        AcademicYear::query()->getConnection()->transaction(function () use ($academicYear): void {
            AcademicYear::query()->lockForUpdate()->get();
            AcademicYear::query()->update(['is_active' => false]);
            $academicYear->update(['is_active' => true]);
        });

        activity()->log('Activate Academic Year With ID = '.$academicYear->id);

        return redirect()->route('academic_years.index')->with('success', 'Tahun ajaran aktif berhasil diubah.');
    }

    public function destroy(AcademicYear $academicYear): RedirectResponse
    {
        $academicYear->getConnection()->transaction(function () use ($academicYear): void {
            $academicYear = AcademicYear::query()
                ->lockForUpdate()
                ->findOrFail($academicYear->id);

            if ($academicYear->is_active) {
                throw ValidationException::withMessages([
                    'academic_year' => 'Tahun ajaran aktif tidak dapat dihapus.',
                ]);
            }

            if ($academicYear->student_class_histories()->exists()) {
                throw ValidationException::withMessages([
                    'academic_year' => 'Tahun ajaran yang sudah memiliki riwayat kelas siswa tidak dapat dihapus.',
                ]);
            }

            $academicYear->delete();
        });

        activity()->log('Delete Academic Year With ID = '.$academicYear->id);

        return redirect()->route('academic_years.index')->with('success', 'Tahun ajaran berhasil dihapus.');
    }

    /**
     * @return array{name: string, start_date: ?string, end_date: ?string}
     */
    private function validatedData(Request $request, ?AcademicYear $academicYear = null): array
    {
        $endDateRules = ['nullable', 'date'];

        if ($request->filled('start_date')) {
            $endDateRules[] = 'after_or_equal:start_date';
        }

        $nameRule = Rule::unique('academic_years', 'name');

        if ($academicYear !== null) {
            $nameRule->ignore($academicYear->id);
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:255', $nameRule],
            'start_date' => ['nullable', 'date'],
            'end_date' => $endDateRules,
        ]);
    }
}
