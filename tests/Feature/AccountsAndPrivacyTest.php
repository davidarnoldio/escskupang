<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountsAndPrivacyTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $activeTeacher;
    private User $inactiveTeacher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Apris Leky Manu, S.T',
            'email' => 'admin@ntokupang.sch.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->activeTeacher = User::create([
            'name' => 'Guru Kelas 1A',
            'email' => 'guru1a@teacherntokupang',
            'password' => Hash::make('ntokupang123'),
            'role' => 'guru',
            'assigned_class' => 'Kelas 1A',
            'is_active' => true,
        ]);

        $this->inactiveTeacher = User::create([
            'name' => 'Guru Nonaktif',
            'email' => 'mapel@teacherntokupang',
            'password' => Hash::make('ntokupang123'),
            'role' => 'guru',
            'assigned_class' => null,
            'is_active' => false,
        ]);
    }

    public function test_inactive_teacher_cannot_login_and_custom_class_teacher_appears_in_index(): void
    {
        $this->post('/login', [
            'email' => 'mapel@teacherntokupang',
            'password' => 'ntokupang123',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();

        // Custom class teacher ('Kelas 1A') must appear in /teachers
        $res = $this->actingAs($this->admin)->get('/teachers');
        $res->assertStatus(200);
        $res->assertSee('Guru Kelas 1A');
        $res->assertSee('Kelas 1A');
    }

    public function test_manual_student_password_is_saved_synced_and_hidden_from_teachers(): void
    {
        $this->actingAs($this->admin)->post('/students', [
            'nisn' => '5001',
            'nama' => 'Budi Santoso',
            'kelas' => 'Kelas 1A',
            'jenis_kelamin' => 'L',
            'password' => 'RahasiaBudi99',
        ])->assertRedirect('/students');

        $student = Student::where('nisn', '5001')->firstOrFail();
        $parent = User::where('student_id', $student->id)->firstOrFail();

        $this->assertSame('budi.santoso@studentntokupang', $parent->email);
        $this->assertSame('RahasiaBudi99', $parent->plain_password);
        $this->assertTrue(Hash::check('RahasiaBudi99', $parent->password));

        // Admin sees student email & password; Teacher MUST NOT see them
        $adminList = $this->actingAs($this->admin)->get('/students');
        $adminList->assertSee('budi.santoso@studentntokupang');
        $adminList->assertSee('RahasiaBudi99');

        $teacherList = $this->actingAs($this->activeTeacher)->get('/students');
        $teacherList->assertDontSee('budi.santoso@studentntokupang');
        $teacherList->assertDontSee('RahasiaBudi99');

        $teacherShow = $this->actingAs($this->activeTeacher)->get('/students/' . $student->id);
        $teacherShow->assertDontSee('budi.santoso@studentntokupang');
        $teacherShow->assertDontSee('RahasiaBudi99');

        // Parent changes password -> plain_password syncs immediately
        $this->actingAs($parent)->post('/parent/update-account', [
            'email' => 'budi.santoso@studentntokupang',
            'password' => 'SandiBaruParent2026',
            'password_confirmation' => 'SandiBaruParent2026',
        ])->assertRedirect();

        $parent->refresh();
        $this->assertSame('SandiBaruParent2026', $parent->plain_password);
    }
}
