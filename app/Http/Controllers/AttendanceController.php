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
     * Export attendance recap report to downloadable CSV / Excel format.
     */
    public function export(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $assignedClass = $user ? $user->getAssignedClass() : null;

        $bulan = $request->input('bulan', now()->format('Y-m'));
        $kelas = $assignedClass ?? $request->input('kelas');

        $studentsQuery = Student::with(['attendances' => function ($q) use ($bulan) {
            $q->where('tanggal', 'like', "{$bulan}%");
        }])->orderBy('nama', 'asc');

        if ($assignedClass) {
            $studentsQuery->where('kelas', $assignedClass);
        } elseif ($kelas) {
            $studentsQuery->where('kelas', $kelas);
        }

        $students = $studentsQuery->get();

        $fileName = 'Laporan_Presensi_ESCS_' . $bulan . ($kelas ? '_' . str_replace(' ', '_', $kelas) : '') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        $callback = function () use ($students) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header row
            fputcsv($file, [
                'No',
                'NIS',
                'Nama Siswa',
                'Kelas',
                'Hadir',
                'Izin',
                'Sakit',
                'Alpa',
                'Libur',
                'Total Pertemuan',
                'Persentase Kehadiran (%)',
            ]);

            foreach ($students as $index => $student) {
                $hadir = $student->attendances->where('status', 'hadir')->count();
                $izin = $student->attendances->where('status', 'izin')->count();
                $sakit = $student->attendances->where('status', 'sakit')->count();
                $alpa = $student->attendances->where('status', 'alpa')->count();
                $libur = $student->attendances->where('status', 'libur')->count();
                $total = $hadir + $izin + $sakit + $alpa;
                $pct = $total > 0 ? round(($hadir / $total) * 100, 1) . '%' : '0%';

                fputcsv($file, [
                    $index + 1,
                    $student->nis,
                    $student->nama,
                    $student->kelas,
                    $hadir,
                    $izin,
                    $sakit,
                    $alpa,
                    $libur,
                    $total,
                    $pct,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
        $kelas = $assignedClass ?? $request->input('kelas', Student::OFFICIAL_CLASSES[0]);

        $studentsQuery = Student::with(['attendances' => function ($q) use ($bulan) {
            $q->where('tanggal', 'like', "{$bulan}%");
        }])->orderBy('nama', 'asc');

        if ($assignedClass) {
            $studentsQuery->where('kelas', $assignedClass);
        } elseif ($kelas) {
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

        foreach ($students as $student) {
            $totalHadir += $student->attendances->where('status', 'hadir')->count();
            $totalAlpa += $student->attendances->where('status', 'alpa')->count();
            $totalSakit += $student->attendances->where('status', 'sakit')->count();
            $totalIzin += $student->attendances->where('status', 'izin')->count();
            $totalLibur += $student->attendances->where('status', 'libur')->count();
        }

        $waliKelasUser = \App\Models\User::where('role', 'guru')
            ->where('name', 'like', "%{$kelas}%")
            ->first();
        $teacherName = $waliKelasUser ? $waliKelasUser->name : 'Wali Kelas ' . $kelas;

        return view('attendances.print_rekap', compact(
            'students',
            'bulan',
            'kelas',
            'effectiveDays',
            'totalHadir',
            'totalAlpa',
            'totalSakit',
            'totalIzin',
            'totalLibur',
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
