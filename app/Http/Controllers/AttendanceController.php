<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    /**
     * Display daily attendance entry form for students.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || (!$user->isAdmin() && !$user->isTeacher())) {
            abort(403, 'Akses presensi khusus Guru dan Administrator.');
        }

        $assignedClass = $user->getAssignedClass();

        $tanggal = $request->input('tanggal', now()->format('Y-m-d'));
        $kelas = $assignedClass ?? $request->input('kelas');

        $classList = Student::getAllClasses();

        $studentsQuery = Student::aktif()->orderBy('nama', 'asc');

        if ($assignedClass) {
            $studentsQuery->where('kelas', $assignedClass);
        } elseif ($kelas) {
            $studentsQuery->where('kelas', $kelas);
        }

        $students = $studentsQuery->get();

        // Existing attendance for the selected date
        $attendances = Attendance::where('tanggal', $tanggal)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->keyBy('student_id');

        return view('attendances.index', compact('students', 'attendances', 'tanggal', 'kelas', 'classList', 'assignedClass'));
    }

    /**
     * Store bulk daily attendance for students.
     */
    public function store(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Akses simpan presensi manual khusus Administrator. Guru hanya berhak melihat daftar presensi.');
        }

        $request->validate([
            'tanggal' => ['required', 'date'],
            'attendances' => ['required', 'array'],
            'attendances.*.status' => ['nullable', Rule::in(['hadir', 'izin', 'sakit', 'alpa', 'libur'])],
            'attendances.*.keterangan' => ['nullable', 'string', 'max:255'],
            'attendances.*.jam_masuk' => ['nullable', 'string', 'max:10'],
            'attendances.*.jam_pulang' => ['nullable', 'string', 'max:10'],
        ]);

        $tanggal = $request->input('tanggal');
        $attendanceInputs = $request->input('attendances', []);
        $savedCount = 0;

        $jamTerlambatReguler = \App\Models\Setting::get('jam_terlambat', '07:30');
        $jamPulangReguler = \App\Models\Setting::get('jam_pulang', '14:00');
        $jamPulangAbk = \App\Models\Setting::get('jam_pulang_abk', '13:00');
        $studentsMap = Student::whereIn('id', array_keys($attendanceInputs))->get()->keyBy('id');

        foreach ($attendanceInputs as $studentId => $data) {
            if (empty($data['status'])) {
                continue;
            }

            $payload = [
                'status' => $data['status'],
                'keterangan' => $data['keterangan'] ?? null,
            ];

            if ($data['status'] === 'hadir') {
                $student = $studentsMap->get($studentId);
                $jamMasukInput = !empty($data['jam_masuk'])
                    ? (strlen($data['jam_masuk']) === 5 ? $data['jam_masuk'] . ':00' : $data['jam_masuk'])
                    : null;
                $jamPulangInput = !empty($data['jam_pulang'])
                    ? (strlen($data['jam_pulang']) === 5 ? $data['jam_pulang'] . ':00' : $data['jam_pulang'])
                    : null;

                if ($jamMasukInput !== null) {
                    $payload['jam_masuk'] = $jamMasukInput;
                    if ($student && !$student->is_abk && substr($jamMasukInput, 0, 5) > $jamTerlambatReguler) {
                        $masukCarbon = \Carbon\Carbon::parse($tanggal . ' ' . $jamMasukInput, 'Asia/Makassar');
                        $targetCarbon = \Carbon\Carbon::parse($tanggal . ' ' . $jamTerlambatReguler . ':00', 'Asia/Makassar');
                        $lateMinutes = max(1, abs((int) $masukCarbon->diffInMinutes($targetCarbon)));
                        $ket = $payload['keterangan'] ?? '';
                        if (!str_contains(strtolower($ket), 'terlambat')) {
                            $lateNote = "Masuk: [{$jamMasukInput}] - Terlambat {$lateMinutes} menit";
                            $payload['keterangan'] = $ket ? "{$lateNote} | {$ket}" : $lateNote;
                        }
                    }
                } elseif (array_key_exists('jam_masuk', $data)) {
                    $payload['jam_masuk'] = null;
                }

                if ($jamPulangInput !== null) {
                    $payload['jam_pulang'] = $jamPulangInput;
                    $targetPulang = ($student && $student->is_abk) ? $jamPulangAbk : $jamPulangReguler;
                    if (substr($jamPulangInput, 0, 5) < $targetPulang) {
                        $pulangCarbon = \Carbon\Carbon::parse($tanggal . ' ' . $jamPulangInput, 'Asia/Makassar');
                        $pulangTargetCarbon = \Carbon\Carbon::parse($tanggal . ' ' . $targetPulang . ':00', 'Asia/Makassar');
                        $earlyMinutes = max(1, abs((int) $pulangCarbon->diffInMinutes($pulangTargetCarbon)));
                        $ket = $payload['keterangan'] ?? '';
                        if (!str_contains(strtolower($ket), 'pulang awal') && !str_contains(strtolower($ket), 'pulang duluan')) {
                            $earlyNote = "Pulang Awal (Duluan {$earlyMinutes} menit): [{$jamPulangInput}]";
                            $payload['keterangan'] = $ket ? "{$ket} | {$earlyNote}" : $earlyNote;
                        }
                    }
                } elseif (array_key_exists('jam_pulang', $data)) {
                    $payload['jam_pulang'] = null;
                }
            } else {
                $payload['jam_masuk'] = null;
                $payload['jam_pulang'] = null;
            }

            Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'tanggal' => $tanggal,
                ],
                $payload
            );
            $savedCount++;
        }

        if ($savedCount === 0) {
            return redirect()->back()->with('error', 'Belum ada siswa yang dipilih status presensinya.');
        }

        return redirect()->back()->with('success', "Presensi untuk {$savedCount} siswa berhasil disimpan.");
    }

    /**
     * Monthly Attendance Recap Dashboard with Chart Data.
     */
    public function rekap(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || (!$user->isAdmin() && !$user->isTeacher())) {
            abort(403, 'Akses rekap presensi khusus Guru dan Administrator.');
        }

        $assignedClass = $user->getAssignedClass();

        $bulan = $request->input('bulan', now()->format('Y-m'));
        $kelas = $assignedClass ?? $request->input('kelas');

        $classList = Student::getAllClasses();

        $studentsQuery = Student::aktif()->with(['attendances' => function ($q) use ($bulan) {
            $q->where('tanggal', 'like', "{$bulan}%");
        }])->orderBy('nama', 'asc');

        if ($assignedClass) {
            $studentsQuery->where('kelas', $assignedClass);
        } elseif ($kelas) {
            $studentsQuery->where('kelas', $kelas);
        }

        $students = $studentsQuery->get();

        $daysInMonth = \Carbon\Carbon::parse($bulan . '-01')->daysInMonth;
        $year = (int) substr($bulan, 0, 4);
        $monthNum = (int) substr($bulan, 5, 2);

        // Kalender Siswa: 5 Hari Kerja (Senin - Jumat)
        $effectiveDays = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dateObj = \Carbon\Carbon::createFromDate($year, $monthNum, $d);
            if ($dateObj->isWeekday()) {
                $effectiveDays[] = $d;
            }
        }

        $jamTerlambatReguler = \App\Models\Setting::get('jam_terlambat', '07:30');
        $jamTerlambatAbk = \App\Models\Setting::get('jam_terlambat_abk', '08:30');
        $jamPulangReguler = \App\Models\Setting::get('jam_pulang', '14:00');
        $jamPulangAbk = \App\Models\Setting::get('jam_pulang_abk', '13:00');

        $matrix = [];
        $detailLogs = collect();
        foreach ($students as $student) {
            $targetTerlambat = $student->is_abk ? $jamTerlambatAbk : $jamTerlambatReguler;
            $targetPulang = $student->is_abk ? $jamPulangAbk : $jamPulangReguler;
            foreach ($student->attendances as $att) {
                $matrix[$student->id][$att->tanggal] = $att;

                // Kecuali siswa ABK: hadir lewat jam pada siswa reguler masuk kategori Terlambat (bukan Hadir + Terlambat)
                $isLate = !$student->is_abk && $att->status === 'hadir' && (
                    (!empty($att->jam_masuk) && substr($att->jam_masuk, 0, 5) > $targetTerlambat)
                    || str_contains(strtolower($att->keterangan ?? ''), 'terlambat')
                );
                $isEarlyOut = $att->status === 'hadir' && !empty($att->jam_pulang) && (
                    substr($att->jam_pulang, 0, 5) < $targetPulang
                    || str_contains(strtolower($att->keterangan ?? ''), 'pulang awal')
                );

                $detailLogs->push((object) [
                    'student' => $student,
                    'tanggal' => $att->tanggal,
                    'jam_masuk' => $att->jam_masuk ? substr($att->jam_masuk, 0, 5) : null,
                    'jam_pulang' => $att->jam_pulang ? substr($att->jam_pulang, 0, 5) : null,
                    'status' => $att->status,
                    'is_late' => $isLate,
                    'is_early_out' => $isEarlyOut,
                    'keterangan' => $att->keterangan,
                ]);
            }
        }
        $detailLogs = $detailLogs->sortBy([['tanggal', 'desc'], ['student.nama', 'asc']])->values();

        // Determine active date for indicator summary cards (latest date in selected month with attendance data, or today)
        $latestDateInMonth = Attendance::where('tanggal', 'like', "{$bulan}%")
            ->whereIn('student_id', $students->pluck('id'))
            ->max('tanggal');

        $summaryDate = $latestDateInMonth ?? now()->format('Y-m-d');

        $summaryAttendances = Attendance::where('tanggal', $summaryDate)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->keyBy('student_id');

        // Calculate Indicator Totals per student for active summary date (bounded by total students)
        $totalHadir = 0;
        $totalIzin = 0;
        $totalSakit = 0;
        $totalAlpa = 0;
        $totalLibur = 0;

        foreach ($students as $student) {
            $att = $summaryAttendances->get($student->id);
            if ($att) {
                if ($att->status === 'hadir') {
                    $isLateOnSummary = !$student->is_abk && (
                        (!empty($att->jam_masuk) && substr($att->jam_masuk, 0, 5) > $jamTerlambatReguler)
                        || str_contains(strtolower($att->keterangan ?? ''), 'terlambat')
                    );
                    if (!$isLateOnSummary) {
                        $totalHadir++;
                    }
                } elseif ($att->status === 'izin') {
                    $totalIzin++;
                } elseif ($att->status === 'sakit') {
                    $totalSakit++;
                } elseif ($att->status === 'alpa') {
                    $totalAlpa++;
                } elseif ($att->status === 'libur') {
                    $totalLibur++;
                }
            }
        }

        // Calculate late students list across selected month (kecuali siswa ABK)
        $lateStudentsList = collect();
        foreach ($students as $student) {
            if ($student->is_abk) {
                continue;
            }
            $jamTerlambatConfig = $jamTerlambatReguler;
            foreach ($student->attendances as $att) {
                $isLateEntry = $att->status === 'hadir' && (
                    (!empty($att->jam_masuk) && substr($att->jam_masuk, 0, 5) > $jamTerlambatConfig)
                    || str_contains(strtolower($att->keterangan ?? ''), 'terlambat')
                );
                if ($isLateEntry) {
                    $scanTimeStr = $att->jam_masuk ?: '-';
                    $lateMins = 0;

                    if (!empty($att->jam_masuk)) {
                        $scanCarbon = \Carbon\Carbon::parse($att->tanggal . ' ' . $att->jam_masuk, 'Asia/Makassar');
                        $thresholdCarbon = \Carbon\Carbon::parse($att->tanggal . ' ' . $jamTerlambatConfig . ':00', 'Asia/Makassar');
                        if ($scanCarbon->greaterThan($thresholdCarbon)) {
                            $lateMins = max(1, abs((int) $scanCarbon->diffInMinutes($thresholdCarbon)));
                        }
                    } elseif (preg_match('/\[(\d{2}:\d{2}:\d{2})\]/', $att->keterangan ?? '', $timeMatches)) {
                        $scanTimeStr = $timeMatches[1];
                        $scanCarbon = \Carbon\Carbon::parse($att->tanggal . ' ' . $scanTimeStr, 'Asia/Makassar');
                        $thresholdCarbon = \Carbon\Carbon::parse($att->tanggal . ' ' . $jamTerlambatConfig . ':00', 'Asia/Makassar');
                        if ($scanCarbon->greaterThan($thresholdCarbon)) {
                            $lateMins = max(1, abs((int) $scanCarbon->diffInMinutes($thresholdCarbon)));
                        }
                    } elseif (preg_match('/Terlambat\s+(\d+)\s+menit/i', $att->keterangan ?? '', $m)) {
                        $lateMins = (int) $m[1];
                    }

                    $lateFormatted = '';
                    if ($lateMins >= 60) {
                        $hrs = floor($lateMins / 60);
                        $mins = $lateMins % 60;
                        $lateFormatted = $mins > 0 ? "{$hrs}j {$mins}m" : "{$hrs}j";
                    } else {
                        $lateFormatted = "{$lateMins}m";
                    }

                    $lateStudentsList->push((object)[
                        'student' => $student,
                        'tanggal' => $att->tanggal,
                        'waktu_scan' => $scanTimeStr,
                        'jam_terlambat' => $jamTerlambatConfig,
                        'late_minutes' => $lateMins,
                        'late_formatted' => $lateFormatted,
                        'keterangan' => $att->keterangan,
                    ]);
                }
            }
        }

        $lateStudentsList = $lateStudentsList->sortByDesc('tanggal')->values();
        $totalTerlambat = $lateStudentsList->pluck('student.id')->unique()->count();

        $totalRecords = $totalHadir + $totalIzin + $totalSakit + $totalAlpa;
        $rataRataKehadiran = $students->count() > 0 ? round(($totalHadir / $students->count()) * 100, 1) : 0;

        return view('attendances.rekap', compact(
            'students',
            'bulan',
            'kelas',
            'classList',
            'daysInMonth',
            'effectiveDays',
            'matrix',
            'detailLogs',
            'jamTerlambatReguler',
            'jamTerlambatAbk',
            'jamPulangReguler',
            'jamPulangAbk',
            'totalHadir',
            'totalTerlambat',
            'lateStudentsList',
            'totalIzin',
            'totalSakit',
            'totalAlpa',
            'totalLibur',
            'totalRecords',
            'rataRataKehadiran',
            'assignedClass',
            'summaryDate'
        ));
    }



    /**
     * Print official attendance recap matching Gambar 1.
     */
    public function printRekap(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || (!$user->isAdmin() && !$user->isTeacher())) {
            abort(403, 'Akses cetak rekap khusus Guru dan Administrator.');
        }

        $assignedClass = $user->getAssignedClass();

        $bulan = $request->input('bulan', now()->format('Y-m'));
        $kelas = $assignedClass ?? $request->input('kelas');

        $studentsQuery = Student::aktif()->with(['attendances' => function ($q) use ($bulan) {
            $q->where('tanggal', 'like', "{$bulan}%");
        }])->orderBy('kelas', 'asc')->orderBy('nama', 'asc');

        if ($assignedClass) {
            $studentsQuery->where('kelas', $assignedClass);
        } elseif (!empty($kelas)) {
            $studentsQuery->where('kelas', $kelas);
        }

        $students = $studentsQuery->get();

        // Calculate all days of the month and effective school days (Monday-Friday)
        $year = (int) substr($bulan, 0, 4);
        $monthNum = (int) substr($bulan, 5, 2);
        $daysInMonth = \Carbon\Carbon::createFromDate($year, $monthNum, 1)->daysInMonth;

        $effectiveDays = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = \Carbon\Carbon::createFromDate($year, $monthNum, $d);
            if ($date->isWeekday()) {
                $effectiveDays[] = $d;
            }
        }

        // Totals & Detail Logs (Jam Masuk & Jam Pulang)
        $jamTerlambatReguler = \App\Models\Setting::get('jam_terlambat', '07:30');
        $jamTerlambatAbk = \App\Models\Setting::get('jam_terlambat_abk', '08:30');
        $jamPulangReguler = \App\Models\Setting::get('jam_pulang', '14:00');
        $jamPulangAbk = \App\Models\Setting::get('jam_pulang_abk', '13:00');
        $detailLogs = collect();

        $totalHadir = 0;
        $totalAlpa = 0;
        $totalSakit = 0;
        $totalIzin = 0;
        $totalLibur = 0;
        $totalTerlambat = 0;

        foreach ($students as $student) {
            $targetTerlambat = $student->is_abk ? $jamTerlambatAbk : $jamTerlambatReguler;
            $targetPulang = $student->is_abk ? $jamPulangAbk : $jamPulangReguler;
            foreach ($student->attendances as $att) {
                $isLate = false;
                if ($att->status === 'hadir') {
                    $isLateEntry = !$student->is_abk && (
                        (!empty($att->jam_masuk) && substr($att->jam_masuk, 0, 5) > $targetTerlambat)
                        || str_contains(strtolower($att->keterangan ?? ''), 'terlambat')
                    );
                    if ($isLateEntry) {
                        $totalTerlambat++;
                        $isLate = true;
                    } else {
                        $totalHadir++;
                    }
                } elseif ($att->status === 'alpa') {
                    $totalAlpa++;
                } elseif ($att->status === 'sakit') {
                    $totalSakit++;
                } elseif ($att->status === 'izin') {
                    $totalIzin++;
                } elseif ($att->status === 'libur') {
                    $totalLibur++;
                }

                $isEarlyOut = $att->status === 'hadir' && !empty($att->jam_pulang) && (
                    substr($att->jam_pulang, 0, 5) < $targetPulang
                    || str_contains(strtolower($att->keterangan ?? ''), 'pulang awal')
                );

                $detailLogs->push((object) [
                    'student' => $student,
                    'tanggal' => $att->tanggal,
                    'jam_masuk' => $att->jam_masuk ? substr($att->jam_masuk, 0, 5) : null,
                    'jam_pulang' => $att->jam_pulang ? substr($att->jam_pulang, 0, 5) : null,
                    'status' => $att->status,
                    'is_late' => $isLate,
                    'is_early_out' => $isEarlyOut,
                    'keterangan' => $att->keterangan,
                ]);
            }
        }
        $detailLogs = $detailLogs->sortBy([['tanggal', 'asc'], ['student.nama', 'asc']])->values();

        $displayKelas = !empty($kelas) ? $kelas : 'Semua Kelas';

        $teacherName = 'Wali Kelas / Koordinator Presensi';
        if (!empty($kelas)) {
            // Bug #4 Fix: query both 'guru' and 'wali_kelas' roles
            $waliKelasUser = \App\Models\User::whereIn('role', ['guru', 'wali_kelas'])
                ->where('assigned_class', $kelas)
                ->first();
            if (!$waliKelasUser) {
                $waliKelasUser = \App\Models\User::whereIn('role', ['guru', 'wali_kelas'])
                    ->where('name', 'like', "%{$kelas}%")
                    ->first();
            }
            $teacherName = $waliKelasUser ? $waliKelasUser->name : \App\Models\Student::formatWaliClass($kelas);
        }

        return view('attendances.print_rekap', compact(
            'students',
            'bulan',
            'kelas',
            'displayKelas',
            'effectiveDays',
            'detailLogs',
            'jamTerlambatReguler',
            'jamTerlambatAbk',
            'jamPulangReguler',
            'jamPulangAbk',
            'totalHadir',
            'totalAlpa',
            'totalSakit',
            'totalIzin',
            'totalLibur',
            'totalTerlambat',
            'teacherName'
        ));
    }

    /**
     * Parent permission letters reception page for Homeroom Teachers & Admin.
     */
    public function letters(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || (!$user->isAdmin() && !$user->isTeacher())) {
            abort(403, 'Akses surat izin khusus Guru dan Administrator.');
        }

        $assignedClass = $user->getAssignedClass();
        $statusFilter = $request->input('status');
        $search = $request->input('search');

        $baseQuery = Attendance::with('student')
            ->whereNotNull('surat_izin');

        if ($assignedClass) {
            $baseQuery->whereHas('student', function ($q) use ($assignedClass) {
                $q->where('kelas', $assignedClass);
            });
        }

        $hasSuratStatus = Schema::hasColumn('attendances', 'surat_status');

        // Stats calculation
        $totalLetters = (clone $baseQuery)->count();
        if ($hasSuratStatus) {
            $stats = [
                'total' => $totalLetters,
                'menunggu' => (clone $baseQuery)->where(function ($q) {
                    $q->where('surat_status', 'menunggu')->orWhereNull('surat_status');
                })->count(),
                'disetujui' => (clone $baseQuery)->where('surat_status', 'disetujui')->count(),
                'ditolak' => (clone $baseQuery)->where('surat_status', 'ditolak')->count(),
            ];
        } else {
            $stats = [
                'total' => $totalLetters,
                'menunggu' => $totalLetters,
                'disetujui' => 0,
                'ditolak' => 0,
            ];
        }

        $query = clone $baseQuery;

        if ($statusFilter && $hasSuratStatus) {
            if ($statusFilter === 'menunggu') {
                $query->where(function ($q) {
                    $q->where('surat_status', 'menunggu')->orWhereNull('surat_status');
                });
            } else {
                $query->where('surat_status', $statusFilter);
            }
        }

        if ($search) {
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $attendances = $query->orderBy('tanggal', 'desc')->paginate(15)->withQueryString();

        return view('attendances.letters', compact('attendances', 'assignedClass', 'stats', 'statusFilter', 'search'));
    }

    /**
     * Homeroom Teacher verifies student permission letter (Accept/Reject).
     * ADMIN IS FORBIDDEN from verifying/rejecting letters (read-only monitor only).
     */
    public function verifyLetter(Request $request, Attendance $attendance)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // 1. Strict Role Check: Admin (non-teacher) cannot verify/reject letters
        if (!$user) {
            abort(403, 'Akses khusus Guru / Wali Kelas.');
        }

        if ($user->isAdmin()) {
            abort(403, 'Akses ditolak. Administrator hanya memiliki wewenang memantau data. Konfirmasi terima atau tolak surat izin merupakan wewenang khusus Guru / Wali Kelas.');
        }

        // 2. Strict Teacher Check
        if (!$user->isTeacher()) {
            abort(403, 'Akses khusus Guru / Wali Kelas.');
        }

        // 3. Class Authorization Check
        $teacherClass = $user->getAssignedClass();
        if ($teacherClass && $attendance->student && strtolower($attendance->student->kelas) !== strtolower($teacherClass)) {
            abort(403, "Anda hanya berwenang memverifikasi surat izin siswa di kelas binaan Anda ({$teacherClass}).");
        }

        $validated = $request->validate([
            'action' => ['required', 'in:setujui,tolak'],
            'catatan_guru' => ['nullable', 'string', 'max:500'],
        ]);

        $studentName = $attendance->student->nama ?? 'Siswa';

        if ($validated['action'] === 'setujui') {
            $attendance->update([
                'surat_status' => 'disetujui',
                'catatan_guru' => ($validated['catatan_guru'] ?? '') ?: 'Surat permohonan izin/sakit telah diverifikasi dan disetujui oleh Wali Kelas.',
            ]);

            return redirect()->back()->with('success', "Surat permohonan izin/sakit untuk {$studentName} berhasil DISETUJUI / DITERIMA.");
        } else {
            // If rejected, attendance status changes to 'alpa'
            $attendance->update([
                'surat_status' => 'ditolak',
                'status'       => 'alpa',
                'catatan_guru' => ($validated['catatan_guru'] ?? '') ?: 'Surat izin ditolak oleh Wali Kelas. Siswa dinyatakan Alpa pada tanggal tersebut.',
            ]);

            return redirect()->back()->with('success', "Surat permohonan izin/sakit untuk {$studentName} DITOLAK. Status kehadiran diubah menjadi ALPA.");
        }
    }

    /**
     * Delete student permission letter attachment.
     */
    public function destroyLetter(Attendance $attendance)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        $isTeacher = $user->isTeacher() && (!$user->getAssignedClass() || ($attendance->student && strtolower($attendance->student->kelas) === strtolower($user->getAssignedClass())));
        $isAdmin = $user->isAdmin();
        $isParent = $user->isParent() && ((int)$user->student_id === (int)$attendance->student_id);

        if (!$isTeacher && !$isAdmin && !$isParent) {
            abort(403, 'Anda tidak berwenang menghapus berkas surat ini.');
        }

        if ($attendance->surat_izin && Storage::disk('public')->exists($attendance->surat_izin)) {
            Storage::disk('public')->delete($attendance->surat_izin);
        }

        $attendance->update([
            'surat_izin' => null,
            'surat_status' => null,
            'catatan_guru' => null,
        ]);

        return redirect()->back()->with('success', 'Berkas foto surat izin berhasil dihapus.');
    }
}
