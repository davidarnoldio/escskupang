<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlumniController extends Controller
{
    /**
     * Display alumni listing with statistics and filters.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Akses data alumni khusus Administrator.');
        }

        $search = $request->input('search');
        $tahunLulus = $request->input('tahun_lulus');
        $kelas = $request->input('kelas');

        $query = Student::alumni();

        if ($tahunLulus) {
            $query->where('tahun_lulus', $tahunLulus);
        }

        if ($kelas) {
            $query->where('kelas', $kelas);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('no_ijazah', 'like', "%{$search}%")
                  ->orWhere('sekolah_lanjutan', 'like', "%{$search}%");
            });
        }

        $alumni = $query->orderBy('tahun_lulus', 'desc')
                        ->orderBy('nama', 'asc')
                        ->paginate(15)
                        ->withQueryString();

        // Statistics
        $totalAlumni = Student::alumni()->count();
        $totalLaki = Student::alumni()->whereIn('jenis_kelamin', ['L', 'Laki-laki'])->count();
        $totalPerempuan = Student::alumni()->whereIn('jenis_kelamin', ['P', 'Perempuan'])->count();

        // Distinct list of graduation years
        $angkatanList = Student::alumni()
            ->whereNotNull('tahun_lulus')
            ->distinct()
            ->orderBy('tahun_lulus', 'desc')
            ->pluck('tahun_lulus');

        $classList = Student::OFFICIAL_CLASSES;

        return view('alumni.index', compact(
            'alumni',
            'totalAlumni',
            'totalLaki',
            'totalPerempuan',
            'angkatanList',
            'classList',
            'search',
            'tahunLulus',
            'kelas'
        ));
    }

    /**
     * Graduate a single student to alumni.
     */
    public function graduate(Request $request, Student $student)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Hanya Administrator yang berwenang meluluskan siswa.');
        }

        $validated = $request->validate([
            'tahun_lulus' => 'required|string|max:20',
            'tanggal_lulus' => 'nullable|date',
            'no_ijazah' => 'nullable|string|max:100',
            'sekolah_lanjutan' => 'nullable|string|max:150',
            'catatan_kelulusan' => 'nullable|string|max:500',
        ]);

        $student->update([
            'status' => 'lulus',
            'tahun_lulus' => $validated['tahun_lulus'],
            'tanggal_lulus' => $validated['tanggal_lulus'] ?? now()->toDateString(),
            'no_ijazah' => $validated['no_ijazah'] ?? null,
            'sekolah_lanjutan' => $validated['sekolah_lanjutan'] ?? null,
            'catatan_kelulusan' => $validated['catatan_kelulusan'] ?? null,
        ]);

        return redirect()->back()->with('success', "Siswa {$student->nama} berhasil diluluskan dan dipindahkan ke Data Alumni.");
    }

    /**
     * Bulk graduate students in a class.
     */
    public function bulkGraduate(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Hanya Administrator yang berwenang meluluskan siswa.');
        }

        $validated = $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
            'tahun_lulus' => 'required|string|max:20',
            'tanggal_lulus' => 'nullable|date',
            'catatan_kelulusan' => 'nullable|string|max:500',
        ], [
            'student_ids.required' => 'Pilih minimal satu siswa yang akan diluluskan.',
            'tahun_lulus.required' => 'Tahun kelulusan wajib diisi.',
        ]);

        $count = count($validated['student_ids']);

        Student::whereIn('id', $validated['student_ids'])->update([
            'status' => 'lulus',
            'tahun_lulus' => $validated['tahun_lulus'],
            'tanggal_lulus' => $validated['tanggal_lulus'] ?? now()->toDateString(),
            'catatan_kelulusan' => $validated['catatan_kelulusan'] ?? null,
        ]);

        return redirect()->route('alumni.index', ['tahun_lulus' => $validated['tahun_lulus']])
            ->with('success', "Berhasil meluluskan {$count} siswa dan memindahkannya ke Data Alumni.");
    }

    /**
     * Revert alumni status back to active student.
     */
    public function revert(Student $student)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Hanya Administrator yang berwenang mengembalikan status alumni.');
        }

        $student->update([
            'status' => 'aktif',
        ]);

        return redirect()->back()->with('success', "Status kelulusan {$student->nama} berhasil dibatalkan. Siswa telah kembali ke Data Siswa aktif.");
    }

    /**
     * Update alumni graduation details (e.g. diploma number, next school).
     */
    public function update(Request $request, Student $student)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Hanya Administrator yang berwenang mengubah data alumni.');
        }

        $validated = $request->validate([
            'tahun_lulus' => 'required|string|max:20',
            'tanggal_lulus' => 'nullable|date',
            'no_ijazah' => 'nullable|string|max:100',
            'sekolah_lanjutan' => 'nullable|string|max:150',
            'catatan_kelulusan' => 'nullable|string|max:500',
        ]);

        $student->update($validated);

        return redirect()->back()->with('success', "Data kelulusan {$student->nama} berhasil diperbarui.");
    }

    /**
     * Print official alumni book recap (A4 Landscape Print View).
     */
    public function printRekap(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Akses cetak rekap alumni khusus Administrator.');
        }

        $tahunLulus = $request->input('tahun_lulus');
        $kelas = $request->input('kelas');

        $query = Student::alumni();

        if ($tahunLulus) {
            $query->where('tahun_lulus', $tahunLulus);
        }

        if ($kelas) {
            $query->where('kelas', $kelas);
        }

        $alumni = $query->orderBy('tahun_lulus', 'desc')
                        ->orderBy('nama', 'asc')
                        ->get();

        $sekolahNama = 'EXCELLENT SPIRIT CHRISTIAN SCHOOL (NTO) KUPANG';
        $tanggalCetak = now()->translatedFormat('d F Y');

        return view('alumni.print_rekap', compact(
            'alumni',
            'tahunLulus',
            'kelas',
            'sekolahNama',
            'tanggalCetak'
        ));
    }

    /**
     * Print individual Surat Keterangan Lulus (SKL) in A4 Portrait.
     */
    public function printSkl(Student $student)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Akses cetak SKL khusus Administrator.');
        }

        if ($student->status !== 'lulus') {
            return redirect()->back()->with('error', 'Surat Keterangan Lulus hanya dapat dicetak untuk siswa yang sudah berstatus alumni.');
        }

        $tanggalCetak = now()->translatedFormat('d F Y');
        $tahunAjaran = $student->tahun_lulus ?? (date('Y') . '/' . (date('Y') + 1));

        return view('alumni.print_skl', compact(
            'student',
            'tanggalCetak',
            'tahunAjaran'
        ));
    }
}
