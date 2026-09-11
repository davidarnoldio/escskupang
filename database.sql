-- Absensi Sekolah MySQL Database Schema (Clean State)
-- NTO National Plus Primary School
-- Only Administrator Account Included

CREATE DATABASE IF NOT EXISTS `absensi_sekolah`;
USE `absensi_sekolah`;

SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- Table structure for table `migrations`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `migrations`
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
('1', '0001_01_01_000000_create_users_table', '1'),
('2', '0001_01_01_000001_create_cache_table', '1'),
('3', '0001_01_01_000002_create_jobs_table', '1'),
('4', '2026_04_18_130329_add_role_to_users_table', '1'),
('5', '2026_04_20_181302_create_students_table', '1'),
('6', '2026_04_20_181303_create_attendances_table', '1'),
('7', '2026_04_24_000001_add_student_id_to_users_table', '1'),
('8', '2026_04_25_000000_create_settings_table', '1'),
('9', '2026_04_26_000000_add_foto_to_students_table', '1'),
('10', '2026_04_27_000000_add_surat_izin_to_attendances_table', '1'),
('11', '2026_04_28_000000_add_is_abk_to_students_table', '1'),
('12', '2026_08_25_200000_alter_attendances_status_to_varchar', '1'),
('13', '2026_08_25_210000_add_assigned_class_to_users_table', '1'),
('14', '2026_08_25_220000_create_password_reset_requests_table', '1'),
('15', '2026_08_25_221000_add_plain_password_to_users_table', '1'),
('16', '2026_09_05_000001_create_payments_table', '1'),
('17', '2026_09_05_000002_create_homeworks_table', '1'),
('18', '2026_09_05_000003_create_homework_submissions_table', '1'),
('19', '2026_09_05_000004_add_student_id_to_homeworks_table', '1'),
('20', '2026_09_10_000001_add_surat_status_and_catatan_guru_to_attendances_table', '1');

-- --------------------------------------------------------
-- Table structure for table `students`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `students`;
CREATE TABLE `students` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nis` varchar(255) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `kelas` varchar(255) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL DEFAULT 'L',
  `alamat` text DEFAULT NULL,
  `telepon` varchar(255) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `is_abk` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `students_nis_unique` (`nis`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'guru',
  `assigned_class` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `plain_password` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `student_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_student_id_foreign` (`student_id`),
  CONSTRAINT `users_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `users` (Only Administrator)
INSERT INTO `users` (`id`, `name`, `email`, `role`, `assigned_class`, `email_verified_at`, `password`, `plain_password`, `remember_token`, `created_at`, `updated_at`, `student_id`) VALUES
('1', 'Administrator NTO', 'admin@nto-kupang.sch.id', 'admin', NULL, NOW(), '$2y$12$YNmYmUL4lRfmDB/.AFl5dOxrPM7u2SPcjix1idH2PzyJ3vg8v66Gi', NULL, NULL, NOW(), NOW(), NULL);

-- --------------------------------------------------------
-- Table structure for table `attendances`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `attendances`;
CREATE TABLE `attendances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `tanggal` date NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'hadir',
  `keterangan` text DEFAULT NULL,
  `surat_izin` varchar(255) DEFAULT NULL,
  `surat_status` varchar(50) DEFAULT 'menunggu',
  `catatan_guru` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attendances_student_id_tanggal_unique` (`student_id`,`tanggal`),
  CONSTRAINT `attendances_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `settings`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `settings`
INSERT INTO `settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
('1', 'jam_masuk', '07:00', NOW(), NOW()),
('2', 'jam_terlambat', '07:30', NOW(), NOW()),
('3', 'jam_pulang', '14:00', NOW(), NOW()),
('4', 'jam_masuk_abk', '08:00', NOW(), NOW()),
('5', 'jam_terlambat_abk', '08:30', NOW(), NOW()),
('6', 'jam_pulang_abk', '13:00', NOW(), NOW());

-- --------------------------------------------------------
-- Table structure for table `password_reset_requests`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `password_reset_requests`;
CREATE TABLE `password_reset_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'orang_tua',
  `status` enum('pending','resolved') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `password_reset_requests_user_id_foreign` (`user_id`),
  CONSTRAINT `password_reset_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `payments`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `judul` varchar(255) NOT NULL,
  `jumlah` decimal(12,2) NOT NULL,
  `jatuh_tempo` date NOT NULL,
  `keterangan` text DEFAULT NULL,
  `status` enum('belum_lunas','menunggu_konfirmasi','lunas','ditolak') NOT NULL DEFAULT 'belum_lunas',
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `catatan_admin` text DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_student_id_foreign` (`student_id`),
  CONSTRAINT `payments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `homeworks`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `homeworks`;
CREATE TABLE `homeworks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `teacher_id` bigint(20) unsigned NOT NULL,
  `student_id` bigint(20) unsigned DEFAULT NULL,
  `kelas` varchar(255) NOT NULL,
  `mata_pelajaran` varchar(255) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `deadline` datetime NOT NULL,
  `lampiran_guru` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `homeworks_teacher_id_foreign` (`teacher_id`),
  KEY `homeworks_student_id_foreign` (`student_id`),
  CONSTRAINT `homeworks_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `homeworks_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `homework_submissions`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `homework_submissions`;
CREATE TABLE `homework_submissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `homework_id` bigint(20) unsigned NOT NULL,
  `student_id` bigint(20) unsigned NOT NULL,
  `foto_pr` varchar(255) NOT NULL,
  `catatan_siswa` text DEFAULT NULL,
  `nilai` int(11) DEFAULT NULL,
  `catatan_guru` text DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `graded_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `homework_submissions_homework_id_foreign` (`homework_id`),
  KEY `homework_submissions_student_id_foreign` (`student_id`),
  CONSTRAINT `homework_submissions_homework_id_foreign` FOREIGN KEY (`homework_id`) REFERENCES `homeworks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `homework_submissions_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
