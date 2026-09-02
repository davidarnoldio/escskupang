<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QrAttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_qr_scanner(): void
    {
        $response = $this->get(route('qr.scan'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_view_qr_scanner_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('qr.scan'));

        $response->assertStatus(200);
        $response->assertSee('Scan QR Code Presensi');
    }

    public function test_scanning_valid_student_nis_records_attendance(): void
    {
        $user = User::factory()->create();
        $student = Student::factory()->create(['nis' => '10201', 'nama' => 'Ahmad Subagja']);

        $response = $this->actingAs($user)->postJson(route('qr.process'), [
            'nis' => '10201',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'student' => [
                'nis' => '10201',
                'nama' => 'Ahmad Subagja',
            ],
        ]);

        $this->assertDatabaseHas('attendances', [
            'student_id' => $student->id,
            'tanggal' => now()->format('Y-m-d'),
            'status' => 'hadir',
        ]);
    }

    public function test_scanning_invalid_student_nis_returns_error(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('qr.process'), [
            'nis' => '99999',
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
        ]);
    }

    public function test_scanning_already_recorded_student_updates_and_returns_success(): void
    {
        $user = User::factory()->create();
        $student = Student::factory()->create(['nis' => '10201']);

        // Record initial attendance as izin
        Attendance::create([
            'student_id' => $student->id,
            'tanggal' => now()->format('Y-m-d'),
            'status' => 'izin',
        ]);

        // Scanning QR updates status to hadir
        $response = $this->actingAs($user)->postJson(route('qr.process'), [
            'nis' => '10201',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('attendances', [
            'student_id' => $student->id,
            'tanggal' => now()->format('Y-m-d'),
            'status' => 'hadir',
        ]);
    }

    public function test_authenticated_users_can_view_student_qr_card(): void
    {
        $user = User::factory()->create();
        $student = Student::factory()->create(['nama' => 'Ahmad Subagja']);

        $response = $this->actingAs($user)->get(route('students.qr-card', $student));

        $response->assertStatus(200);
        $response->assertSee('Kartu Pelajar');
        $response->assertSee('Ahmad Subagja');
    }

    public function test_scanning_json_and_prefixed_qr_payloads_works(): void
    {
        $user = User::factory()->create();
        $student = Student::factory()->create(['nis' => '0003.26.0236', 'nama' => 'Sierrafim Malelak']);

        // JSON payload scan
        $responseJson = $this->actingAs($user)->postJson(route('qr.process'), [
            'nis' => json_encode(['nis' => '0003.26.0236']),
        ]);
        $responseJson->assertStatus(200);
        $responseJson->assertJson(['success' => true]);

        // Prefixed string scan
        $responsePrefixed = $this->actingAs($user)->postJson(route('qr.process'), [
            'nis' => 'NIS: 0003.26.0236',
        ]);
        $responsePrefixed->assertStatus(200);
        $responsePrefixed->assertJson(['success' => true]);
    }

    public function test_scanning_qr_when_late_computes_exact_late_minutes_starting_from_threshold_time(): void
    {
        $user = User::factory()->create();
        $student = Student::factory()->create(['nis' => '10202', 'nama' => 'Budi Late Student', 'is_abk' => false]);

        // Mock current time to 15:20 WITA
        \Carbon\Carbon::setTestNow(\Carbon\Carbon::parse(now()->format('Y-m-d') . ' 15:20:00', 'Asia/Makassar'));

        $response = $this->actingAs($user)->postJson(route('qr.process'), [
            'nis' => '10202',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'attendance' => [
                'is_late' => true,
                'late_minutes' => 470,
            ],
        ]);

        \Carbon\Carbon::setTestNow();
    }

    public function test_rescanning_qr_on_same_day_preserves_initial_scan_time_and_does_not_overwrite_late_minutes(): void
    {
        $user = User::factory()->create();
        $student = Student::factory()->create(['nis' => '10740', 'nama' => 'Sierrafim Malelak', 'is_abk' => false]);
        $todayStr = now()->format('Y-m-d');

        // First scan at 07:40 WITA (10 minutes late)
        \Carbon\Carbon::setTestNow(\Carbon\Carbon::parse($todayStr . ' 07:40:00', 'Asia/Makassar'));
        $firstResponse = $this->actingAs($user)->postJson(route('qr.process'), [
            'nis' => '10740',
        ]);

        $firstResponse->assertStatus(200);
        $firstResponse->assertJson([
            'success' => true,
            'attendance' => [
                'waktu' => '07:40:00',
                'late_minutes' => 10,
            ],
        ]);

        // Second scan at 09:15 WITA (95 minutes late if overwritten)
        \Carbon\Carbon::setTestNow(\Carbon\Carbon::parse($todayStr . ' 09:15:00', 'Asia/Makassar'));
        $secondResponse = $this->actingAs($user)->postJson(route('qr.process'), [
            'nis' => '10740',
        ]);

        $secondResponse->assertStatus(200);
        $secondResponse->assertJson([
            'success' => true,
            'already_scanned' => true,
            'attendance' => [
                'waktu' => '07:40:00', // MUST STILL BE ORIGINAL 07:40:00!
                'late_minutes' => 10,  // MUST STILL BE ORIGINAL 10 MINUTES!
            ],
        ]);

        $this->assertDatabaseHas('attendances', [
            'student_id' => $student->id,
            'tanggal' => $todayStr,
            'keterangan' => 'Scan QR [07:40:00] - Terlambat 10 menit',
        ]);

        \Carbon\Carbon::setTestNow();
    }
}
