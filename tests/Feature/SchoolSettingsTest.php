<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_settings_page(): void
    {
        $response = $this->get(route('settings.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_view_settings_page(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get(route('settings.index'));

        $response->assertStatus(200);
        $response->assertSee('Pengaturan Jam Presensi Sekolah');
    }

    public function test_authenticated_users_can_update_school_operating_hours(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->post(route('settings.update'), [
            'jam_masuk' => '06:45',
            'jam_terlambat' => '07:15',
            'jam_pulang' => '13:30',
        ]);

        $response->assertRedirect(route('settings.index'));
        $response->assertSessionHas('success');

        $this->assertEquals('06:45', Setting::get('jam_masuk'));
        $this->assertEquals('07:15', Setting::get('jam_terlambat'));
        $this->assertEquals('13:30', Setting::get('jam_pulang'));
    }
}
