<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'nis' => fake()->unique()->numerify('00##.26.0###'),
            'nama' => fake()->name(),
            'kelas' => fake()->randomElement([
                'Nursery',
                'Pre-K',
                'Kindergarten',
                'Primary Preparation',
                'Primary A',
                'Primary B',
                'Primary C',
                'Junior High',
                'Senior High',
            ]),
            'jenis_kelamin' => fake()->randomElement(['L', 'P']),
            'alamat' => fake()->address(),
            'telepon' => fake()->phoneNumber(),
        ];
    }
}
