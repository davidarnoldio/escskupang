<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentGuidanceJournal;
use App\Models\StudentGuidanceMonthlyRecap;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class StudentGuidanceController extends Controller
{
    /**
     * Dashboard Jurnal Observasi & Bimbingan Siswa.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || (!$user->isTeacher() && !$user->isAdmin())) {
            abort(403, 'Akses khusus untuk Wali Kelas dan Administrator.');
        }

        // Tentukan kelas yang aktif
        $availableClasses = Student::distinct()->pluck('kelas')->filter()->values();
        $assignedClass = $user->isTeacher() ? $user->getAssignedClass() : null;

        if ($user->isTeacher()) {
            $selectedClass = $assignedClass;
            if (!$selectedClass) {
                return view('teachers.guidance.no_class');
            }
        } else {
            // Admin can choose class from query, default to first available class
            $selectedClass = $request->input('kelas', $availableClasses->first() ?? '1');
        }

        // Tentukan periode bulan
        $selectedMonth = $request->input('bulan', now()->format('Y-m'));

        // Query Jurnal Observasi Harian
        $query = StudentGuidanceJournal::with(['student', 'teacher'])
            ->where('kelas', $selectedClass)
            ->where('tanggal', 'like', "{$selectedMonth}%");

        // Filter Kategori (A, S, D, P)
        if ($request->filled('kategori') && in_array($request->kategori, ['A', 'S', 'D', 'P'])) {
            $query->where('kategori', $request->kategori);
        }

        // Filter Pencarian Siswa / Catatan
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('nama', 'like', "%{$search}%");
                })
                ->orWhere('perilaku_kejadian', 'like', "%{$search}%")
                ->orWhere('identifikasi_masalah', 'like', "%{$search}%")
                ->orWhere('pendekatan_wali_kelas', 'like', "%{$search}%");
            });
        }

        $journals = $query->orderBy('tanggal', 'asc')->orderBy('id', 'asc')->get();

        // Hitung statistik kasus per kategori
        $allMonthJournals = StudentGuidanceJournal::where('kelas', $selectedClass)
            ->where('tanggal', 'like', "{$selectedMonth}%")
            ->get();

        $countA = $allMonthJournals->where('kategori', 'A')->count();
        $countS = $allMonthJournals->where('kategori', 'S')->count();
        $countD = $allMonthJournals->where('kategori', 'D')->count();
        $countP = $allMonthJournals->where('kategori', 'P')->count();
        $totalKasus = $allMonthJournals->count();

        // Ambil atau inisialisasi Catatan Umum & Rekap Bulanan (Bagian C & D)
        $monthlyRecap = StudentGuidanceMonthlyRecap::firstOrNew([
            'kelas' => $selectedClass,
            'bulan' => $selectedMonth,
        ]);

        // Daftar Siswa di Kelas tersebut untuk dropdown pilihan siswa
        $students = Student::where('kelas', $selectedClass)
            ->where('status', 'aktif')
            ->orderBy('nama', 'asc')
            ->get();

        return view('teachers.guidance.index', compact(
            'journals',
            'monthlyRecap',
            'students',
            'selectedClass',
            'selectedMonth',
            'availableClasses',
            'countA',
            'countS',
            'countD',
            'countP',
            'totalKasus'
        ));
    }

    /**
     * Simpan entri jurnal observasi baru.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $assignedClass = $user->isTeacher() ? $user->getAssignedClass() : null;

        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'tanggal' => ['required', 'date'],
            'perilaku_kejadian' => ['required', 'string'],
            'kategori' => ['required', 'in:A,S,D,P'],
            'identifikasi_masalah' => ['nullable', 'string'],
            'pendekatan_wali_kelas' => ['nullable', 'string'],
            'komitmen_siswa' => ['nullable', 'string'],
            'tindak_lanjut' => ['nullable', 'string'],
        ], [
            'student_id.required' => 'Pilih nama siswa yang diobservasi.',
            'tanggal.required' => 'Tanggal observasi wajib diisi.',
            'perilaku_kejadian.required' => 'Perilaku atau kejadian wajib diuraikan.',
            'kategori.required' => 'Pilih kategori perilaku (A/S/D/P).',
        ]);

        $student = Student::findOrFail($validated['student_id']);

        // Pastikan kelas sesuai
        $targetClass = $user->isTeacher() ? $assignedClass : $student->kelas;

        StudentGuidanceJournal::create([
            'student_id' => $student->id,
            'teacher_id' => $user->id,
            'kelas' => $targetClass,
            'tanggal' => $validated['tanggal'],
            'perilaku_kejadian' => $validated['perilaku_kejadian'],
            'kategori' => $validated['kategori'],
            'identifikasi_masalah' => $validated['identifikasi_masalah'] ?? null,
            'pendekatan_wali_kelas' => $validated['pendekatan_wali_kelas'] ?? null,
            'komitmen_siswa' => $validated['komitmen_siswa'] ?? null,
            'tindak_lanjut' => $validated['tindak_lanjut'] ?? null,
            'paraf' => true,
        ]);

        $month = Carbon::parse($validated['tanggal'])->format('Y-m');

        return redirect()->route('guidance-journal.index', [
            'kelas' => $targetClass,
            'bulan' => $month,
        ])->with('success', 'Entri jurnal observasi siswa berhasil dicatat.');
    }

    /**
     * Update entri jurnal observasi.
     */
    public function update(Request $request, StudentGuidanceJournal $journal)
    {
        $user = Auth::user();
        if ($user->isTeacher() && $user->getAssignedClass() !== $journal->kelas) {
            abort(403, 'Anda tidak memiliki hak untuk mengubah data kelas lain.');
        }

        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'tanggal' => ['required', 'date'],
            'perilaku_kejadian' => ['required', 'string'],
            'kategori' => ['required', 'in:A,S,D,P'],
            'identifikasi_masalah' => ['nullable', 'string'],
            'pendekatan_wali_kelas' => ['nullable', 'string'],
            'komitmen_siswa' => ['nullable', 'string'],
            'tindak_lanjut' => ['nullable', 'string'],
        ]);

        $journal->update($validated);

        $month = Carbon::parse($validated['tanggal'])->format('Y-m');

        return redirect()->route('guidance-journal.index', [
            'kelas' => $journal->kelas,
            'bulan' => $month,
        ])->with('success', 'Data jurnal observasi siswa berhasil diperbarui.');
    }

    /**
     * Hapus entri jurnal observasi.
     */
    public function destroy(StudentGuidanceJournal $journal)
    {
        $user = Auth::user();
        if ($user->isTeacher() && $user->getAssignedClass() !== $journal->kelas) {
            abort(403, 'Akses ditolak.');
        }

        $kelas = $journal->kelas;
        $month = Carbon::parse($journal->tanggal)->format('Y-m');

        $journal->delete();

        return redirect()->route('guidance-journal.index', [
            'kelas' => $kelas,
            'bulan' => $month,
        ])->with('success', 'Entri jurnal observasi berhasil dihapus.');
    }

    /**
     * Simpan Catatan Umum Wali Kelas & Rekap Singkat Bulanan (Bagian C & D).
     */
    public function saveRecap(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'kelas' => ['required', 'string'],
            'bulan' => ['required', 'string'],
            'kondisi_umum_kelas' => ['nullable', 'string'],
            'siswa_perhatian_khusus' => ['nullable', 'string'],
            'tindak_lanjut_ortu_guru' => ['nullable', 'string'],
            'rekomendasi_berikutnya' => ['nullable', 'string'],
            'ket_akademik' => ['nullable', 'string'],
            'ket_sosial_emosional' => ['nullable', 'string'],
            'ket_kedisiplinan' => ['nullable', 'string'],
            'ket_potensi_prestasi' => ['nullable', 'string'],
        ]);

        if ($user->isTeacher() && $user->getAssignedClass() !== $validated['kelas']) {
            abort(403, 'Akses ditolak.');
        }

        StudentGuidanceMonthlyRecap::updateOrCreate(
            [
                'kelas' => $validated['kelas'],
                'bulan' => $validated['bulan'],
            ],
            array_merge($validated, ['teacher_id' => $user->id])
        );

        return redirect()->route('guidance-journal.index', [
            'kelas' => $validated['kelas'],
            'bulan' => $validated['bulan'],
        ])->with('success', 'Catatan umum kelas dan keterangan rekap bulanan berhasil disimpan.');
    }

    /**
     * Cetak Lembar Buku Bimbingan Siswa Format Resmi A4.
     */
    public function print(Request $request)
    {
        $user = Auth::user();
        $selectedClass = $request->input('kelas', $user->isTeacher() ? $user->getAssignedClass() : '1');
        $selectedMonth = $request->input('bulan', now()->format('Y-m'));

        $journals = StudentGuidanceJournal::with(['student', 'teacher'])
            ->where('kelas', $selectedClass)
            ->where('tanggal', 'like', "{$selectedMonth}%")
            ->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $monthlyRecap = StudentGuidanceMonthlyRecap::where('kelas', $selectedClass)
            ->where('bulan', $selectedMonth)
            ->first();

        // Hitung total kasus
        $countA = $journals->where('kategori', 'A')->count();
        $countS = $journals->where('kategori', 'S')->count();
        $countD = $journals->where('kategori', 'D')->count();
        $countP = $journals->where('kategori', 'P')->count();

        // Ambil nama Wali Kelas
        $waliKelas = User::where('assigned_class', $selectedClass)
            ->where('role', 'teacher')
            ->first() ?? $user;

        // Tanggal Kupang
        $dateCarbon = Carbon::parse($selectedMonth . '-01')->endOfMonth();
        $tanggalCetak = 'Kupang, ' . $dateCarbon->isoFormat('D MMMM Y');

        return view('teachers.guidance.print', compact(
            'journals',
            'monthlyRecap',
            'selectedClass',
            'selectedMonth',
            'countA',
            'countS',
            'countD',
            'countP',
            'waliKelas',
            'tanggalCetak'
        ));
    }
}
