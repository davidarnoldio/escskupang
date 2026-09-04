<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Student;
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
        \App\Models\Setting::set('jam_masuk', '07:00');
        \App\Models\Setting::set('jam_terlambat', '07:30');
        \App\Models\Setting::set('jam_pulang', '14:00');

        // Default Demo Accounts
        User::updateOrCreate(['email' => 'test@example.com'], ['name' => 'Guru Pengajar', 'password' => Hash::make('password'), 'plain_password' => 'password', 'role' => 'guru', 'email_verified_at' => now()]);
        User::updateOrCreate(['email' => 'admin@nto-kupang.sch.id'], ['name' => 'Administrator NTO', 'password' => Hash::make('admin123'), 'plain_password' => 'admin123', 'role' => 'admin', 'email_verified_at' => now()]);

        // Homeroom Teacher Accounts from NTO National Plus Data
        User::updateOrCreate(['email' => 'vivinalle@nto-kupang.sch.id'], ['name' => 'Nursery (Wali Kelas Nursery)', 'password' => Hash::make('vivi123'), 'plain_password' => 'vivi123', 'email_verified_at' => now()]);
        User::updateOrCreate(['email' => 'asnat@nto-kupang.sch.id'], ['name' => 'Pre-K (Wali Kelas Pre-K)', 'password' => Hash::make('asnat123'), 'plain_password' => 'asnat123', 'email_verified_at' => now()]);
        User::updateOrCreate(['email' => 'ellen@nto-kupang.sch.id'], ['name' => 'Kindergarten (Wali Kelas Kindergarten)', 'password' => Hash::make('ellen123'), 'plain_password' => 'ellen123', 'email_verified_at' => now()]);
        User::updateOrCreate(['email' => 'kezia@nto-kupang.sch.id'], ['name' => 'Primary Preparation (Wali Kelas Primary Preparation)', 'password' => Hash::make('kezia123'), 'plain_password' => 'kezia123', 'email_verified_at' => now()]);
        User::updateOrCreate(['email' => 'amel@nto-kupang.sch.id'], ['name' => 'Primary A (Wali Kelas Primary A)', 'password' => Hash::make('amel123'), 'plain_password' => 'amel123', 'email_verified_at' => now()]);
        User::updateOrCreate(['email' => 'wulan@nto-kupang.sch.id'], ['name' => 'Primary B (Wali Kelas Primary B)', 'password' => Hash::make('wulan123'), 'plain_password' => 'wulan123', 'email_verified_at' => now()]);
        User::updateOrCreate(['email' => 'aldi@nto-kupang.sch.id'], ['name' => 'Primary C (Wali Kelas Primary C)', 'password' => Hash::make('aldi123'), 'plain_password' => 'aldi123', 'email_verified_at' => now()]);
        User::updateOrCreate(['email' => 'beatrix@nto-kupang.sch.id'], ['name' => 'Junior High (Wali Kelas Junior High)', 'password' => Hash::make('beatrix123'), 'plain_password' => 'beatrix123', 'email_verified_at' => now()]);
        User::updateOrCreate(['email' => 'anjash@nto-kupang.sch.id'], ['name' => 'Senior High (Wali Kelas Senior High)', 'password' => Hash::make('anjash123'), 'plain_password' => 'anjash123', 'email_verified_at' => now()]);

        // Seed Real Student Data from ESCS Kupang Spreadsheet
        if (Student::count() === 0) {
            $studentsData = [
                ['nis' => '0003.26.0236', 'nama' => 'Sierrafim Kanaya Yuthika Malelak', 'kelas' => 'Nursery', 'jenis_kelamin' => 'L'],
                ['nis' => '0002.26.0235', 'nama' => 'Yosua Jan Derant Ndeo', 'kelas' => 'Nursery', 'jenis_kelamin' => 'P'],
                ['nis' => '0005.24.0190', 'nama' => 'Calvin Samuel Dima', 'kelas' => 'Pre-K', 'jenis_kelamin' => 'L'],
                ['nis' => '0003.26.0243', 'nama' => 'Claire Illona Koehuan', 'kelas' => 'Pre-K', 'jenis_kelamin' => 'P'],
                ['nis' => '0012.24.0202', 'nama' => 'Elvidmesz Daniel Adoe Jr', 'kelas' => 'Pre-K', 'jenis_kelamin' => 'L'],
                ['nis' => '0005.25.0220', 'nama' => 'Felicya Manuella Bako', 'kelas' => 'Pre-K', 'jenis_kelamin' => 'P'],
                ['nis' => '0003.25.0204', 'nama' => 'Filipus Vico Okhotan', 'kelas' => 'Pre-K', 'jenis_kelamin' => 'L'],
                ['nis' => '0001.26.0232', 'nama' => 'Gersia Deloveva Loasana', 'kelas' => 'Pre-K', 'jenis_kelamin' => 'P'],
                ['nis' => '0003.26.0240', 'nama' => 'Marco Gaurlin Benjamin Langkola', 'kelas' => 'Pre-K', 'jenis_kelamin' => 'L'],
                ['nis' => '0003.26.0244', 'nama' => 'Mikayla Alinka Bhadjowawo', 'kelas' => 'Pre-K', 'jenis_kelamin' => 'P'],
                ['nis' => '0007.25.0215', 'nama' => 'Zachary Alexander Yonah Pian', 'kelas' => 'Pre-K', 'jenis_kelamin' => 'L'],
                ['nis' => '0003.25.0205', 'nama' => 'Zane Xavier Amtiran', 'kelas' => 'Pre-K', 'jenis_kelamin' => 'P'],
                ['nis' => '0008.24.0196', 'nama' => 'Alfhanz Acknowledge Nggi', 'kelas' => 'Kindergarten', 'jenis_kelamin' => 'L'],
                ['nis' => '0003.25.0207', 'nama' => 'Alosius Richard Ganggut', 'kelas' => 'Kindergarten', 'jenis_kelamin' => 'P'],
                ['nis' => '0001.24.0172', 'nama' => 'Audlyn Wijaya Ang', 'kelas' => 'Kindergarten', 'jenis_kelamin' => 'L'],
                ['nis' => '0007.25.0231', 'nama' => 'Dafrile Ghava Ndun', 'kelas' => 'Kindergarten', 'jenis_kelamin' => 'P'],
                ['nis' => '0003.26.0245', 'nama' => 'Dilla Cattleya Kefi Amtiran', 'kelas' => 'Kindergarten', 'jenis_kelamin' => 'L'],
                ['nis' => '0003.25.0206', 'nama' => 'Eileen Christabel Ainsley Boelan', 'kelas' => 'Kindergarten', 'jenis_kelamin' => 'P'],
                ['nis' => '0003.26.0237', 'nama' => 'Elshema Mariska Tlonaen', 'kelas' => 'Kindergarten', 'jenis_kelamin' => 'L'],
                ['nis' => '0001.23.0145', 'nama' => 'Gyon Tanonef', 'kelas' => 'Kindergarten', 'jenis_kelamin' => 'P'],
                ['nis' => '0006.25.0221', 'nama' => 'Harvey Alexander Ndaumanu', 'kelas' => 'Kindergarten', 'jenis_kelamin' => 'L'],
                ['nis' => '0007.25.0214', 'nama' => 'Khaterine Yuki Djong Taliwang', 'kelas' => 'Kindergarten', 'jenis_kelamin' => 'P'],
                ['nis' => '0005.24.0191', 'nama' => 'Leander Gevariel Sepanca', 'kelas' => 'Kindergarten', 'jenis_kelamin' => 'L'],
                ['nis' => '0006.25.0225', 'nama' => 'Maria Gaudensia Queendy Wea', 'kelas' => 'Kindergarten', 'jenis_kelamin' => 'P'],
                ['nis' => '0003.23.0169', 'nama' => 'Nicklaus Kenrich Lake', 'kelas' => 'Kindergarten', 'jenis_kelamin' => 'L'],
                ['nis' => '0003.24.0182', 'nama' => 'Rafael Elgar Tanoto', 'kelas' => 'Kindergarten', 'jenis_kelamin' => 'P'],
                ['nis' => '0006.24.0192', 'nama' => 'Reinhard Lianto Hartanto', 'kelas' => 'Kindergarten', 'jenis_kelamin' => 'L'],
                ['nis' => '0002.24.0178', 'nama' => 'Aleysia Syahquita Armaris Noach', 'kelas' => 'Primary Preparation', 'jenis_kelamin' => 'P'],
                ['nis' => '0001.23.0143', 'nama' => 'Benedict Harvey Alexander Lopez', 'kelas' => 'Primary Preparation', 'jenis_kelamin' => 'L'],
                ['nis' => '0006.26.0252', 'nama' => 'David Ben-Gurion Rihi Biha', 'kelas' => 'Primary Preparation', 'jenis_kelamin' => 'P'],
                ['nis' => '0008.24.0195', 'nama' => 'Felicia Blandriani Bani', 'kelas' => 'Primary Preparation', 'jenis_kelamin' => 'L'],
                ['nis' => '0006.26.0251', 'nama' => 'Gavrill Clavheino Adiputra Gerimu', 'kelas' => 'Primary Preparation', 'jenis_kelamin' => 'P'],
                ['nis' => '0011.24.0200', 'nama' => 'Grizhelyne Julievia Dodo', 'kelas' => 'Primary Preparation', 'jenis_kelamin' => 'L'],
                ['nis' => '0001.24.0171', 'nama' => 'Haidee Belva Aleysia Manafe', 'kelas' => 'Primary Preparation', 'jenis_kelamin' => 'P'],
                ['nis' => '0002.24.0176', 'nama' => 'Kinanti Jacinda Bana', 'kelas' => 'Primary Preparation', 'jenis_kelamin' => 'L'],
                ['nis' => '0003.26.0238', 'nama' => 'Lovely Iselda Asarela Rohi', 'kelas' => 'Primary Preparation', 'jenis_kelamin' => 'P'],
                ['nis' => '0001.23.0140', 'nama' => 'Qinnara Alessandra Liudianto', 'kelas' => 'Primary Preparation', 'jenis_kelamin' => 'L'],
                ['nis' => '0006.22.0115', 'nama' => 'Alleluia Ivanna Laticha Kalla', 'kelas' => 'Primary A', 'jenis_kelamin' => 'P'],
                ['nis' => '0001.23.0162', 'nama' => 'Asher Imanuel Junior Padjilomi', 'kelas' => 'Primary A', 'jenis_kelamin' => 'L'],
                ['nis' => '0007.21.0088', 'nama' => 'Aura Majesty Zevanya Karunia Amalo', 'kelas' => 'Primary A', 'jenis_kelamin' => 'P'],
                ['nis' => '0007.25.0229', 'nama' => 'Beatrix Agustina Raya Lende', 'kelas' => 'Primary A', 'jenis_kelamin' => 'L'],
                ['nis' => '0011.24.0199', 'nama' => 'Chalbel Fidencio Nitti', 'kelas' => 'Primary A', 'jenis_kelamin' => 'P'],
                ['nis' => '0003.24.0188', 'nama' => 'Clara Thalita Amaral', 'kelas' => 'Primary A', 'jenis_kelamin' => 'L'],
                ['nis' => '0006.25.0222', 'nama' => 'Danna Chiquita Kefi Amtiran', 'kelas' => 'Primary A', 'jenis_kelamin' => 'P'],
                ['nis' => '0001.23.0163', 'nama' => 'Delonix Hezky Nenobahan', 'kelas' => 'Primary A', 'jenis_kelamin' => 'L'],
                ['nis' => '0008.23.0168', 'nama' => 'Elia Junus Silvandro Lily', 'kelas' => 'Primary A', 'jenis_kelamin' => 'P'],
                ['nis' => '0001.23.0139', 'nama' => 'Elrich Hugo Djung', 'kelas' => 'Primary A', 'jenis_kelamin' => 'L'],
                ['nis' => '0007.25.0230', 'nama' => 'Evano Reinaldo Shaquille Wege', 'kelas' => 'Primary A', 'jenis_kelamin' => 'P'],
                ['nis' => '0003.24.0184', 'nama' => 'Favor Ayub Yehezkiel Sahertian', 'kelas' => 'Primary A', 'jenis_kelamin' => 'L'],
                ['nis' => '0001.23.0153', 'nama' => 'Giliya Emmanuela Rihi Biha', 'kelas' => 'Primary A', 'jenis_kelamin' => 'P'],
                ['nis' => '0007.22.0031', 'nama' => 'Greta Liliana Wong', 'kelas' => 'Primary A', 'jenis_kelamin' => 'L'],
                ['nis' => '0007.22.0124', 'nama' => 'Jonathan Aiden Elim', 'kelas' => 'Primary A', 'jenis_kelamin' => 'P'],
                ['nis' => '0003.25.0210', 'nama' => 'Kencanalova Putripusaka Loresten Koeslulat', 'kelas' => 'Primary A', 'jenis_kelamin' => 'L'],
                ['nis' => '0007.22.0131', 'nama' => 'Kiana Elaine Mily Lassa', 'kelas' => 'Primary A', 'jenis_kelamin' => 'P'],
                ['nis' => '0001.23.0160', 'nama' => 'Luchio Leonardo Kehi Correia Gama', 'kelas' => 'Primary A', 'jenis_kelamin' => 'L'],
                ['nis' => '0004.25.0218', 'nama' => 'Maria Margarita Grisel M. Awa', 'kelas' => 'Primary A', 'jenis_kelamin' => 'P'],
                ['nis' => '0001.23.0161', 'nama' => 'Matthew Jesrael Brema Pollatu', 'kelas' => 'Primary A', 'jenis_kelamin' => 'L'],
                ['nis' => '0003.25.0209', 'nama' => 'Noah Fordjhon Karundeng', 'kelas' => 'Primary A', 'jenis_kelamin' => 'P'],
                ['nis' => '0007.21.0084', 'nama' => 'Precious Miracle Avigail Kharisma Amalo', 'kelas' => 'Primary A', 'jenis_kelamin' => 'L'],
                ['nis' => '0001.23.0141', 'nama' => 'Queen Emmanuella Hun', 'kelas' => 'Primary A', 'jenis_kelamin' => 'P'],
                ['nis' => '0007.22.0129', 'nama' => 'Raelyn Lianto Hartanto', 'kelas' => 'Primary A', 'jenis_kelamin' => 'L'],
                ['nis' => '0001.23.0147', 'nama' => 'Rayden Prince Djami', 'kelas' => 'Primary A', 'jenis_kelamin' => 'P'],
                ['nis' => '0006.22.0116', 'nama' => 'Serena Christabelle Eliane Bulan', 'kelas' => 'Primary A', 'jenis_kelamin' => 'L'],
                ['nis' => '0006.25.0226', 'nama' => 'Seyna Joevanca Tacoy', 'kelas' => 'Primary A', 'jenis_kelamin' => 'P'],
                ['nis' => '0011.24.0198', 'nama' => 'Sheena Zelia Kimberly Modok', 'kelas' => 'Primary A', 'jenis_kelamin' => 'L'],
                ['nis' => '0001.23.0135', 'nama' => 'Tristan Jacob Kase', 'kelas' => 'Primary A', 'jenis_kelamin' => 'P'],
                ['nis' => '0001.23.0151', 'nama' => 'Umbu Jesse De Henson Kia', 'kelas' => 'Primary A', 'jenis_kelamin' => 'L'],
                ['nis' => '0007.21.0095', 'nama' => 'Ailsie Lianto Hartanto', 'kelas' => 'Primary B', 'jenis_kelamin' => 'P'],
                ['nis' => '0010.21.0104', 'nama' => 'Darell Gerarldo Pandie', 'kelas' => 'Primary B', 'jenis_kelamin' => 'L'],
                ['nis' => '0006.22.0119', 'nama' => 'Disral Gibyan Ndun', 'kelas' => 'Primary B', 'jenis_kelamin' => 'P'],
                ['nis' => '0007.21.0091', 'nama' => 'Elsie Grezia Walengi', 'kelas' => 'Primary B', 'jenis_kelamin' => 'L'],
                ['nis' => '0001.19.0047', 'nama' => 'Fransisco Pattrick Foeh', 'kelas' => 'Primary B', 'jenis_kelamin' => 'P'],
                ['nis' => '0002.24.0175', 'nama' => 'Juan Pedro Hottaruly Berelaku', 'kelas' => 'Primary B', 'jenis_kelamin' => 'L'],
                ['nis' => '0007.21.0085', 'nama' => 'King James Hun', 'kelas' => 'Primary C', 'jenis_kelamin' => 'P'],
                ['nis' => '0007.22.0123', 'nama' => 'Marcelino Frans Boy Hizikiel Nait', 'kelas' => 'Primary B', 'jenis_kelamin' => 'L'],
                ['nis' => '0007.21.0092', 'nama' => 'Noel Abraham Rihi Biha', 'kelas' => 'Primary B', 'jenis_kelamin' => 'P'],
                ['nis' => '0007.19.0054', 'nama' => 'Nyala Abraham Samuel Pian', 'kelas' => 'Primary B', 'jenis_kelamin' => 'L'],
                ['nis' => '0002.26.0234', 'nama' => 'Queenie Alexa Fiorentino', 'kelas' => 'Primary B', 'jenis_kelamin' => 'P'],
                ['nis' => '0004.22.0110', 'nama' => 'Rafan Althaf Hermantoro', 'kelas' => 'Primary B', 'jenis_kelamin' => 'L'],
                ['nis' => '0003.24.0183', 'nama' => 'Reynard Ethan Tanoto', 'kelas' => 'Primary C', 'jenis_kelamin' => 'P'],
                ['nis' => '0007.19.0050', 'nama' => 'Tiara Dominique Kase', 'kelas' => 'Primary C', 'jenis_kelamin' => 'L'],
                ['nis' => '0007.19.0051', 'nama' => 'Vanessa Audrey Tjung', 'kelas' => 'Primary B', 'jenis_kelamin' => 'P'],
                ['nis' => '0007.20.0072', 'nama' => 'Viany Nathania Maakh', 'kelas' => 'Primary B', 'jenis_kelamin' => 'L'],
                ['nis' => '0007.19.0060', 'nama' => 'Vienna Makayla Djung', 'kelas' => 'Primary B', 'jenis_kelamin' => 'P'],
                ['nis' => '0003.26.0246', 'nama' => 'Veronica Ivana Godelava', 'kelas' => 'Primary B', 'jenis_kelamin' => 'L'],
                ['nis' => '0001.23.0155', 'nama' => 'Allen Zefano Li Tanudjaja', 'kelas' => 'Primary C', 'jenis_kelamin' => 'P'],
                ['nis' => '0001.23.0136', 'nama' => 'Dheara Viddi Christiani Weo Tambaru', 'kelas' => 'Primary C', 'jenis_kelamin' => 'L'],
                ['nis' => '0007.18.0031', 'nama' => 'Janice Lianto', 'kelas' => 'Primary C', 'jenis_kelamin' => 'P'],
                ['nis' => '0007.19.0053', 'nama' => 'Katelynn Aileen Talitan', 'kelas' => 'Primary C', 'jenis_kelamin' => 'L'],
                ['nis' => '0006.26.0242', 'nama' => 'Michael Louisra Kilasaduk', 'kelas' => 'Primary C', 'jenis_kelamin' => 'P'],
                ['nis' => '0001.23.0157', 'nama' => 'Scarlet Damiaty Trinch Fangidae', 'kelas' => 'Primary C', 'jenis_kelamin' => 'L'],
                ['nis' => '0007.17.0010', 'nama' => 'Ziven Gio Han Lassa', 'kelas' => 'Primary C', 'jenis_kelamin' => 'P'],
                ['nis' => '0008.23.0166', 'nama' => 'Altair Patrickliano Umbu Wokura', 'kelas' => 'Junior High', 'jenis_kelamin' => 'L'],
                ['nis' => '0007.17.0008', 'nama' => 'Celine Elianna Tjiang', 'kelas' => 'Junior High', 'jenis_kelamin' => 'P'],
                ['nis' => '0001.19.0044', 'nama' => 'Charise Graciana Jesslyn Irwan', 'kelas' => 'Junior High', 'jenis_kelamin' => 'L'],
                ['nis' => '0004.25.0219', 'nama' => 'Christabelle Fariistha Maya Manafe', 'kelas' => 'Junior High', 'jenis_kelamin' => 'P'],
                ['nis' => '0007.18.0037', 'nama' => 'Dewey Aiden Keeley Lassa', 'kelas' => 'Junior High', 'jenis_kelamin' => 'L'],
                ['nis' => '0003.26.0249', 'nama' => 'Eunike Christabel Panggo', 'kelas' => 'Junior High', 'jenis_kelamin' => 'P'],
                ['nis' => '0007.18.0035', 'nama' => 'Ghijkl Mellchrist Michael Hormu Atock', 'kelas' => 'Junior High', 'jenis_kelamin' => 'L'],
                ['nis' => '0003.26.0247', 'nama' => 'Giovanny Gracia Godelava', 'kelas' => 'Junior High', 'jenis_kelamin' => 'P'],
                ['nis' => '0007.17.0004', 'nama' => 'Harveson Lie Djiauw', 'kelas' => 'Junior High', 'jenis_kelamin' => 'L'],
                ['nis' => '0007.17.0001', 'nama' => 'Jaden Lianto', 'kelas' => 'Junior High', 'jenis_kelamin' => 'P'],
                ['nis' => '0010.19.0063', 'nama' => 'Jaden Emmanuel Tjung', 'kelas' => 'Junior High', 'jenis_kelamin' => 'L'],
                ['nis' => '0007.17.0011', 'nama' => 'Jasen Lianto', 'kelas' => 'Junior High', 'jenis_kelamin' => 'P'],
                ['nis' => '0001.23.0138', 'nama' => 'Jodhean Christian Vidald Weo Tambaru', 'kelas' => 'Junior High', 'jenis_kelamin' => 'L'],
                ['nis' => '0003.26.0248', 'nama' => 'Joshua Fidelis Elan', 'kelas' => 'Junior High', 'jenis_kelamin' => 'P'],
                ['nis' => '0006.25.0224', 'nama' => 'Josua Febryan Simargus Polin', 'kelas' => 'Junior High', 'jenis_kelamin' => 'L'],
                ['nis' => '0003.24.0185', 'nama' => 'Joy Angels Tirzah Sahertian', 'kelas' => 'Junior High', 'jenis_kelamin' => 'P'],
                ['nis' => '0007.22.0122', 'nama' => 'Ormaxwell Abel M. Nait', 'kelas' => 'Junior High', 'jenis_kelamin' => 'L'],
                ['nis' => '0001.19.0045', 'nama' => 'Owen Shine Irwan', 'kelas' => 'Junior High', 'jenis_kelamin' => 'P'],
                ['nis' => '0007.17.0005', 'nama' => 'Sonia Marshia Walengi', 'kelas' => 'Junior High', 'jenis_kelamin' => 'L'],
                ['nis' => '0001.21.0077', 'nama' => 'William Lionel Tunga', 'kelas' => 'Junior High', 'jenis_kelamin' => 'P'],
                ['nis' => '0004.22.0111', 'nama' => 'Willyam Adoe Jr', 'kelas' => 'Junior High', 'jenis_kelamin' => 'L'],
                ['nis' => '0003.26.0239', 'nama' => 'Zefa Abyatar Welkis', 'kelas' => 'Junior High', 'jenis_kelamin' => 'P'],
                ['nis' => '0007.18.0036', 'nama' => 'Abcdef Mellchrist Joyness Hormu Atock', 'kelas' => 'Senior High', 'jenis_kelamin' => 'L'],
                ['nis' => '0001.24.0173', 'nama' => 'Adolf Yogi Manuel F. Djami Rebo', 'kelas' => 'Senior High', 'jenis_kelamin' => 'P'],
                ['nis' => '0007.22.0121', 'nama' => 'Angelita C. Varaquenza Nait', 'kelas' => 'Senior High', 'jenis_kelamin' => 'L'],
                ['nis' => '0007.17.0007', 'nama' => 'Bryan Emmanuel Tjiang', 'kelas' => 'Senior High', 'jenis_kelamin' => 'P'],
                ['nis' => '0007.17.0006', 'nama' => 'Christy Esther Walengi', 'kelas' => 'Senior High', 'jenis_kelamin' => 'L'],
                ['nis' => '0010.19.0062', 'nama' => 'Clara Adela Itu', 'kelas' => 'Senior High', 'jenis_kelamin' => 'P'],
                ['nis' => '0003.26.0241', 'nama' => 'Gilbert Evan Christian Kilasaduk', 'kelas' => 'Senior High', 'jenis_kelamin' => 'L'],
                ['nis' => '0007.17.0003', 'nama' => 'Hannah Lenora Djiauw', 'kelas' => 'Senior High', 'jenis_kelamin' => 'P'],
                ['nis' => '0007.21.0078', 'nama' => 'Joshua Voss', 'kelas' => 'Senior High', 'jenis_kelamin' => 'L'],
                ['nis' => '0007.17.0002', 'nama' => 'Lavena Hayley Djiauw', 'kelas' => 'Senior High', 'jenis_kelamin' => 'P'],
                ['nis' => '0001.26.0233', 'nama' => 'Lelicia Francisca Dos Santos Ribeiro', 'kelas' => 'Senior High', 'jenis_kelamin' => 'L'],
                ['nis' => '0011.24.0201', 'nama' => 'Maggi Rosario Nheu Mak', 'kelas' => 'Senior High', 'jenis_kelamin' => 'P'],
                ['nis' => '0007.22.0120', 'nama' => 'Medalin G. Margareth Nait', 'kelas' => 'Senior High', 'jenis_kelamin' => 'L'],
                ['nis' => '0007.21.0081', 'nama' => 'Miguel Gavillov R. K Amalo', 'kelas' => 'Senior High', 'jenis_kelamin' => 'P'],
                ['nis' => '0006.25.0223', 'nama' => 'Natasya Dillarya', 'kelas' => 'Senior High', 'jenis_kelamin' => 'L'],
                ['nis' => '0006.26.0252-B', 'nama' => 'Pricilla Abigail Amalo', 'kelas' => 'Senior High', 'jenis_kelamin' => 'P'],
                ['nis' => '0003.26.0250', 'nama' => 'Rachelle Grazmey Tunliu', 'kelas' => 'Senior High', 'jenis_kelamin' => 'L'],
                ['nis' => '0005.18.0026', 'nama' => 'Samuel Ulrich Raya', 'kelas' => 'Senior High', 'jenis_kelamin' => 'P'],
                ['nis' => '0003.25.0213', 'nama' => 'Soli Deo Glorya Ratu Lado', 'kelas' => 'Senior High', 'jenis_kelamin' => 'L'],
                ['nis' => '0004.25.0217', 'nama' => 'Valentino Agustinus Doko Junior', 'kelas' => 'Senior High', 'jenis_kelamin' => 'P'],
                ['nis' => '0001.23.0137', 'nama' => 'Vidhea Christiani Ruth Weo Tambaru', 'kelas' => 'Junior High', 'jenis_kelamin' => 'L'],
            ];

            foreach ($studentsData as $data) {
                $student = Student::firstOrCreate(['nis' => $data['nis']], $data);

                // Seed sample today attendance for demonstration
                Attendance::create([
                    'student_id' => $student->id,
                    'tanggal' => now()->format('Y-m-d'),
                    'status' => $student->id % 7 === 0 ? 'sakit' : ($student->id % 5 === 0 ? 'izin' : ($student->id % 11 === 0 ? 'alpa' : 'hadir')),
                    'keterangan' => 'Presensi Otomatis ESCS',
                ]);

                // Create Parent Account (nama_depan.nama_belakang@student.sch.id) for student
                $parentEmail = \App\Http\Controllers\StudentController::generateParentEmail($student->nama, $student->id);
                User::updateOrCreate(
                    ['student_id' => $student->id, 'role' => 'orang_tua'],
                    [
                        'name' => 'Orang Tua (' . $student->nama . ')',
                        'email' => $parentEmail,
                        'password' => Hash::make('password'),
                        'email_verified_at' => now(),
                    ]
                );
            }
        }
    }
}
