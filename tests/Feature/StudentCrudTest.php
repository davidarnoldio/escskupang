<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_students_page(): void
    {
        $response = $this->get(route('students.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_view_students_list(): void
    {
        $user = User::factory()->create();
        $student = Student::factory()->create(['nama' => 'Ahmad Subagja']);

        $response = $this->actingAs($user)->get(route('students.index'));

        $response->assertStatus(200);
        $response->assertSee('Ahmad Subagja');
    }

    public function test_authenticated_users_can_filter_students_by_class_and_search(): void
    {
        $user = User::factory()->create();
        $student1 = Student::factory()->create(['nis' => '0003.26.0236', 'nama' => 'Sierrafim Malelak', 'kelas' => 'Primary A']);
        $student2 = Student::factory()->create(['nis' => '0002.26.0235', 'nama' => 'Yosua Ndeo', 'kelas' => 'Primary B']);

        // Search by name
        $response1 = $this->actingAs($user)->get(route('students.index', ['search' => 'Sierrafim']));
        $response1->assertStatus(200);
        $response1->assertSee('Sierrafim Malelak');
        $response1->assertDontSee('Yosua Ndeo');

        // Filter by class
        $response2 = $this->actingAs($user)->get(route('students.index', ['kelas' => 'Primary B']));
        $response2->assertStatus(200);
        $response2->assertSee('Yosua Ndeo');
        $response2->assertDontSee('Sierrafim Malelak');
    }

    public function test_authenticated_users_can_create_a_student(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $studentData = [
            'nis' => '0003.26.0236',
            'nama' => 'Sierrafim Malelak',
            'kelas' => 'Primary A',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jl. Pendidikan No. 1, Kupang',
            'telepon' => '081234567890',
        ];

        $response = $this->actingAs($user)->post(route('students.store'), $studentData);

        $response->assertRedirect(route('students.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('students', [
            'nis' => '0003.26.0236',
            'nama' => 'Sierrafim Malelak',
        ]);
    }

    public function test_student_creation_validation_fails_for_duplicate_nis(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        Student::factory()->create(['nis' => '10201']);

        $studentData = [
            'nis' => '10201',
            'nama' => 'Budi Santoso',
            'kelas' => '10-IPA-1',
            'jenis_kelamin' => 'L',
        ];

        $response = $this->actingAs($user)->post(route('students.store'), $studentData);

        $response->assertSessionHasErrors('nis');
    }

    public function test_student_creation_validation_fails_for_invalid_gender(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $studentData = [
            'nis' => '10201',
            'nama' => 'Ahmad Subagja',
            'kelas' => '10-IPA-1',
            'jenis_kelamin' => 'X', // Invalid
        ];

        $response = $this->actingAs($user)->post(route('students.store'), $studentData);

        $response->assertSessionHasErrors('jenis_kelamin');
    }

    public function test_authenticated_users_can_update_student(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $student = Student::factory()->create(['nis' => '10201', 'nama' => 'Nama Lama']);

        $updateData = [
            'nis' => '10201',
            'nama' => 'Nama Baru Updated',
            'kelas' => '10-IPA-2',
            'jenis_kelamin' => 'L',
        ];

        $response = $this->actingAs($user)->put(route('students.update', $student), $updateData);

        $response->assertRedirect(route('students.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'nama' => 'Nama Baru Updated',
            'kelas' => '10-IPA-2',
        ]);
    }

    public function test_authenticated_users_can_delete_student(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $student = Student::factory()->create();

        $response = $this->actingAs($user)->delete(route('students.destroy', $student));

        $response->assertRedirect(route('students.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('students', [
            'id' => $student->id,
        ]);
    }
}
