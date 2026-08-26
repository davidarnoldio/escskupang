<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TeacherClassScopingAndPermissionLetterTest extends TestCase
{
    use RefreshDatabase;

    public function test_homeroom_teacher_only_sees_students_in_assigned_class(): void
    {
        $prekStudent = Student::factory()->create(['nama' => 'PreK Student', 'kelas' => 'Pre-K']);
        $nurseryStudent = Student::factory()->create(['nama' => 'Nursery Student', 'kelas' => 'Nursery']);

        $prekTeacher = User::factory()->create([
            'role' => 'guru',
            'name' => 'Pre-K (Wali Kelas Pre-K)',
        ]);

        $response = $this->actingAs($prekTeacher)->get(route('students.index'));

        $response->assertStatus(200);
        $response->assertSee('PreK Student');
        $response->assertDontSee('Nursery Student');
    }

    public function test_homeroom_teacher_dashboard_statistics_scoped_to_assigned_class(): void
    {
        Student::factory()->create(['kelas' => 'Pre-K']);
        Student::factory()->create(['kelas' => 'Pre-K']);
        Student::factory()->create(['kelas' => 'Nursery']);

        $prekTeacher = User::factory()->create([
            'role' => 'guru',
            'name' => 'Pre-K (Wali Kelas Pre-K)',
        ]);

        $response = $this->actingAs($prekTeacher)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Total Siswa');
    }

    public function test_parent_can_upload_permission_letter_photo_for_attendance(): void
    {
        Storage::fake('public');
        $student = Student::factory()->create(['kelas' => 'Pre-K']);
        $parent = User::factory()->create([
            'role' => 'orang_tua',
            'student_id' => $student->id,
            'email' => 'parent.letter@student.sch.id',
        ]);

        $file = UploadedFile::fake()->create('surat_dokter.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($parent)->post(route('parent.upload-letter'), [
            'tanggal' => now()->format('Y-m-d'),
            'status' => 'sakit',
            'keterangan' => 'Sakit Demam dengan Surat Dokter',
            'surat_izin' => $file,
        ]);

        $response->assertRedirect(route('parent.dashboard'));

        $attendance = Attendance::where('student_id', $student->id)
            ->where('tanggal', now()->format('Y-m-d'))
            ->first();

        $this->assertNotNull($attendance);
        $this->assertEquals('sakit', $attendance->status);
        $this->assertNotNull($attendance->surat_izin);
        $this->assertStringContainsString('uploads/letters/', $attendance->surat_izin);
    }

    public function test_teacher_can_record_libur_status_attendance(): void
    {
        $student = Student::factory()->create(['kelas' => 'Pre-K']);
        $teacher = User::factory()->create([
            'role' => 'guru',
            'name' => 'Pre-K (Wali Kelas Pre-K)',
        ]);

        $today = now()->format('Y-m-d');

        $response = $this->actingAs($teacher)->post(route('attendances.store'), [
            'tanggal' => $today,
            'attendances' => [
                $student->id => [
                    'status' => 'libur',
                    'keterangan' => 'Libur Nasional',
                ],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('attendances', [
            'student_id' => $student->id,
            'tanggal' => $today,
            'status' => 'libur',
            'keterangan' => 'Libur Nasional',
        ]);
    }
}
