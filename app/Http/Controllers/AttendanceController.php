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

        $studentsQuery = Student::query()->orderBy('nama', 'asc');

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
        if (!$user || (!$user->isAdmin() && !$user->isTeacher())) {
            abort(403, 'Akses simpan presensi khusus Guru dan Administrator.');
        }

        $request->validate([
            'tanggal' => ['required', 'date'],
            'attendances' => ['required', 'array'],
            'attendances.*.status' => ['required', Rule::in(['hadir', 'izin', 'sakit', 'alpa', 'libur'])],
            'attendances.*.keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        $tanggal = $request->input('tanggal');
        $attendanceInputs = $request->input('attendances', []);

        foreach ($attendanceInputs as $studentId => $data) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'tanggal' => $tanggal,
                ],
                [
                    'status' => $data['status'],
                    'keterangan' => $data['keterangan'] ?? null,
                ]
            );
        }

        return redirect()->back()->with('success', 'Presensi siswa berhasil disimpan.');
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

        $studentsQuery = Student::with(['attendances' => function ($q) use ($bulan) {
            $q->where('tanggal', 'like', "{$bulan}%");
        }])->orderBy('nama', 'asc');

        if ($assignedClass) {
            $studentsQuery->where('kelas', $assignedClass);
        } elseif ($kelas) {
            $studentsQuery->where('kelas', $kelas);
        }

        $students = $studentsQuery->get();

        $daysInMonth = \Carbon\Carbon::parse($bulan . '-01')->daysInMonth;

        $matrix = [];
        foreach ($students as $student) {
            foreach ($student->attendances as $att) {
                $matrix[$student->id][$att->tanggal] = $att;
            }
        }

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
                    $totalHadir++;
                } elseif ($att->status === 'izin') {
                    $totalIzin++;
                } elseif ($att->status === 'sakit') {
                    $totalSakit++;
                } elseif ($att->status === 'alpa') {
                    $totalAlpa++;
                } elseif ($att->status === 'libur') {
                    $totalLibur++;
                }
            } else {
                // If no record on summary date yet, student is counted under Hadir by default
                $totalHadir++;
            }
        }

        // Calculate late students list across selected month
        $lateStudentsList = collect();
        foreach ($students as $student) {
            foreach ($student->attendances as $att) {
                if ($att->status === 'hadir' && str_contains(strtolower($att->keterangan ?? ''), 'terlambat')) {
                    $jamTerlambatConfig = $student->is_abk
                        ? \App\Models\Setting::get('jam_terlambat_abk', '08:30')
                        : \App\Models\Setting::get('jam_terlambat', '07:30');

                    $scanTimeStr = '-';
                    $lateMins = 0;

                    if (preg_match('/\[(\d{2}:\d{2}:\d{2})\]/', $att->keterangan, $timeMatches)) {
                        $scanTimeStr = $timeMatches[1];
                        $scanCarbon = \Carbon\Carbon::parse($att->tanggal . ' ' . $scanTimeStr, 'Asia/Makassar');
                        $thresholdCarbon = \Carbon\Carbon::parse($att->tanggal . ' ' . $jamTerlambatConfig . ':00', 'Asia/Makassar');
                        if ($scanCarbon->greaterThan($thresholdCarbon)) {
                            $lateMins = abs((int) $scanCarbon->diffInMinutes($thresholdCarbon));
                        }
                    } elseif (preg_match('/Terlambat\s+(\d+)\s+menit/i', $att->keterangan, $m)) {
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
            'matrix',
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

        $studentsQuery = Student::with(['attendances' => function ($q) use ($bulan) {
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

        // Totals
        $totalHadir = 0;
        $totalAlpa = 0;
        $totalSakit = 0;
        $totalIzin = 0;
        $totalLibur = 0;
        $totalTerlambat = 0;

        foreach ($students as $student) {
            foreach ($student->attendances as $att) {
                if ($att->status === 'hadir') {
                    $totalHadir++;
                    if (str_contains(strtolower($att->keterangan ?? ''), 'terlambat')) {
                        $totalTerlambat++;
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
            }
        }

        $displayKelas = !empty($kelas) ? $kelas : 'Semua Kelas';

        $teacherName = 'Wali Kelas / Koordinator Presensi';
        if (!empty($kelas)) {
            $waliKelasUser = \App\Models\User::where('role', 'guru')
                ->where('assigned_class', $kelas)
                ->first();
            if (!$waliKelasUser) {
                $waliKelasUser = \App\Models\User::where('role', 'guru')
                    ->where('name', 'like', "%{$kelas}%")
                    ->first();
            }
            $teacherName = $waliKelasUser ? $waliKelasUser->name : 'Wali Kelas ' . $kelas;
        }

        return view('attendances.print_rekap', compact(
            'students',
            'bulan',
            'kelas',
            'displayKelas',
            'effectiveDays',
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
                  ->orWhere('nis', 'like', "%{$search}%");
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

        // 1. Strict Role Check: Admin cannot verify/reject letters
        if ($user && $user->isAdmin() && !$user->isTeacher()) {
            abort(403, 'Akses ditolak. Administrator hanya memiliki wewenang memantau data. Konfirmasi terima atau tolak surat izin merupakan wewenang khusus Guru / Wali Kelas.');
        }

        // 2. Strict Teacher Check
        if (!$user || !$user->isTeacher()) {
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
                'catatan_guru' => $validated['catatan_guru'] ?: 'Surat permohonan izin/sakit telah diverifikasi dan disetujui oleh Wali Kelas.',
            ]);

            return redirect()->back()->with('success', "Surat permohonan izin/sakit untuk {$studentName} berhasil DISETUJUI / DITERIMA.");
        } else {
            // If rejected, attendance status changes to 'alpa'
            $attendance->update([
                'surat_status' => 'ditolak',
                'status' => 'alpa',
                'catatan_guru' => $validated['catatan_guru'] ?: 'Surat izin ditolak oleh Wali Kelas. Siswa dinyatakan Alpa pada tanggal tersebut.',
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
