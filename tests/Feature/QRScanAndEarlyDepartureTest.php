<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class QRScanAndEarlyDepartureTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $teacher;
    private Student $student;

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

        $this->student = Student::create([
            'nisn' => '2001',
            'nis' => '2001',
            'nama' => 'Siswa QR',
            'kelas' => 'Kelas 1',
            'jenis_kelamin' => 'L',
            'is_abk' => false,
        ]);

        Setting::set('jam_masuk', '07:00');
        Setting::set('jam_terlambat', '07:30');
        Setting::set('jam_pulang', '14:00');
    }

    public function test_teacher_cannot_access_qr_scanner_page_or_process_endpoint(): void
    {
        $this->actingAs($this->teacher)->get('/scan-qr')->assertStatus(403);
        $this->actingAs($this->teacher)->postJson('/scan-qr/process', ['code' => '2001', 'mode' => 'masuk'])->assertStatus(403);
    }

    public function test_explicit_masuk_and_pulang_modes_prevent_same_second_double_scan_and_log_early_departure(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-25 07:15:00', 'Asia/Makassar'));

        // 1. Mode pulang before check-in is rejected
        $this->actingAs($this->admin)
            ->postJson('/scan-qr/process', ['code' => '2001', 'mode' => 'pulang'])
            ->assertStatus(200)
            ->assertJson(['success' => false, 'not_checked_in' => true]);

        // 2. Mode masuk succeeds & leaves jam_pulang null
        $this->actingAs($this->admin)
            ->postJson('/scan-qr/process', ['code' => '2001', 'mode' => 'masuk'])
            ->assertStatus(200)
            ->assertJson(['success' => true, 'scan_type' => 'masuk']);

        // 3. Duplicate scan in mode masuk is rejected & does NOT fill jam_pulang
        $this->actingAs($this->admin)
            ->postJson('/scan-qr/process', ['code' => '2001', 'mode' => 'masuk'])
            ->assertStatus(200)
            ->assertJson(['success' => false, 'already_checked_in' => true]);

        $this->assertDatabaseHas('attendances', [
            'student_id' => $this->student->id,
            'tanggal' => '2026-11-25',
            'jam_masuk' => '07:15:00',
            'jam_pulang' => null,
        ]);

        // 4. Switch to mode pulang early (13:00 vs 14:00) -> records Pulang Awal with minute difference
        Carbon::setTestNow(Carbon::parse('2026-11-25 13:00:00', 'Asia/Makassar'));

        $resOut = $this->actingAs($this->admin)
            ->postJson('/scan-qr/process', ['code' => '2001', 'mode' => 'pulang'])
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'scan_type' => 'pulang',
                'attendance' => ['is_early' => true],
            ]);

        $this->assertDatabaseHas('attendances', [
            'student_id' => $this->student->id,
            'tanggal' => '2026-11-25',
            'jam_masuk' => '07:15:00',
            'jam_pulang' => '13:00:00',
        ]);
        $this->assertStringContainsString('60 menit sebelum jam pulang resmi', $resOut->json('message'));
        $att = \App\Models\Attendance::where('student_id', $this->student->id)->first();
        $this->assertNotNull($att);
        $this->assertStringContainsString('Pulang Awal (Duluan 60 menit)', $att->keterangan);

        // 5. Third scan in any mode rejected
        $this->actingAs($this->admin)
            ->postJson('/scan-qr/process', ['code' => '2001', 'mode' => 'pulang'])
            ->assertStatus(200)
            ->assertJson(['success' => false, 'already_completed' => true]);

        Carbon::setTestNow();
    }

    public function test_teacher_early_departure_is_logged_with_minutes(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-25 07:10:00', 'Asia/Makassar'));

        $this->actingAs($this->admin)
            ->postJson('/scan-qr/process', ['code' => 'GURU-' . $this->teacher->id, 'mode' => 'masuk'])
            ->assertStatus(200)
            ->assertJson(['success' => true, 'scan_type' => 'masuk']);

        Carbon::setTestNow(Carbon::parse('2026-11-25 13:30:00', 'Asia/Makassar'));

        $this->actingAs($this->admin)
            ->postJson('/scan-qr/process', ['code' => 'GURU-' . $this->teacher->id, 'mode' => 'pulang'])
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'scan_type' => 'pulang',
                'attendance' => ['is_early' => true],
            ]);

        $att = \App\Models\TeacherAttendance::where('teacher_id', $this->teacher->id)->first();
        $this->assertNotNull($att);
        $this->assertStringContainsString('Pulang Awal (Duluan 30 menit)', $att->keterangan);

        Carbon::setTestNow();
    }

    public function test_qr_scanner_displays_all_today_attendances_without_10_limit_and_syncs_status_badges(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-25 08:00:00', 'Asia/Makassar'));
        $today = '2026-11-25';

        // Seed 12 active students with today's attendance (10 hadir, 1 izin, 1 sakit)
        for ($i = 1; $i <= 12; $i++) {
            $s = Student::create([
                'nisn' => '9100' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'nama' => 'Siswa Aktivitas ' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'kelas' => 'Kelas 1',
                'jenis_kelamin' => 'L',
            ]);
            $status = match ($i) {
                11 => 'izin',
                12 => 'sakit',
                default => 'hadir',
            };
            \App\Models\Attendance::create([
                'student_id' => $s->id,
                'tanggal' => $today,
                'status' => $status,
                'jam_masuk' => $status === 'hadir' ? '07:10:00' : null,
            ]);
        }

        // Add 2 teachers with attendance today (1 hadir, 1 alpa)
        $teacher2 = User::create([
            'name' => 'Guru Dua',
            'email' => 'guru2@teacherntokupang',
            'password' => Hash::make('ntokupang123'),
            'role' => 'guru',
            'assigned_class' => 'Kelas 2',
            'is_active' => true,
        ]);
        \App\Models\TeacherAttendance::create([
            'teacher_id' => $this->teacher->id,
            'tanggal' => $today,
            'status' => 'hadir',
            'jam_masuk' => '07:05:00',
        ]);
        \App\Models\TeacherAttendance::create([
            'teacher_id' => $teacher2->id,
            'tanggal' => $today,
            'status' => 'alpa',
        ]);

        $response = $this->actingAs($this->admin)->get('/scan-qr');
        $response->assertStatus(200);
        $response->assertSee('<span id="today-activity-count-students">12</span>', false);
        $response->assertSee('<span id="today-activity-count-teachers">2</span>', false);
        $response->assertSee('Siswa Aktivitas 12');
        $response->assertSee('📝 IZIN');
        $response->assertSee('🏥 SAKIT');
        $response->assertSee('❌ ALPA');

        Carbon::setTestNow();
    }
}
