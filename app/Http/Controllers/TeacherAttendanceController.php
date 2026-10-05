<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\TeacherAttendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TeacherAttendanceController extends Controller
{
    /**
     * Display daily attendance list of all teachers (Admin Only).
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if (!$user->isAdmin()) {
            abort(403, 'Akses khusus Administrator.');
        }

        $tanggal = $request->input('tanggal', now()->format('Y-m-d'));
        $search = $request->input('search');
        $statusFilter = $request->input('status');

        $query = User::whereIn('role', ['guru', 'wali_kelas']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $teachers = $query->with(['teacherAttendances' => function ($q) use ($tanggal) {
            $q->where('tanggal', $tanggal);
        }])->orderBy('name')->get();

        // Calculate statistics for the selected date
        $totalGuru = $teachers->count();
        $totalHadir = 0;
        $totalIzin = 0;
        $totalSakit = 0;
        $totalAlpa = 0;
        $totalTerlambat = 0;
        $totalBelumPresensi = 0;

        foreach ($teachers as $teacher) {
            $att = $teacher->teacherAttendances->first();
            if ($att) {
                if ($att->status === 'hadir') {
                    $totalHadir++;
                    if (str_contains(strtolower($att->keterangan ?? ''), 'terlambat')) {
                        $totalTerlambat++;
                    }
                } elseif ($att->status === 'izin') {
                    $totalIzin++;
                } elseif ($att->status === 'sakit') {
                    $totalSakit++;
                } elseif ($att->status === 'alpa') {
                    $totalAlpa++;
                }
            } else {
                $totalBelumPresensi++;
            }
        }

        // Apply status filter if selected
        if ($statusFilter) {
            $teachers = $teachers->filter(function ($teacher) use ($statusFilter) {
                $att = $teacher->teacherAttendances->first();
                if ($statusFilter === 'belum_presensi') {
                    return $att === null;
                }
                if ($statusFilter === 'terlambat') {
                    return $att && $att->status === 'hadir' && str_contains(strtolower($att->keterangan ?? ''), 'terlambat');
                }
                return $att && $att->status === $statusFilter;
            });
        }

        $jamMasukConfig = Setting::get('jam_masuk', '07:00');
        $jamTerlambatConfig = Setting::get('jam_terlambat', '07:30');
        $jamPulangConfig = Setting::get('jam_pulang', '14:00');

        return view('teachers.attendances.index', compact(
            'teachers',
            'tanggal',
            'search',
            'statusFilter',
            'totalGuru',
            'totalHadir',
            'totalIzin',
            'totalSakit',
            'totalAlpa',
            'totalTerlambat',
            'totalBelumPresensi',
            'jamMasukConfig',
            'jamTerlambatConfig',
            'jamPulangConfig'
        ));
    }

    /**
     * Admin updates a teacher's attendance status (Hadir, Izin, Sakit, Alpa).
     */
    public function updateStatus(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if (!$user->isAdmin()) {
            abort(403, 'Akses khusus Administrator.');
        }

        $validated = $request->validate([
            'teacher_id' => ['required', 'exists:users,id'],
            'tanggal'    => ['required', 'date'],
            'status'     => ['required', 'in:hadir,izin,sakit,alpa,hapus'],
            'keterangan' => ['nullable', 'string', 'max:255'],
            'jam_masuk'  => ['nullable', 'string', 'max:10'],
            'jam_pulang' => ['nullable', 'string', 'max:10'],
        ]);

        $teacher = User::findOrFail($validated['teacher_id']);

        if ($validated['status'] === 'hapus') {
            TeacherAttendance::where('teacher_id', $teacher->id)
                ->where('tanggal', $validated['tanggal'])
                ->delete();

            return redirect()->back()->with('success', "Data presensi Guru {$teacher->name} pada {$validated['tanggal']} berhasil dihapus.");
        }

        $attendance = TeacherAttendance::firstOrNew([
            'teacher_id' => $teacher->id,
            'tanggal'    => $validated['tanggal'],
        ]);

        $attendance->status = $validated['status'];
        $attendance->keterangan = $validated['keterangan'] ?? ($validated['status'] === 'alpa' ? 'Tanpa Keterangan (Ditetapkan Admin)' : null);

        if ($validated['status'] === 'hadir') {
            if (!empty($validated['jam_masuk'])) {
                $attendance->jam_masuk = strlen($validated['jam_masuk']) === 5 ? $validated['jam_masuk'] . ':00' : $validated['jam_masuk'];
            } elseif (empty($attendance->jam_masuk)) {
                $attendance->jam_masuk = now()->format('H:i:s');
            }

            if (!empty($validated['jam_pulang'])) {
                $attendance->jam_pulang = strlen($validated['jam_pulang']) === 5 ? $validated['jam_pulang'] . ':00' : $validated['jam_pulang'];
            }
        } else {
            // For Izin, Sakit, Alpa: reset check-in/out times unless explicitly kept
            if (empty($validated['jam_masuk'])) {
                $attendance->jam_masuk = null;
            }
            if (empty($validated['jam_pulang'])) {
                $attendance->jam_pulang = null;
            }
        }

        $attendance->save();

        $statusLabel = match($validated['status']) {
            'hadir' => 'HADIR',
            'izin'  => 'IZIN',
            'sakit' => 'SAKIT',
            'alpa'  => 'ALPA',
            default => strtoupper($validated['status']),
        };

        return redirect()->back()->with('success', "Status presensi Guru {$teacher->name} berhasil disimpan sebagai [{$statusLabel}].");
    }

    /**
     * Display Monthly Recap Matrix for all Teachers (Admin Only).
     */
    public function rekap(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if (!$user->isAdmin()) {
            abort(403, 'Akses khusus Administrator.');
        }

        $bulan = $request->input('bulan', now()->format('Y-m'));
        $search = $request->input('search');

        $carbonMonth = Carbon::parse($bulan . '-01');
        $daysInMonth = $carbonMonth->daysInMonth;

        $query = User::whereIn('role', ['guru', 'wali_kelas']);

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $teachers = $query->with(['teacherAttendances' => function ($q) use ($bulan) {
            $q->where('tanggal', 'like', "{$bulan}%");
        }])->orderBy('name')->get();

        // Build 2D matrix: [teacher_id][YYYY-MM-DD] = TeacherAttendance
        $matrix = [];
        $teacherTotals = [];

        $grandHadir = 0;
        $grandIzin = 0;
        $grandSakit = 0;
        $grandAlpa = 0;
        $grandTerlambat = 0;

        foreach ($teachers as $teacher) {
            $totals = [
                'hadir'     => 0,
                'izin'      => 0,
                'sakit'     => 0,
                'alpa'      => 0,
                'terlambat' => 0,
            ];

            foreach ($teacher->teacherAttendances as $att) {
                $tanggalStr = $att->tanggal instanceof Carbon ? $att->tanggal->format('Y-m-d') : substr((string)$att->tanggal, 0, 10);
                $matrix[$teacher->id][$tanggalStr] = $att;

                if ($att->status === 'hadir') {
                    $totals['hadir']++;
                    if (str_contains(strtolower($att->keterangan ?? ''), 'terlambat')) {
                        $totals['terlambat']++;
                    }
                } elseif ($att->status === 'izin') {
                    $totals['izin']++;
                } elseif ($att->status === 'sakit') {
                    $totals['sakit']++;
                } elseif ($att->status === 'alpa') {
                    $totals['alpa']++;
                }
            }

            $grandHadir += $totals['hadir'];
            $grandIzin += $totals['izin'];
            $grandSakit += $totals['sakit'];
            $grandAlpa += $totals['alpa'];
            $grandTerlambat += $totals['terlambat'];

            $teacherTotals[$teacher->id] = $totals;
        }

        return view('teachers.attendances.rekap', compact(
            'teachers',
            'matrix',
            'teacherTotals',
            'bulan',
            'daysInMonth',
            'carbonMonth',
            'search',
            'grandHadir',
            'grandIzin',
            'grandSakit',
            'grandAlpa',
            'grandTerlambat'
        ));
    }

    /**
     * Print-ready A4 Recap for Teachers (Admin Only).
     */
    public function printRekap(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if (!$user->isAdmin()) {
            abort(403, 'Akses khusus Administrator.');
        }

        $bulan = $request->input('bulan', now()->format('Y-m'));

        $carbonMonth = Carbon::parse($bulan . '-01');
        $daysInMonth = $carbonMonth->daysInMonth;

        $teachers = User::whereIn('role', ['guru', 'wali_kelas'])
            ->with(['teacherAttendances' => function ($q) use ($bulan) {
                $q->where('tanggal', 'like', "{$bulan}%");
            }])
            ->orderBy('name')
            ->get();

        $matrix = [];
        $teacherTotals = [];

        $grandHadir = 0;
        $grandIzin = 0;
        $grandSakit = 0;
        $grandAlpa = 0;
        $grandTerlambat = 0;

        foreach ($teachers as $teacher) {
            $totals = [
                'hadir'     => 0,
                'izin'      => 0,
                'sakit'     => 0,
                'alpa'      => 0,
                'terlambat' => 0,
            ];

            foreach ($teacher->teacherAttendances as $att) {
                $tanggalStr = $att->tanggal instanceof Carbon ? $att->tanggal->format('Y-m-d') : substr((string)$att->tanggal, 0, 10);
                $matrix[$teacher->id][$tanggalStr] = $att;

                if ($att->status === 'hadir') {
                    $totals['hadir']++;
                    if (str_contains(strtolower($att->keterangan ?? ''), 'terlambat')) {
                        $totals['terlambat']++;
                    }
                } elseif ($att->status === 'izin') {
                    $totals['izin']++;
                } elseif ($att->status === 'sakit') {
                    $totals['sakit']++;
                } elseif ($att->status === 'alpa') {
                    $totals['alpa']++;
                }
            }

            $grandHadir += $totals['hadir'];
            $grandIzin += $totals['izin'];
            $grandSakit += $totals['sakit'];
            $grandAlpa += $totals['alpa'];
            $grandTerlambat += $totals['terlambat'];

            $teacherTotals[$teacher->id] = $totals;
        }

        return view('teachers.attendances.print_rekap', compact(
            'teachers',
            'matrix',
            'teacherTotals',
            'bulan',
            'daysInMonth',
            'carbonMonth',
            'grandHadir',
            'grandIzin',
            'grandSakit',
            'grandAlpa',
            'grandTerlambat'
        ));
    }
}
