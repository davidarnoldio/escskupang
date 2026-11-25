<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\TeacherAttendance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AttendanceIsolationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $teacher;
    private User $teacher2;

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

        $this->teacher2 = User::create([
            'name' => 'Guru Kelas 2',
            'email' => 'guru2@teacherntokupang',
            'password' => Hash::make('ntokupang123'),
            'role' => 'guru',
            'assigned_class' => 'Kelas 2',
            'is_active' => true,
        ]);
    }

    public function test_unmarked_students_do_not_default_to_hadir_and_saving_one_does_not_affect_others(): void
    {
        $s1 = Student::create(['nisn' => '1001', 'nis' => '1001', 'nama' => 'Siswa Satu', 'kelas' => 'Kelas 1', 'jenis_kelamin' => 'L']);
        $s2 = Student::create(['nisn' => '1002', 'nis' => '1002', 'nama' => 'Siswa Dua', 'kelas' => 'Kelas 1', 'jenis_kelamin' => 'P']);
        $s3 = Student::create(['nisn' => '1003', 'nis' => '1003', 'nama' => 'Siswa Tiga', 'kelas' => 'Kelas 1', 'jenis_kelamin' => 'L']);

        $today = now()->format('Y-m-d');

        // 1. Page must show "Belum absen" and MUST NOT pre-check any radio as checked
        $res = $this->actingAs($this->admin)->get('/attendances?tanggal=' . $today);
        $res->assertStatus(200);
        $res->assertSee('Belum absen');
        $this->assertStringNotContainsString('checked>', $res->getContent());

        // 2. Submit ONLY Siswa Satu as "izin", leaving Siswa Dua & Siswa Tiga unselected
        $post = $this->actingAs($this->admin)->post('/attendances', [
            'tanggal' => $today,
            'attendances' => [
                $s1->id => ['status' => 'izin', 'keterangan' => 'Acara keluarga'],
                $s2->id => ['keterangan' => null],
                $s3->id => ['keterangan' => null],
            ],
        ]);
        $post->assertRedirect();
        $post->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'student_id' => $s1->id,
            'tanggal' => $today,
            'status' => 'izin',
        ]);
        $this->assertDatabaseMissing('attendances', ['student_id' => $s2->id, 'tanggal' => $today]);
        $this->assertDatabaseMissing('attendances', ['student_id' => $s3->id, 'tanggal' => $today]);

        // 3. Submit Siswa Dua as "hadir", Siswa Tiga still untouched
        $this->actingAs($this->admin)->post('/attendances', [
            'tanggal' => $today,
            'attendances' => [
                $s1->id => ['status' => 'izin', 'keterangan' => 'Acara keluarga'],
                $s2->id => ['status' => 'hadir', 'keterangan' => 'Masuk manual'],
                $s3->id => ['keterangan' => null],
            ],
        ])->assertRedirect();

        $this->assertDatabaseHas('attendances', ['student_id' => $s1->id, 'status' => 'izin']);
        $this->assertDatabaseHas('attendances', ['student_id' => $s2->id, 'status' => 'hadir']);
        $this->assertDatabaseMissing('attendances', ['student_id' => $s3->id, 'tanggal' => $today]);
    }

    public function test_changing_student_from_hadir_to_sakit_clears_checkin_times(): void
    {
        $s1 = Student::create(['nisn' => '1004', 'nis' => '1004', 'nama' => 'Siswa Empat', 'kelas' => 'Kelas 1', 'jenis_kelamin' => 'L']);
        $today = now()->format('Y-m-d');

        Attendance::create([
            'student_id' => $s1->id,
            'tanggal' => $today,
            'jam_masuk' => '07:10:00',
            'jam_pulang' => '14:05:00',
            'status' => 'hadir',
        ]);

        $this->actingAs($this->admin)->post('/attendances', [
            'tanggal' => $today,
            'attendances' => [
                $s1->id => ['status' => 'sakit', 'keterangan' => 'Demam'],
            ],
        ])->assertRedirect();

        $this->assertDatabaseHas('attendances', [
            'student_id' => $s1->id,
            'tanggal' => $today,
            'status' => 'sakit',
            'jam_masuk' => null,
            'jam_pulang' => null,
        ]);
    }

    public function test_teacher_cannot_store_student_attendance(): void
    {
        $s1 = Student::create(['nisn' => '1005', 'nis' => '1005', 'nama' => 'Siswa Lima', 'kelas' => 'Kelas 1', 'jenis_kelamin' => 'L']);

        $this->actingAs($this->teacher)
            ->post('/attendances', [
                'tanggal' => now()->format('Y-m-d'),
                'attendances' => [
                    $s1->id => ['status' => 'hadir'],
                ],
            ])
            ->assertStatus(403);
    }

    public function test_teacher_attendance_is_isolated_and_non_hadir_clears_times_and_delete_works(): void
    {
        $today = now()->format('Y-m-d');

        // 1. Mark teacher1 as hadir with times -> teacher2 must stay untouched
        $this->actingAs($this->admin)->post('/teacher-attendances/status', [
            'teacher_id' => $this->teacher->id,
            'tanggal' => $today,
            'status' => 'hadir',
            'jam_masuk' => '07:05',
            'jam_pulang' => '14:05',
        ])->assertRedirect();

        $this->assertDatabaseHas('teacher_attendances', [
            'teacher_id' => $this->teacher->id,
            'tanggal' => $today,
            'status' => 'hadir',
            'jam_masuk' => '07:05:00',
        ]);
        $this->assertDatabaseMissing('teacher_attendances', [
            'teacher_id' => $this->teacher2->id,
            'tanggal' => $today,
        ]);

        // 2. Change teacher1 from hadir to izin (even if stale jam_masuk submitted) -> clears jam_masuk/jam_pulang
        $this->actingAs($this->admin)->post('/teacher-attendances/status', [
            'teacher_id' => $this->teacher->id,
            'tanggal' => $today,
            'status' => 'izin',
            'jam_masuk' => '07:05',
            'jam_pulang' => '14:05',
            'keterangan' => 'Izin urusan dinas',
        ])->assertRedirect();

        $this->assertDatabaseHas('teacher_attendances', [
            'teacher_id' => $this->teacher->id,
            'tanggal' => $today,
            'status' => 'izin',
            'jam_masuk' => null,
            'jam_pulang' => null,
        ]);

        // 3. Delete teacher1 attendance
        $this->actingAs($this->admin)->post('/teacher-attendances/status', [
            'teacher_id' => $this->teacher->id,
            'tanggal' => $today,
            'status' => 'hapus',
        ])->assertRedirect();

        $this->assertDatabaseMissing('teacher_attendances', [
            'teacher_id' => $this->teacher->id,
            'tanggal' => $today,
        ]);
    }
}
