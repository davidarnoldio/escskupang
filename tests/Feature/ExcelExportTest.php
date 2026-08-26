<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExcelExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_download_excel_export(): void
    {
        $response = $this->get(route('attendances.export'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_download_excel_attendance_report(): void
    {
        $user = User::factory()->create();
        Student::factory()->create([
            'nis' => '0003.26.0236',
            'nama' => 'Sierrafim Malelak',
            'kelas' => 'Primary A',
        ]);

        $response = $this->actingAs($user)->get(route('attendances.export', [
            'bulan' => now()->format('Y-m'),
            'kelas' => 'Primary A',
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');
        $this->assertTrue(str_contains($response->headers->get('Content-Disposition'), 'Laporan_Presensi_ESCS_'));
    }
}
