<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImpersonateTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_switch_to_teacher_account_without_logout(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create([
            'role' => 'guru',
            'name' => 'Wali Kelas Primary B',
            'assigned_class' => 'Primary B',
        ]);

        $response = $this->actingAs($admin)->post(route('impersonate.switch', $teacher));

        $response->assertRedirect(route('dashboard'));
        $this->assertEquals($teacher->id, auth()->id());
        $this->assertEquals($admin->id, session('impersonated_by'));
    }

    public function test_switched_teacher_sees_assigned_class_students(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $primaryBStudent = Student::factory()->create(['nama' => 'Murid Primary B', 'kelas' => 'Primary B']);
        $seniorHighStudent = Student::factory()->create(['nama' => 'Murid Senior High', 'kelas' => 'Senior High']);

        $teacher = User::factory()->create([
            'role' => 'guru',
            'assigned_class' => 'Primary B',
        ]);

        $this->actingAs($admin)->post(route('impersonate.switch', $teacher));

        $dashboardResponse = $this->get(route('dashboard'));
        $dashboardResponse->assertStatus(200);

        $studentListResponse = $this->get(route('students.index'));
        $studentListResponse->assertStatus(200);
        $studentListResponse->assertSee('Murid Primary B');
        $studentListResponse->assertDontSee('Murid Senior High');
    }

    public function test_user_can_switch_back_to_admin_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['role' => 'guru']);

        // Switch to teacher
        $this->actingAs($admin)->post(route('impersonate.switch', $teacher));
        $this->assertEquals($teacher->id, auth()->id());

        // Switch back to admin
        $leaveResponse = $this->post(route('impersonate.leave'));
        $leaveResponse->assertRedirect(route('dashboard'));

        $this->assertEquals($admin->id, auth()->id());
        $this->assertFalse(session()->has('impersonated_by'));
    }

    public function test_non_admin_and_parents_cannot_impersonate_others(): void
    {
        $teacher = User::factory()->create(['role' => 'guru']);
        $targetTeacher = User::factory()->create(['role' => 'guru']);
        $parent = User::factory()->create(['role' => 'orang_tua']);

        // Teacher trying to switch to another teacher
        $teacherResponse = $this->actingAs($teacher)->post(route('impersonate.switch', $targetTeacher));
        $teacherResponse->assertStatus(403);

        // Parent trying to switch to a teacher
        $parentResponse = $this->actingAs($parent)->post(route('impersonate.switch', $targetTeacher));
        $parentResponse->assertStatus(403);
    }

    public function test_deleting_teacher_cleans_up_dynamic_list_and_resets_impersonation(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['role' => 'guru', 'name' => 'Guru Temp']);

        // Switch to teacher
        $this->actingAs($admin)->post(route('impersonate.switch', $teacher));
        $this->assertEquals($teacher->id, auth()->id());

        // Admin deletes current teacher while impersonated
        $deleteResponse = $this->delete(route('teachers.destroy', $teacher));
        $deleteResponse->assertRedirect(route('teachers.index'));

        // Verify teacher is deleted and auth is reset back to admin
        $this->assertDatabaseMissing('users', ['id' => $teacher->id]);
        $this->assertEquals($admin->id, auth()->id());
    }
}
