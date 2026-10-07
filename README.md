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
  - [1. Modul Biodata Lengkap Siswa & Profil Dapodik (NISN)](#1-modul-biodata-lengkap-siswa--profil-dapodik-nisn)
  - [2. Modul Alumni & Rekapitulasi Kelulusan Siswa (Khusus Administrator)](#2-modul-alumni--rekapitulasi-kelulusan-siswa-khusus-administrator)
  - [3. Modul Pekerjaan Rumah (PR) & Penilaian Siswa](#3-modul-pekerjaan-rumah-pr--penilaian-siswa)
  - [4. Modul Tagihan & Pembayaran Keuangan (SPP)](#4-modul-tagihan--pembayaran-keuangan-spp)
  - [5. Modul Surat Izin Orang Tua & Verifikasi Wali Kelas](#5-modul-surat-izin-orang-tua--verifikasi-wali-kelas)
  - [6. Modul Presensi Harian & QR Code Scanner (Siswa & Guru)](#6-modul-presensi-harian--qr-code-scanner-siswa--guru)
  - [7. Panel Manajemen Guru & Switch Account (Impersonate)](#7-panel-manajemen-guru--switch-account-impersonate)
  - [8. Manajemen Password & Reset Mandiri](#8-manajemen-password--reset-mandiri)
  - [9. Keamanan & Proteksi Data (Security Hardening)](#9-keamanan--proteksi-data-security-hardening)
  - [10. Sistem Notifikasi Toast Interaktif (Real-Time Feedback)](#10-sistem-notifikasi-toast-interaktif-real-time-feedback)
- [Panduan Instalasi & Pengoperasian](#-panduan-instalasi--pengoperasian)
- [Pengujian Sistem (TDD / Testing)](#-pengujian-sistem-tdd--testing)
- [Lisensi](#-lisensi)

---

## 🏫 Deskripsi Sistem

Portal Sekolah NTO National Plus merupakan platform operasional terpadu yang menghubungkan Administrator, Guru/Wali Kelas, dan Orang Tua Siswa secara *real-time*. Platform ini mengotomatisasi pendataan biodata komprehensif siswa standar Dapodik/Kependudukan, manajemen arsip kelulusan & alumni, pencatatan kehadiran berbasis kartu QR Code dengan NISN, distribusi tugas PR dan penilaian digital, pemantauan pembayaran SPP/tagihan, hingga verifikasi surat izin sakit siswa.

---

## 👥 Akun Pengguna & Akses Sistem

Sistem menerapkan arsitektur **Multi-Role Access Control (RBAC)** ketat yang terbagi ke dalam 3 tingkatan pengguna:

### 1. Akun Default Administrator
Basis data bawaan (*clean database*) telah disediakan akun Administrator utama untuk mengelola seluruh sistem:

| Peran (Role) | Alamat Email | Kata Sandi | Wewenang & Hak Akses |
| :--- | :--- | :--- | :--- |
| **Administrator Utama** | `admin@ntokupang.sch.id` | `ntokupang123` | Akses Penuh Sistem: Master Data Biodata Lengkap Siswa, Kelulusan & Arsip Alumni, Kelola Akun Guru, Buat Tagihan SPP & Verifikasi Pembayaran, Switch Mode Guru (Impersonate), Pengaturan Jam Masuk/Pulang, Pantau Surat Izin, Reset Sandi Pengguna. |

---

### 2. Akun Guru / Wali Kelas
Akun Guru / Wali Kelas dapat ditambahkan dan diatur oleh Administrator melalui menu **Kelola Guru**. Setiap wali kelas memiliki isolasi data (*scoping*) sesuai kelas yang diampu:
- **Tingkat Kelas Tersedia:** *Nursery, Pre-K, Kindergarten, Primary Preparation, Primary A, Primary B, Primary C, Junior High, Senior High, Kelas 1 - 6*.
- **Hak Akses:** Input presensi harian kelas binaan, buat & bagikan PR, periksa foto jawaban PR, beri nilai (0-100) & catatan guru, cetak rekap nilai A4, konfirmasi terima/tolak surat izin siswa, melihat foto profil & kartu QR siswa binaan (khusus menu Data Alumni dikelola eksklusif oleh Administrator).

---

### 3. Portal Orang Tua Siswa
Akun Orang Tua dibuat secara otomatis ketika data siswa ditambahkan oleh Admin:
- **Format Email:** `nama_depan.nama_belakang@student.sch.id` (contoh: `david.pratama@student.sch.id`)
- **Kata Sandi Default:** `password`
- **Hak Akses:** Melihat foto profil dan tren kehadiran anak, mengunggah surat izin/sakit beserta foto fisik, melihat tugas PR & mengunggah foto pengerjaan PR anak, memantau rincian tagihan SPP & mengunggah bukti transfer pembayaran (*read-only* terhadap data pribadi anak untuk keamanan).

---

## 🚀 Modul & Fitur Utama

### 1. Modul Biodata Lengkap Siswa & Profil Dapodik (NISN)
- **Standarisasi NISN & Kompatibilitas:** Migrasi field identitas siswa dari NIS menjadi **NISN (Nomor Induk Siswa Nasional)** dengan validasi keunikan serta *backward-compatibility accessor* untuk integrasi sistem yang sudah berjalan.
- **Form Pendaftaran & Pembaruan Terstruktur (5 Kategori):**
  1. **Identitas Siswa:** Nama Lengkap, NISN, NIK (16 digit), Jenis Kelamin (Laki-laki / Perempuan), No. KK, Foto Profil Siswa, dan Status Kebutuhan Khusus (ABK).
  2. **Prestasi Siswa:** Pilihan kategori prestasi interaktif berbasis **Radio Button** (*Tidak Ada, Akademik, Seni, Olahraga, Lain-lain*) dilengkapi kolom rincian keterangan capaian prestasi.
  3. **Kelahiran & Domisili:** Tempat Lahir, Tanggal Lahir, No. Registrasi Akta Kelahiran, Agama (Kristen, Katolik, Islam, Hindu, Buddha, Konghucu), Kewarganegaraan (WNI / WNA), dan Alamat Lengkap Domisili.
  4. **Data Fisik Siswa:** Tinggi Badan (cm), Berat Badan (kg), Lingkar Kepala (cm), dan Jumlah Saudara Kandung.
  5. **Data Orang Tua / Wali:** Profil lengkap Ayah dan Ibu mencakup Nama Lengkap, NIK (16 digit), Tahun Lahir, Riwayat Pendidikan Terakhir, serta Estimasi Rentang Penghasilan Bulanan.
- **Proteksi Privasi & Otorisasi Ketat:**
  - Hanya **Administrator** yang berwenang menambah, menyunting, menghapus, atau melihat rincian kependudukan & finansial keluarga siswa.
  - Guru dan Orang Tua hanya memiliki akses *read-only* untuk foto dan informasi akademik demi menjamin perlindungan privasi data pribadi anak.
- **Tampilan Foto Profil Siswa Terpadu:**
  - Foto resmi siswa yang diinput oleh Administrator otomatis disinkronisasi ke Dashboard Orang Tua, Portal Guru, Kartu Identitas QR Code, dan Scanner Presensi tanpa risiko *broken image*.

### 2. Modul Alumni & Rekapitulasi Kelulusan Siswa (Khusus Administrator)
- **Akses Eksklusif Administrator:** Sesuai kebijakan sekolah, seluruh tombol, navigasi, dan pengelolaan Data Alumni hanya dapat diakses oleh Administrator (tersembunyi secara total dari Guru/Wali Kelas dan Orang Tua).
- **Dua Mekanisme Pemindahan Kelulusan:**
  - **Kelulusan Satuan:** Tombol `🎓 Luluskan` di setiap baris siswa dengan modal interaktif untuk menginput tahun kelulusan, nomor seri ijazah, sekolah lanjutan, dan catatan prestasi.
  - **Kelulusan Massal (Bulk Graduation):** Fitur seleksi checklist siswa per kelas untuk meluluskan puluhan siswa sekaligus secara serentak ke direktori alumni hanya dalam 1-klik.
- **Fitur Pembatalan Kelulusan (Revert to Active):** Opsi instan bagi Administrator untuk mengembalikan status alumni menjadi siswa aktif jika terjadi kekeliruan tanpa menghilangkan integritas data riwayat sebelumnya.
- **Isolasi Data Otomatis:** Siswa yang berstatus Alumni otomatis disembunyikan dari presensi harian kelas berjalan oleh guru dan terisolasi dari penagihan SPP baru, sementara rekap riwayat masa lalunya tetap tersimpan utuh di sistem.
- **2 Pilihan Cetak Dokumen Resmi (Print to PDF):**
  1. **Buku Rekapitulasi Data Alumni (A4 Lanskap):** Format cetak resmi ber-Kop Sekolah NTO Kupang dengan filter per angkatan kelulusan atau keseluruhan, dilengkapi tanda tangan Kepala Sekolah.
  2. **Surat Keterangan Lulus / SKL Satuan (A4 Potret):** Format surat resmi satuan untuk legalisir kelulusan siswa, lengkap dengan pasfoto, nomor surat, identitas kependudukan, dan pengesahan sekolah.

### 3. Modul Pekerjaan Rumah (PR) & Penilaian Siswa
- **Pembuatan Tugas Fleksibel:** Guru dapat menugaskan PR untuk seluruh siswa di kelasnya atau khusus siswa tertentu (*remedial/tugas khusus*) dengan batas waktu (*deadline*) dan lampiran berkas materi/soal.
- **Pengumpulan Digital oleh Siswa:** Orang tua dapat melihat instruksi dan mengunggah foto lembar jawaban PR langsung dari portal.
- **Penilaian & Evaluasi Guru:** Guru dapat melihat pratinjau foto jawaban, menginput nilai skala 0–100, memberikan catatan *feedback*, serta melihat statistik nilai tertinggi, terendah, dan rata-rata kelas.
- **Fitur Hapus PR Manual Siswa:** Guru/Wali Kelas memiliki opsi untuk menghapus data pengumpulan PR siswa secara manual (termasuk yang sudah dinilai) lengkap dengan pembersihan file foto dari storage jika diperlukan pengumpulan ulang.
- **Cetak Rekap Nilai A4:** Cetak rekapitulasi penilaian dan pengumpulan tugas dalam format resmi A4.

### 4. Modul Tagihan & Pembayaran Keuangan (SPP)
- **Penerbitan Tagihan:** Admin dapat membuat tagihan per siswa spesifik atau serentak ke seluruh siswa dalam satu kelas.
- **Pengunggahan Bukti Transfer:** Orang tua dapat melihat status tagihan (*Belum Lunas, Menunggu Verifikasi, Lunas, Ditolak*) dan mengunggah bukti transfer/struk pembayaran.
- **Verifikasi Pembayaran oleh Admin:** Admin meninjau bukti pembayaran lalu menyetujui (status menjadi *LUNAS*) atau menolak bukti dengan catatan revisi.

### 5. Modul Surat Izin Orang Tua & Verifikasi Wali Kelas
- **Pengajuan Izin Mandiri:** Orang tua dapat mengajukan izin/sakit dengan melampirkan foto surat dokter atau surat izin bertandatangan.
- **Otoritas Khusus Wali Kelas:** Hak konfirmasi Terima (*Setujui*) atau Tolak surat izin hanya dimiliki oleh Guru/Wali Kelas dari kelas siswa terkait. Jika ditolak, status kehadiran siswa otomatis berubah menjadi **Alpa**.
- **Mode Monitor Admin:** Admin bertindak sebagai pemantau (*read-only*) untuk menjaga integritas data tanpa mengambil alih wewenang wali kelas.
- **Hapus Berkas Surat:** Guru, Admin, atau Orang Tua pemilik dapat menghapus berkas lampiran jika terjadi kesalahan unggah.

### 6. Modul Presensi Harian & QR Code Scanner (Siswa & Guru)
- **Mekanisme Presensi 2-Tahap (Masuk & Pulang):**
  - **Scan Pertama (Absen Masuk):** Sistem mencatat jam kedatangan (`jam_masuk`) secara akurat dan otomatis mendeteksi keterlambatan dengan notifikasi: *Hadir Tepat Waktu* atau *Terlambat X Menit* (berdasarkan batas jam masuk reguler vs ABK).
  - **Scan Kedua (Absen Pulang):** Sistem otomatis mendeteksi bahwa yang bersangkutan sudah check-in dan mencatat kepulangan (`jam_pulang`) beserta status: *Pulang Tepat Waktu* atau *Pulang Awal* dengan kejelasan jam masuk dan jam pulang yang telah diselesaikan.
  - **Scan Lanjutan (Scan 3+):** Sistem memberikan peringatan informatif bahwa presensi hari ini telah lengkap (menampilkan rincian waktu masuk & pulang).
- **Presensi Mandiri Guru Berbasis QR Code:**
  - Akun Guru kini memiliki **Kartu Presensi Guru QR Digital** siap cetak (berbasis kode `GURU-{id}` atau email).
  - Guru dapat melakukan scan mandiri di pos scanner sekolah dengan alur yang sama persis seperti siswa (Scan 1: Masuk, Scan 2: Pulang) dan pencatatan riwayat terpisah di tabel `teacher_attendances`.
- **Cetak Kartu QR Siswa & Guru:** Kartu ID berpenampilan resmi lengkap dengan logo NTO National Plus, identitas kelas/peran, dan QR Code siap scan.
- **Deteksi Keterlambatan Otomatis:** Perhitungan durasi keterlambatan presisi (dalam menit/jam) berdasarkan jam operasional sekolah untuk siswa reguler, siswa ABK, dan guru.
- **Rekap Bulanan & Cetak A4 Lanskap:** Visualisasi matriks kehadiran bulanan dan cetak rekap absensi resmi A4.

### 7. Panel Manajemen Guru & Switch Account (Impersonate)
- **Kelola Akun Guru:** Admin dapat menambah, mengedit, atau menghapus akun guru serta menetapkan kelas yang diampu.
- **Fitur Switch Account:** Fasilitas 1-klik bagi Admin untuk beralih sesi (*login sebagai guru*) guna memeriksa tampilan portal guru tanpa perlu logout, serta tombol kembali ke akun Admin kapan saja.

### 8. Manajemen Password & Reset Mandiri
- **Permintaan Reset Sandi:** Pengguna yang lupa kata sandi dapat mengirimkan permohonan melalui halaman Login tanpa konfigurasi SMTP email rumit.
- **Panel Persetujuan Admin:** Admin menerima daftar permintaan reset dan dapat menetapkan password baru secara instan.
- **Show / Hide Password:** Ikon toggle mata pada seluruh form input sandi untuk kenyamanan pengguna.

### 9. Keamanan & Proteksi Data (Security Hardening)
- **Input Sanitization & CSRF Protection:** Validasi ketat pada seluruh form input data siswa, sanitasi tipe data numerik (NIK 16 digit, NISN, No KK), serta perlindungan CSRF token bawaan Laravel.
- **Path Traversal Protection:** Penanganan route storage fallback diamankan secara ketat terhadap injeksi `..`, null-byte, dan penolakan otomatis untuk file konfigurasi sensitif (*dotfiles* seperti `.env`).
- **Orphan File Cleanup:** Pembersihan file fisik otomatis di disk storage saat data siswa, foto profil, surat izin, bukti pembayaran, atau PR dihapus/diperbarui.
- **Strict Role Authorization:** Seluruh aksi manipulasi data divalidasi pada level controller dengan guard `isAdmin()` untuk mencegah eskalasi hak akses (*privilege escalation*).

### 10. Sistem Notifikasi Toast Interaktif (Real-Time Feedback)
- **Feedback Otomatis Setiap Aksi Simpan:** Notifikasi pop-up melayang (*floating toast*) otomatis muncul di pojok kanan atas layar setiap kali pengguna berhasil menyimpan perubahan data (edit siswa, simpan nilai PR, update jam operasional, mutasi kelulusan alumni, dsb.).
- **Indikator Multi-Tipe & Visual Interaktif:** Dilengkapi ikon animasi, indikator warna tematik (*Success, Error, Warning, Info*), badge status, dan tombol dismiss silang (`×`).
- **Progress Bar Countdown:** Dilengkapi bar waktu mundur animasi selama 5 detik yang secara otomatis menutup notifikasi tanpa mengganggu alur kerja pengguna.

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
php artisan migrate
```
*(Atau gunakan perintah `php artisan migrate:fresh --seed` jika ingin menginisialisasi ulang dari nol).*

> [!TIP]
> **Skema Basis Data Siap Pakai:** Berkas [`database.sql`](database.sql) di root repositori telah disinkronkan secara lengkap dengan seluruh migrasi terbaru (termasuk migrasi `NIS` ke `NISN`, data kependudukan NIK/KK, fisik, orang tua, dan prestasi). Anda dapat mengimpor langsung berkas ini ke MySQL/MariaDB.

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
- **Email:** `admin@ntokupang.sch.id`
- **Password:** `ntokupang123`

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
