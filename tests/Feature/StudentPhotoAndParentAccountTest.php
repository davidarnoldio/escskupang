<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudentPhotoAndParentAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_storing_student_auto_creates_parent_account_with_student_sch_id_email(): void
    {
        $admin = User::factory()->create(['role' => 'guru', 'name' => 'Administrator']);

        $response = $this->actingAs($admin)->post(route('students.store'), [
            'nis' => '9999.26.0001',
            'nama' => 'Budi Santoso',
            'kelas' => 'Nursery',
            'jenis_kelamin' => 'L',
        ]);

        $response->assertRedirect(route('students.index'));
        $this->assertDatabaseHas('students', ['nis' => '9999.26.0001']);

        $student = Student::where('nis', '9999.26.0001')->first();
        $this->assertDatabaseHas('users', [
            'role' => 'orang_tua',
            'student_id' => $student->id,
            'email' => 'budi.santoso@student.sch.id',
        ]);
    }

    public function test_parent_login_redirects_directly_to_parent_dashboard(): void
    {
        $student = Student::factory()->create();
        $parent = User::factory()->create([
            'role' => 'orang_tua',
            'student_id' => $student->id,
            'email' => 'parent.test@student.sch.id',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'parent.test@student.sch.id',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('parent.dashboard'));
    }

    public function test_parent_photo_upload_route_has_been_removed(): void
    {
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('parent.upload-photo'));
    }

    public function test_parent_can_update_email_requiring_student_sch_id_suffix(): void
    {
        $student = Student::factory()->create();
        $parent = User::factory()->create([
            'role' => 'orang_tua',
            'student_id' => $student->id,
            'email' => 'old.email@student.sch.id',
        ]);

        // Failed attempt with invalid domain
        $responseFailed = $this->actingAs($parent)->post(route('parent.update-account'), [
            'email' => 'invalid.email@gmail.com',
        ]);
        $responseFailed->assertSessionHasErrors(['email']);

        // Success attempt with @student.sch.id domain
        $responseSuccess = $this->actingAs($parent)->post(route('parent.update-account'), [
            'email' => 'new.custom@student.sch.id',
        ]);
        $responseSuccess->assertRedirect(route('parent.dashboard'));
        $this->assertDatabaseHas('users', [
            'id' => $parent->id,
            'email' => 'new.custom@student.sch.id',
        ]);
    }

    public function test_photo_privacy_visibility_permissions(): void
    {
        $nurseryStudent = Student::factory()->create(['kelas' => 'Nursery', 'foto' => 'uploads/students/test.jpg']);
        $otherStudent = Student::factory()->create(['kelas' => 'Senior High']);

        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Administrator ESCS']);
        $nurseryTeacher = User::factory()->create(['role' => 'guru', 'name' => 'Nursery (Wali Kelas Nursery)']);
        $primaryATeacher = User::factory()->create(['role' => 'guru', 'name' => 'Primary A (Wali Kelas Primary A)']);
        $ownParent = User::factory()->create(['role' => 'orang_tua', 'student_id' => $nurseryStudent->id]);
        $otherParent = User::factory()->create(['role' => 'orang_tua', 'student_id' => $otherStudent->id]);

        // Admin, Nursery Teacher, and Own Parent CAN view photo
        $this->assertTrue($nurseryStudent->canViewPhoto($admin));
        $this->assertTrue($nurseryStudent->canViewPhoto($nurseryTeacher));
        $this->assertTrue($nurseryStudent->canViewPhoto($ownParent));

        // Other teacher and other parent CANNOT view photo
        $this->assertFalse($nurseryStudent->canViewPhoto($primaryATeacher));
        $this->assertFalse($nurseryStudent->canViewPhoto($otherParent));
    }

    public function test_artisan_parent_generate_command_creates_missing_parent_accounts(): void
    {
        $student = Student::factory()->create(['nama' => 'Rina Wijaya']);

        $this->artisan('parent:generate')
            ->expectsOutput("Created parent account for student Rina Wijaya: rina.wijaya@student.sch.id")
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'role' => 'orang_tua',
            'student_id' => $student->id,
            'email' => 'rina.wijaya@student.sch.id',
        ]);
    }

    public function test_student_show_route_renders_detail_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Administrator']);
        $student = Student::factory()->create(['nama' => 'Test Student Show', 'kelas' => 'Pre-K']);

        $response = $this->actingAs($admin)->get(route('students.show', $student));
        $response->assertStatus(200);
        $response->assertSee('Test Student Show');
        $response->assertSee('Pre-K');
    }
}
