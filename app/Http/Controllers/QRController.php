<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\TeacherAttendance;
use App\Models\User;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class QRController extends Controller
{
    /**
     * Display the QR Code Attendance Scanner page.
     */
    public function index()
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Akses scanner presensi QR khusus Administrator.');
        }

        $today = now()->format('Y-m-d');
        $jamTerlambatReguler = Setting::get('jam_terlambat', '07:30');
        $jamTerlambatGuru = Setting::get('jam_terlambat_guru', $jamTerlambatReguler);
        
        // All active student attendance today (synced with Dashboard/Attendance)
        $todayAttendances = Attendance::with('student')
            ->where('tanggal', $today)
            ->whereHas('student', function ($q) {
                $q->aktif();
            })
            ->latest('updated_at')
            ->get();

        // All teacher attendance today
        $todayTeacherAttendances = TeacherAttendance::with('teacher')
            ->where('tanggal', $today)
            ->whereHas('teacher')
            ->latest('updated_at')
            ->get();

        return view('qr.scan', compact(
            'todayAttendances',
            'todayTeacherAttendances',
            'today',
            'jamTerlambatReguler',
            'jamTerlambatGuru'
        ));
    }

    /**
     * Process scanned QR Code (Student NISN/NIS or Teacher QR).
     */
    public function process(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        if (!$user || !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses scanner presensi QR khusus Administrator.',
            ], 403);
        }

        if ($request->isMethod('get') && !$request->expectsJson() && !$request->ajax()) {
            return redirect()->route('qr.scan');
        }

        $request->validate([
            'nis'  => ['nullable', 'string', 'max:100'],
            'nisn' => ['nullable', 'string', 'max:100'],
            'code' => ['nullable', 'string', 'max:100'],
            'mode' => ['nullable', 'string', 'in:masuk,pulang'],
        ]);

        $scanMode = $request->input('mode', 'masuk');
        $rawInput = trim($request->input('code') ?? $request->input('nisn') ?? $request->input('nis') ?? '');

        if (empty($rawInput)) {
            return response()->json([
                'success' => false,
                'message' => 'Kode QR kosong atau tidak terbaca.',
            ], 200);
        }

        $isTeacherScan = false;
        $teacherId = null;
        $teacherEmail = null;

        // Check if payload is JSON
        if (str_starts_with($rawInput, '{') && str_ends_with($rawInput, '}')) {
            $json = json_decode($rawInput, true);
            if (is_array($json)) {
                if (isset($json['type']) && in_array(strtolower($json['type']), ['teacher', 'guru'])) {
                    $isTeacherScan = true;
                    $teacherId = $json['id'] ?? null;
                    $teacherEmail = $json['email'] ?? null;
                } elseif (isset($json['teacher_id'])) {
                    $isTeacherScan = true;
                    $teacherId = $json['teacher_id'];
                } elseif (isset($json['nisn'])) {
                    $rawInput = trim($json['nisn']);
                } elseif (isset($json['nis'])) {
                    $rawInput = trim($json['nis']);
                }
            }
        }

        // Check prefix patterns for teacher
        if (!$isTeacherScan) {
            if (preg_match('/^(guru|teacher)[-:\s]+(\d+)$/i', $rawInput, $matches)) {
                $isTeacherScan = true;
                $teacherId = (int)$matches[2];
            } elseif (preg_match('/^(guru|teacher)[-:\s]+(.+)$/i', $rawInput, $matches)) {
                $isTeacherScan = true;
                $teacherEmail = trim($matches[2]);
            }
        }

        // If it's a teacher scan, process teacher attendance
        if ($isTeacherScan) {
            return $this->processTeacherAttendance($teacherId, $teacherEmail, $rawInput, $scanMode);
        }

        // Normalize student input prefix e.g. "NISN: 0003.26.0236" or "NIS: 0003.26.0236"
        if (preg_match('/^(nisn|nis)[:\s]+(.*)$/i', $rawInput, $matches)) {
            $rawInput = trim($matches[2]);
        }

        // Check if raw input matches a teacher's email directly
        $possibleTeacher = User::whereIn('role', ['guru', 'wali_kelas'])
            ->where('email', $rawInput)
            ->first();
        if ($possibleTeacher) {
            return $this->processTeacherAttendance($possibleTeacher->id, null, $rawInput, $scanMode);
        }

        // Look up student by NISN
        $student = Student::where('nisn', $rawInput)->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => "Data siswa atau guru dengan kode/identitas '{$rawInput}' tidak ditemukan!",
            ], 200);
        }

        if ($student->isAlumni()) {
            return response()->json([
                'success' => false,
                'message' => "Siswa {$student->nama} telah berstatus LULUS (Alumni) dan tidak dapat melakukan presensi.",
            ], 200);
        }

        return $this->processStudentAttendance($student, $scanMode);
    }

    /**
     * Process student 2-phase attendance based on explicit scan mode ('masuk' or 'pulang').
     */
    protected function processStudentAttendance(Student $student, string $scanMode = 'masuk')
    {
        $today = now()->format('Y-m-d');
        $time = now()->format('H:i:s');
        $currentTimeHM = now()->format('H:i');

        $jamMasukConfig = $student->is_abk
            ? Setting::get('jam_masuk_abk', '08:00')
            : Setting::get('jam_masuk', '07:00');

        $jamTerlambatConfig = $student->is_abk
            ? Setting::get('jam_terlambat_abk', '08:30')
            : Setting::get('jam_terlambat', '07:30');

        $jamPulangConfig = $student->is_abk
            ? Setting::get('jam_pulang_abk', '13:00')
            : Setting::get('jam_pulang', '14:00');

        $attendance = Attendance::where('student_id', $student->id)
            ->where('tanggal', $today)
            ->first();

        $personData = [
            'id' => $student->id,
            'nama' => $student->nama,
            'nisn' => $student->nisn ?? $student->nis,
            'kelas' => $student->kelas,
            'foto_url' => $student->foto_url,
            'is_abk' => $student->is_abk,
        ];

        // -------------------------------------------------------------
        // SCAN 3+: SUDAH MASUK & SUDAH PULANG -> TIDAK VALID / DITOLAK
        // -------------------------------------------------------------
        if ($attendance && !empty($attendance->jam_masuk) && !empty($attendance->jam_pulang)) {
            $message = "Presensi TIDAK VALID! Siswa {$student->nama} ({$student->kelas}) SUDAH melakukan scan masuk jam [{$attendance->jam_masuk}] dan scan pulang jam [{$attendance->jam_pulang}]. Batas scan presensi hari ini maksimal 2 kali (1x Masuk & 1x Keluar). Silakan hadir kembali besok.";

            return response()->json([
                'success' => false,
                'already_completed' => true,
                'scan_type' => 'invalid',
                'title' => 'Presensi Tidak Valid (Batas Scan Habis)!',
                'role_type' => 'student',
                'message' => $message,
                'person' => $personData,
                'attendance' => [
                    'tanggal' => $today,
                    'jam_masuk' => $attendance->jam_masuk,
                    'jam_pulang' => $attendance->jam_pulang,
                    'status_text' => 'TIDAK VALID (Batas 2x Scan)',
                ],
            ], 200);
        }

        // -------------------------------------------------------------
        // MODE PULANG: HANYA PROSES ABSEN PULANG
        // -------------------------------------------------------------
        if ($scanMode === 'pulang') {
            if (!$attendance || empty($attendance->jam_masuk)) {
                return response()->json([
                    'success' => false,
                    'not_checked_in' => true,
                    'scan_type' => 'not_checked_in',
                    'title' => 'Belum Absen Masuk!',
                    'role_type' => 'student',
                    'message' => "Siswa {$student->nama} ({$student->kelas}) belum tercatat ABSEN MASUK hari ini. Silakan pilih mode 'Scan Absen Masuk' terlebih dahulu.",
                    'person' => $personData,
                    'attendance' => [
                        'tanggal' => $today,
                        'jam_masuk' => null,
                        'jam_pulang' => null,
                        'status_text' => 'BELUM ABSEN MASUK',
                    ],
                ], 200);
            }

            $isEarlyDeparture = $currentTimeHM < $jamPulangConfig;

            if ($isEarlyDeparture) {
                $pulangScanCarbon = Carbon::parse($today . ' ' . $time, 'Asia/Makassar');
                $pulangTargetCarbon = Carbon::parse($today . ' ' . $jamPulangConfig . ':00', 'Asia/Makassar');
                $earlyMinutes = max(1, abs((int) $pulangScanCarbon->diffInMinutes($pulangTargetCarbon)));

                $statusText = "PULANG AWAL ({$earlyMinutes}m Lebih Awal)";
                $message = "Presensi PULANG Siswa Dicatat! {$student->nama} ({$student->kelas}) pulang DULUAN pada jam [{$time}] ({$earlyMinutes} menit sebelum jam pulang resmi {$jamPulangConfig} WITA). Jam Masuk: [{$attendance->jam_masuk}].";
                $keteranganPulang = "Pulang Awal (Duluan {$earlyMinutes} menit): [{$time}]";
            } else {
                $statusText = "PULANG TEPAT WAKTU (Jam {$time})";
                $message = "Presensi PULANG Berhasil! Siswa {$student->nama} ({$student->kelas}) dicatat PULANG TEPAT WAKTU pada jam [{$time}]. Jam Masuk: [{$attendance->jam_masuk}].";
                $keteranganPulang = "Pulang: [{$time}]";
            }

            $newKeterangan = $attendance->keterangan
                ? $attendance->keterangan . " | " . $keteranganPulang
                : $keteranganPulang;

            $attendance->update([
                'jam_pulang' => $time,
                'keterangan' => $newKeterangan,
            ]);

            return response()->json([
                'success' => true,
                'scan_type' => 'pulang',
                'role_type' => 'student',
                'message' => $message,
                'person' => $personData,
                'attendance' => [
                    'tanggal' => $today,
                    'jam_masuk' => $attendance->jam_masuk,
                    'jam_pulang' => $time,
                    'is_late' => false,
                    'is_early' => $isEarlyDeparture,
                    'status_text' => $statusText,
                    'jam_pulang_target' => $jamPulangConfig,
                ],
            ]);
        }

        // -------------------------------------------------------------
        // MODE MASUK: TOLAK JIKA SUDAH ABSEN MASUK HARI INI
        // -------------------------------------------------------------
        if ($attendance && !empty($attendance->jam_masuk)) {
            return response()->json([
                'success' => false,
                'already_checked_in' => true,
                'scan_type' => 'already_checked_in',
                'title' => 'Sudah Absen Masuk!',
                'role_type' => 'student',
                'message' => "Siswa {$student->nama} ({$student->kelas}) sudah tercatat ABSEN MASUK hari ini pada jam [{$attendance->jam_masuk}]. Untuk mencatat kepulangan, silakan ubah mode ke 'Scan Absen Pulang'.",
                'person' => $personData,
                'attendance' => [
                    'tanggal' => $today,
                    'jam_masuk' => $attendance->jam_masuk,
                    'jam_pulang' => null,
                    'status_text' => 'SUDAH ABSEN MASUK',
                ],
            ], 200);
        }

        // -------------------------------------------------------------
        // CEK JAM LUAR SEKOLAH: ISENG ABSEN MASUK SETELAH JAM PULANG
        // -------------------------------------------------------------
        if ($currentTimeHM >= $jamPulangConfig || $currentTimeHM < '05:30') {
            $message = "Presensi MASUK Ditolak! Waktu presensi hari ini sudah berakhir (Batas akhir presensi masuk: {$jamPulangConfig} WITA). Siswa {$student->nama} dipersilakan hadir dan melakukan presensi kembali besok pagi.";

            return response()->json([
                'success' => false,
                'outside_hours' => true,
                'scan_type' => 'outside_hours',
                'title' => 'Presensi Masuk Ditolak!',
                'role_type' => 'student',
                'message' => $message,
                'person' => [
                    'id' => $student->id,
                    'nama' => $student->nama,
                    'nisn' => $student->nisn ?? $student->nis,
                    'kelas' => $student->kelas,
                    'foto_url' => $student->foto_url,
                    'is_abk' => $student->is_abk,
                ],
                'attendance' => [
                    'tanggal' => $today,
                    'waktu' => $time,
                    'status_text' => 'DITOLAK (Di Luar Jam Masuk)',
                    'jam_pulang_target' => $jamPulangConfig,
                ],
            ], 200);
        }

        // -------------------------------------------------------------
        // SCAN 1: CHECK-IN (ABSEN MASUK SISWA) - Kecuali ABK tidak dihitung terlambat
        // -------------------------------------------------------------
        $isLate = !$student->is_abk && ($currentTimeHM > $jamTerlambatConfig);
        $lateMinutes = 0;

        if ($isLate) {
            $scanCarbon = Carbon::parse($today . ' ' . $time, 'Asia/Makassar');
            $thresholdCarbon = Carbon::parse($today . ' ' . $jamTerlambatConfig . ':00', 'Asia/Makassar');
            $lateMinutes = max(1, abs((int) $scanCarbon->diffInMinutes($thresholdCarbon)));
        }

        $abkLabel = $student->is_abk ? ' (ABK)' : '';
        $statusNote = $isLate
            ? "Masuk: [{$time}] - Terlambat {$lateMinutes} menit{$abkLabel}"
            : "Masuk: [{$time}] - " . ($student->is_abk ? 'Hadir (ABK)' : 'Tepat Waktu');

        $statusText = $isLate ? "TERLAMBAT ({$lateMinutes}m)" : ($student->is_abk ? "HADIR (ABK)" : "HADIR (Tepat Waktu)");
        $message = $isLate
            ? "Presensi MASUK Dicatat! Siswa {$student->nama} ({$student->kelas}) dicatat TERLAMBAT {$lateMinutes} menit pada jam [{$time}] (Batas Masuk: {$jamTerlambatConfig})."
            : "Presensi MASUK Berhasil! Siswa {$student->nama} ({$student->kelas}) dicatat HADIR" . ($student->is_abk ? ' (ABK)' : ' TEPAT WAKTU') . " pada jam [{$time}].";

        $attendance = Attendance::updateOrCreate(
            [
                'student_id' => $student->id,
                'tanggal' => $today,
            ],
            [
                'jam_masuk' => $time,
                'status' => 'hadir',
                'keterangan' => $statusNote,
            ]
        );

        return response()->json([
            'success' => true,
            'scan_type' => 'masuk',
            'role_type' => 'student',
            'message' => $message,
            'person' => [
                'id' => $student->id,
                'nama' => $student->nama,
                'nisn' => $student->nisn ?? $student->nis,
                'kelas' => $student->kelas,
                'foto_url' => $student->foto_url,
                'is_abk' => $student->is_abk,
            ],
            'attendance' => [
                'tanggal' => $today,
                'jam_masuk' => $time,
                'jam_pulang' => null,
                'is_late' => $isLate,
                'late_minutes' => $lateMinutes,
                'status_text' => $statusText,
                'jam_terlambat' => $jamTerlambatConfig,
            ],
        ]);
    }

    /**
     * Process teacher 2-phase attendance based on explicit scan mode ('masuk' or 'pulang').
     */
    protected function processTeacherAttendance($teacherId, $teacherEmail, $rawInput, string $scanMode = 'masuk')
    {
        $query = User::whereIn('role', ['guru', 'wali_kelas']);
        if ($teacherId) {
            $query->where('id', $teacherId);
        } elseif ($teacherEmail) {
            $query->where('email', $teacherEmail);
        } else {
            $query->where(function ($q) use ($rawInput) {
                $q->where('email', $rawInput)->orWhere('id', $rawInput);
            });
        }

        $teacher = $query->first();

        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => "Data Guru dengan identitas QR '{$rawInput}' tidak ditemukan!",
            ], 200);
        }

        $today = now()->format('Y-m-d');
        $time = now()->format('H:i:s');
        $currentTimeHM = now()->format('H:i');

        $jamMasukConfig = Setting::get('jam_masuk', '07:00');
        $jamTerlambatConfig = Setting::get('jam_terlambat', '07:30');
        $jamPulangConfig = Setting::get('jam_pulang', '14:00');

        $teacherAttendance = TeacherAttendance::where('teacher_id', $teacher->id)
            ->where('tanggal', $today)
            ->first();

        $personData = [
            'id' => $teacher->id,
            'nama' => $teacher->name,
            'email' => $teacher->email,
            'kelas' => $teacher->getAssignedClass() ?? 'Guru Pengajar',
            'is_guru' => true,
        ];

        // -------------------------------------------------------------
        // SCAN 3+: SUDAH MASUK & SUDAH PULANG -> TIDAK VALID / DITOLAK
        // -------------------------------------------------------------
        if ($teacherAttendance && !empty($teacherAttendance->jam_masuk) && !empty($teacherAttendance->jam_pulang)) {
            $message = "Presensi TIDAK VALID! Guru {$teacher->name} SUDAH melakukan scan masuk jam [{$teacherAttendance->jam_masuk}] dan scan pulang jam [{$teacherAttendance->jam_pulang}]. Batas scan presensi hari ini maksimal 2 kali (1x Masuk & 1x Keluar). Silakan hadir kembali besok.";

            return response()->json([
                'success' => false,
                'already_completed' => true,
                'scan_type' => 'invalid',
                'title' => 'Presensi Tidak Valid (Batas Scan Habis)!',
                'role_type' => 'teacher',
                'message' => $message,
                'person' => $personData,
                'attendance' => [
                    'tanggal' => $today,
                    'jam_masuk' => $teacherAttendance->jam_masuk,
                    'jam_pulang' => $teacherAttendance->jam_pulang,
                    'status_text' => 'TIDAK VALID (Batas 2x Scan)',
                ],
            ], 200);
        }

        // -------------------------------------------------------------
        // MODE PULANG: HANYA PROSES ABSEN PULANG GURU
        // -------------------------------------------------------------
        if ($scanMode === 'pulang') {
            if (!$teacherAttendance || empty($teacherAttendance->jam_masuk)) {
                return response()->json([
                    'success' => false,
                    'not_checked_in' => true,
                    'scan_type' => 'not_checked_in',
                    'title' => 'Belum Absen Masuk!',
                    'role_type' => 'teacher',
                    'message' => "Guru {$teacher->name} belum tercatat ABSEN MASUK hari ini. Silakan pilih mode 'Scan Absen Masuk' terlebih dahulu.",
                    'person' => $personData,
                    'attendance' => [
                        'tanggal' => $today,
                        'jam_masuk' => null,
                        'jam_pulang' => null,
                        'status_text' => 'BELUM ABSEN MASUK',
                    ],
                ], 200);
            }

            $isEarlyDeparture = $currentTimeHM < $jamPulangConfig;

            if ($isEarlyDeparture) {
                $pulangScanCarbon = Carbon::parse($today . ' ' . $time, 'Asia/Makassar');
                $pulangTargetCarbon = Carbon::parse($today . ' ' . $jamPulangConfig . ':00', 'Asia/Makassar');
                $earlyMinutes = max(1, abs((int) $pulangScanCarbon->diffInMinutes($pulangTargetCarbon)));

                $statusText = "PULANG AWAL ({$earlyMinutes}m Lebih Awal)";
                $message = "Presensi PULANG Guru Dicatat! {$teacher->name} pulang DULUAN pada jam [{$time}] ({$earlyMinutes} menit sebelum jam pulang resmi {$jamPulangConfig} WITA). Jam Masuk: [{$teacherAttendance->jam_masuk}].";
                $keteranganPulang = "Pulang Awal (Duluan {$earlyMinutes} menit): [{$time}]";
            } else {
                $statusText = "PULANG (Jam {$time})";
                $message = "Presensi PULANG Guru Berhasil! {$teacher->name} dicatat PULANG pada jam [{$time}]. Jam Masuk: [{$teacherAttendance->jam_masuk}].";
                $keteranganPulang = "Pulang: [{$time}]";
            }

            $newKeterangan = $teacherAttendance->keterangan
                ? $teacherAttendance->keterangan . " | " . $keteranganPulang
                : $keteranganPulang;

            $teacherAttendance->update([
                'jam_pulang' => $time,
                'keterangan' => $newKeterangan,
            ]);

            return response()->json([
                'success' => true,
                'scan_type' => 'pulang',
                'role_type' => 'teacher',
                'message' => $message,
                'person' => $personData,
                'attendance' => [
                    'tanggal' => $today,
                    'jam_masuk' => $teacherAttendance->jam_masuk,
                    'jam_pulang' => $time,
                    'is_late' => false,
                    'is_early' => $isEarlyDeparture,
                    'status_text' => $statusText,
                    'jam_pulang_target' => $jamPulangConfig,
                ],
            ]);
        }

        // -------------------------------------------------------------
        // MODE MASUK: TOLAK JIKA SUDAH ABSEN MASUK HARI INI
        // -------------------------------------------------------------
        if ($teacherAttendance && !empty($teacherAttendance->jam_masuk)) {
            return response()->json([
                'success' => false,
                'already_checked_in' => true,
                'scan_type' => 'already_checked_in',
                'title' => 'Sudah Absen Masuk!',
                'role_type' => 'teacher',
                'message' => "Guru {$teacher->name} sudah tercatat ABSEN MASUK hari ini pada jam [{$teacherAttendance->jam_masuk}]. Untuk mencatat kepulangan, silakan ubah mode ke 'Scan Absen Pulang'.",
                'person' => $personData,
                'attendance' => [
                    'tanggal' => $today,
                    'jam_masuk' => $teacherAttendance->jam_masuk,
                    'jam_pulang' => null,
                    'status_text' => 'SUDAH ABSEN MASUK',
                ],
            ], 200);
        }

        // -------------------------------------------------------------
        // CEK JAM LUAR SEKOLAH: ISENG ABSEN MASUK SETELAH JAM PULANG
        // -------------------------------------------------------------
        if ($currentTimeHM >= $jamPulangConfig || $currentTimeHM < '05:30') {
            $message = "Presensi MASUK Guru Ditolak! Waktu presensi hari ini sudah berakhir (Batas akhir presensi masuk: {$jamPulangConfig} WITA). Guru {$teacher->name} dipersilakan hadir dan melakukan presensi kembali besok pagi.";

            return response()->json([
                'success' => false,
                'outside_hours' => true,
                'scan_type' => 'outside_hours',
                'title' => 'Presensi Masuk Ditolak!',
                'role_type' => 'teacher',
                'message' => $message,
                'person' => [
                    'id' => $teacher->id,
                    'nama' => $teacher->name,
                    'email' => $teacher->email,
                    'kelas' => $teacher->getAssignedClass() ?? 'Guru Pengajar',
                    'is_guru' => true,
                ],
                'attendance' => [
                    'tanggal' => $today,
                    'waktu' => $time,
                    'status_text' => 'DITOLAK (Di Luar Jam Masuk)',
                    'jam_pulang_target' => $jamPulangConfig,
                ],
            ], 200);
        }

        // -------------------------------------------------------------
        // SCAN 1: CHECK-IN (ABSEN MASUK GURU)
        // -------------------------------------------------------------
        $isLate = $currentTimeHM > $jamTerlambatConfig;
        $lateMinutes = 0;

        if ($isLate) {
            $scanCarbon = Carbon::parse($today . ' ' . $time, 'Asia/Makassar');
            $thresholdCarbon = Carbon::parse($today . ' ' . $jamTerlambatConfig . ':00', 'Asia/Makassar');
            $lateMinutes = max(1, abs((int) $scanCarbon->diffInMinutes($thresholdCarbon)));
        }

        $statusNote = $isLate 
            ? "Masuk: [{$time}] - Terlambat {$lateMinutes} menit" 
            : "Masuk: [{$time}] - Tepat Waktu";

        $statusText = $isLate ? "TERLAMBAT ({$lateMinutes}m)" : "HADIR (Tepat Waktu)";
        $message = $isLate
            ? "Presensi MASUK Guru Dicatat! {$teacher->name} dicatat TERLAMBAT {$lateMinutes} menit pada jam [{$time}] (Batas: {$jamTerlambatConfig})."
            : "Presensi MASUK Guru Berhasil! {$teacher->name} dicatat HADIR TEPAT WAKTU pada jam [{$time}].";

        TeacherAttendance::updateOrCreate(
            [
                'teacher_id' => $teacher->id,
                'tanggal' => $today,
            ],
            [
                'jam_masuk' => $time,
                'status' => 'hadir',
                'keterangan' => $statusNote,
            ]
        );

        return response()->json([
            'success' => true,
            'scan_type' => 'masuk',
            'role_type' => 'teacher',
            'message' => $message,
            'person' => [
                'id' => $teacher->id,
                'nama' => $teacher->name,
                'email' => $teacher->email,
                'kelas' => $teacher->getAssignedClass() ?? 'Guru Pengajar',
                'is_guru' => true,
            ],
            'attendance' => [
                'tanggal' => $today,
                'jam_masuk' => $time,
                'jam_pulang' => null,
                'is_late' => $isLate,
                'late_minutes' => $lateMinutes,
                'status_text' => $statusText,
                'jam_terlambat' => $jamTerlambatConfig,
            ],
        ]);
    }

    /**
     * Display printable Student QR ID Card.
     */
    public function card(Student $student)
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        if (!$user) {
            abort(403);
        }

        $isAuthorized = $user->isAdmin()
            || ($user->isParent() && $user->student_id === $student->id)
            || ($user->isTeacher() && (!$user->getAssignedClass() || strtolower($user->getAssignedClass()) === strtolower($student->kelas)));

        if (!$isAuthorized) {
            abort(403, 'Anda tidak memiliki wewenang untuk mencetak kartu QR siswa ini.');
        }

        return view('qr.card', compact('student'));
    }

    /**
     * Display printable Teacher QR ID Card.
     */
    public function teacherCard(User $teacher)
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        if (!$user) {
            abort(403);
        }

        if (!$teacher->isTeacher()) {
            abort(404, 'User ini bukan akun Guru.');
        }

        $isAuthorized = $user->isAdmin() || $user->id === $teacher->id;
        if (!$isAuthorized) {
            abort(403, 'Anda tidak memiliki wewenang untuk mengakses kartu QR Guru ini.');
        }

        return view('qr.teacher-card', compact('teacher'));
    }
}
