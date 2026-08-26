<?php

namespace Tests\Feature\Auth;

use App\Models\PasswordResetRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
        $response->assertSee('Lupa Kata Sandi Akun');
    }

    public function test_reset_password_notification_can_be_requested_to_admin(): void
    {
        $user = User::factory()->create(['email' => 'user.forgot@escs-kupang.sch.id']);

        $response = $this->post('/forgot-password', ['email' => $user->email]);

        $response->assertSessionHas('status');
        $this->assertDatabaseHas('password_reset_requests', [
            'email' => 'user.forgot@escs-kupang.sch.id',
            'status' => 'pending',
        ]);
    }
}
