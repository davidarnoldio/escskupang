<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'tanggal' => now()->format('Y-m-d'),
            'status' => fake()->randomElement(['hadir', 'izin', 'sakit', 'alpa']),
            'keterangan' => null,
        ];
    }
}
