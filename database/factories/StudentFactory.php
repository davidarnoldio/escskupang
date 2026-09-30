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
            'nisn'                   => fake()->unique()->numerify('00########'),
            'nik'                    => fake()->numerify('53710###########'),
            'no_kk'                  => fake()->numerify('53710###########'),
            'nama'                   => fake()->name(),
            'kelas'                  => fake()->randomElement([
                'TK',
                'Kelas 1',
                'Kelas 2',
                'Kelas 3',
                'Kelas 4',
                'Kelas 5',
                'Kelas 6',
            ]),
            'jenis_kelamin'          => fake()->randomElement(['L', 'P']),
            'tempat_lahir'           => fake()->city(),
            'tanggal_lahir'          => fake()->dateTimeBetween('-12 years', '-5 years')->format('Y-m-d'),
            'no_akta_kelahiran'      => fake()->bothify('5371-LT-######'),
            'agama'                  => fake()->randomElement(['Kristen Protestan', 'Katolik', 'Islam']),
            'kewarganegaraan'        => 'WNI',
            'kategori_prestasi'      => fake()->randomElement(['Tidak Ada', 'Akademik', 'Seni', 'Olahraga', 'Lain-lain']),
            'keterangan_prestasi'    => fake()->optional(0.3)->sentence(4),
            'tinggi_badan'           => fake()->numberBetween(100, 150),
            'berat_badan'            => fake()->numberBetween(18, 45),
            'lingkar_kepala'         => fake()->numberBetween(48, 55),
            'jumlah_saudara_kandung' => fake()->numberBetween(0, 4),
            'nama_ayah'              => fake()->name('male'),
            'nik_ayah'               => fake()->numerify('53710###########'),
            'tahun_lahir_ayah'       => (string) fake()->numberBetween(1975, 1990),
            'pendidikan_ayah'        => 'S1 / D4',
            'penghasilan_ayah'       => 'Rp 2.000.000 - Rp 5.000.000',
            'nama_ibu'               => fake()->name('female'),
            'nik_ibu'                => fake()->numerify('53710###########'),
            'tahun_lahir_ibu'        => (string) fake()->numberBetween(1978, 1993),
            'pendidikan_ibu'         => 'S1 / D4',
            'penghasilan_ibu'        => 'Rp 2.000.000 - Rp 5.000.000',
            'alamat'                 => fake()->address(),
            'telepon'                => fake()->phoneNumber(),
            'is_abk'                 => false,
        ];
    }
}
