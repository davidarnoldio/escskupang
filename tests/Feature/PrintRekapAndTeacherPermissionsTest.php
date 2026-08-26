<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrintRekapAndTeacherPermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_print_rekap_absensi_renders_officially_styled_view(): void
    {
        $student = Student::factory()->create(['kelas' => 'Kindergarten', 'nama' => 'Audlyn Wijaya Ang']);
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('attendances.print-rekap', [
            'bulan' => now()->format('Y-m'),
            'kelas' => 'Kindergarten',
        ]));

        $response->assertStatus(200);
        $response->assertSee('REKAP ABSENSI SISWA');
        $response->assertSee('Audlyn Wijaya Ang');
        $response->assertSee('TOTAL KELAS');
        $response->assertSee('Wali Kelas');
    }

    public function test_homeroom_teacher_cannot_see_pilih_kelas_dropdown_in_daily_attendance(): void
    {
        $teacher = User::factory()->create([
            'role' => 'guru',
            'name' => 'Primary C (Wali Kelas Primary C)',
        ]);

        $response = $this->actingAs($teacher)->get(route('attendances.index'));

        $response->assertStatus(200);
        $response->assertSee('Kelas Primary C (Wali Kelas)');
        $response->assertDontSee('option value="Kindergarten"', false);
    }

    public function test_letters_reception_page_displays_uploaded_parent_letters(): void
    {
        $student = Student::factory()->create(['kelas' => 'Pre-K', 'nama' => 'Claire Koehuan']);
        $teacher = User::factory()->create(['role' => 'guru', 'name' => 'Pre-K (Wali Kelas Pre-K)']);

        Attendance::factory()->create([
            'student_id' => $student->id,
            'tanggal' => now()->format('Y-m-d'),
            'status' => 'sakit',
            'surat_izin' => 'uploads/letters/test_letter.jpg',
            'keterangan' => 'Surat Sakit Dokter',
        ]);

        $response = $this->actingAs($teacher)->get(route('attendances.letters'));

        $response->assertStatus(200);
        $response->assertSee('Claire Koehuan');
        $response->assertSee('Surat Sakit Dokter');
    }
}
