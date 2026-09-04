<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\PasswordResetRequest;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationBadgeTddTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_notification_badges_render_correct_counts()
    {
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@nto-kupang.sch.id']);

        // Create 2 pending password reset requests
        PasswordResetRequest::create(['user_id' => $admin->id, 'email' => 'user1@nto-kupang.sch.id', 'status' => 'pending']);
        PasswordResetRequest::create(['user_id' => $admin->id, 'email' => 'user2@nto-kupang.sch.id', 'status' => 'pending']);

        // Create 3 pending payments awaiting verification
        $student = Student::factory()->create();
        Payment::create(['student_id' => $student->id, 'judul' => 'SPP Sep', 'jumlah' => 100, 'jatuh_tempo' => now()->addDays(10), 'status' => 'menunggu_konfirmasi']);
        Payment::create(['student_id' => $student->id, 'judul' => 'SPP Oct', 'jumlah' => 100, 'jatuh_tempo' => now()->addDays(10), 'status' => 'menunggu_konfirmasi']);
        Payment::create(['student_id' => $student->id, 'judul' => 'SPP Nov', 'jumlah' => 100, 'jatuh_tempo' => now()->addDays(10), 'status' => 'menunggu_konfirmasi']);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('2'); // Password reset badge count
        $response->assertSee('3'); // Payment verification badge count
    }

    public function test_teacher_notification_badges_scoped_to_assigned_class()
    {
        $teacher = User::factory()->create([
            'name' => 'Wali Kelas Kelas 1A',
            'role' => 'guru',
        ]);

        $student1A = Student::factory()->create(['kelas' => 'Kelas 1A']);
        $student1B = Student::factory()->create(['kelas' => 'Kelas 1B']);

        // 1 pending homework submission for Class 1A, 1 for Class 1B
        $hw1A = Homework::create(['teacher_id' => $teacher->id, 'judul' => 'PR 1A', 'mata_pelajaran' => 'Matematika', 'deskripsi' => 'Deskripsi 1A', 'kelas' => 'Kelas 1A', 'deadline' => now()->addDays(2)]);
        $hw1B = Homework::create(['teacher_id' => $teacher->id, 'judul' => 'PR 1B', 'mata_pelajaran' => 'IPA', 'deskripsi' => 'Deskripsi 1B', 'kelas' => 'Kelas 1B', 'deadline' => now()->addDays(2)]);

        HomeworkSubmission::create(['homework_id' => $hw1A->id, 'student_id' => $student1A->id, 'foto_pr' => 'pr_submissions/1A.jpg', 'nilai' => null]);
        HomeworkSubmission::create(['homework_id' => $hw1B->id, 'student_id' => $student1B->id, 'foto_pr' => 'pr_submissions/1B.jpg', 'nilai' => null]);

        $response = $this->actingAs($teacher)->get(route('dashboard'));

        $response->assertStatus(200);
        // Teacher 1A should see notification for Class 1A submission
        $response->assertSee('1');
    }

    public function test_parent_notification_badges_render_unpaid_bills_and_pending_homework()
    {
        $teacher = User::factory()->create(['role' => 'guru', 'name' => 'Wali Kelas Kelas 1A']);
        $student = Student::factory()->create(['kelas' => 'Kelas 1A']);
        $parent = User::factory()->create([
            'role' => 'orang_tua',
            'student_id' => $student->id,
        ]);

        // Unpaid bill
        Payment::create(['student_id' => $student->id, 'judul' => 'SPP Sep', 'jumlah' => 200000, 'jatuh_tempo' => now()->addDays(10), 'status' => 'belum_lunas']);

        // Pending homework
        Homework::create(['teacher_id' => $teacher->id, 'judul' => 'PR Matematika 1A', 'mata_pelajaran' => 'Matematika', 'deskripsi' => 'Soal 1-5', 'kelas' => 'Kelas 1A', 'deadline' => now()->addDays(2)]);

        $response = $this->actingAs($parent)->get(route('parent.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Tagihan Pembayaran');
    }
}
