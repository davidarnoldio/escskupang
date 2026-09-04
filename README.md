# Portal Absensi Sekolah Digital - NTO National Plus

Sistem Informasi Manajemen Presensi Sekolah Terpadu Berbasis Web yang dirancang menggunakan Laravel 11, Tailwind CSS, Alpine.js, Chart.js, dan Cropper.js. Sistem ini menyesuaikan standar operasional dan identitas visual Excellent Spirit Christian School (NTO) Kupang.

![PHP Version](https://img.shields.io/badge/PHP-8.3-777BB4?style=flat&logo=php)
![Laravel Version](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=flat&logo=laravel)
![Tests Status](https://img.shields.io/badge/Tests-82%20Passed-emerald?style=flat&logo=phpunit)
![License](https://img.shields.io/badge/License-MIT-blue)

---

## Daftar Isi

- [Portal Absensi Sekolah Digital - NTO National Plus](#portal-absensi-sekolah-digital---escs-kupang)
  - [Daftar Isi](#daftar-isi)
  - [Deskripsi Sistem](#deskripsi-sistem)
  - [Akun Pengguna dan Hak Akses](#akun-pengguna-dan-hak-akses)
    - [1. Administrator Utama](#1-administrator-utama)
    - [2. Wali Kelas Per Tingkat](#2-wali-kelas-per-tingkat)
    - [3. Portal Orang Tua Siswa](#3-portal-orang-tua-siswa)
      - [Contoh Akun Demo Orang Tua:](#contoh-akun-demo-orang-tua)
  - [Fitur Utama Sistem](#fitur-utama-sistem)
  - [Panduan Instalasi dan Pengoperasian](#panduan-instalasi-dan-pengoperasian)
  - [Pengujian Otomatis (Testing)](#pengujian-otomatis-testing)
  - [Lisensi](#lisensi)

---

## Deskripsi Sistem

Portal Absensi Sekolah NTO National Plus merupakan solusi terpadu untuk pencatatan, pemantauan, dan pelaporan kehadiran siswa secara harian dan bulanan. Sistem dilengkapi fitur pindai QR Code, perizinan surat orang tua, pencatatan durasi keterlambatan, analisis statistik interaktif, serta rekapitulasi presensi cetak A4.

---

## Akun Pengguna dan Hak Akses

Sistem menerapkan arsitektur Multi-Role Access Control yang terbagi menjadi 3 tingkat pengguna:

### 1. Administrator Utama

Administrator memiliki hak akses penuh ke seluruh modul sistem, termasuk kelola akun guru, reset kata sandi, pengaturan jam presensi, dan supervisi data presensi.

| Peran (Role) | Alamat Email (Username) | Kata Sandi (Password) | Hak Akses |
| :--- | :--- | :--- | :--- |
| **Administrator Utama** | `admin@nto-kupang.sch.id` | `admin123` | Akses Penuh Sistem (Kelola Guru, Reset Sandi, Pengaturan Jam, Master Siswa, Scan QR, Impersonate, Rekap A4, Export Excel) |
| **Guru Pengajar (Demo)** | `test@example.com` | `password` | Input Presensi Harian, Scan QR, Rekap & Export Excel |

---

### 2. Wali Kelas Per Tingkat

Setiap wali kelas memiliki batasan akses data (Scoping) otomatis, sehingga hanya dapat melihat dan mengelola data siswa pada kelas binaannya.

| Tingkat Kelas | Nama Wali Kelas | Alamat Email (Username) | Kata Sandi | Batasan Akses Data |
| :--- | :--- | :--- | :--- | :--- |
| **Nursery** | Vivi Nalle | `vivinalle@nto-kupang.sch.id` | `vivi123` | Khusus Kelas Nursery |
| **Pre-K** | Asnat | `asnat@nto-kupang.sch.id` | `asnat123` | Khusus Kelas Pre-K |
| **Kindergarten** | Ellen | `ellen@nto-kupang.sch.id` | `ellen123` | Khusus Kelas Kindergarten |
| **Primary Preparation** | Kezia | `kezia@nto-kupang.sch.id` | `kezia123` | Khusus Kelas Primary Preparation |
| **Primary A** | Amel | `amel@nto-kupang.sch.id` | `amel123` | Khusus Kelas Primary A |
| **Primary B** | Wulan | `wulan@nto-kupang.sch.id` | `wulan123` | Khusus Kelas Primary B |
| **Primary C** | Aldi | `aldi@nto-kupang.sch.id` | `aldi123` | Khusus Kelas Primary C |
| **Junior High** | Beatrix | `beatrix@nto-kupang.sch.id` | `beatrix123` | Khusus Kelas Junior High |
| **Senior High** | Anjash | `anjash@nto-kupang.sch.id` | `anjash123` | Khusus Kelas Senior High |

---

### 3. Portal Orang Tua Siswa

Setiap siswa memiliki akun Portal Orang Tua terintegrasi dengan format email `nama_depan.nama_belakang@student.sch.id` dan kata sandi awal `password`.

#### Contoh Akun Demo Orang Tua:

| Nama Siswa | Alamat Email (Username) | Kata Sandi | Tingkat Kelas |
| :--- | :--- | :--- | :--- |
| **Sierrafim Malelak** | `sierrafim.malelak@student.sch.id` | `password` | Nursery |
| **Yosua Ndeo** | `yosua.ndeo@student.sch.id` | `password` | Nursery |
| **Calvin Dima** | `calvin.dima@student.sch.id` | `password` | Pre-K |
| **Claire Koehuan** | `claire.koehuan@student.sch.id` | `password` | Pre-K |
| **Audlyn Ang** | `audlyn.ang@student.sch.id` | `password` | Kindergarten |
| **Alleluia Kalla** | `alleluia.kalla@student.sch.id` | `password` | Primary A |
| **Abcdef Atock** | `abcdef.atock@student.sch.id` | `password` | Senior High |

---

## Fitur Utama Sistem

1. **Pemindaian QR Code dan Kalkulasi Keterlambatan Presisi:**
   - Pemindaian NIS via kamera atau scanner barcode.
   - Deteksi keterlambatan otomatis berdasarkan jam masuk reguler (07:30 WITA) dan khusus siswa ABK (08:30 WITA).
   - Pencatatan durasi keterlambatan presisi dalam satuan menit dan jam (contoh: Hadir - Terlambat 15m).
   - Perlindungan stempel scan pertama: Pemindaian ulang pada hari yang sama tetap mempertahankan jam scan dan durasi keterlambatan pertama.

2. **Modul Rekapitulasi Keterlambatan Siswa:**
   - Kartu statistik indikator khusus Total Terlambat (Siswa).
   - Tabel rekapitulasi khusus siswa yang terlambat beserta jam scan, batas jam masuk, dan durasi keterlambatan.

3. **Permintaan Reset Kata Sandi dan Panel Admin:**
   - Pengajuan permohonan reset password dari pengguna tanpa pengiriman email SMTP.
   - Panel persetujuan Admin untuk memperbarui kata sandi secara instan.
   - Fitur Show / Hide Password (Ikon Mata) pada seluruh formulir autentikasi.

4. **Fitur Impersonate Akun Guru (Switch Account):**
   - Fasilitas bagi Administrator untuk beralih sesi ke akun wali kelas tertentu tanpa perlu melakukan logout dan login ulang.

5. **Portal Orang Tua dan Pengiriman Surat Izin:**
   - Grafik tren presensi harian dan bulanan khusus untuk setiap anak.
   - Pengiriman surat perizinan / sakit beserta lampiran foto fisik dokumen.

6. **Manajemen Presensi 5-Status dan Batch Input:**
   - Opsi status presensi: Hadir, Izin, Sakit, Alpa, dan Libur.
   - Fitur cepat "Set Semua Hadir" dan "Set Semua Libur".

7. **Pelaporan dan Rekapitulasi Presensi Cetak A4:**
   - Cetak rekapitulasi bulanan berformat resmi A4 Lanskap.

---

## Panduan Instalasi dan Pengoperasian

Follow langkah-langkah berikut untuk menjalankan sistem di lingkungan lokal:

1. **Clone dan Masuk ke Direktori Proyek:**
   ```bash
   cd absensi-sekolah
   ```

2. **Instal Dependensi PHP dan JavaScript:**
   ```bash
   composer install
   npm install
   ```

3. **Salin File Environment dan Generate Key:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Konfigurasi Database MySQL di File `.env`:**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=absensi_sekolah
   DB_USERNAME=root
   DB_PASSWORD=
   APP_TIMEZONE=Asia/Makassar
   ```

5. **Jalankan Migrasi Database dan Seed Data:**
   ```bash
   php artisan migrate:fresh --seed
   ```
   *(Atau impor file `database.sql` ke dalam MySQL)*

6. **Jalankan Server Lokal dan Compiler Asset:**
   - Terminal 1 (PHP Server):
     ```bash
     php -S localhost:5000 server.php
     ```
   - Terminal 2 (Vite Compiler):
     ```bash
     npm run dev
     ```

7. **Buka Aplikasi di Browser:**
   Akses alamat: **[http://localhost:5000/login](http://localhost:5000/login)**

---

## Pengujian Otomatis (Testing)

Pengujian perangkat lunak dilakukan menggunakan PHPUnit (Test Driven Development):

```bash
vendor/bin/phpunit
```

Status Pengujian: `OK (82 tests, 262 assertions)`

---

## Lisensi

Sistem ini dirilis di bawah lisensi [MIT License](LICENSE).
