<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\StudentClassHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AcademicYearManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_an_academic_year_as_inactive_by_default(): void
    {
        $this->withoutMiddleware()
            ->post(route('academic_years.store'), [
                'name' => '2026/2027',
                'start_date' => '2026-07-01',
                'end_date' => '2027-06-30',
            ])
            ->assertRedirect(route('academic_years.index'));

        $this->assertDatabaseHas('academic_years', [
            'name' => '2026/2027',
            'is_active' => false,
        ]);
    }

    public function test_activating_an_academic_year_deactivates_the_other_years(): void
    {
        $currentYear = AcademicYear::create(['name' => '2025/2026', 'is_active' => true]);
        $nextYear = AcademicYear::create(['name' => '2026/2027', 'is_active' => false]);

        $this->withoutMiddleware()
            ->post(route('academic_years.activate', $nextYear))
            ->assertRedirect(route('academic_years.index'));

        $this->assertDatabaseHas('academic_years', [
            'id' => $currentYear->id,
            'is_active' => false,
        ]);
        $this->assertDatabaseHas('academic_years', [
            'id' => $nextYear->id,
            'is_active' => true,
        ]);
    }

    public function test_it_prevents_deleting_an_academic_year_with_student_history(): void
    {
        $academicYear = AcademicYear::create(['name' => '2025/2026']);
        $classId = DB::table('classes')->insertGetId([
            'work_unit_id' => 1,
            'name' => 'Kelas 1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $studentId = DB::table('students')->insertGetId([
            'classes_id' => $classId,
            'id_student' => 'student-1',
            'name' => 'Siswa Uji',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        StudentClassHistory::create([
            'student_id' => $studentId,
            'class_id' => $classId,
            'academic_year_id' => $academicYear->id,
            'status' => 'aktif',
        ]);

        $this->withoutMiddleware()
            ->delete(route('academic_years.destroy', $academicYear))
            ->assertSessionHasErrors('academic_year');

        $this->assertModelExists($academicYear);
    }

    public function test_promotion_requires_an_active_source_academic_year(): void
    {
        $targetYear = AcademicYear::create(['name' => '2026/2027']);
        $sourceClassId = DB::table('classes')->insertGetId([
            'work_unit_id' => 1,
            'name' => 'Kelas 1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $targetClassId = DB::table('classes')->insertGetId([
            'work_unit_id' => 1,
            'name' => 'Kelas 2',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->withoutMiddleware()
            ->post(route('student_promotions.store'), [
                'target_academic_year_id' => $targetYear->id,
                'mappings' => [$sourceClassId => $targetClassId],
            ])
            ->assertSessionHasErrors('target_academic_year_id');

        $this->assertDatabaseCount('student_class_histories', 0);
    }
}
