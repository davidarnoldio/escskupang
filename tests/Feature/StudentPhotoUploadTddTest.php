<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudentPhotoUploadTddTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_student_with_avatar_photo()
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => 'admin']);
        $fakePhoto = UploadedFile::fake()->image('pas_foto_siswa.jpg', 400, 600);

        $response = $this->actingAs($admin)->post(route('students.store'), [
            'nis' => '10099',
            'nama' => 'Siswa Foto Test',
            'kelas' => 'Kelas 1A',
            'jenis_kelamin' => 'L',
            'foto' => $fakePhoto,
        ]);

        $response->assertRedirect(route('students.index'));
        $response->assertSessionHas('success');

        $student = Student::where('nis', '10099')->first();
        $this->assertNotNull($student);
        $this->assertNotNull($student->foto);
        Storage::disk('public')->assertExists($student->foto);
    }

    public function test_admin_can_update_student_photo()
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => 'admin']);
        $student = Student::factory()->create([
            'nama' => 'Siswa Update Foto',
        ]);

        $newPhoto = UploadedFile::fake()->image('foto_baru.jpg', 400, 600);

        $response = $this->actingAs($admin)->put(route('students.update', $student), [
            'nis' => $student->nis,
            'nama' => 'Siswa Update Foto Changed',
            'kelas' => $student->kelas,
            'jenis_kelamin' => $student->jenis_kelamin,
            'foto' => $newPhoto,
        ]);

        $response->assertRedirect(route('students.index'));

        $student->refresh();
        $this->assertNotNull($student->foto);
        Storage::disk('public')->assertExists($student->foto);
    }

    public function test_user_can_view_student_qr_card()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = Student::factory()->create([
            'nama' => 'Siswa Kartu QR',
            'nis' => '12345',
        ]);

        $response = $this->actingAs($admin)->get(route('students.qr-card', $student));

        $response->assertStatus(200);
        $response->assertSee('Siswa Kartu QR');
        $response->assertSee('KARTU TANDA PELAJAR');
    }
}
