<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $assignedClass = $user ? $user->getAssignedClass() : null;

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
        $assignedClass = $user ? $user->getAssignedClass() : null;

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
        $assignedClass = $user ? $user->getAssignedClass() : null;

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
        $assignedClass = $user ? $user->getAssignedClass() : null;

        $query = Attendance::with('student')
            ->whereNotNull('surat_izin')
            ->orderBy('tanggal', 'desc');

        if ($assignedClass) {
            $query->whereHas('student', function ($q) use ($assignedClass) {
                $q->where('kelas', $assignedClass);
            });
        }

        $attendances = $query->paginate(15);

        return view('attendances.letters', compact('attendances', 'assignedClass'));
    }
}
