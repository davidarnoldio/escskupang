<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\Request;

class QRController extends Controller
{
    /**
     * Display the QR Code Attendance Scanner page.
     */
    public function index()
    {
        $today = now()->format('Y-m-d');
        $todayAttendances = Attendance::with('student')
            ->where('tanggal', $today)
            ->latest('updated_at')
            ->take(10)
            ->get();

        return view('qr.scan', compact('todayAttendances', 'today'));
    }

    /**
     * Process scanned QR Code NIS for daily attendance.
     */
    public function process(Request $request)
    {
        $request->validate([
            'nis' => ['required', 'string'],
        ]);

        $rawInput = trim($request->input('nis'));

        // Handle JSON payload input
        if (str_starts_with($rawInput, '{') && str_ends_with($rawInput, '}')) {
            $json = json_decode($rawInput, true);
            if (isset($json['nis'])) {
                $rawInput = trim($json['nis']);
            }
        }

        // Handle prefix string e.g. "NIS: 0003.26.0236"
        if (preg_match('/^nis[:\s]+(.*)$/i', $rawInput, $matches)) {
            $rawInput = trim($matches[1]);
        }

        $student = Student::where('nis', $rawInput)
            ->orWhere('nis', 'like', "%{$rawInput}%")
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => "Data siswa dengan NIS '{$rawInput}' tidak ditemukan!",
            ], 404);
        }

        $today = now()->format('Y-m-d');
        $time = now()->format('H:i:s');
        $currentTimeHM = now()->format('H:i');

        $jamTerlambatConfig = $student->is_abk
            ? \App\Models\Setting::get('jam_terlambat_abk', '08:30')
            : \App\Models\Setting::get('jam_terlambat', '07:30');

        $isLate = $currentTimeHM > $jamTerlambatConfig;
        $lateMinutes = 0;
        if ($isLate) {
            $scanCarbon = \Carbon\Carbon::parse($today . ' ' . $time);
            $thresholdCarbon = \Carbon\Carbon::parse($today . ' ' . $jamTerlambatConfig . ':00');
            $lateMinutes = max(1, (int) $scanCarbon->diffInMinutes($thresholdCarbon));
        }

        $abkLabel = $student->is_abk ? ' (ABK)' : '';
        $statusNote = $isLate 
            ? "Scan QR [{$time}] - Terlambat {$lateMinutes} menit{$abkLabel}" 
            : "Scan QR [{$time}] - Tepat Waktu{$abkLabel}";

        $attendance = Attendance::updateOrCreate(
            [
                'student_id' => $student->id,
                'tanggal' => $today,
            ],
            [
                'status' => 'hadir',
                'keterangan' => $statusNote,
            ]
        );

        $statusText = $isLate ? "HADIR (Terlambat {$lateMinutes}m)" : "HADIR";
        $message = $isLate
            ? "Presensi Berhasil! {$student->nama} ({$student->kelas}) dicatat HADIR (TERLAMBAT {$lateMinutes} menit - Jam scan {$time}, batas jam {$jamTerlambatConfig})."
            : "Presensi Berhasil! {$student->nama} ({$student->kelas}) dicatat HADIR (Tepat Waktu).";

        return response()->json([
            'success' => true,
            'message' => $message,
            'student' => [
                'id' => $student->id,
                'nis' => $student->nis,
                'nama' => $student->nama,
                'kelas' => $student->kelas,
                'jenis_kelamin' => $student->jenis_kelamin,
                'is_abk' => $student->is_abk,
            ],
            'attendance' => [
                'tanggal' => $today,
                'waktu' => $time,
                'status' => 'hadir',
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
        return view('qr.card', compact('student'));
    }
}
