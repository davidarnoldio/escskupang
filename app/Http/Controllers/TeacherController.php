<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{
    /**
     * Display a listing of teachers grouped by official class levels.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if (!$user->isAdmin()) {
            abort(403, 'Akses khusus Administrator.');
        }

        $search = $request->input('search');
        $selectedClass = $request->input('kelas');

        $query = User::whereIn('role', ['guru', 'wali_kelas']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $teachers = $query->orderBy('name')->get();

        // Map teachers to their assigned class
        $teachersByClass = [];
        foreach (Student::getAllClasses() as $class) {
            $teachersByClass[$class] = [];
        }
        $teachersByClass['Unassigned'] = [];

        foreach ($teachers as $teacher) {
            $class = $teacher->getAssignedClass() ?? 'Unassigned';
            $teachersByClass[$class][] = $teacher;
        }

        return view('teachers.index', compact('teachers', 'teachersByClass', 'search', 'selectedClass'));
    }

    /**
     * Show the form for creating a new teacher.
     */
    public function create()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if (!$user->isAdmin()) {
            abort(403, 'Akses khusus Administrator.');
        }

        $officialClasses = Student::getAllClasses();
        $statusKepegawaianOptions = User::STATUS_KEPEGAWAIAN_OPTIONS;
        return view('teachers.create', compact('officialClasses', 'statusKepegawaianOptions'));
    }

    /**
     * Store a newly created teacher in storage.
     */
    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if (!$user->isAdmin()) {
            abort(403, 'Akses khusus Administrator.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
            'assigned_class' => ['nullable', 'string', 'max:100'],
            'nik' => ['nullable', 'string', 'max:30'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'nama_ibu_kandung' => ['nullable', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'rt' => ['nullable', 'string', 'max:10'],
            'rw' => ['nullable', 'string', 'max:10'],
            'dusun' => ['nullable', 'string', 'max:100'],
            'desa_kelurahan' => ['nullable', 'string', 'max:100'],
            'kecamatan' => ['nullable', 'string', 'max:100'],
            'lintang' => ['nullable', 'string', 'max:50'],
            'bujur' => ['nullable', 'string', 'max:50'],
            'kode_pos' => ['nullable', 'string', 'max:20'],
            'status_kepegawaian' => ['nullable', 'string', 'max:100'],
            'niy_nigk' => ['nullable', 'string', 'max:50'],
            'nuptk' => ['nullable', 'string', 'max:50'],
            'sk_pengangkatan' => ['nullable', 'string', 'max:100'],
            'tmt_pengangkatan' => ['nullable', 'date'],
            'lembaga_pengangkat' => ['nullable', 'string', 'max:150'],
            'sumber_gaji' => ['nullable', 'string', 'max:100'],
            'keahlian_laboratorium' => ['nullable', 'string', 'max:150'],
            'mampu_menangani_kebutuhan_khusus' => ['nullable', 'in:Ya,Tidak'],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'alasan_keluar_kerja' => ['nullable', 'string'],
            'jenis_sertifikasi' => ['nullable', 'string', 'max:150'],
            'nomor_sertifikasi' => ['nullable', 'string', 'max:100'],
            'tahun_sertifikasi' => ['nullable', 'string', 'max:10'],
            'bidang_studi_sertifikasi' => ['nullable', 'string', 'max:150'],
            'nrg' => ['nullable', 'string', 'max:50'],
            'nomor_peserta' => ['nullable', 'string', 'max:100'],
        ]);

        $assignedClass = $validated['assigned_class'] ?? null;
        if (!$assignedClass) {
            foreach (Student::getAllClasses() as $class) {
                if (str_contains(strtolower($validated['name']), strtolower($class))) {
                    $assignedClass = $class;
                    break;
                }
            }
        }

        $data = $validated;
        $data['password'] = Hash::make($validated['password']);
        $data['role'] = 'guru';
        $data['assigned_class'] = $assignedClass;

        User::create($data);

        return redirect()->route('teachers.index')->with('success', 'Akun Guru dan Biodata Dapodik berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified teacher.
     */
    public function edit(User $teacher)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if (!$user->isAdmin()) {
            abort(403, 'Akses khusus Administrator.');
        }

        $officialClasses = Student::getAllClasses();
        $statusKepegawaianOptions = User::STATUS_KEPEGAWAIAN_OPTIONS;
        return view('teachers.edit', compact('teacher', 'officialClasses', 'statusKepegawaianOptions'));
    }

    /**
     * Update the specified teacher in storage.
     */
    public function update(Request $request, User $teacher)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if (!$user->isAdmin()) {
            abort(403, 'Akses khusus Administrator.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($teacher->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'assigned_class' => ['nullable', 'string', 'max:100'],
            'nik' => ['nullable', 'string', 'max:30'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'nama_ibu_kandung' => ['nullable', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'rt' => ['nullable', 'string', 'max:10'],
            'rw' => ['nullable', 'string', 'max:10'],
            'dusun' => ['nullable', 'string', 'max:100'],
            'desa_kelurahan' => ['nullable', 'string', 'max:100'],
            'kecamatan' => ['nullable', 'string', 'max:100'],
            'lintang' => ['nullable', 'string', 'max:50'],
            'bujur' => ['nullable', 'string', 'max:50'],
            'kode_pos' => ['nullable', 'string', 'max:20'],
            'status_kepegawaian' => ['nullable', 'string', 'max:100'],
            'niy_nigk' => ['nullable', 'string', 'max:50'],
            'nuptk' => ['nullable', 'string', 'max:50'],
            'sk_pengangkatan' => ['nullable', 'string', 'max:100'],
            'tmt_pengangkatan' => ['nullable', 'date'],
            'lembaga_pengangkat' => ['nullable', 'string', 'max:150'],
            'sumber_gaji' => ['nullable', 'string', 'max:100'],
            'keahlian_laboratorium' => ['nullable', 'string', 'max:150'],
            'mampu_menangani_kebutuhan_khusus' => ['nullable', 'in:Ya,Tidak'],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'alasan_keluar_kerja' => ['nullable', 'string'],
            'jenis_sertifikasi' => ['nullable', 'string', 'max:150'],
            'nomor_sertifikasi' => ['nullable', 'string', 'max:100'],
            'tahun_sertifikasi' => ['nullable', 'string', 'max:10'],
            'bidang_studi_sertifikasi' => ['nullable', 'string', 'max:150'],
            'nrg' => ['nullable', 'string', 'max:50'],
            'nomor_peserta' => ['nullable', 'string', 'max:100'],
        ]);

        $assignedClass = $validated['assigned_class'] ?? null;
        if (!$assignedClass) {
            foreach (Student::getAllClasses() as $class) {
                if (str_contains(strtolower($validated['name']), strtolower($class))) {
                    $assignedClass = $class;
                    break;
                }
            }
        }

        $data = $validated;
        $data['assigned_class'] = $assignedClass;

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        } else {
            unset($data['password']);
        }

        $teacher->update($data);

        return redirect()->route('teachers.index')->with('success', 'Data Guru dan Biodata Dapodik berhasil diperbarui.');
    }

    /**
     * Remove the specified teacher from storage.
     */
    public function destroy(User $teacher)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $adminId = session('impersonated_by');
        $isCurrentlyImpersonated = $adminId !== null;
        $isDeletingSelf = (auth()->id() === $teacher->id);

        // Strict check: only a real admin (not an impersonated teacher) can delete other teachers.
        // Exception: an impersonated teacher can delete their own account (self-delete via admin UI).
        if (!$user->isAdmin() && !($isCurrentlyImpersonated && $isDeletingSelf)) {
            abort(403, 'Akses khusus Administrator.');
        }

        $teacher->delete();

        // If the currently-impersonated teacher deleted themselves, restore admin session
        if ($isDeletingSelf && $isCurrentlyImpersonated) {
            session()->forget('impersonated_by');
            $adminUser = User::find($adminId);
            if ($adminUser) {
                \Illuminate\Support\Facades\Auth::login($adminUser);
            }
            return redirect()->route('teachers.index')->with('success', 'Akun Guru berhasil dihapus dan Anda kembali ke Akun Admin.');
        }

        return redirect()->route('teachers.index')->with('success', 'Akun Guru / Wali Kelas berhasil dihapus.');
    }
}
