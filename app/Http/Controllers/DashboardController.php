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

        $studentQuery = Student::query();
        if ($assignedClass) {
            $studentQuery->where('kelas', $assignedClass);
        }

        $totalSiswa = (clone $studentQuery)->count();
        $recentStudents = (clone $studentQuery)->latest()->take(5)->get();

        $attendanceQuery = Attendance::where('tanggal', $today);
        if ($assignedClass) {
            $attendanceQuery->whereHas('student', function ($q) use ($assignedClass) {
                $q->where('kelas', $assignedClass);
            });
        }

        $hadirHariIni = (clone $attendanceQuery)->where('status', 'hadir')->count();
        $izinHariIni = (clone $attendanceQuery)->where('status', 'izin')->count();
        $sakitHariIni = (clone $attendanceQuery)->where('status', 'sakit')->count();
        $alpaHariIni = (clone $attendanceQuery)->where('status', 'alpa')->count();
        $izinSakitHariIni = $izinHariIni + $sakitHariIni;
        $belumPresensiHariIni = max(0, $totalSiswa - ($hadirHariIni + $izinSakitHariIni + $alpaHariIni));

        // 7-day attendance trend data for chart
        $weeklyDates = [];
        $weeklyHadir = [];
        $weeklyIzinSakit = [];
        $weeklyAlpa = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $weeklyDates[] = $date->translatedFormat('d M');

            $dayAttQuery = Attendance::where('tanggal', $dateStr);
            if ($assignedClass) {
                $dayAttQuery->whereHas('student', function ($q) use ($assignedClass) {
                    $q->where('kelas', $assignedClass);
                });
            }

            $weeklyHadir[] = (clone $dayAttQuery)->where('status', 'hadir')->count();
            $weeklyIzinSakit[] = (clone $dayAttQuery)->whereIn('status', ['izin', 'sakit'])->count();
            $weeklyAlpa[] = (clone $dayAttQuery)->where('status', 'alpa')->count();
        }

        return view('dashboard', compact(
            'totalSiswa',
            'hadirHariIni',
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
