<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Setting;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdditionalImprovementsTest extends TestCase
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
            'name' => 'Guru Kelas 1A',
            'email' => 'guru1a@teacherntokupang',
            'password' => Hash::make('ntokupang123'),
            'role' => 'guru',
            'assigned_class' => 'Kelas 1A',
            'is_active' => true,
        ]);

        Setting::set('jam_masuk', '07:00');
        Setting::set('jam_terlambat', '07:30');
        Setting::set('jam_pulang', '14:00');
        Setting::set('jam_terlambat_abk', '08:30');
        Setting::set('jam_pulang_abk', '13:00');
    }

    public function test_external_qr_api_is_used_across_cards_modal_and_parent_dashboard_with_nisn_fallback(): void
    {
        $student = Student::create([
            'nisn' => '99001122',
            'nis' => null,
            'nama' => 'Siswa Tanpa NIS Lama',
            'kelas' => 'Kelas 1A',
            'jenis_kelamin' => 'L',
        ]);

        $parent = User::create([
            'name' => 'Orang Tua (Siswa Tanpa NIS Lama)',
            'email' => 'siswa.lama@studentntokupang',
            'password' => Hash::make('Rahasia123'),
            'plain_password' => 'Rahasia123',
            'role' => 'orang_tua',
            'student_id' => $student->id,
            'is_active' => true,
        ]);

        $parentRes = $this->actingAs($parent)->get('/parent/dashboard');
        $parentRes->assertStatus(200);
        $parentRes->assertSee('https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=99001122', false);

        $studentCard = $this->actingAs($this->admin)->get('/students/' . $student->id . '/qr-card');
        $studentCard->assertStatus(200);
        $studentCard->assertSee('https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=99001122', false);
        $studentCard->assertSee('https://chart.googleapis.com/chart?chs=180x180&cht=qr&chl=', false);

        $teacherCard = $this->actingAs($this->admin)->get('/teachers/' . $this->teacher->id . '/qr-card');
        $teacherCard->assertStatus(200);
        $teacherCard->assertSee('https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=GURU-' . $this->teacher->id, false);

        // Student index dropdown must include custom class 'Kelas 1A' & use external QR modal
        $studentsIndex = $this->actingAs($this->admin)->get('/students');
        $studentsIndex->assertStatus(200);
        $studentsIndex->assertSee('Kelas 1A');
        $studentsIndex->assertSee('https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=', false);
    }

    public function test_dashboard_separates_late_regular_students_from_hadir_while_abk_stays_hadir(): void
    {
        $regularLate = Student::create(['nisn' => '6001', 'nama' => 'Reguler Telat', 'kelas' => 'Kelas 1', 'jenis_kelamin' => 'L', 'is_abk' => false]);
        $regularOnTime = Student::create(['nisn' => '6002', 'nama' => 'Reguler Tepat', 'kelas' => 'Kelas 1', 'jenis_kelamin' => 'P', 'is_abk' => false]);
        $abkLate = Student::create(['nisn' => '6003', 'nama' => 'ABK Hadir', 'kelas' => 'Kelas 1', 'jenis_kelamin' => 'L', 'is_abk' => true]);

        $today = now()->format('Y-m-d');

        Attendance::create(['student_id' => $regularLate->id, 'tanggal' => $today, 'jam_masuk' => '08:00:00', 'status' => 'hadir']);
        Attendance::create(['student_id' => $regularOnTime->id, 'tanggal' => $today, 'jam_masuk' => '07:05:00', 'status' => 'hadir']);
        Attendance::create(['student_id' => $abkLate->id, 'tanggal' => $today, 'jam_masuk' => '09:00:00', 'status' => 'hadir']);

        $res = $this->actingAs($this->admin)->get('/dashboard');
        $res->assertStatus(200);
        $res->assertViewHas('hadirHariIni', 2); // regularOnTime + abkLate
        $res->assertViewHas('terlambatHariIni', 1); // regularLate
        $res->assertSee('1 Terlambat');
    }

    public function test_manual_student_attendance_saves_optional_checkin_and_checkout_times_with_auto_notes(): void
    {
        $student = Student::create(['nisn' => '7001', 'nama' => 'Siswa Manual Jam', 'kelas' => 'Kelas 1', 'jenis_kelamin' => 'L', 'is_abk' => false]);
        $today = now()->format('Y-m-d');

        $this->actingAs($this->admin)->post('/attendances', [
            'tanggal' => $today,
            'attendances' => [
                $student->id => [
                    'status' => 'hadir',
                    'jam_masuk' => '08:00',
                    'jam_pulang' => '13:30',
                    'keterangan' => null,
                ],
            ],
        ])->assertRedirect();

        $att = Attendance::where('student_id', $student->id)->where('tanggal', $today)->firstOrFail();
        $this->assertSame('08:00:00', $att->jam_masuk);
        $this->assertSame('13:30:00', $att->jam_pulang);
        $this->assertStringContainsString('Terlambat 30 menit', $att->keterangan);
        $this->assertStringContainsString('Pulang Awal (Duluan 30 menit)', $att->keterangan);
    }
}
