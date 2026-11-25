<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the main application dashboard with attendance stats and charts.
     */
    public function index()
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();

        if ($user && $user->isParent()) {
            return redirect()->route('parent.dashboard');
        }

        $assignedClass = $user ? $user->getAssignedClass() : null;

        $today = now()->format('Y-m-d');

        $studentQuery = Student::aktif();
        if ($assignedClass) {
            $studentQuery->where('kelas', $assignedClass);
        }

        $totalSiswa = (clone $studentQuery)->count();
        $recentStudents = (clone $studentQuery)->latest()->take(5)->get();

        $jamTerlambatReguler = \App\Models\Setting::get('jam_terlambat', '07:30');

        $todayAttendances = Attendance::with('student')
            ->where('tanggal', $today)
            ->whereHas('student', function ($q) use ($assignedClass) {
                $q->aktif();
                if ($assignedClass) {
                    $q->where('kelas', $assignedClass);
                }
            })->get();

        $hadirHariIni = 0;
        $terlambatHariIni = 0;
        $izinHariIni = 0;
        $sakitHariIni = 0;
        $alpaHariIni = 0;

        foreach ($todayAttendances as $att) {
            if ($att->status === 'hadir') {
                $isLate = $att->student && !$att->student->is_abk && (
                    (!empty($att->jam_masuk) && substr($att->jam_masuk, 0, 5) > $jamTerlambatReguler)
                    || str_contains(strtolower($att->keterangan ?? ''), 'terlambat')
                );
                if ($isLate) {
                    $terlambatHariIni++;
                } else {
                    $hadirHariIni++;
                }
            } elseif ($att->status === 'izin') {
                $izinHariIni++;
            } elseif ($att->status === 'sakit') {
                $sakitHariIni++;
            } elseif ($att->status === 'alpa') {
                $alpaHariIni++;
            }
        }

        $izinSakitHariIni = $izinHariIni + $sakitHariIni;
        $belumPresensiHariIni = max(0, $totalSiswa - ($hadirHariIni + $terlambatHariIni + $izinSakitHariIni + $alpaHariIni));

        // 7-day attendance trend data for chart
        $weeklyDates = [];
        $weeklyHadir = [];
        $weeklyIzinSakit = [];
        $weeklyAlpa = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $weeklyDates[] = $date->translatedFormat('d M');

            $dayAttQuery = Attendance::where('tanggal', $dateStr)
                ->whereHas('student', function ($q) use ($assignedClass) {
                    $q->aktif();
                    if ($assignedClass) {
                        $q->where('kelas', $assignedClass);
                    }
                });

            $weeklyHadir[] = (clone $dayAttQuery)->where('status', 'hadir')->count();
            $weeklyIzinSakit[] = (clone $dayAttQuery)->whereIn('status', ['izin', 'sakit'])->count();
            $weeklyAlpa[] = (clone $dayAttQuery)->where('status', 'alpa')->count();
        }

        return view('dashboard', compact(
            'totalSiswa',
            'hadirHariIni',
            'terlambatHariIni',
            'izinHariIni',
            'sakitHariIni',
            'izinSakitHariIni',
            'alpaHariIni',
            'belumPresensiHariIni',
            'recentStudents',
            'assignedClass',
            'weeklyDates',
            'weeklyHadir',
            'weeklyIzinSakit',
            'weeklyAlpa'
        ));
    }
}
