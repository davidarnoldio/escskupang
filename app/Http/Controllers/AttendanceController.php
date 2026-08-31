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

        $classList = Student::OFFICIAL_CLASSES;

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

        $classList = Student::OFFICIAL_CLASSES;

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

        // Calculate Indicator Totals across loaded students
        $totalHadir = 0;
        $totalIzin = 0;
        $totalSakit = 0;
        $totalAlpa = 0;
        $totalLibur = 0;

        foreach ($students as $student) {
            $totalHadir += $student->attendances->where('status', 'hadir')->count();
            $totalIzin += $student->attendances->where('status', 'izin')->count();
            $totalSakit += $student->attendances->where('status', 'sakit')->count();
            $totalAlpa += $student->attendances->where('status', 'alpa')->count();
            $totalLibur += $student->attendances->where('status', 'libur')->count();
        }

        $totalRecords = $totalHadir + $totalIzin + $totalSakit + $totalAlpa;
        $rataRataKehadiran = $totalRecords > 0 ? round(($totalHadir / $totalRecords) * 100, 1) : 0;

        return view('attendances.rekap', compact(
            'students',
            'bulan',
            'kelas',
            'classList',
            'daysInMonth',
            'matrix',
            'totalHadir',
            'totalIzin',
            'totalSakit',
            'totalAlpa',
            'totalLibur',
            'totalRecords',
            'rataRataKehadiran',
            'assignedClass'
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
