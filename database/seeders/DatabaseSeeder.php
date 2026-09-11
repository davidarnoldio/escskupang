<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Default Jam Presensi Settings
        Setting::set('jam_masuk', '07:00');
        Setting::set('jam_terlambat', '07:30');
        Setting::set('jam_pulang', '14:00');
        Setting::set('jam_masuk_abk', '08:00');
        Setting::set('jam_terlambat_abk', '08:30');
        Setting::set('jam_pulang_abk', '13:00');

        // Only Administrator Account
        User::updateOrCreate(
            ['email' => 'admin@nto-kupang.sch.id'],
            [
                'name' => 'Administrator NTO',
                'password' => Hash::make('admin123'),
                'plain_password' => null,
                'role' => 'admin',
                'assigned_class' => null,
                'student_id' => null,
                'email_verified_at' => now(),
            ]
        );
    }
}
