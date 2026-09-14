# Portal Sistem Informasi & Absensi Sekolah Digital - NTO National Plus

Sistem Informasi Manajemen Sekolah dan Presensi Terpadu Berbasis Web yang dirancang menggunakan **Laravel 12 / 13**, **Tailwind CSS**, **Alpine.js**, **Chart.js**, dan arsitektur keamanan modern. Sistem ini disesuaikan khusus dengan standar operasional dan identitas visual **Excellent Spirit Christian School (NTO) Kupang**.

![PHP Version](https://img.shields.io/badge/PHP-8.3%20%7C%208.4-777BB4?style=flat&logo=php)
![Laravel Version](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=flat&logo=laravel)
![Security](https://img.shields.io/badge/Security-RBAC%20%26%20Traversal--Proof-emerald?style=flat&logo=shield)
![License](https://img.shields.io/badge/License-MIT-blue)

---

## 📑 Daftar Isi

- [Deskripsi Sistem](#-deskripsi-sistem)
- [Akun Pengguna & Akses Sistem](#-akun-pengguna--akses-sistem)
  - [1. Akun Default Administrator](#1-akun-default-administrator)
  - [2. Akun Guru / Wali Kelas](#2-akun-guru--wali-kelas)
  - [3. Portal Orang Tua Siswa](#3-portal-orang-tua-siswa)
- [Modul & Fitur Utama](#-modul--fitur-utama)
  - [1. Modul Pekerjaan Rumah (PR) & Penilaian Siswa](#1-modul-pekerjaan-rumah-pr--penilaian-siswa)
  - [2. Modul Tagihan & Pembayaran Keuangan (SPP)](#2-modul-tagihan--pembayaran-keuangan-spp)
  - [3. Modul Surat Izin Orang Tua & Verifikasi Wali Kelas](#3-modul-surat-izin-orang-tua--verifikasi-wali-kelas)
  - [4. Modul Presensi Harian & QR Code Scanner](#4-modul-presensi-harian--qr-code-scanner)
  - [5. Panel Manajemen Guru & Switch Account (Impersonate)](#5-panel-manajemen-guru--switch-account-impersonate)
  - [6. Manajemen Password & Reset Mandiri](#6-manajemen-password--reset-mandiri)
  - [7. Keamanan & Proteksi Data (Security Hardening)](#7-keamanan--proteksi-data-security-hardening)
- [Panduan Instalasi & Pengoperasian](#-panduan-instalasi--pengoperasian)
- [Pengujian Sistem (TDD / Testing)](#-pengujian-sistem-tdd--testing)
- [Lisensi](#-lisensi)

---

## 🏫 Deskripsi Sistem

Portal Sekolah NTO National Plus merupakan platform operasional terpadu yang menghubungkan Administrator, Guru/Wali Kelas, dan Orang Tua Siswa secara *real-time*. Platform ini mengotomatisasi pencatatan kehadiran berbasis kartu QR Code, distribusi tugas PR dan penilaian digital, pemantauan pembayaran SPP/tagihan, hingga verifikasi surat izin sakit siswa.

---

## 👥 Akun Pengguna & Akses Sistem

Sistem menerapkan arsitektur **Multi-Role Access Control (RBAC)** ketat yang terbagi ke dalam 3 tingkatan pengguna:

### 1. Akun Default Administrator
Basis data bawaan (*clean database*) telah disediakan akun Administrator utama untuk mengelola seluruh sistem:

| Peran (Role) | Alamat Email | Kata Sandi | Wewenang & Hak Akses |
| :--- | :--- | :--- | :--- |
| **Administrator Utama** | `admin@nto-kupang.sch.id` | `admin123` | Akses Penuh Sistem: Master Data Siswa, Kelola Akun Guru, Buat Tagihan SPP & Verifikasi Pembayaran, Switch Mode Guru (Impersonate), Pengaturan Jam Masuk/Pulang, Pantau Surat Izin, Reset Sandi Pengguna. |

---

### 2. Akun Guru / Wali Kelas
Akun Guru / Wali Kelas dapat ditambahkan dan diatur oleh Administrator melalui menu **Kelola Guru**. Setiap wali kelas memiliki isolasi data (*scoping*) sesuai kelas yang diampu:
- **Tingkat Kelas Tersedia:** *Nursery, Pre-K, Kindergarten, Primary Preparation, Primary A, Primary B, Primary C, Junior High, Senior High, Kelas 1 - 6*.
- **Hak Akses:** Input presensi harian kelas binaan, buat & bagikan PR, periksa foto jawaban PR, beri nilai (0-100) & catatan guru, cetak rekap nilai A4, konfirmasi terima/tolak surat izin siswa.

---

### 3. Portal Orang Tua Siswa
Akun Orang Tua dibuat secara otomatis ketika data siswa ditambahkan oleh Admin:
- **Format Email:** `nama_depan.nama_belakang@student.sch.id` (contoh: `david.pratama@student.sch.id`)
- **Kata Sandi Default:** `password`
- **Hak Akses:** Melihat tren kehadiran anak, mengunggah surat izin/sakit beserta foto fisik, melihat tugas PR & mengunggah foto pengerjaan PR anak, memantau rincian tagihan SPP & mengunggah bukti transfer pembayaran.

---

## 🚀 Modul & Fitur Utama

### 1. Modul Pekerjaan Rumah (PR) & Penilaian Siswa
- **Pembuatan Tugas Fleksibel:** Guru dapat menugaskan PR untuk seluruh siswa di kelasnya atau khusus siswa tertentu (*remedial/tugas khusus*) dengan batas waktu (*deadline*) dan lampiran berkas materi/soal.
- **Pengumpulan Digital oleh Siswa:** Orang tua dapat melihat instruksi dan mengunggah foto lembar jawaban PR langsung dari portal.
- **Penilaian & Evaluasi Guru:** Guru dapat melihat pratinjau foto jawaban, menginput nilai skala 0–100, memberikan catatan *feedback*, serta melihat statistik nilai tertinggi, terendah, dan rata-rata kelas.
- **Fitur Hapus PR Manual Siswa:** Guru/Wali Kelas memiliki opsi untuk menghapus data pengumpulan PR siswa secara manual (termasuk yang sudah dinilai) lengkap dengan pembersihan file foto dari storage jika diperlukan pengumpulan ulang.
- **Cetak Rekap Nilai A4:** Cetak rekapitulasi penilaian dan pengumpulan tugas dalam format resmi A4.

### 2. Modul Tagihan & Pembayaran Keuangan (SPP)
- **Penerbitan Tagihan:** Admin dapat membuat tagihan per siswa spesifik atau serentak ke seluruh siswa dalam satu kelas.
- **Pengunggahan Bukti Transfer:** Orang tua dapat melihat status tagihan (*Belum Lunas, Menunggu Verifikasi, Lunas, Ditolak*) dan mengunggah bukti transfer/struk pembayaran.
- **Verifikasi Pembayaran oleh Admin:** Admin meninjau bukti pembayaran lalu menyetujui (status menjadi *LUNAS*) atau menolak bukti dengan catatan revisi.

### 3. Modul Surat Izin Orang Tua & Verifikasi Wali Kelas
- **Pengajuan Izin Mandiri:** Orang tua dapat mengajukan izin/sakit dengan melampirkan foto surat dokter atau surat izin bertandatangan.
- **Otoritas Khusus Wali Kelas:** Hak konfirmasi Terima (*Setujui*) atau Tolak surat izin hanya dimiliki oleh Guru/Wali Kelas dari kelas siswa terkait. Jika ditolak, status kehadiran siswa otomatis berubah menjadi **Alpa**.
- **Mode Monitor Admin:** Admin bertindak sebagai pemantau (*read-only*) untuk menjaga integritas data tanpa mengambil alih wewenang wali kelas.
- **Hapus Berkas Surat:** Guru, Admin, atau Orang Tua pemilik dapat menghapus berkas lampiran jika terjadi kesalahan unggah.

### 4. Modul Presensi Harian & QR Code Scanner
- **Cetak Kartu QR Code:** Setiap siswa memiliki kartu ID berisikan QR Code unik NIS siap cetak.
- **Scanner Terintegrasi:** Mendukung pemindaian via kamera perangkat (laptop/smartphone) maupun barcode scanner eksternal.
- **Deteksi Keterlambatan Otomatis:** Perhitungan durasi keterlambatan presisi (dalam menit/jam) berdasarkan jam operasional sekolah untuk siswa reguler maupun siswa ABK.
- **Proteksi Scan Pertama:** Pemindaian ganda pada hari yang sama tidak akan menimpa waktu scan kedatangan pertama.
- **Rekap Bulanan & Cetak A4 Lanskap:** Visualisasi matriks kehadiran bulanan dan cetak rekap absensi resmi A4.

### 5. Panel Manajemen Guru & Switch Account (Impersonate)
- **Kelola Akun Guru:** Admin dapat menambah, mengedit, atau menghapus akun guru serta menetapkan kelas yang diampu.
- **Fitur Switch Account:** Fasilitas 1-klik bagi Admin untuk beralih sesi (*login sebagai guru*) guna memeriksa tampilan portal guru tanpa perlu logout, serta tombol kembali ke akun Admin kapan saja.

### 6. Manajemen Password & Reset Mandiri
- **Permintaan Reset Sandi:** Pengguna yang lupa kata sandi dapat mengirimkan permohonan melalui halaman Login tanpa konfigurasi SMTP email rumit.
- **Panel Persetujuan Admin:** Admin menerima daftar permintaan reset dan dapat menetapkan password baru secara instan.
- **Show / Hide Password:** Ikon toggle mata pada seluruh form input sandi untuk kenyamanan pengguna.

### 7. Keamanan & Proteksi Data (Security Hardening)
- **Path Traversal Protection:** Penanganan route storage fallback diamankan secara ketat terhadap injeksi `..`, null-byte, dan penolakan otomatis untuk file konfigurasi sensitif (*dotfiles* seperti `.env`).
- **Orphan File Cleanup:** Pembersihan file fisik otomatis di disk storage saat data siswa, surat izin, bukti pembayaran, atau PR dihapus/diperbarui.
- **Strict Role Authorization:** Seluruh aksi sensitif divalidasi pada level controller untuk mencegah eskalasi hak akses (*privilege escalation*).

---

## 🛠️ Panduan Instalasi & Pengoperasian

Ikuti langkah-langkah berikut untuk menjalankan sistem di komputer lokal:

### 1. Masuk ke Direktori Proyek
```bash
cd absensi-sekolah
```

### 2. Instal Dependensi PHP & JavaScript
```bash
composer install
npm install
```

### 3. Konfigurasi Environment (`.env`)
Salin file `.env.example` ke `.env` dan generate application key:
```bash
cp .env.example .env
php artisan key:generate
```

Pastikan konfigurasi database di file `.env` sudah sesuai:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=absensi_sekolah
DB_USERNAME=root
DB_PASSWORD=
APP_TIMEZONE=Asia/Makassar
```

### 4. Migrasi & Seed Database
Jalankan migrasi database:
```bash
php artisan migrate:fresh --seed
```
*(Atau Anda dapat mengimpor langsung berkas `database.sql` ke database MySQL).*

### 5. Jalankan Link Storage
```bash
php artisan storage:link
```

### 6. Jalankan Server Lokal
- **Terminal 1 (Server PHP):**
  ```bash
  php -S localhost:5000 server.php
  ```
  *atau:*
  ```bash
  php artisan serve
  ```
- **Terminal 2 (Asset Compiler - Opsional saat development):**
  ```bash
  npm run dev
  ```

### 7. Buka di Browser
Akses aplikasi melalui peramban: **[http://localhost:5000/login](http://localhost:5000/login)**  
Gunakan akun Administrator:
- **Email:** `admin@nto-kupang.sch.id`
- **Password:** `admin123`

---

## 🧪 Pengujian Sistem (TDD / Testing)

Sistem telah diuji secara komprehensif menggunakan PHPUnit / Laravel Feature Testing:

```bash
php artisan test
```

Cakupan pengujian mencakup:
- ✅ Otorisasi Hak Akses Multi-Role (Admin, Guru, Orang Tua)
- ✅ Alur Pengunggahan, Penilaian, dan Penghapusan PR
- ✅ Verifikasi Surat Izin Khusus Wali Kelas
- ✅ Isolasi Tagihan & Pembayaran SPP
- ✅ Proteksi Path Traversal pada File Serving Fallback

---

## 📄 Lisensi

Sistem Informasi Absensi Sekolah NTO National Plus dirilis di bawah lisensi [MIT License](LICENSE).
