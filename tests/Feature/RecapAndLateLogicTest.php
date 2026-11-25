<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Setting;
use App\Models\Student;
use App\Models\TeacherAttendance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RecapAndLateLogicTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $teacher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Apris Leky Manu, S.T',
            'email' => 'admin@ntokupang.sch.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->teacher = User::create([
            'name' => 'Guru Kelas 1',
            'email' => 'guru1@teacherntokupang',
            'password' => Hash::make('ntokupang123'),
            'role' => 'guru',
            'assigned_class' => 'Kelas 1',
            'is_active' => true,
        ]);

        Setting::set('jam_masuk', '07:00');
        Setting::set('jam_terlambat', '07:30');
        Setting::set('jam_pulang', '14:00');
        Setting::set('jam_terlambat_abk', '08:30');
        Setting::set('jam_pulang_abk', '13:00');
    }

    public function test_late_regular_student_and_teacher_count_only_as_terlambat_while_abk_stays_hadir(): void
    {
        $regular = Student::create(['nisn' => '3001', 'nis' => '3001', 'nama' => 'Siswa Reguler', 'kelas' => 'Kelas 1', 'jenis_kelamin' => 'L', 'is_abk' => false]);
        $abk = Student::create(['nisn' => '3002', 'nis' => '3002', 'nama' => 'Siswa ABK', 'kelas' => 'Kelas 1', 'jenis_kelamin' => 'P', 'is_abk' => true]);

        // Wednesday 2026-11-25: Regular arrives late (08:00), ABK arrives at 09:00
        Attendance::create([
            'student_id' => $regular->id,
            'tanggal' => '2026-11-25',
            'jam_masuk' => '08:00:00',
            'jam_pulang' => '14:00:00',
            'status' => 'hadir',
            'keterangan' => 'Masuk: [08:00:00] - Terlambat 30 menit',
        ]);

        Attendance::create([
            'student_id' => $abk->id,
            'tanggal' => '2026-11-25',
            'jam_masuk' => '09:00:00',
            'jam_pulang' => '13:00:00',
            'status' => 'hadir',
            'keterangan' => 'Masuk: [09:00:00] - Hadir (ABK)',
        ]);

        // Teacher arrives late at 13:20 even WITHOUT "Terlambat" text in keterangan
        TeacherAttendance::create([
            'teacher_id' => $this->teacher->id,
            'tanggal' => '2026-11-25',
            'jam_masuk' => '13:20:00',
            'jam_pulang' => '13:30:00',
            'status' => 'hadir',
            'keterangan' => 'Pulang Awal (Duluan 30 menit): [13:30:00]',
        ]);

        // Verify Student Print Rekap totals: ABK = 1 Hadir, Regular = 1 Terlambat (NOT both!)
        $studentPrint = $this->actingAs($this->admin)->get('/attendances/print-rekap?bulan=2026-11');
        $studentPrint->assertStatus(200);
        $studentPrint->assertViewHas('totalHadir', 1);
        $studentPrint->assertViewHas('totalTerlambat', 1);
        $studentPrint->assertSee('08:00');
        $studentPrint->assertSee('14:00');
        $studentPrint->assertSee('Beatrice Kianindra, B.Art');
        $studentPrint->assertSee('Apris Leky Manu, S.T');

        // Verify Student Web Rekap: 5-day effectiveDays (21 weekdays in Nov 2026) & ABK excluded from lateStudentsList
        $studentRekap = $this->actingAs($this->admin)->get('/rekap-absensi?bulan=2026-11');
        $studentRekap->assertStatus(200);
        $studentRekap->assertViewHas('totalHadir', 1);
        $studentRekap->assertViewHas('totalTerlambat', 1);
        $this->assertCount(21, $studentRekap->viewData('effectiveDays'));
        $this->assertCount(1, $studentRekap->viewData('lateStudentsList'));

        // Verify Teacher Rekap: 7-day full month (30 days in Nov 2026), grandHadir = 0, grandTerlambat = 1
        $teacherRekap = $this->actingAs($this->admin)->get('/teacher-attendances/rekap?bulan=2026-11');
        $teacherRekap->assertStatus(200);
        $teacherRekap->assertViewHas('daysInMonth', 30);
        $teacherRekap->assertViewHas('grandHadir', 0);
        $teacherRekap->assertViewHas('grandTerlambat', 1);
        $teacherRekap->assertSee('13:20');
        $teacherRekap->assertSee('13:30');

        // Verify Teacher Print Rekap includes signatures & times
        $teacherPrint = $this->actingAs($this->admin)->get('/teacher-attendances/print-rekap?bulan=2026-11');
        $teacherPrint->assertStatus(200);
        $teacherPrint->assertSee('Beatrice Kianindra, B.Art');
        $teacherPrint->assertSee('Apris Leky Manu, S.T');
        $teacherPrint->assertSee('13:20');
    }
}
