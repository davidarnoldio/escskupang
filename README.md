# 🏫 Portal Absensi Sekolah Digital - ESCS Kupang

Aplikasi Manajemen Presensi Sekolah Terpadu Berbasis Web menggunakan **Laravel 11**, **Tailwind CSS**, **Alpine.js**, **Chart.js**, dan **Cropper.js** dengan tema visual branding **Excellent Spirit Christian School (ESCS) Kupang** (Royal Blue, Sky Blue, & Slate Navy).

![PHP Version](https://img.shields.io/badge/PHP-8.2.12-777BB4?style=flat&logo=php)
![Laravel Version](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=flat&logo=laravel)
![Tests Status](https://img.shields.io/badge/Tests-82%20Passed-emerald?style=flat&logo=phpunit)
![License](https://img.shields.io/badge/License-MIT-blue)

---

## 🔑 Kredensial Login Lengkap (User Accounts)

Aplikasi mendukung 3 peran pengguna (*multi-role system*): **Administrator**, **Guru / Wali Kelas Per Tingkat**, dan **Orang Tua Siswa**.

### 1. 🛡️ Akun Administrator Utama
| Peran (Role) | Alamat Email (Username) | Kata Sandi (Password) | Deskripsi Hak Akses |
| :--- | :--- | :--- | :--- |
| **Administrator Utama** | `admin@escs-kupang.sch.id` | `admin123` | Akses Penuh (Reset Sandi Instan, Notifikasi Lupa Password, Switch Akun Guru, Kelola Guru, Master Siswa, Scan QR, Export Excel, Cetak Rekap A4, Pengaturan Jam) |
| **Guru Pengajar (Demo)** | `test@example.com` | `password` | Input Presensi Harian, Scan QR, Rekap & Export Excel |

---

### 2. 🏫 Akun Wali Kelas Per Tingkat (Homeroom Teachers)
*Wali Kelas memiliki pembatasan akses (*scoping*) otomatis sehingga hanya melihat data siswa, statistik dashboard, presensi, dan rekapitulasi kelasnya masing-masing.*

| Tingkat Kelas | Wali Kelas | Alamat Email (Username) | Kata Sandi | Pembatasan Akses Data |
| :--- | :--- | :--- | :--- | :--- |
| **Nursery** | Vivi Nalle | `vivinalle@escs-kupang.sch.id` | `vivi123` | Hanya Siswa & Rekap Kelas Nursery |
| **Pre-K** | Asnat | `asnat@escs-kupang.sch.id` | `asnat123` | Hanya Siswa & Rekap Kelas Pre-K |
| **Kindergarten** | Ellen | `ellen@escs-kupang.sch.id` | `ellen123` | Hanya Siswa & Rekap Kelas Kindergarten |
| **Primary Preparation** | Kezia | `kezia@escs-kupang.sch.id` | `kezia123` | Hanya Siswa & Rekap Kelas Primary Preparation |
| **Primary A** | Amel | `amel@escs-kupang.sch.id` | `amel123` | Hanya Siswa & Rekap Kelas Primary A |
| **Primary B** | Wulan | `wulan@escs-kupang.sch.id` | `wulan123` | Hanya Siswa & Rekap Kelas Primary B |
| **Primary C** | Aldi | `aldi@escs-kupang.sch.id` | `aldi123` | Hanya Siswa & Rekap Kelas Primary C |
| **Junior High** | Beatrix | `beatrix@escs-kupang.sch.id` | `beatrix123` | Hanya Siswa & Rekap Kelas Junior High |
| **Senior High** | Anjash | `anjash@escs-kupang.sch.id` | `anjash123` | Hanya Siswa & Rekap Kelas Senior High |

---

### 3. 👨‍👩‍👧 Akun Orang Tua Siswa (Parent Portal `@student.sch.id`)
*Setiap siswa di sekolah (135 siswa) secara otomatis dibuatkan akun Orang Tua berformat email:* **`nama_depan.nama_belakang@student.sch.id`** *dengan kata sandi default:* **`password`**.

#### Contoh Akun Demo Orang Tua:
| Nama Murid | Alamat Email (Username) | Kata Sandi | Tingkat Kelas |
| :--- | :--- | :--- | :--- |
| **Sierrafim Malelak** | `sierrafim.malelak@student.sch.id` | `password` | Nursery |
| **Yosua Ndeo** | `yosua.ndeo@student.sch.id` | `password` | Nursery |
| **Calvin Dima** | `calvin.dima@student.sch.id` | `password` | Pre-K |
| **Claire Koehuan** | `claire.koehuan@student.sch.id` | `password` | Pre-K |
| **Audlyn Ang** | `audlyn.ang@student.sch.id` | `password` | Kindergarten |
| **Alleluia Kalla** | `alleluia.kalla@student.sch.id` | `password` | Primary A |
| **Abcdef Atock** | `abcdef.atock@student.sch.id` | `password` | Senior High |

---

## ✨ Rincian Seluruh Fitur Sistem

### 🔔 1. Notifikasi Lupa Password & Reset Sandi Instan oleh Admin
- **Form Lupa Password Bahasa Indonesia (`/forgot-password`):** Pengguna (Guru/Orang Tua) cukup memasukkan email lalu klik tombol **📢 Kirim Notifikasi Lupa Password ke Admin**.
- **Panel Reset Password Admin (`/admin/password-requests`):** Admin menerima notifikasi badge merah dan dapat langsung melihat email/nama pengguna yang meminta reset. Admin dapat memasukkan password baru dalam 1-klik sehingga akun dapat langsung digunakan login kembali saat itu juga.
- **Tampilan Password Guru:** Admin dapat melihat kata sandi aktif (*plain password*) setiap guru pada tabel Kelola Guru.

### 🔄 2. Admin Switch Akun Guru Instan (Impersonate Teacher)
- **Menu Dropdown Khusus Admin:** Ditempatkan pada menu profil kanan atas di antara link Profile dan tombol Log Out.
- **Switch Tanpa Logout & Login:** Admin mengeklik nama guru/kelas -> Sesi berpindah langsung ke dashboard guru tersebut. Opsi **⏪ Kembali ke Akun Admin** mengembalikan sesi dalam 1-klik.

### 👨‍🏫 3. Kelola Data Guru & Scoping Kelas Dinamis
- **Pengelompokan Berdasarkan Tingkat Kelas:** Tampilan panel admin mengelompokkan guru berdasarkan 9 tingkat kelas resmi (`Nursery` s/d `Senior High`).

### 🔒 4. Autentikasi & Keamanan Sistem
- **Strict Login Protection:** Redirect otomatis pengakses anonim ke `/login`.
- **Show Password Toggle (Ikon Mata):** Tombol toggle mata interaktif pada form login.

### 🏖️ 5. Pengelolaan Status Presensi 5-Opsi (Termasuk 'Libur')
- **5 Opsi Presensi:** `Hadir`, `Izin`, `Sakit`, `Alpa`, dan `Libur`. Tombol cepat **Set Semua Hadir** & **Set Semua Libur**.

### 🌟 6. Pengkategorian Siswa Berkebutuhan Khusus (ABK) & Portal Orang Tua
- Penandaan ABK, Cropper.js foto profil, unggah surat sakit/izin ortu.

### 📄 7. Cetak Rekapitulasi Presensi Resmi A4 Lanskap & Export CSV
- Cetak rekap A4 lanskap per kelas & wali kelas, grafik Chart.js, serta export CSV UTF-8.

---

## 🛠️ Cara Menjalankan Aplikasi di Komputer Lokal (Local Setup)

1. **Clone & Buka Folder Project:**
   ```bash
   cd absensi-sekolah
   ```
2. **Install Dependensi PHP & JavaScript:**
   ```bash
   composer install
   npm install
   ```
3. **Salin File Environment & Generate Key:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. **Konfigurasi Database MySQL di `.env`:**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=absensi_sekolah
   DB_USERNAME=root
   DB_PASSWORD=
   ```
5. **Migrasi Database & Seed Data Asli ESCS Kupang:**
   ```bash
   php artisan migrate:fresh --seed
   ```
   *(Atau impor file `database.sql` langsung ke MySQL)*

6. **Jalankan Server Lokal (Port 5000) & Build Assets:**
   - **Terminal 1 (PHP Web Server):**
     ```bash
     php -S localhost:5000 server.php
     ```
   - **Terminal 2 (Vite Compiler):**
     ```bash
     npm run dev
     ```
7. **Akses di Browser:**
   Buka **[http://localhost:5000/login](http://localhost:5000/login)**

---

## 🧪 Eksekusi Pengujian Otomatis (TDD)

Untuk menjalankan seluruh test suite:
```bash
vendor/bin/phpunit
```
**Hasil Test Suite:** `OK (82 tests, 262 assertions)`

---

## 📄 Lisensi

Aplikasi ini dikembangkan di bawah lisensi [MIT License](LICENSE).
