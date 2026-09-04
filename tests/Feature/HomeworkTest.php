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

class HomeworkTest extends TestCase
{
    use RefreshDatabase;

    protected User $teacher;
    protected User $parentUser;
    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->teacher = User::factory()->create([
            'role' => 'guru',
            'name' => 'Guru Primary A',
            'assigned_class' => 'Primary A',
        ]);

        $this->student = Student::create([
            'nis' => '2001',
            'nama' => 'Ani Wijaya',
            'kelas' => 'Primary A',
            'jenis_kelamin' => 'P',
        ]);

        $this->parentUser = User::factory()->create([
            'role' => 'orang_tua',
            'student_id' => $this->student->id,
        ]);
    }

    public function test_teacher_can_create_homework_for_assigned_class(): void
    {
        $response = $this->actingAs($this->teacher)->post(route('homeworks.store'), [
            'target_type' => 'all',
            'kelas' => 'Primary A',
            'mata_pelajaran' => 'Matematika',
            'judul' => 'Latihan Soal Bab 1',
            'deskripsi' => 'Kerjakan hal 10-12',
            'deadline' => now()->addDays(2)->format('Y-m-d H:i:s'),
        ]);

        $response->assertRedirect(route('homeworks.index'));
        $this->assertDatabaseHas('homeworks', [
            'kelas' => 'Primary A',
            'mata_pelajaran' => 'Matematika',
            'judul' => 'Latihan Soal Bab 1',
            'student_id' => null,
        ]);
    }

    public function test_teacher_can_create_homework_for_specific_student(): void
    {
        $response = $this->actingAs($this->teacher)->post(route('homeworks.store'), [
            'target_type' => 'student',
            'student_id' => $this->student->id,
            'kelas' => 'Primary A',
            'mata_pelajaran' => 'Bahasa Inggris',
            'judul' => 'Tugas Remedial Grammer',
            'deskripsi' => 'Kerjakan modul 2',
            'deadline' => now()->addDays(3)->format('Y-m-d H:i:s'),
        ]);

        $response->assertRedirect(route('homeworks.index'));
        $this->assertDatabaseHas('homeworks', [
            'kelas' => 'Primary A',
            'student_id' => $this->student->id,
            'judul' => 'Tugas Remedial Grammer',
        ]);
    }

    public function test_parent_can_view_homework_and_upload_photo_submission(): void
    {
        Storage::fake('public');

        $homework = Homework::create([
            'teacher_id' => $this->teacher->id,
            'kelas' => 'Primary A',
            'mata_pelajaran' => 'Matematika',
            'judul' => 'PR Perkalian',
            'deskripsi' => 'Kerjakan soal 1-5',
            'deadline' => now()->addDays(2),
        ]);

        $response = $this->actingAs($this->parentUser)->get(route('parent.homeworks'));
        $response->assertStatus(200);
        $response->assertSee('PR Perkalian');

        $photo = UploadedFile::fake()->image('jawaban_pr.jpg');

        $submitResponse = $this->actingAs($this->parentUser)->post(route('parent.submit-homework', $homework), [
            'foto_pr' => $photo,
            'catatan_siswa' => 'Sudah dikerjakan rapi.',
        ]);

        $submitResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('homework_submissions', [
            'homework_id' => $homework->id,
            'student_id' => $this->student->id,
            'catatan_siswa' => 'Sudah dikerjakan rapi.',
        ]);
    }

    public function test_teacher_can_view_submissions_and_grade_homework(): void
    {
        $homework = Homework::create([
            'teacher_id' => $this->teacher->id,
            'kelas' => 'Primary A',
            'mata_pelajaran' => 'Matematika',
            'judul' => 'PR Perkalian',
            'deskripsi' => 'Kerjakan soal 1-5',
            'deadline' => now()->addDays(2),
        ]);

        $submission = HomeworkSubmission::create([
            'homework_id' => $homework->id,
            'student_id' => $this->student->id,
            'foto_pr' => 'pr_submissions/dummy.jpg',
            'submitted_at' => now(),
        ]);

        $recapResponse = $this->actingAs($this->teacher)->get(route('homeworks.submissions', $homework));
        $recapResponse->assertStatus(200);
        $recapResponse->assertSee('Ani Wijaya');
        $recapResponse->assertSee('Rekapan Nilai');

        $gradeResponse = $this->actingAs($this->teacher)->post(route('homeworks.grade', $submission), [
            'nilai' => 95,
            'catatan_guru' => 'Sangat bagus dan teliti!',
        ]);

        $submission->refresh();
        $this->assertEquals(95, $submission->nilai);
        $this->assertEquals('Sangat bagus dan teliti!', $submission->catatan_guru);
        $this->assertNotNull($submission->graded_at);
    }

    public function test_teacher_can_view_print_recap_page(): void
    {
        $homework = Homework::create([
            'teacher_id' => $this->teacher->id,
            'kelas' => 'Primary A',
            'mata_pelajaran' => 'Matematika',
            'judul' => 'PR Perkalian A4 Test',
            'deskripsi' => 'Kerjakan soal 1-5',
            'deadline' => now()->addDays(2),
        ]);

        $response = $this->actingAs($this->teacher)->get(route('homeworks.print-recap', $homework));
        $response->assertStatus(200);
        $response->assertSee('REKAPITULASI NILAI TUGAS / PR SISWA');
        $response->assertSee('Ani Wijaya');
    }
}
