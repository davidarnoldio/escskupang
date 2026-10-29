<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus akun dummy guru jika masih ada agar penugasan kelas bersih
        User::where('email', 'riki@teacher.sch.id')->delete();

        $defaultPassword = 'ntokupang123';
        $hashedPassword = Hash::make($defaultPassword);

        $teachers = [
            [
                'name'               => 'Beatrice Kianindra, B.Art',
                'email'              => 'beatrice.kianindra@teacherntokupang',
                'nuptk'              => null,
                'jenis_kelamin'      => 'P',
                'tempat_lahir'       => 'Kupang',
                'tanggal_lahir'      => '1984-01-24',
                'nama_ibu_kandung'   => 'Liliana Fransisca',
                'desa_kelurahan'     => 'LLBK',
                'kecamatan'          => 'Kec. Alak',
                'nik'                => '5371036401840003',
                'status_kepegawaian' => 'GTY/PTY',
                'sk_pengangkatan'    => '003/SK.YTH/VII/2017',
                'tmt_pengangkatan'   => '2017-07-22',
                'lembaga_pengangkat' => 'Ketua Yayasan',
                'sumber_gaji'        => 'Yayasan',
                'no_hp'              => '085239808002',
                'role'               => 'guru',
                'assigned_class'     => null, // Kepsek
                'is_active'          => false, // Guru non-wali / mapel dinonaktifkan secara default
            ],
            [
                'name'               => 'Apris Leky Manu, S.T',
                'email'              => 'apris.manu@teacherntokupang',
                'nuptk'              => '1746771672130292',
                'jenis_kelamin'      => 'L',
                'tempat_lahir'       => 'Oeekam',
                'tanggal_lahir'      => '1993-04-14',
                'nama_ibu_kandung'   => 'Antunia Nomleni',
                'desa_kelurahan'     => 'Penfui Timur',
                'kecamatan'          => 'Kec. Kupang Tengah',
                'nik'                => '5302041404930001',
                'status_kepegawaian' => 'GTY/PTY',
                'sk_pengangkatan'    => '001/SK.NTO/VII/2019',
                'tmt_pengangkatan'   => '2019-07-01',
                'lembaga_pengangkat' => 'Ketua Yayasan',
                'sumber_gaji'        => 'Yayasan',
                'no_hp'              => '085238699927',
                'role'               => 'guru',
                'assigned_class'     => null, // Operator
                'is_active'          => false, // Non-wali dimatikan secara default
            ],
            [
                'name'               => 'Angelina Fitri Mali, S.Pd., Gr',
                'email'              => 'angelina.mali@teacherntokupang',
                'nuptk'              => '8451777678230132',
                'jenis_kelamin'      => 'P',
                'tempat_lahir'       => 'Kupang',
                'tanggal_lahir'      => '1999-01-19',
                'nama_ibu_kandung'   => 'Maria Boe Bere',
                'desa_kelurahan'     => 'Oebufu',
                'kecamatan'          => 'Kec. Oebobo',
                'nik'                => '5371045901990001',
                'status_kepegawaian' => 'GTY/PTY',
                'sk_pengangkatan'    => '010/SK.YTHM/VI/2022',
                'tmt_pengangkatan'   => '2022-06-01',
                'lembaga_pengangkat' => 'Ketua Yayasan',
                'sumber_gaji'        => 'Yayasan',
                'no_hp'              => '087863567215',
                'role'               => 'guru',
                'assigned_class'     => 'Kelas 5', // Guru Kelas V
                'is_active'          => true,
            ],
            [
                'name'               => 'Framinus Deade Larisano, S.Pd., Gr',
                'email'              => 'framinus.larisano@teacherntokupang',
                'nuptk'              => '5955776677130222',
                'jenis_kelamin'      => 'L',
                'tempat_lahir'       => 'Kopalando',
                'tanggal_lahir'      => '1998-06-23',
                'nama_ibu_kandung'   => 'Maria Mia Engol',
                'desa_kelurahan'     => 'Liliba',
                'kecamatan'          => 'Kec. Oebobo',
                'nik'                => '5319062306980002',
                'status_kepegawaian' => 'GTY/PTY',
                'sk_pengangkatan'    => '011/SK.YTHM/VI/2022',
                'tmt_pengangkatan'   => '2022-06-01',
                'lembaga_pengangkat' => 'Ketua Yayasan',
                'sumber_gaji'        => 'Yayasan',
                'no_hp'              => '081338479391',
                'role'               => 'guru',
                'assigned_class'     => 'Kelas 1', // Guru Kelas I
                'is_active'          => true,
            ],
            [
                'name'               => 'Jorjelina Rantasia Da Costa, S.Pd., Gr',
                'email'              => 'jorjelina.dacosta@teacherntokupang',
                'nuptk'              => '8434770671230402',
                'jenis_kelamin'      => 'P',
                'tempat_lahir'       => 'Denpasar',
                'tanggal_lahir'      => '1992-01-02',
                'nama_ibu_kandung'   => 'Nasmini',
                'desa_kelurahan'     => 'Oesapa',
                'kecamatan'          => 'Kec. Kelapa Lima',
                'nik'                => '5301064201920001',
                'status_kepegawaian' => 'GTY/PTY',
                'sk_pengangkatan'    => '012/SK.YTHM/VI/2023',
                'tmt_pengangkatan'   => '2023-06-01',
                'lembaga_pengangkat' => 'Ketua Yayasan',
                'sumber_gaji'        => 'Yayasan',
                'no_hp'              => '085280746492',
                'role'               => 'guru',
                'assigned_class'     => 'Kelas 6', // Guru Kelas VI
                'is_active'          => true,
            ],
            [
                'name'               => 'Linda Nara, S.Pd',
                'email'              => 'linda.nara@teacherntokupang',
                'nuptk'              => null,
                'jenis_kelamin'      => 'P',
                'tempat_lahir'       => 'Tanajawa',
                'tanggal_lahir'      => '1999-12-01',
                'nama_ibu_kandung'   => 'Damaris Nara',
                'desa_kelurahan'     => 'Sikumana',
                'kecamatan'          => 'Kec. Maulafa',
                'nik'                => '5320024112990002',
                'status_kepegawaian' => 'GTY/PTY',
                'sk_pengangkatan'    => '007/SK.YTHM/VII/2024',
                'tmt_pengangkatan'   => '2024-07-01',
                'lembaga_pengangkat' => 'Ketua Yayasan',
                'sumber_gaji'        => 'Yayasan',
                'no_hp'              => '082155383730',
                'role'               => 'guru',
                'assigned_class'     => 'Kelas 4', // Guru Kelas IV
                'is_active'          => true,
            ],
            [
                'name'               => 'Marselindah Oberty Kekado, S.Pd., Gr',
                'email'              => 'marselindah.kekado@teacherntokupang',
                'nuptk'              => '9352776677230033',
                'jenis_kelamin'      => 'P',
                'tempat_lahir'       => 'Kupang',
                'tanggal_lahir'      => '1998-10-20',
                'nama_ibu_kandung'   => 'Naomi Bua',
                'desa_kelurahan'     => 'Lasiana',
                'kecamatan'          => 'Kec. Kelapa Lima',
                'nik'                => '5371036010970003',
                'status_kepegawaian' => 'GTY/PTY',
                'sk_pengangkatan'    => '008/SK.YTH/VII/2021',
                'tmt_pengangkatan'   => '2021-07-01',
                'lembaga_pengangkat' => 'Ketua Yayasan',
                'sumber_gaji'        => 'Yayasan',
                'no_hp'              => '081259263803',
                'role'               => 'guru',
                'assigned_class'     => null, // Guru Mapel
                'is_active'          => false, // Guru mapel dimatikan secara default
            ],
            [
                'name'               => 'Rosita Da Costa, S.Pd., Gr',
                'email'              => 'rosita.dacosta@teacherntokupang',
                'nuptk'              => '7243772673230193',
                'jenis_kelamin'      => 'P',
                'tempat_lahir'       => 'Lospalos',
                'tanggal_lahir'      => '1994-09-11',
                'nama_ibu_kandung'   => 'Ermelinda Da Costa',
                'desa_kelurahan'     => 'Naibonat',
                'kecamatan'          => 'Kec. Kupang Timur',
                'nik'                => '5301065109940004',
                'status_kepegawaian' => 'GTY/PTY',
                'sk_pengangkatan'    => '002/SK.YTH/VII/2019',
                'tmt_pengangkatan'   => '2019-07-01',
                'lembaga_pengangkat' => 'Ketua Yayasan',
                'sumber_gaji'        => 'Yayasan',
                'no_hp'              => '081338029377',
                'role'               => 'guru',
                'assigned_class'     => 'Kelas 2', // Guru Kelas II
                'is_active'          => true,
            ],
            [
                'name'               => 'Sancha Praecelya Buan, S.Pd., Gr',
                'email'              => 'sancha.buan@teacherntokupang',
                'nuptk'              => '8545776677230172',
                'jenis_kelamin'      => 'P',
                'tempat_lahir'       => 'Kupang',
                'tanggal_lahir'      => '1998-02-13',
                'nama_ibu_kandung'   => 'Elisabeth Sikku',
                'desa_kelurahan'     => 'Oebufu',
                'kecamatan'          => 'Kec. Oebobo',
                'nik'                => '5371035302980005',
                'status_kepegawaian' => 'GTY/PTY',
                'sk_pengangkatan'    => '013/SK.YTHM/VI/2023',
                'tmt_pengangkatan'   => '2023-06-01',
                'lembaga_pengangkat' => 'Ketua Yayasan',
                'sumber_gaji'        => 'Yayasan',
                'no_hp'              => '081236592043',
                'role'               => 'guru',
                'assigned_class'     => null, // Guru Mapel
                'is_active'          => false, // Guru mapel dimatikan secara default
            ],
            [
                'name'               => 'Santi Yunita Naif, S.Pd., Gr',
                'email'              => 'santi.naif@teacherntokupang',
                'nuptk'              => '2460766667300012',
                'jenis_kelamin'      => 'P',
                'tempat_lahir'       => 'Koat',
                'tanggal_lahir'      => '1988-01-28',
                'nama_ibu_kandung'   => 'Martha Tuniama',
                'desa_kelurahan'     => 'Penfui Timur',
                'kecamatan'          => 'Kec. Kupang Tengah',
                'nik'                => '5301066801880003',
                'status_kepegawaian' => 'GTY/PTY',
                'sk_pengangkatan'    => '002/SK.YTH/VII/2017',
                'tmt_pengangkatan'   => '2017-07-22',
                'lembaga_pengangkat' => 'Ketua Yayasan',
                'sumber_gaji'        => 'Yayasan',
                'no_hp'              => '085253149331',
                'role'               => 'guru',
                'assigned_class'     => 'Kelas 3', // Guru Kelas III
                'is_active'          => true,
            ],
            [
                'name'               => 'Joice Yovika Tampani, S.Psi',
                'email'              => 'joice.tampani@teacherntokupang',
                'nuptk'              => null,
                'jenis_kelamin'      => 'P',
                'tempat_lahir'       => 'Kupang',
                'tanggal_lahir'      => '2001-06-20',
                'nama_ibu_kandung'   => 'Viktoria Margarita Rassi',
                'desa_kelurahan'     => 'Maulafa',
                'kecamatan'          => 'Kec. Maulafa',
                'nik'                => '5371026006010002',
                'status_kepegawaian' => 'GTY/PTY',
                'sk_pengangkatan'    => '008/421.2/SDNTO/VII/2024',
                'tmt_pengangkatan'   => '2024-07-01',
                'lembaga_pengangkat' => 'Ketua Yayasan',
                'sumber_gaji'        => 'Yayasan',
                'no_hp'              => '082144473576',
                'role'               => 'guru',
                'assigned_class'     => null, // Guru Pendamping Inklusi
                'is_active'          => false, // Non-wali dimatikan secara default
            ],
        ];

        foreach ($teachers as $data) {
            $data['password'] = $hashedPassword;
            $data['plain_password'] = $defaultPassword;
            $data['email_verified_at'] = now();

            // Match by NIK or Name so existing accounts with old email format are properly updated
            $existing = User::where('name', $data['name'])
                ->orWhere(function ($q) use ($data) {
                    if (!empty($data['nik'])) {
                        $q->where('nik', $data['nik']);
                    }
                })->first();

            if ($existing) {
                $existing->update($data);
            } else {
                User::create($data);
            }
        }
    }
}
