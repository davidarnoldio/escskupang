<?php

namespace Tests\Feature;

use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HomeworkImageUploadTddTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_can_upload_homework_photo_submission()
    {
        Storage::fake('public');

        $teacher = User::factory()->create(['role' => 'guru', 'name' => 'Guru Kelas 1A']);

        $student = Student::factory()->create([
            'nama' => 'Siswa TDD PR',
            'kelas' => 'Kelas 1A',
        ]);

        $parent = User::factory()->create([
            'role' => 'orang_tua',
            'student_id' => $student->id,
        ]);

        $homework = Homework::create([
            'teacher_id' => $teacher->id,
            'judul' => 'Tugas Matematika Bab 1',
            'mata_pelajaran' => 'Matematika',
            'deskripsi' => 'Kerjakan soal 1-10',
            'kelas' => 'Kelas 1A',
            'deadline' => now()->addDays(2),
        ]);

        $fakePhoto = UploadedFile::fake()->image('jawaban_pr.jpg', 600, 600);

        $response = $this->actingAs($parent)->post(route('parent.submit-homework', $homework), [
            'catatan_siswa' => 'Berikut foto lembar jawaban PR saya.',
            'foto_pr' => $fakePhoto,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('homework_submissions', [
            'homework_id' => $homework->id,
            'student_id' => $student->id,
            'catatan_siswa' => 'Berikut foto lembar jawaban PR saya.',
        ]);

        $submission = HomeworkSubmission::first();
        $this->assertNotNull($submission->foto_pr);
        Storage::disk('public')->assertExists($submission->foto_pr);
    }

    public function test_teacher_can_grade_homework_submission()
    {
        $teacher = User::factory()->create([
            'role' => 'guru',
            'name' => 'Wali Kelas Kelas 1A',
        ]);

        $student = Student::factory()->create(['kelas' => 'Kelas 1A']);

        $homework = Homework::create([
            'teacher_id' => $teacher->id,
            'judul' => 'Tugas IPA',
            'mata_pelajaran' => 'IPA',
            'deskripsi' => 'Pengamatan tanaman',
            'kelas' => 'Kelas 1A',
            'deadline' => now()->addDays(2),
        ]);

        $submission = HomeworkSubmission::create([
            'homework_id' => $homework->id,
            'student_id' => $student->id,
            'catatan_siswa' => 'Jawaban IPA',
            'foto_pr' => 'pr_submissions/test.jpg',
        ]);

        $response = $this->actingAs($teacher)->post(route('homeworks.grade', $submission), [
            'nilai' => 95,
            'catatan_guru' => 'Bagus sekali, pertahankan!',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('homework_submissions', [
            'id' => $submission->id,
            'nilai' => 95,
            'catatan_guru' => 'Bagus sekali, pertahankan!',
        ]);
    }

    public function test_teacher_can_access_a4_homework_recap_print()
    {
        $teacher = User::factory()->create([
            'role' => 'guru',
            'name' => 'Wali Kelas Kelas 1A',
        ]);

        $homework = Homework::create([
            'teacher_id' => $teacher->id,
            'judul' => 'PR Bahasa Indonesia',
            'mata_pelajaran' => 'Bahasa Indonesia',
            'deskripsi' => 'Membaca puisi',
            'kelas' => 'Kelas 1A',
            'deadline' => now()->addDays(2),
        ]);

        $response = $this->actingAs($teacher)->get(route('homeworks.print-recap', $homework));

        $response->assertStatus(200);
        $response->assertSee('PR Bahasa Indonesia');
        $response->assertSee('REKAPITULASI NILAI TUGAS / PR SISWA');
    }

    public function test_parent_cannot_upload_invalid_file_type_for_homework()
    {
        Storage::fake('public');

        $teacher = User::factory()->create(['role' => 'guru']);
        $student = Student::factory()->create(['kelas' => 'Kelas 1A']);
        $parent = User::factory()->create([
            'role' => 'orang_tua',
            'student_id' => $student->id,
        ]);

        $homework = Homework::create([
            'teacher_id' => $teacher->id,
            'judul' => 'Tugas Seni',
            'mata_pelajaran' => 'Seni',
            'deskripsi' => 'Menggambar pemandangan',
            'kelas' => 'Kelas 1A',
            'deadline' => now()->addDays(2),
        ]);

        $invalidFile = UploadedFile::fake()->create('script.exe', 500);

        $response = $this->actingAs($parent)->post(route('parent.submit-homework', $homework), [
            'foto_pr' => $invalidFile,
        ]);

        $response->assertSessionHasErrors('foto_pr');
    }
}
