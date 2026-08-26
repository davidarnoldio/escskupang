<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParentPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_parent_dashboard(): void
    {
        $response = $this->get(route('parent.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_parent_user_can_access_parent_dashboard_and_see_child_data(): void
    {
        $student = Student::factory()->create([
            'nis' => '0003.26.0236',
            'nama' => 'Sierrafim Malelak',
            'kelas' => 'Primary A',
        ]);

        $parent = User::factory()->create([
            'role' => 'orang_tua',
            'student_id' => $student->id,
        ]);

        $response = $this->actingAs($parent)->get(route('parent.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Portal Orang Tua Siswa');
        $response->assertSee('Sierrafim Malelak');
        $response->assertSee('0003.26.0236');
    }
}
