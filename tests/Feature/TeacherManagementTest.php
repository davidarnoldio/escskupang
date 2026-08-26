<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_teacher_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create([
            'role' => 'guru',
            'name' => 'Wali Kelas Primary A',
            'email' => 'primary.a@escs-kupang.sch.id',
            'assigned_class' => 'Primary A',
        ]);

        $response = $this->actingAs($admin)->get(route('teachers.index'));

        $response->assertStatus(200);
        $response->assertSee('Wali Kelas Primary A');
        $response->assertSee('primary.a@escs-kupang.sch.id');
        $response->assertSee('Primary A');
    }

    public function test_admin_can_create_new_teacher_with_assigned_class(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('teachers.store'), [
            'name' => 'Guru Baru Kindergarten',
            'email' => 'guru.kindergarten@escs-kupang.sch.id',
            'password' => 'password123',
            'assigned_class' => 'Kindergarten',
        ]);

        $response->assertRedirect(route('teachers.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'Guru Baru Kindergarten',
            'email' => 'guru.kindergarten@escs-kupang.sch.id',
            'role' => 'guru',
            'assigned_class' => 'Kindergarten',
        ]);
    }

    public function test_admin_can_reassign_teacher_class_level_and_teacher_automatically_scopes_to_new_class(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $primaryAStudent = Student::factory()->create(['nama' => 'Murid A', 'kelas' => 'Primary A']);
        $seniorHighStudent = Student::factory()->create(['nama' => 'Murid Senior High', 'kelas' => 'Senior High']);

        $teacher = User::factory()->create([
            'role' => 'guru',
            'name' => 'Guru Pengajar',
            'email' => 'guru.pengajar@escs-kupang.sch.id',
            'assigned_class' => 'Primary A',
        ]);

        // 1. Verify initially scoped to Primary A
        $response = $this->actingAs($teacher)->get(route('students.index'));
        $response->assertSee('Murid A');
        $response->assertDontSee('Murid Senior High');

        // 2. Admin reassigns teacher to Senior High
        $updateResponse = $this->actingAs($admin)->put(route('teachers.update', $teacher), [
            'name' => 'Guru Pengajar Senior High',
            'email' => 'guru.pengajar@escs-kupang.sch.id',
            'assigned_class' => 'Senior High',
        ]);
        $updateResponse->assertRedirect(route('teachers.index'));

        // 3. Verify teacher automatically scopes to Senior High
        $teacher->refresh();
        $this->assertEquals('Senior High', $teacher->getAssignedClass());

        $reassignedResponse = $this->actingAs($teacher)->get(route('students.index'));
        $reassignedResponse->assertSee('Murid Senior High');
        $reassignedResponse->assertDontSee('Murid A');
    }

    public function test_admin_can_delete_teacher(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create([
            'role' => 'guru',
            'name' => 'Guru Yang Akan Dihapus',
            'email' => 'hapus.guru@escs-kupang.sch.id',
        ]);

        $response = $this->actingAs($admin)->delete(route('teachers.destroy', $teacher));

        $response->assertRedirect(route('teachers.index'));
        $this->assertDatabaseMissing('users', ['id' => $teacher->id]);
    }

    public function test_non_admin_cannot_access_teacher_management(): void
    {
        $teacher = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($teacher)->get(route('teachers.index'));
        $response->assertStatus(403);
    }

    public function test_teacher_edits_from_dashboard_reflect_realtime_across_system(): void
    {
        $teacher = User::factory()->create(['role' => 'guru', 'name' => 'Guru Primary C', 'assigned_class' => 'Primary C']);
        $student = Student::factory()->create(['nama' => 'Siswa Test C', 'kelas' => 'Primary C']);

        $today = now()->format('Y-m-d');

        // Teacher records attendance from dashboard/presensi form
        $saveResponse = $this->actingAs($teacher)->post(route('attendances.store'), [
            'tanggal' => $today,
            'attendances' => [
                $student->id => [
                    'status' => 'hadir',
                    'keterangan' => 'Tepat waktu',
                ],
            ],
        ]);
        $saveResponse->assertRedirect();

        // Immediately verify dashboard statistics reflect real-time count
        $dashboardResponse = $this->actingAs($teacher)->get(route('dashboard'));
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Hadir');

        // Immediately verify Database has stored attendance record
        $this->assertDatabaseHas('attendances', [
            'student_id' => $student->id,
            'tanggal' => $today,
            'status' => 'hadir',
        ]);
    }
}
