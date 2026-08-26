<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AbkStudentAndLogoProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_accessing_main_dashboard_is_redirected_to_parent_dashboard(): void
    {
        $student = Student::factory()->create(['nama' => 'Siswa Test']);
        $parent = User::factory()->create([
            'role' => 'orang_tua',
            'email' => 'parent@student.sch.id',
            'student_id' => $student->id,
        ]);

        $response = $this->actingAs($parent)->get(route('dashboard'));

        $response->assertRedirect(route('parent.dashboard'));
    }

    public function test_can_create_and_update_abk_student(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Create ABK Student
        $response = $this->actingAs($admin)->post(route('students.store'), [
            'nis' => '9999.00.123',
            'nama' => 'Budi ABK',
            'kelas' => 'Pre-K',
            'jenis_kelamin' => 'L',
            'is_abk' => '1',
        ]);

        $response->assertRedirect(route('students.index'));
        $this->assertDatabaseHas('students', [
            'nis' => '9999.00.123',
            'is_abk' => true,
        ]);

        $student = Student::where('nis', '9999.00.123')->first();

        // Update ABK Student back to regular
        $updateResponse = $this->actingAs($admin)->put(route('students.update', $student), [
            'nis' => '9999.00.123',
            'nama' => 'Budi ABK Regular',
            'kelas' => 'Pre-K',
            'jenis_kelamin' => 'L',
            'is_abk' => '0',
        ]);

        $updateResponse->assertRedirect(route('students.index'));
        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'is_abk' => false,
        ]);
    }

    public function test_abk_student_operating_hours_settings_and_qr_scan_tardiness(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Update settings for ABK
        $this->actingAs($admin)->post(route('settings.update'), [
            'jam_masuk' => '07:00',
            'jam_terlambat' => '07:30',
            'jam_pulang' => '14:00',
            'jam_masuk_abk' => '08:00',
            'jam_terlambat_abk' => '08:30',
            'jam_pulang_abk' => '13:00',
        ]);

        $this->assertEquals('08:30', Setting::get('jam_terlambat_abk'));

        $abkStudent = Student::factory()->create([
            'nis' => '8888.00.111',
            'nama' => 'Cika ABK',
            'is_abk' => true,
        ]);

        // Scan process for ABK student
        $scanResponse = $this->actingAs($admin)->postJson(route('qr.process'), [
            'nis' => '8888.00.111',
        ]);

        $scanResponse->assertStatus(200);
        $scanResponse->assertJson(['success' => true]);
    }
}
