<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_dashboard(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_view_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Portal Presensi ESCS Kupang');
    }

    public function test_dashboard_displays_accurate_statistics_and_recent_students(): void
    {
        $user = User::factory()->create();

        $student1 = Student::factory()->create(['nis' => '10201', 'nama' => 'Ahmad Subagja', 'kelas' => '10-IPA-1']);
        $student2 = Student::factory()->create(['nis' => '10202', 'nama' => 'Budi Santoso', 'kelas' => '10-IPA-1']);
        $student3 = Student::factory()->create(['nis' => '10203', 'nama' => 'Citra Lestari', 'kelas' => '10-IPA-2']);

        $today = now()->format('Y-m-d');

        // Student 1: Hadir today
        Attendance::create([
            'student_id' => $student1->id,
            'tanggal' => $today,
            'status' => 'hadir',
        ]);

        // Student 2: Izin today
        Attendance::create([
            'student_id' => $student2->id,
            'tanggal' => $today,
            'status' => 'izin',
        ]);

        // Student 3: Alpa today
        Attendance::create([
            'student_id' => $student3->id,
            'tanggal' => $today,
            'status' => 'alpa',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('totalSiswa', 3);
        $response->assertViewHas('hadirHariIni', 1);
        $response->assertViewHas('izinSakitHariIni', 1);
        $response->assertViewHas('alpaHariIni', 1);
        $response->assertSee('Ahmad Subagja');
        $response->assertSee('Budi Santoso');
        $response->assertSee('Citra Lestari');
    }
}
