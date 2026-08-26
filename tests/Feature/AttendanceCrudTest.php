<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_attendances(): void
    {
        $response = $this->get(route('attendances.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_view_attendances_page(): void
    {
        $user = User::factory()->create();
        $student = Student::factory()->create(['nama' => 'Budi Santoso', 'kelas' => '10-IPA-1']);

        $response = $this->actingAs($user)->get(route('attendances.index', ['kelas' => '10-IPA-1']));

        $response->assertStatus(200);
        $response->assertSee('Budi Santoso');
    }

    public function test_authenticated_users_can_filter_attendances_by_date_and_class(): void
    {
        $user = User::factory()->create();
        $student1 = Student::factory()->create(['nama' => 'Ahmad Subagja', 'kelas' => '10-IPA-1']);
        $student2 = Student::factory()->create(['nama' => 'Citra Lestari', 'kelas' => '10-IPA-2']);

        $response = $this->actingAs($user)->get(route('attendances.index', [
            'tanggal' => now()->format('Y-m-d'),
            'kelas' => '10-IPA-1',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Ahmad Subagja');
        $response->assertDontSee('Citra Lestari');
    }

    public function test_authenticated_users_can_store_bulk_attendance(): void
    {
        $user = User::factory()->create();
        $student1 = Student::factory()->create(['kelas' => '10-IPA-1']);
        $student2 = Student::factory()->create(['kelas' => '10-IPA-1']);

        $today = now()->format('Y-m-d');

        $attendanceData = [
            'tanggal' => $today,
            'attendances' => [
                $student1->id => [
                    'status' => 'hadir',
                    'keterangan' => 'Tepat waktu',
                ],
                $student2->id => [
                    'status' => 'izin',
                    'keterangan' => 'Acara keluarga',
                ],
            ],
        ];

        $response = $this->actingAs($user)->post(route('attendances.store'), $attendanceData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'student_id' => $student1->id,
            'tanggal' => $today,
            'status' => 'hadir',
        ]);

        $this->assertDatabaseHas('attendances', [
            'student_id' => $student2->id,
            'tanggal' => $today,
            'status' => 'izin',
        ]);
    }

    public function test_authenticated_users_can_view_attendance_rekap_with_indicator_totals(): void
    {
        $user = User::factory()->create();
        $student1 = Student::factory()->create(['kelas' => '10-IPA-1']);
        $student2 = Student::factory()->create(['kelas' => '10-IPA-1']);

        $today = now()->format('Y-m-d');

        Attendance::create([
            'student_id' => $student1->id,
            'tanggal' => $today,
            'status' => 'hadir',
        ]);

        Attendance::create([
            'student_id' => $student2->id,
            'tanggal' => $today,
            'status' => 'alpa',
        ]);

        $response = $this->actingAs($user)->get(route('attendances.rekap'));

        $response->assertStatus(200);
        $response->assertViewHas('totalHadir', 1);
        $response->assertViewHas('totalIzin', 0);
        $response->assertViewHas('totalSakit', 0);
        $response->assertViewHas('totalAlpa', 1);
        $response->assertViewHas('rataRataKehadiran', 50.0);
        $response->assertSee('Rekap Presensi Real-Time');
    }
}
