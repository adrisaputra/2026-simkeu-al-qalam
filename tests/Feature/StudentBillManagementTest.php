<?php

namespace Tests\Feature;

use App\Http\Controllers\StudentBillController;
use App\Http\Middleware\RoleMiddleware;
use App\Models\AcademicYear;
use App\Models\StudentBill;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class StudentBillManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_publishes_a_bill_for_each_selected_student_in_their_work_unit(): void
    {
        $fixture = $this->createFixture();

        $this->withoutMiddleware(RoleMiddleware::class)
            ->post(route('student_bill.store'), [
                'academic_year_id' => $fixture['academic_year_id'],
                'work_unit_id' => 3,
                'student_bill_type_id' => $fixture['student_bill_type_id'],
                'period_start' => '2026-07-01',
                'due_date' => '2026-07-10',
                'amounts' => [$fixture['student_id'] => '250000'],
            ])
            ->assertRedirect(route('student_bill.create', [
                'work_unit_id' => 3,
                'academic_year_id' => $fixture['academic_year_id'],
                'student_bill_type_id' => $fixture['student_bill_type_id'],
                'period_start' => '2026-07-01',
            ]))
            ->assertSessionHas('success', 'Tagihan berhasil diterbitkan untuk 1 siswa.');

        $this->assertDatabaseHas('student_bills', [
            'student_id' => $fixture['student_id'],
            'student_bill_type_id' => $fixture['student_bill_type_id'],
            'academic_year_id' => $fixture['academic_year_id'],
            'period_start' => '2026-07-01',
            'period_end' => '2026-07-31',
            'amount' => 250000,
            'due_date' => '2026-07-10',
        ]);
    }

    public function test_it_rejects_students_from_another_work_unit_when_creating_bills(): void
    {
        $fixture = $this->createFixture(studentWorkUnitId: 4);

        $this->withoutMiddleware(RoleMiddleware::class)
            ->from(route('student_bill.create'))
            ->post(route('student_bill.store'), [
                'academic_year_id' => $fixture['academic_year_id'],
                'work_unit_id' => 3,
                'student_bill_type_id' => $fixture['student_bill_type_id'],
                'period_start' => '2026-07-01',
                'amounts' => [$fixture['student_id'] => '250000'],
            ])
            ->assertSessionHasErrors([
                'amounts' => 'Daftar siswa berubah atau memuat siswa di luar unit kerja yang dipilih. Muat ulang halaman.',
            ]);

        $this->assertDatabaseCount('student_bills', 0);
    }

    public function test_it_does_not_create_duplicate_bills_for_a_period(): void
    {
        $fixture = $this->createFixture();
        $this->createBill($fixture);

        $this->withoutMiddleware(RoleMiddleware::class)
            ->from(route('student_bill.create'))
            ->post(route('student_bill.store'), [
                'academic_year_id' => $fixture['academic_year_id'],
                'work_unit_id' => 3,
                'student_bill_type_id' => $fixture['student_bill_type_id'],
                'period_start' => '2026-07-01',
                'amounts' => [$fixture['student_id'] => '300000'],
            ])
            ->assertSessionHasErrors([
                'amounts' => 'Beberapa siswa sudah memiliki tagihan pada periode ini. Muat ulang halaman untuk melihat status terbaru.',
            ]);

        $this->assertDatabaseCount('student_bills', 1);
        $this->assertDatabaseHas('student_bills', [
            'student_id' => $fixture['student_id'],
            'amount' => 250000,
        ]);
    }

    public function test_it_loads_students_and_periods_for_the_mass_bill_form(): void
    {
        $fixture = $this->createFixture();
        $this->configureDirectoryConnections($fixture['class_id']);

        $view = app(StudentBillController::class)->create(Request::create('/student_bill/create', 'GET', [
            'academic_year_id' => $fixture['academic_year_id'],
            'work_unit_id' => 3,
            'student_bill_type_id' => $fixture['student_bill_type_id'],
        ]));
        $data = $view->getData();

        $this->assertSame('2026-07-01', $data['selectedPeriod']['period_start']);
        $this->assertSame('2026-07-31', $data['selectedPeriod']['period_end']);
        $this->assertCount(1, $data['students']);
        $this->assertSame($fixture['student_id'], $data['students']->first()->id);
        $this->assertSame(250000.0, (float) $data['selectedBillType']->default_amount);
    }

    public function test_it_updates_an_unpaid_bill(): void
    {
        $fixture = $this->createFixture();
        $bill = $this->createBill($fixture);

        $this->withoutMiddleware(RoleMiddleware::class)
            ->put(route('student_bill.update', $bill), [
                'amount' => '300000',
                'due_date' => '2026-07-15',
                'notes' => 'Perubahan nominal',
            ])
            ->assertRedirect(route('student_bill.show', [
                'student' => $fixture['student_id'],
                'academic_year_id' => $fixture['academic_year_id'],
            ]));

        $this->assertDatabaseHas('student_bills', [
            'id' => $bill->id,
            'amount' => 300000,
            'due_date' => '2026-07-15',
            'notes' => 'Perubahan nominal',
        ]);
    }

    public function test_it_deletes_an_unpaid_bill(): void
    {
        $fixture = $this->createFixture();
        $bill = $this->createBill($fixture);

        $this->withoutMiddleware(RoleMiddleware::class)
            ->delete(route('student_bill.destroy', $bill))
            ->assertRedirect(route('student_bill.show', [
                'student' => $fixture['student_id'],
                'academic_year_id' => $fixture['academic_year_id'],
            ]));

        $this->assertDatabaseMissing('student_bills', ['id' => $bill->id]);
    }

    public function test_it_prevents_deleting_a_bill_that_has_payments(): void
    {
        $fixture = $this->createFixture();
        $bill = $this->createBill($fixture);
        $this->createPayment($fixture, 100000);

        $this->withoutMiddleware(RoleMiddleware::class)
            ->from(route('student_bill.show', $fixture['student_id']))
            ->delete(route('student_bill.destroy', $bill))
            ->assertSessionHasErrors([
                'bill' => 'Tagihan yang sudah memiliki pembayaran tidak dapat dihapus.',
            ]);

        $this->assertDatabaseHas('student_bills', ['id' => $bill->id]);
    }

    public function test_it_filters_students_by_unit_class_type_year_status_and_name(): void
    {
        $fixture = $this->createFixture();
        $this->createBill($fixture);
        $this->configureDirectoryConnections($fixture['class_id']);

        DB::table('students')->insert([
            'classes_id' => $fixture['class_id'],
            'id_student' => 'SISWA-LAIN',
            'name' => 'Nama Lain',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $view = app(StudentBillController::class)->index(Request::create('/student_bill', 'GET', [
            'academic_year_id' => $fixture['academic_year_id'],
            'work_unit_id' => 3,
            'class_id' => $fixture['class_id'],
            'student_bill_type_id' => $fixture['student_bill_type_id'],
            'status' => 'outstanding',
            'search' => 'Siswa Uji',
        ]));
        $data = $view->getData();

        $this->assertCount(1, $data['students']->items());
        $this->assertSame($fixture['student_id'], $data['students']->items()[0]->id);
        $this->assertSame('Siswa Uji', $data['students']->items()[0]->name);
        $this->assertSame(250000.0, (float) $data['summary']->total_amount);
        $this->assertSame(250000.0, (float) $data['summary']->remaining_amount);
    }

    public function test_it_includes_all_academic_years_when_the_year_filter_is_cleared(): void
    {
        $fixture = $this->createFixture();
        $this->createBill($fixture);
        $nextYear = AcademicYear::query()->create([
            'name' => '2027/2028',
            'start_date' => '2027-07-01',
            'end_date' => '2028-06-30',
            'is_active' => false,
        ]);
        StudentBill::query()->create([
            'student_id' => $fixture['student_id'],
            'student_bill_type_id' => $fixture['student_bill_type_id'],
            'academic_year_id' => $nextYear->id,
            'period_start' => '2027-07-01',
            'period_end' => '2027-07-31',
            'amount' => 100000,
        ]);
        $this->configureDirectoryConnections($fixture['class_id']);

        $view = app(StudentBillController::class)->index(Request::create('/student_bill', 'GET', [
            'academic_year_id' => null,
            'work_unit_id' => 3,
        ]));
        $data = $view->getData();

        $this->assertSame(350000.0, (float) $data['summary']->total_amount);
        $this->assertSame(1, $data['students']->total());
    }

    public function test_it_reports_partial_payments_and_filters_partially_paid_students(): void
    {
        $fixture = $this->createFixture();
        $this->createBill($fixture);
        $this->configureDirectoryConnections($fixture['class_id']);
        $this->createPayment($fixture, 100000);

        $view = app(StudentBillController::class)->index(Request::create('/student_bill', 'GET', [
            'academic_year_id' => $fixture['academic_year_id'],
            'work_unit_id' => 3,
            'student_bill_type_id' => $fixture['student_bill_type_id'],
            'status' => 'partial',
        ]));
        $data = $view->getData();

        $this->assertCount(1, $data['students']->items());
        $this->assertSame(100000.0, (float) $data['summary']->paid_amount);
        $this->assertSame(150000.0, (float) $data['summary']->remaining_amount);
    }

    public function test_it_filters_fully_paid_students(): void
    {
        $fixture = $this->createFixture();
        $this->createBill($fixture);
        $this->configureDirectoryConnections($fixture['class_id']);
        $this->createPayment($fixture, 250000);

        $view = app(StudentBillController::class)->index(Request::create('/student_bill', 'GET', [
            'academic_year_id' => $fixture['academic_year_id'],
            'work_unit_id' => 3,
            'student_bill_type_id' => $fixture['student_bill_type_id'],
            'status' => 'paid',
        ]));
        $data = $view->getData();

        $this->assertCount(1, $data['students']->items());
        $this->assertSame(250000.0, (float) $data['summary']->paid_amount);
        $this->assertSame(0.0, (float) $data['summary']->remaining_amount);
    }

    /**
     * @return array{academic_year_id: int, class_id: int, student_id: int, student_bill_type_id: int}
     */
    private function createFixture(int $studentWorkUnitId = 3): array
    {
        $academicYear = AcademicYear::query()->create([
            'name' => '2026/2027',
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
            'is_active' => true,
        ]);
        $classId = DB::table('classes')->insertGetId([
            'work_unit_id' => $studentWorkUnitId,
            'name' => 'Kelas Uji',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $studentId = DB::table('students')->insertGetId([
            'classes_id' => $classId,
            'id_student' => 'SISWA-UJI',
            'name' => 'Siswa Uji',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $studentBillTypeId = DB::table('student_bill_types')->insertGetId([
            'work_unit_id' => 3,
            'name' => 'SPP',
            'frequency' => 'monthly',
            'default_amount' => 250000,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            'academic_year_id' => $academicYear->id,
            'class_id' => $classId,
            'student_id' => $studentId,
            'student_bill_type_id' => $studentBillTypeId,
        ];
    }

    /**
     * @param  array{academic_year_id: int, class_id: int, student_id: int, student_bill_type_id: int}  $fixture
     */
    private function createBill(array $fixture): StudentBill
    {
        return StudentBill::query()->create([
            'student_id' => $fixture['student_id'],
            'student_bill_type_id' => $fixture['student_bill_type_id'],
            'academic_year_id' => $fixture['academic_year_id'],
            'period_start' => '2026-07-01',
            'period_end' => '2026-07-31',
            'amount' => 250000,
            'due_date' => '2026-07-10',
        ]);
    }

    private function configureDirectoryConnections(int $classId): void
    {
        $sqliteConnection = config('database.connections.sqlite');
        config([
            'database.connections.mysql' => $sqliteConnection,
            'database.connections.simpeg_mysql' => $sqliteConnection,
        ]);
        DB::purge('mysql');
        DB::purge('simpeg_mysql');

        Schema::connection('mysql')->create('classes', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('work_unit_id');
            $table->string('name');
            $table->timestamps();
        });
        DB::connection('mysql')->table('classes')->insert([
            'id' => $classId,
            'work_unit_id' => 3,
            'name' => 'Kelas Uji',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Schema::connection('simpeg_mysql')->create('work_units', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('name');
        });
        DB::connection('simpeg_mysql')->table('work_units')->insert([
            'id' => 3,
            'name' => 'SDIT',
        ]);
    }

    /**
     * @param  array{academic_year_id: int, class_id: int, student_id: int, student_bill_type_id: int}  $fixture
     */
    private function createPayment(array $fixture, int $amount): void
    {
        Schema::table('student_bill_types', function (Blueprint $table): void {
            $table->unsignedInteger('transaction_code_id')->nullable();
        });
        Schema::table('transaction_banks', function (Blueprint $table): void {
            $table->unsignedInteger('transaction_code_id')->nullable();
        });

        $descriptionCategoryId = DB::table('desc_categories')->insertGetId([
            'name' => 'Pembayaran Uji',
            'entered_by' => '1',
            'created_at' => now(),
        ]);
        $userId = DB::table('users')->insertGetId([
            'name' => 'Pengguna Uji',
            'email' => 'payment-test@example.test',
            'password' => 'password',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('student_bill_types')
            ->where('id', $fixture['student_bill_type_id'])
            ->update(['transaction_code_id' => 10]);
        DB::table('transaction_banks')->insert([
            'desc_category_id' => $descriptionCategoryId,
            'student_id' => $fixture['student_id'],
            'transaction_code_id' => 10,
            'transaction_number' => 'PAYMENT-UJI',
            'date' => '2026-07-15',
            'time' => '10:00:00',
            'amount' => $amount,
            'user_id' => $userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
