-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: absensi_sekolah
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */
;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */
;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */
;
/*!50503 SET NAMES utf8mb4 */
;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */
;
/*!40103 SET TIME_ZONE='+00:00' */
;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */
;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */
;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */
;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */
;

--
-- Table structure for table `attendances`
--

DROP TABLE IF EXISTS `attendances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */
;
/*!50503 SET character_set_client = utf8mb4 */
;
CREATE TABLE `attendances` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `student_id` bigint unsigned NOT NULL,
    `tanggal` date NOT NULL,
    `jam_masuk` time DEFAULT NULL,
    `jam_pulang` time DEFAULT NULL,
    `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hadir',
    `keterangan` text COLLATE utf8mb4_unicode_ci,
    `surat_izin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `surat_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `catatan_guru` text COLLATE utf8mb4_unicode_ci,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `attendances_student_id_tanggal_unique` (`student_id`, `tanggal`),
    CONSTRAINT `attendances_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 2 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */
;

--
-- Dumping data for table `attendances`
--

LOCK TABLES `attendances` WRITE;
/*!40000 ALTER TABLE `attendances` DISABLE KEYS */
;
/*!40000 ALTER TABLE `attendances` ENABLE KEYS */
;
UNLOCK TABLES;

--
-- Table structure for table `homework_submissions`
--

DROP TABLE IF EXISTS `homework_submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */
;
/*!50503 SET character_set_client = utf8mb4 */
;
CREATE TABLE `homework_submissions` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `homework_id` bigint unsigned NOT NULL,
    `student_id` bigint unsigned NOT NULL,
    `foto_pr` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `catatan_siswa` text COLLATE utf8mb4_unicode_ci,
    `nilai` int DEFAULT NULL,
    `catatan_guru` text COLLATE utf8mb4_unicode_ci,
    `submitted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `graded_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `homework_submissions_homework_id_foreign` (`homework_id`),
    KEY `homework_submissions_student_id_foreign` (`student_id`),
    CONSTRAINT `homework_submissions_homework_id_foreign` FOREIGN KEY (`homework_id`) REFERENCES `homeworks` (`id`) ON DELETE CASCADE,
    CONSTRAINT `homework_submissions_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */
;

--
-- Dumping data for table `homework_submissions`
--

LOCK TABLES `homework_submissions` WRITE;
/*!40000 ALTER TABLE `homework_submissions` DISABLE KEYS */
;
/*!40000 ALTER TABLE `homework_submissions` ENABLE KEYS */
;
UNLOCK TABLES;

--
-- Table structure for table `homeworks`
--

DROP TABLE IF EXISTS `homeworks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */
;
/*!50503 SET character_set_client = utf8mb4 */
;
CREATE TABLE `homeworks` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `teacher_id` bigint unsigned NOT NULL,
    `student_id` bigint unsigned DEFAULT NULL,
    `kelas` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `mata_pelajaran` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `judul` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `deskripsi` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `deadline` datetime NOT NULL,
    `lampiran_guru` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `homeworks_teacher_id_foreign` (`teacher_id`),
    KEY `homeworks_student_id_foreign` (`student_id`),
    CONSTRAINT `homeworks_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
    CONSTRAINT `homeworks_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */
;

--
-- Dumping data for table `homeworks`
--

LOCK TABLES `homeworks` WRITE;
/*!40000 ALTER TABLE `homeworks` DISABLE KEYS */
;
/*!40000 ALTER TABLE `homeworks` ENABLE KEYS */
;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */
;
/*!50503 SET character_set_client = utf8mb4 */
;
CREATE TABLE `migrations` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `batch` int NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 26 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */
;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */
;
INSERT INTO
    `migrations`
VALUES (
        1,
        '0001_01_01_000000_create_users_table',
        1
    ),
    (
        2,
        '0001_01_01_000001_create_cache_table',
        1
    ),
    (
        3,
        '0001_01_01_000002_create_jobs_table',
        1
    ),
    (
        4,
        '2026_04_18_130329_add_role_to_users_table',
        1
    ),
    (
        5,
        '2026_04_20_181302_create_students_table',
        1
    ),
    (
        6,
        '2026_04_20_181303_create_attendances_table',
        1
    ),
    (
        7,
        '2026_04_24_000001_add_student_id_to_users_table',
        1
    ),
    (
        8,
        '2026_04_25_000000_create_settings_table',
        1
    ),
    (
        9,
        '2026_04_26_000000_add_foto_to_students_table',
        1
    ),
    (
        10,
        '2026_04_27_000000_add_surat_izin_to_attendances_table',
        1
    ),
    (
        11,
        '2026_04_28_000000_add_is_abk_to_students_table',
        1
    ),
    (
        12,
        '2026_08_25_200000_alter_attendances_status_to_varchar',
        1
    ),
    (
        13,
        '2026_08_25_210000_add_assigned_class_to_users_table',
        1
    ),
    (
        14,
        '2026_08_25_220000_create_password_reset_requests_table',
        1
    ),
    (
        15,
        '2026_08_25_221000_add_plain_password_to_users_table',
        1
    ),
    (
        16,
        '2026_09_05_000001_create_payments_table',
        1
    ),
    (
        17,
        '2026_09_05_000002_create_homeworks_table',
        1
    ),
    (
        18,
        '2026_09_05_000003_create_homework_submissions_table',
        1
    ),
    (
        19,
        '2026_09_05_000004_add_student_id_to_homeworks_table',
        1
    ),
    (
        20,
        '2026_09_10_000001_add_surat_status_and_catatan_guru_to_attendances_table',
        1
    ),
    (
        21,
        '2026_09_28_231618_normalize_surat_status_null_to_menunggu_in_attendances',
        2
    ),
    (
        22,
        '2026_09_30_000001_add_detailed_fields_and_rename_nis_to_nisn_in_students_table',
        3
    ),
    (
        23,
        '2026_10_02_000001_add_alumni_fields_to_students_table',
        4
    ),
    (
        24,
        '2026_10_05_195912_add_jam_masuk_and_jam_pulang_to_attendances_table',
        5
    ),
    (
        25,
        '2026_10_05_195929_create_teacher_attendances_table',
        5
    );
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */
;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_requests`
--

DROP TABLE IF EXISTS `password_reset_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */
;
/*!50503 SET character_set_client = utf8mb4 */
;
CREATE TABLE `password_reset_requests` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `user_id` bigint unsigned DEFAULT NULL,
    `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'orang_tua',
    `status` enum('pending', 'resolved') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
    `notes` text COLLATE utf8mb4_unicode_ci,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `password_reset_requests_user_id_foreign` (`user_id`),
    CONSTRAINT `password_reset_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */
;

--
-- Dumping data for table `password_reset_requests`
--

LOCK TABLES `password_reset_requests` WRITE;
/*!40000 ALTER TABLE `password_reset_requests` DISABLE KEYS */
;
/*!40000 ALTER TABLE `password_reset_requests` ENABLE KEYS */
;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */
;
/*!50503 SET character_set_client = utf8mb4 */
;
CREATE TABLE `payments` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `student_id` bigint unsigned NOT NULL,
    `judul` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `jumlah` decimal(12, 2) NOT NULL,
    `jatuh_tempo` date NOT NULL,
    `keterangan` text COLLATE utf8mb4_unicode_ci,
    `status` enum(
        'belum_lunas',
        'menunggu_konfirmasi',
        'lunas',
        'ditolak'
    ) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'belum_lunas',
    `bukti_pembayaran` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `catatan_admin` text COLLATE utf8mb4_unicode_ci,
    `paid_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `payments_student_id_foreign` (`student_id`),
    CONSTRAINT `payments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */
;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */
;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */
;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */
;
/*!50503 SET character_set_client = utf8mb4 */
;
CREATE TABLE `settings` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `value` text COLLATE utf8mb4_unicode_ci,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE = InnoDB AUTO_INCREMENT = 7 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */
;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */
;
INSERT INTO
    `settings`
VALUES (
        1,
        'jam_masuk',
        '07:00',
        '2026-09-30 03:14:35',
        '2026-09-30 03:14:35'
    ),
    (
        2,
        'jam_terlambat',
        '07:30',
        '2026-09-30 03:14:35',
        '2026-09-30 03:14:35'
    ),
    (
        3,
        'jam_pulang',
        '14:00',
        '2026-09-30 03:14:35',
        '2026-09-30 03:14:35'
    ),
    (
        4,
        'jam_masuk_abk',
        '08:00',
        '2026-09-30 03:14:35',
        '2026-09-30 03:14:35'
    ),
    (
        5,
        'jam_terlambat_abk',
        '08:30',
        '2026-09-30 03:14:35',
        '2026-09-30 03:14:35'
    ),
    (
        6,
        'jam_pulang_abk',
        '13:00',
        '2026-09-30 03:14:35',
        '2026-09-30 03:14:35'
    );
/*!40000 ALTER TABLE `settings` ENABLE KEYS */
;
UNLOCK TABLES;

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */
;
/*!50503 SET character_set_client = utf8mb4 */
;
CREATE TABLE `students` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `nisn` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `nik` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `no_kk` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `kelas` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
    `tahun_lulus` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `tanggal_lulus` date DEFAULT NULL,
    `no_ijazah` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `sekolah_lanjutan` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `catatan_kelulusan` text COLLATE utf8mb4_unicode_ci,
    `jenis_kelamin` enum('L', 'P') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'L',
    `tempat_lahir` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `tanggal_lahir` date DEFAULT NULL,
    `no_akta_kelahiran` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `agama` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `kewarganegaraan` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'WNI',
    `kategori_prestasi` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `keterangan_prestasi` text COLLATE utf8mb4_unicode_ci,
    `tinggi_badan` smallint unsigned DEFAULT NULL,
    `berat_badan` smallint unsigned DEFAULT NULL,
    `lingkar_kepala` smallint unsigned DEFAULT NULL,
    `jumlah_saudara_kandung` tinyint unsigned DEFAULT '0',
    `nama_ayah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `nik_ayah` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `tahun_lahir_ayah` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `pendidikan_ayah` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `penghasilan_ayah` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `nama_ibu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `nik_ibu` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `tahun_lahir_ibu` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `pendidikan_ibu` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `penghasilan_ibu` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `alamat` text COLLATE utf8mb4_unicode_ci,
    `telepon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `is_abk` tinyint(1) NOT NULL DEFAULT '0',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `students_nisn_unique` (`nisn`)
) ENGINE = InnoDB AUTO_INCREMENT = 3 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */
;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */
;
INSERT INTO
    `students`
VALUES (
        2,
        '0007.18.0036',
        '937937439',
        '38493737434738',
        'DAVID PRATAMA',
        'Kelas 2',
        'aktif',
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        'L',
        'Kupang',
        '2005-01-20',
        'e587393u39',
        'Katolik',
        'WNI',
        'Olahraga',
        'Juara Futsal NTT KABOAX CUP',
        178,
        67,
        50,
        0,
        'John',
        '39938938933893893',
        '1998',
        'S1 / D4',
        'Lebih dari Rp 5.000.000',
        'Freya',
        '9839793793793',
        '1999',
        'D1 / D2 / D3',
        'Rp 1.000.000 - Rp 2.000.000',
        'Gading Serpong',
        '089669139907',
        'students/GNacUSS3bdqm0jywEi7siMUu7g0CCpbbTww3fauH.png',
        0,
        '2026-10-02 03:30:50',
        '2026-10-02 03:30:50'
    );
/*!40000 ALTER TABLE `students` ENABLE KEYS */
;
UNLOCK TABLES;

--
-- Table structure for table `teacher_attendances`
--

DROP TABLE IF EXISTS `teacher_attendances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */
;
/*!50503 SET character_set_client = utf8mb4 */
;
CREATE TABLE `teacher_attendances` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `teacher_id` bigint unsigned NOT NULL,
    `tanggal` date NOT NULL,
    `jam_masuk` time DEFAULT NULL,
    `jam_pulang` time DEFAULT NULL,
    `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hadir',
    `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `teacher_attendances_teacher_id_tanggal_unique` (`teacher_id`, `tanggal`),
    CONSTRAINT `teacher_attendances_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 2 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */
;

--
-- Dumping data for table `teacher_attendances`
--

LOCK TABLES `teacher_attendances` WRITE;
/*!40000 ALTER TABLE `teacher_attendances` DISABLE KEYS */
;
/*!40000 ALTER TABLE `teacher_attendances` ENABLE KEYS */
;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */
;
/*!50503 SET character_set_client = utf8mb4 */
;
CREATE TABLE `users` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'guru',
    `assigned_class` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `email_verified_at` timestamp NULL DEFAULT NULL,
    `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `plain_password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    `student_id` bigint unsigned DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`),
    KEY `users_student_id_foreign` (`student_id`),
    CONSTRAINT `users_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL
) ENGINE = InnoDB AUTO_INCREMENT = 4 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */
;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */
;
INSERT INTO
    `users`
VALUES (
        1,
        'Administrator NTO',
        'admin@ntokupang.sch.id',
        'admin',
        NULL,
        '2026-09-30 03:14:35',
        '$2y$12$YNmYmUL4lRfmDB/.AFl5dOxrPM7u2SPcjix1idH2PzyJ3vg8v66Gi',
        NULL,
        NULL,
        '2026-09-30 03:14:35',
        '2026-09-30 03:14:35',
        NULL
    ),
    (
        2,
        'Orang Tua (DAVID PRATAMA)',
        'david.pratama@student.sch.id',
        'orang_tua',
        NULL,
        NULL,
        '$2y$12$t6EsoSDbfQAmI.TFlx9Uo.sAf7qlKlsTvs8Qa3wMaBsh8gB3EpMj6',
        NULL,
        NULL,
        '2026-10-02 03:30:50',
        '2026-10-02 03:30:50',
        2
    ),
    (
        3,
        'Riki Martin',
        'riki@teacher.sch.id',
        'guru',
        'Kelas 2',
        NULL,
        '$2y$12$jSASzpii6Q8CpNQRNiBBV.47jqWFwhb4oOnEfdzPd2TbeNYpEssXC',
        NULL,
        NULL,
        '2026-10-02 03:34:15',
        '2026-10-02 03:34:15',
        NULL
    );
/*!40000 ALTER TABLE `users` ENABLE KEYS */
;
UNLOCK TABLES;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */
;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */
;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */
;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */
;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */
;

-- Dump completed on 2026-10-05 19:09:00