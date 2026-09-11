<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ParentController extends Controller
{
    /**
     * Parent Portal Dashboard showing linked child's attendance & QR Code.
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();

        if ($user && !$user->isParent()) {
            return redirect()->route('dashboard');
        }

        // Get linked student for parent
        $student = $user ? $user->student : null;

        if (!$student) {
            return view('parent.dashboard', [
                'student' => null,
                'todayAttendance' => null,
                'attendances' => collect(),
                'totalHadir' => 0,
                'totalIzin' => 0,
                'totalSakit' => 0,
                'totalAlpa' => 0,
                'persentase' => 0,
            ]);
        }

        $today = now()->format('Y-m-d');
        $todayAttendance = Attendance::where('student_id', $student->id)
            ->where('tanggal', $today)
            ->first();

        $month = $request->input('month', now()->format('m'));
        $year = $request->input('year', now()->format('Y'));

        $attendances = Attendance::where('student_id', $student->id)
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->orderBy('tanggal', 'desc')
            ->get();

        $totalRecords = $attendances->count();
        $totalHadir = $attendances->where('status', 'hadir')->count();
        $totalIzin = $attendances->where('status', 'izin')->count();
        $totalSakit = $attendances->where('status', 'sakit')->count();
        $totalAlpa = $attendances->where('status', 'alpa')->count();

        $persentase = $totalRecords > 0 ? round(($totalHadir / $totalRecords) * 100, 1) : 0;

        // 7-day attendance trend data for child's chart
        $weeklyDates = [];
        $weeklyHadir = [];
        $weeklyIzinSakit = [];
        $weeklyAlpa = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $weeklyDates[] = $date->translatedFormat('d M');

            $dayAtt = Attendance::where('student_id', $student->id)
                ->where('tanggal', $dateStr)
                ->first();

            $weeklyHadir[] = ($dayAtt && $dayAtt->status === 'hadir') ? 1 : 0;
            $weeklyIzinSakit[] = ($dayAtt && in_array($dayAtt->status, ['izin', 'sakit'])) ? 1 : 0;
            $weeklyAlpa[] = ($dayAtt && $dayAtt->status === 'alpa') ? 1 : 0;
        }

        return view('parent.dashboard', compact(
            'student',
            'todayAttendance',
            'attendances',
            'totalHadir',
            'totalIzin',
            'totalSakit',
            'totalAlpa',
            'persentase',
            'month',
            'year',
            'weeklyDates',
            'weeklyHadir',
            'weeklyIzinSakit',
            'weeklyAlpa'
        ));
    }


    /**
     * Upload Permission / Sick Letter Photo to Homeroom Teacher.
     */
    public function uploadLetter(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user || !$user->isParent() || !$user->student) {
            abort(403, 'Akses khusus akun Orang Tua yang terhubung dengan data siswa.');
        }

        $student = $user->student;

        $request->validate([
            'tanggal' => ['required', 'date'],
            'status' => ['required', \Illuminate\Validation\Rule::in(['izin', 'sakit'])],
            'keterangan' => ['nullable', 'string', 'max:255'],
            'surat_izin' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $letterPath = null;
        if ($request->hasFile('surat_izin')) {
            $existing = Attendance::where('student_id', $student->id)
                ->where('tanggal', $request->input('tanggal'))
                ->first();

            if ($existing && $existing->surat_izin && Storage::disk('public')->exists($existing->surat_izin)) {
                Storage::disk('public')->delete($existing->surat_izin);
            }

            $letterPath = $request->file('surat_izin')->store('letters', 'public');
        }

        Attendance::updateOrCreate(
            [
                'student_id' => $student->id,
                'tanggal' => $request->input('tanggal'),
            ],
            [
                'status' => $request->input('status'),
                'keterangan' => $request->input('keterangan') ?: 'Surat ' . ucfirst($request->input('status')) . ' dari Orang Tua',
                'surat_izin' => $letterPath,
                'surat_status' => 'menunggu',
                'catatan_guru' => null,
            ]
        );

        return redirect()->route('parent.dashboard')->with('success', 'Foto Surat Izin / Sakit berhasil dikirimkan ke Wali Kelas!');
    }

    /**
     * Update parent email (enforcing @student.sch.id suffix) & password.
     */
    public function updateAccount(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user || !$user->isParent()) {
            abort(403, 'Akses khusus akun Orang Tua.');
        }

        $request->validate([
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'regex:/@student\.sch\.id$/i',
                \Illuminate\Validation\Rule::unique('users')->ignore($user->id),
            ],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ], [
            'email.regex' => 'Alamat email wajib menggunakan domain berakhiran @student.sch.id',
        ]);

        $user->email = $request->input('email');
        if ($request->filled('password')) {
            $user->password = \Illuminate\Support\Facades\Hash::make($request->input('password'));
        }
        $user->save();

        return redirect()->route('parent.dashboard')->with('success', 'Akun Orang Tua (Email & Password) berhasil diperbarui!');
    }
}
