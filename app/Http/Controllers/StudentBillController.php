<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Student;
use App\Models\StudentBill;
use App\Models\StudentBillType;
use App\Models\TransactionBank;
use App\Models\WorkUnit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StudentBillController extends Controller
{
    private const WORK_UNIT_IDS = [3, 4, 5, 6];

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'academic_year_id' => ['nullable', 'integer', 'exists:academic_years,id'],
            'work_unit_id' => ['nullable', 'integer', Rule::in(self::WORK_UNIT_IDS)],
            'class_id' => ['nullable', 'integer', 'exists:classes,id'],
            'student_bill_type_id' => ['nullable', 'integer', 'exists:student_bill_types,id'],
            'status' => ['nullable', Rule::in(['outstanding', 'partial', 'paid'])],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $academicYears = AcademicYear::query()->orderByDesc('start_date')->get();
        $activeYear = $academicYears->firstWhere('is_active', true);
        if (! array_key_exists('academic_year_id', $filters)) {
            $filters['academic_year_id'] = $activeYear?->id;
        }

        $workUnits = WorkUnit::query()->whereIn('id', self::WORK_UNIT_IDS)->get(['id', 'name']);
        $unitIds = $workUnits->modelKeys();
        $classes = Classes::query()
            ->whereIn('work_unit_id', $unitIds)
            ->when($filters['work_unit_id'] ?? null, fn (Builder $query, $unitId) => $query->where('work_unit_id', $unitId))
            ->orderBy('name')
            ->get(['id', 'name', 'work_unit_id']);

        $billTypesQuery = StudentBillType::query()
            ->whereIn('work_unit_id', $unitIds)
            ->where('is_active', true)
            ->when($filters['work_unit_id'] ?? null, fn (Builder $query, $unitId) => $query->where('work_unit_id', $unitId));

        if (Schema::hasColumn('student_bill_types', 'transaction_code_id')) {
            $billTypesQuery->with('transaction_code:id,name');
        }

        $billTypes = $billTypesQuery->orderBy('id')->get();
        $studentsQuery = $this->studentOverviewQuery($filters, $unitIds);
        $summary = DB::query()
            ->fromSub((clone $studentsQuery)->toBase(), 'student_bill_overview')
            ->selectRaw(
                'COALESCE(SUM(total_amount), 0) AS total_amount, '.
                'COALESCE(SUM(paid_amount), 0) AS paid_amount, '.
                'COALESCE(SUM(remaining_amount), 0) AS remaining_amount'
            )
            ->first();

        $students = $studentsQuery->orderBy('students.name')->paginate(25)->withQueryString();

        return view('admin.student_bill.index', compact(
            'academicYears',
            'billTypes',
            'classes',
            'filters',
            'summary',
            'students',
            'workUnits'
        ));
    }

    public function create(Request $request): View
    {
        $filters = $request->validate([
            'academic_year_id' => ['nullable', 'integer', 'exists:academic_years,id'],
            'work_unit_id' => ['nullable', 'integer', Rule::in(self::WORK_UNIT_IDS)],
            'class_id' => ['nullable', 'integer', 'exists:classes,id'],
            'student_bill_type_id' => ['nullable', 'integer', 'exists:student_bill_types,id'],
            'period_start' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $academicYears = AcademicYear::query()->orderByDesc('start_date')->get();
        $activeYear = $academicYears->firstWhere('is_active', true);
        $selectedYearId = $filters['academic_year_id'] ?? $activeYear?->id;
        $selectedYear = $academicYears->firstWhere('id', $selectedYearId);
        $workUnits = WorkUnit::query()->whereIn('id', self::WORK_UNIT_IDS)->get(['id', 'name']);
        $unitIds = $workUnits->modelKeys();
        $classes = Classes::query()
            ->whereIn('work_unit_id', $unitIds)
            ->when($filters['work_unit_id'] ?? null, fn (Builder $query, $unitId) => $query->where('work_unit_id', $unitId))
            ->orderBy('name')
            ->get(['id', 'name', 'work_unit_id']);
        $billTypesQuery = StudentBillType::query()
            ->whereIn('work_unit_id', $unitIds)
            ->where('is_active', true)
            ->when($filters['work_unit_id'] ?? null, fn (Builder $query, int $unitId) => $query->where('work_unit_id', $unitId));

        if (Schema::hasColumn('student_bill_types', 'transaction_code_id')) {
            $billTypesQuery->with('transaction_code:id,name');
        }

        $billTypes = $billTypesQuery->orderBy('id')->get();

        $selectedBillType = null;
        $periods = [];
        $selectedPeriod = null;
        $students = collect();
        $existingBills = collect();

        if (! empty($filters['student_bill_type_id']) && ! empty($filters['work_unit_id'])) {
            $selectedBillType = StudentBillType::query()
                ->where('work_unit_id', $filters['work_unit_id'])
                ->where('is_active', true)
                ->find($filters['student_bill_type_id']);
        }

        if ($selectedYear && $selectedBillType) {
            $periods = $this->periodOptions($selectedYear, $selectedBillType->frequency);
            $selectedPeriod = collect($periods)->firstWhere(
                'period_start',
                $filters['period_start'] ?? null
            ) ?? ($periods[0] ?? null);

            if ($selectedPeriod) {
                $studentsQuery = Student::withoutGlobalScopes()
                    ->select('students.id', 'students.name', 'students.classes_id')
                    ->with('classes:id,name,work_unit_id')
                    ->whereHas('classes', function (Builder $query) use ($filters): void {
                        $query->where('work_unit_id', $filters['work_unit_id']);

                        if (! empty($filters['class_id'])) {
                            $query->whereKey($filters['class_id']);
                        }
                    });

                if (Schema::hasColumn('students', 'deleted_at')) {
                    $studentsQuery->whereNull('students.deleted_at');
                }

                $students = $studentsQuery->orderBy('students.name')->get();

                $existingBills = StudentBill::query()
                    ->where('academic_year_id', $selectedYear->id)
                    ->where('student_bill_type_id', $selectedBillType->id)
                    ->where('period_start', $selectedPeriod['period_start'])
                    ->whereIn('student_id', $students->modelKeys())
                    ->get()
                    ->keyBy('student_id');
            }
        }

        return view('admin.student_bill.create', compact(
            'academicYears',
            'billTypes',
            'classes',
            'existingBills',
            'filters',
            'periods',
            'selectedBillType',
            'selectedPeriod',
            'selectedYear',
            'students',
            'workUnits'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'academic_year_id' => ['required', 'integer', 'exists:academic_years,id'],
            'work_unit_id' => ['required', 'integer', Rule::in(self::WORK_UNIT_IDS)],
            'class_id' => ['nullable', 'integer', 'exists:classes,id'],
            'student_bill_type_id' => ['required', 'integer', 'exists:student_bill_types,id'],
            'period_start' => ['required', 'date_format:Y-m-d'],
            'due_date' => ['nullable', 'date'],
            'amounts' => ['required', 'array', 'min:1'],
            'amounts.*' => ['required', 'numeric', 'min:0.01', 'max:9999999999999.99'],
        ]);

        $academicYear = AcademicYear::query()->findOrFail($validated['academic_year_id']);
        $studentBillType = StudentBillType::query()
            ->where('work_unit_id', $validated['work_unit_id'])
            ->where('is_active', true)
            ->findOrFail($validated['student_bill_type_id']);
        $period = collect($this->periodOptions($academicYear, $studentBillType->frequency))
            ->firstWhere('period_start', $validated['period_start']);

        if (! $period) {
            throw ValidationException::withMessages([
                'period_start' => 'Periode tidak sesuai dengan tahun ajaran atau frekuensi tagihan.',
            ]);
        }

        $studentIds = array_map('intval', array_keys($validated['amounts']));
        $studentsQuery = DB::table('students')
            ->join('classes', 'classes.id', '=', 'students.classes_id')
            ->whereIn('students.id', $studentIds)
            ->where('classes.work_unit_id', $validated['work_unit_id'])
            ->when($validated['class_id'] ?? null, fn ($query, $classId) => $query->where('classes.id', $classId));

        if (Schema::hasColumn('students', 'deleted_at')) {
            $studentsQuery->whereNull('students.deleted_at');
        }

        $students = $studentsQuery->pluck('students.id');

        if ($students->count() !== count($studentIds)) {
            throw ValidationException::withMessages([
                'amounts' => 'Daftar siswa berubah atau memuat siswa di luar unit kerja yang dipilih. Muat ulang halaman.',
            ]);
        }

        DB::transaction(function () use ($academicYear, $studentBillType, $period, $validated, $studentIds): void {
            AcademicYear::query()->whereKey($academicYear->id)->lockForUpdate()->firstOrFail();

            $existingStudentIds = StudentBill::query()
                ->where('academic_year_id', $academicYear->id)
                ->where('student_bill_type_id', $studentBillType->id)
                ->where('period_start', $period['period_start'])
                ->whereIn('student_id', $studentIds)
                ->pluck('student_id');

            if ($existingStudentIds->isNotEmpty()) {
                throw ValidationException::withMessages([
                    'amounts' => 'Beberapa siswa sudah memiliki tagihan pada periode ini. Muat ulang halaman untuk melihat status terbaru.',
                ]);
            }

            foreach ($validated['amounts'] as $studentId => $amount) {
                StudentBill::query()->create([
                    'student_id' => (int) $studentId,
                    'student_bill_type_id' => $studentBillType->id,
                    'academic_year_id' => $academicYear->id,
                    'period_start' => $period['period_start'],
                    'period_end' => $period['period_end'],
                    'amount' => $amount ? str_replace('.', '', $amount) : 0,
                    'due_date' => $validated['due_date'] ?? null,
                ]);
            }
        });

        activity()->log('Create Student Bills for Academic Year '.$academicYear->name);

        return redirect()
            ->route('student_bill.create', [
                'work_unit_id' => $validated['work_unit_id'],
                'academic_year_id' => $academicYear->id,
                'student_bill_type_id' => $studentBillType->id,
                'class_id' => $validated['class_id'] ?? null,
                'period_start' => $period['period_start'],
            ])
            ->with('success', 'Tagihan berhasil diterbitkan untuk '.$students->count().' siswa.');
    }

    public function show(Request $request, Student $student): View
    {
        $validated = $request->validate([
            'academic_year_id' => ['nullable', 'integer', 'exists:academic_years,id'],
        ]);

        $academicYears = AcademicYear::query()->orderByDesc('start_date')->get();
        $activeYear = $academicYears->firstWhere('is_active', true);
        $selectedYearId = array_key_exists('academic_year_id', $validated)
            ? $validated['academic_year_id']
            : $activeYear?->id;
        $selectedYear = $academicYears->firstWhere('id', $selectedYearId);
        $student->load('classes.work_unit');

        $billsQuery = StudentBill::query()
            ->where('student_id', $student->id)
            ->with('student_bill_type');

        if (Schema::hasColumn('student_bill_types', 'transaction_code_id')) {
            $billsQuery->with('student_bill_type.transaction_code');
        }

        if ($selectedYear) {
            $billsQuery->where('academic_year_id', $selectedYear->id);
        }

        $bills = $billsQuery
            ->orderBy('period_start')
            ->orderBy('id')
            ->get();

        $this->attachPaymentTotals($bills);
        $summary = (object) [
            'total_amount' => $bills->sum('amount'),
            'paid_amount' => $bills->sum('paid_amount'),
            'remaining_amount' => $bills->sum('remaining_amount'),
        ];

        return view('admin.student_bill.show', compact(
            'academicYears',
            'bills',
            'selectedYear',
            'student',
            'summary'
        ));
    }

    public function edit(StudentBill $student_bill): View|RedirectResponse
    {
        if ($this->hasPayments($student_bill)) {
            return redirect()
                ->route('student_bill.show', ['student' => $student_bill->student_id])
                ->withErrors(['bill' => 'Tagihan yang sudah memiliki pembayaran tidak dapat diubah.']);
        }

        $student_bill->load(['student', 'student_bill_type']);

        return view('admin.student_bill.edit', ['bill' => $student_bill]);
    }

    public function update(Request $request, StudentBill $student_bill): RedirectResponse
    {
        if ($this->hasPayments($student_bill)) {
            return back()->withErrors(['bill' => 'Tagihan yang sudah memiliki pembayaran tidak dapat diubah.']);
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999999999.99'],
            'due_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $student_bill->update($validated);

        activity()->log('Edit Student Bill With ID = '.$student_bill->id);

        return redirect()
            ->route('student_bill.show', [
                'student' => $student_bill->student_id,
                'academic_year_id' => $student_bill->academic_year_id,
            ])
            ->with('success', 'Tagihan berhasil diperbarui.');
    }

    public function destroy(StudentBill $student_bill): RedirectResponse
    {
        if ($this->hasPayments($student_bill)) {
            return back()->withErrors(['bill' => 'Tagihan yang sudah memiliki pembayaran tidak dapat dihapus.']);
        }

        $studentId = $student_bill->student_id;
        $academicYearId = $student_bill->academic_year_id;
        $student_bill->delete();

        activity()->log('Delete Student Bill With ID = '.$student_bill->id);

        return redirect()
            ->route('student_bill.show', [
                'student' => $studentId,
                'academic_year_id' => $academicYearId,
            ])
            ->with('success', 'Tagihan berhasil dihapus.');
    }

    /**
     * @param  array<string, mixed>  $filters
     * @param  array<int, int>  $unitIds
     */
    private function studentOverviewQuery(array $filters, array $unitIds): Builder
    {
        $query = Student::withoutGlobalScopes()
            ->join('classes', 'classes.id', '=', 'students.classes_id')
            ->leftJoin('student_bills', function (JoinClause $join) use ($filters): void {
                $join->on('student_bills.student_id', '=', 'students.id');

                if (! empty($filters['academic_year_id'])) {
                    $join->where('student_bills.academic_year_id', $filters['academic_year_id']);
                }

                if (! empty($filters['student_bill_type_id'])) {
                    $join->where('student_bills.student_bill_type_id', $filters['student_bill_type_id']);
                }
            })
            ->leftJoin('student_bill_types', 'student_bill_types.id', '=', 'student_bills.student_bill_type_id')
            ->whereIn('classes.work_unit_id', $unitIds)
            ->when($filters['work_unit_id'] ?? null, fn (Builder $builder, $unitId) => $builder->where('classes.work_unit_id', $unitId))
            ->when($filters['class_id'] ?? null, fn (Builder $builder, $classId) => $builder->where('classes.id', $classId))
            ->when($filters['search'] ?? null, fn (Builder $builder, string $search) => $builder->where('students.name', 'like', '%'.$search.'%'));

        if (Schema::hasColumn('students', 'deleted_at')) {
            $query->whereNull('students.deleted_at');
        }

        $paymentTotal = '0';

        if (Schema::hasColumn('student_bill_types', 'transaction_code_id') && Schema::hasTable('transaction_banks')) {
            $paymentTotal = 'COALESCE((SELECT SUM(bill_payments.amount) '.
                'FROM transaction_banks AS bill_payments '.
                'WHERE bill_payments.student_id = student_bills.student_id '.
                'AND bill_payments.transaction_code_id = student_bill_types.transaction_code_id '.
                'AND bill_payments.date >= student_bills.period_start '.
                'AND bill_payments.date <= student_bills.period_end), 0)';
        }

        $totalAmount = 'COALESCE(SUM(student_bills.amount), 0)';
        $paidAmount = 'COALESCE(SUM(CASE WHEN '.$paymentTotal.' >= student_bills.amount THEN student_bills.amount ELSE '.$paymentTotal.' END), 0)';
        $remainingAmount = 'COALESCE(SUM(CASE WHEN '.$paymentTotal.' >= student_bills.amount THEN 0 ELSE student_bills.amount - '.$paymentTotal.' END), 0)';

        $query->selectRaw(
            'students.id, students.name, students.classes_id, classes.name AS class_name, classes.work_unit_id, '.
            $totalAmount.' AS total_amount, '.
            $paidAmount.' AS paid_amount, '.
            $remainingAmount.' AS remaining_amount'
        )
            ->groupBy('students.id', 'students.name', 'students.classes_id', 'classes.name', 'classes.work_unit_id');

        if (($filters['status'] ?? null) === 'outstanding') {
            $query->havingRaw('remaining_amount > 0');
        } elseif (($filters['status'] ?? null) === 'partial') {
            $query->havingRaw('paid_amount > 0 AND remaining_amount > 0');
        } elseif (($filters['status'] ?? null) === 'paid') {
            $query->havingRaw('total_amount > 0 AND remaining_amount = 0');
        }

        return $query;
    }

    /**
     * @return array<int, array{period_start: string, period_end: string, label: string}>
     */
    private function periodOptions(AcademicYear $academicYear, string $frequency): array
    {
        $startDate = Carbon::parse($academicYear->start_date)->startOfDay();
        $endDate = Carbon::parse($academicYear->end_date)->startOfDay();

        if ($startDate->greaterThan($endDate)) {
            return [];
        }

        $periods = [];

        if ($frequency === 'monthly') {
            $month = $startDate->copy()->startOfMonth();
            $monthNames = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
            ];

            while ($month->lessThanOrEqualTo($endDate)) {
                $periodStart = $month->lessThan($startDate) ? $startDate->copy() : $month->copy();
                $periodEnd = $month->copy()->endOfMonth();

                if ($periodEnd->greaterThan($endDate)) {
                    $periodEnd = $endDate->copy();
                }

                $periods[] = [
                    'period_start' => $periodStart->toDateString(),
                    'period_end' => $periodEnd->toDateString(),
                    'label' => $monthNames[$month->month].' '.$month->year,
                ];
                $month->addMonth();
            }

            return $periods;
        }

        if ($frequency === 'weekly') {
            $periodStart = $startDate->copy();

            while ($periodStart->lessThanOrEqualTo($endDate)) {
                $periodEnd = $periodStart->copy()->addDays(6);

                if ($periodEnd->greaterThan($endDate)) {
                    $periodEnd = $endDate->copy();
                }

                $periods[] = [
                    'period_start' => $periodStart->toDateString(),
                    'period_end' => $periodEnd->toDateString(),
                    'label' => $periodStart->format('d M Y').' - '.$periodEnd->format('d M Y'),
                ];
                $periodStart = $periodEnd->copy()->addDay();
            }

            return $periods;
        }

        if ($frequency === 'semester') {
            $periodStart = $startDate->copy();
            $semester = 1;

            while ($periodStart->lessThanOrEqualTo($endDate)) {
                $periodEnd = $periodStart->copy()->addMonthsNoOverflow(6)->subDay();

                if ($periodEnd->greaterThan($endDate)) {
                    $periodEnd = $endDate->copy();
                }

                $periods[] = [
                    'period_start' => $periodStart->toDateString(),
                    'period_end' => $periodEnd->toDateString(),
                    'label' => 'Semester '.$semester.' ('.$periodStart->format('M Y').' - '.$periodEnd->format('M Y').')',
                ];
                $periodStart = $periodEnd->copy()->addDay();
                $semester++;
            }

            return $periods;
        }

        if ($frequency === 'yearly') {
            return [[
                'period_start' => $startDate->toDateString(),
                'period_end' => $endDate->toDateString(),
                'label' => 'Tahun Ajaran '.$academicYear->name,
            ]];
        }

        return [];
    }

    /**
     * @param  Collection<int, StudentBill>  $bills
     */
    private function attachPaymentTotals(Collection $bills): void
    {
        if ($bills->isEmpty()) {
            return;
        }

        $hasTransactionCode = Schema::hasColumn('student_bill_types', 'transaction_code_id');
        $transactionCodeIds = $hasTransactionCode
            ? $bills->map(fn (StudentBill $bill): ?int => $bill->student_bill_type?->transaction_code_id)->filter()->unique()->values()
            : collect();
        $transactions = collect();

        if ($transactionCodeIds->isNotEmpty()) {
            $transactions = TransactionBank::query()
                ->where('student_id', $bills->first()->student_id)
                ->whereIn('transaction_code_id', $transactionCodeIds)
                ->whereDate('date', '>=', $bills->min('period_start'))
                ->whereDate('date', '<=', $bills->max('period_end'))
                ->get(['transaction_code_id', 'date', 'amount']);
        }

        foreach ($bills as $bill) {
            $transactionCodeId = $bill->student_bill_type?->transaction_code_id;
            $paidAmount = $transactionCodeId
                ? (float) $transactions
                    ->where('transaction_code_id', $transactionCodeId)
                    ->filter(fn (TransactionBank $transaction): bool => $transaction->date >= $bill->period_start && $transaction->date <= $bill->period_end)
                    ->sum('amount')
                : 0;
            $amount = (float) $bill->amount;

            $bill->setAttribute('paid_amount', min($paidAmount, $amount));
            $bill->setAttribute('remaining_amount', max($amount - $paidAmount, 0));
        }
    }

    private function hasPayments(StudentBill $bill): bool
    {
        if (! Schema::hasColumn('student_bill_types', 'transaction_code_id')) {
            return false;
        }

        $transactionCodeId = $bill->student_bill_type?->transaction_code_id;

        if (! $transactionCodeId) {
            return false;
        }

        return TransactionBank::query()
            ->where('student_id', $bill->student_id)
            ->where('transaction_code_id', $transactionCodeId)
            ->whereBetween('date', [$bill->period_start, $bill->period_end])
            ->exists();
    }
}
