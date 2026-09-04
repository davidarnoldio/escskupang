<?php

namespace Tests\Feature;

use App\Models\PasswordResetRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordResetRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_submit_forgot_password_notification_to_admin(): void
    {
        $teacher = User::factory()->create([
            'role' => 'guru',
            'name' => 'Guru Lupa Sandi',
            'email' => 'lupa.sandi@nto-kupang.sch.id',
        ]);

        $response = $this->post(route('password.email'), [
            'email' => 'lupa.sandi@nto-kupang.sch.id',
        ]);

        $response->assertSessionHas('status');
        $this->assertDatabaseHas('password_reset_requests', [
            'email' => 'lupa.sandi@nto-kupang.sch.id',
            'status' => 'pending',
        ]);
    }

    public function test_admin_can_view_pending_reset_requests_and_reset_password_instantly(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $parent = User::factory()->create([
            'role' => 'orang_tua',
            'name' => 'Orang Tua Test',
            'email' => 'ortu.test@student.sch.id',
            'password' => bcrypt('oldpassword'),
        ]);

        $resetReq = PasswordResetRequest::create([
            'user_id' => $parent->id,
            'name' => $parent->name,
            'email' => $parent->email,
            'role' => 'orang_tua',
            'status' => 'pending',
        ]);

        // 1. Admin views password requests page
        $viewResponse = $this->actingAs($admin)->get(route('admin.password-requests.index'));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('ortu.test@student.sch.id');

        // 2. Admin resets password to newpass123
        $resetResponse = $this->actingAs($admin)->post(route('admin.reset-password', $parent), [
            'new_password' => 'newpass123',
        ]);
        $resetResponse->assertRedirect();

        // 3. Verify user password updated and request resolved
        $parent->refresh();
        $this->assertTrue(auth()->attempt(['email' => 'ortu.test@student.sch.id', 'password' => 'newpass123']));
        $this->assertEquals('resolved', $resetReq->fresh()->status);
    }
}
