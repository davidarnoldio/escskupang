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

        $jamMasukConfig = Setting::get('jam_masuk', '07:00');
        $jamTerlambatConfig = Setting::get('jam_terlambat', '07:30');
        $jamPulangConfig = Setting::get('jam_pulang', '14:00');

        // Calculate statistics for the selected date
        $totalGuru = $teachers->count();
        $totalHadir = 0;
        $totalIzin = 0;
        $totalSakit = 0;
        $totalAlpa = 0;
        $totalTerlambat = 0;
        $totalPulangAwal = 0;
        $totalBelumPresensi = 0;

        foreach ($teachers as $teacher) {
            $att = $teacher->teacherAttendances->first();
            if ($att) {
                if ($att->status === 'hadir') {
                    $isLate = (!empty($att->jam_masuk) && substr($att->jam_masuk, 0, 5) > $jamTerlambatConfig)
                        || str_contains(strtolower($att->keterangan ?? ''), 'terlambat');
                    if ($isLate) {
                        $totalTerlambat++;
                    } else {
                        $totalHadir++;
                    }
                    if (!empty($att->jam_pulang) && substr($att->jam_pulang, 0, 5) < $jamPulangConfig) {
                        $totalPulangAwal++;
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
            $teachers = $teachers->filter(function ($teacher) use ($statusFilter, $jamTerlambatConfig, $jamPulangConfig) {
                $att = $teacher->teacherAttendances->first();
                if ($statusFilter === 'belum_presensi') {
                    return $att === null;
                }
                if ($statusFilter === 'terlambat') {
                    return $att && $att->status === 'hadir' && (
                        (!empty($att->jam_masuk) && substr($att->jam_masuk, 0, 5) > $jamTerlambatConfig)
                        || str_contains(strtolower($att->keterangan ?? ''), 'terlambat')
                    );
                }
                if ($statusFilter === 'pulang_awal') {
                    return $att && $att->status === 'hadir' && !empty($att->jam_pulang) && substr($att->jam_pulang, 0, 5) < $jamPulangConfig;
                }
                return $att && $att->status === $statusFilter;
            });
        }

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
            'totalPulangAwal',
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
            $jamTerlambatConfig = Setting::get('jam_terlambat', '07:30');
            $jamPulangConfig = Setting::get('jam_pulang', '14:00');

            if (!empty($validated['jam_masuk'])) {
                $attendance->jam_masuk = strlen($validated['jam_masuk']) === 5 ? $validated['jam_masuk'] . ':00' : $validated['jam_masuk'];
            } elseif (empty($attendance->jam_masuk)) {
                $attendance->jam_masuk = now()->format('H:i:s');
            }

            // Auto-append Terlambat note if jam_masuk exceeds threshold and note is missing
            if (!empty($attendance->jam_masuk) && substr($attendance->jam_masuk, 0, 5) > $jamTerlambatConfig) {
                $masukCarbon = Carbon::parse($validated['tanggal'] . ' ' . $attendance->jam_masuk, 'Asia/Makassar');
                $lateTargetCarbon = Carbon::parse($validated['tanggal'] . ' ' . $jamTerlambatConfig . ':00', 'Asia/Makassar');
                $lateMinutes = max(1, abs((int) $masukCarbon->diffInMinutes($lateTargetCarbon)));
                $existingNote = $attendance->keterangan ?? '';

                if (!str_contains(strtolower($existingNote), 'terlambat')) {
                    $lateNote = "Masuk: [{$attendance->jam_masuk}] - Terlambat {$lateMinutes} menit";
                    $attendance->keterangan = $existingNote ? "{$lateNote} | {$existingNote}" : $lateNote;
                }
            }

            if (!empty($validated['jam_pulang'])) {
                $attendance->jam_pulang = strlen($validated['jam_pulang']) === 5 ? $validated['jam_pulang'] . ':00' : $validated['jam_pulang'];

                if (substr($attendance->jam_pulang, 0, 5) < $jamPulangConfig) {
                    $pulangScanCarbon = Carbon::parse($validated['tanggal'] . ' ' . $attendance->jam_pulang, 'Asia/Makassar');
                    $pulangTargetCarbon = Carbon::parse($validated['tanggal'] . ' ' . $jamPulangConfig . ':00', 'Asia/Makassar');
                    $earlyMinutes = max(1, abs((int) $pulangScanCarbon->diffInMinutes($pulangTargetCarbon)));

                    $existingNote = $attendance->keterangan ?? '';
                    if (!str_contains(strtolower($existingNote), 'pulang awal') && !str_contains(strtolower($existingNote), 'pulang duluan')) {
                        $earlyNote = "Pulang Awal (Duluan {$earlyMinutes} menit): [{$attendance->jam_pulang}]";
                        $attendance->keterangan = $existingNote ? "{$existingNote} | {$earlyNote}" : $earlyNote;
                    }
                }
            } elseif ($request->has('jam_pulang')) {
                $attendance->jam_pulang = null;
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
     * Kalender Guru: 7 Hari Penuh (Senin - Minggu) dengan detail jam masuk & jam pulang.
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

        $data = $this->buildMonthlyRecapData($bulan, $search);
        $data['search'] = $search;

        return view('teachers.attendances.rekap', $data);
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
        $data = $this->buildMonthlyRecapData($bulan);

        return view('teachers.attendances.print_rekap', $data);
    }

    /**
     * Build shared 7-day monthly matrix, totals, and detailed check-in/check-out logs for teachers.
     */
    private function buildMonthlyRecapData(string $bulan, ?string $search = null): array
    {
        $carbonMonth = Carbon::parse($bulan . '-01');
        $daysInMonth = $carbonMonth->daysInMonth;
        $jamTerlambatConfig = Setting::get('jam_terlambat', '07:30');
        $jamPulangConfig = Setting::get('jam_pulang', '14:00');

        $query = User::whereIn('role', ['guru', 'wali_kelas']);

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $teachers = $query->with(['teacherAttendances' => function ($q) use ($bulan) {
            $q->where('tanggal', 'like', "{$bulan}%");
        }])->orderBy('name')->get();

        $matrix = [];
        $teacherTotals = [];
        $detailLogs = collect();

        $grandHadir = 0;
        $grandIzin = 0;
        $grandSakit = 0;
        $grandAlpa = 0;
        $grandTerlambat = 0;
        $grandPulangAwal = 0;

        foreach ($teachers as $teacher) {
            $totals = [
                'hadir'       => 0,
                'izin'        => 0,
                'sakit'       => 0,
                'alpa'        => 0,
                'terlambat'   => 0,
                'pulang_awal' => 0,
            ];

            foreach ($teacher->teacherAttendances as $att) {
                $tanggalStr = $att->tanggal instanceof Carbon ? $att->tanggal->format('Y-m-d') : substr((string)$att->tanggal, 0, 10);
                $matrix[$teacher->id][$tanggalStr] = $att;

                $isLate = false;
                $isEarlyOut = false;

                if ($att->status === 'hadir') {
                    if (
                        (!empty($att->jam_masuk) && substr($att->jam_masuk, 0, 5) > $jamTerlambatConfig)
                        || str_contains(strtolower($att->keterangan ?? ''), 'terlambat')
                    ) {
                        $totals['terlambat']++;
                        $isLate = true;
                    } else {
                        $totals['hadir']++;
                    }
                    if (!empty($att->jam_pulang) && (
                        substr($att->jam_pulang, 0, 5) < $jamPulangConfig
                        || str_contains(strtolower($att->keterangan ?? ''), 'pulang awal')
                    )) {
                        $totals['pulang_awal']++;
                        $isEarlyOut = true;
                    }
                } elseif ($att->status === 'izin') {
                    $totals['izin']++;
                } elseif ($att->status === 'sakit') {
                    $totals['sakit']++;
                } elseif ($att->status === 'alpa') {
                    $totals['alpa']++;
                }

                $detailLogs->push((object) [
                    'teacher' => $teacher,
                    'tanggal' => $tanggalStr,
                    'jam_masuk' => $att->jam_masuk ? substr($att->jam_masuk, 0, 5) : null,
                    'jam_pulang' => $att->jam_pulang ? substr($att->jam_pulang, 0, 5) : null,
                    'status' => $att->status,
                    'is_late' => $isLate,
                    'is_early_out' => $isEarlyOut,
                    'keterangan' => $att->keterangan,
                ]);
            }

            $grandHadir += $totals['hadir'];
            $grandIzin += $totals['izin'];
            $grandSakit += $totals['sakit'];
            $grandAlpa += $totals['alpa'];
            $grandTerlambat += $totals['terlambat'];
            $grandPulangAwal += $totals['pulang_awal'];

            $teacherTotals[$teacher->id] = $totals;
        }

        $detailLogs = $detailLogs->sortBy([['tanggal', 'desc'], ['teacher.name', 'asc']])->values();

        return compact(
            'teachers',
            'matrix',
            'teacherTotals',
            'detailLogs',
            'bulan',
            'daysInMonth',
            'carbonMonth',
            'jamTerlambatConfig',
            'jamPulangConfig',
            'grandHadir',
            'grandIzin',
            'grandSakit',
            'grandAlpa',
            'grandTerlambat',
            'grandPulangAwal'
        );
    }
}
