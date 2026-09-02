<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    /**
     * Display a listing of the students.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $assignedClass = $user ? $user->getAssignedClass() : null;

        $search = $request->input('search');
        $kelas = $assignedClass ?? $request->input('kelas');

        $query = Student::query();

        if ($assignedClass) {
            $query->where('kelas', $assignedClass);
        } elseif ($kelas) {
            $query->where('kelas', $kelas);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('nama', 'asc')->paginate(10)->withQueryString();
        $classList = Student::OFFICIAL_CLASSES;

        return view('students.index', compact('students', 'classList', 'search', 'kelas', 'assignedClass'));
    }

    /**
     * Show the form for creating a new student.
     */
    public function create()
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            return redirect()->route('students.index')->with('error', 'Hanya Admin yang berhak menambahkan data siswa.');
        }

        return view('students.create');
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(Request $request)
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            return redirect()->route('students.index')->with('error', 'Hanya Admin yang berhak menambahkan data siswa.');
        }

        $validated = $request->validate([
            'nis' => ['required', 'string', 'max:50', 'unique:students,nis'],
            'nama' => ['required', 'string', 'max:255'],
            'kelas' => ['required', 'string', 'max:50'],
            'jenis_kelamin' => ['required', Rule::in(['L', 'P'])],
            'alamat' => ['nullable', 'string'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'is_abk' => ['nullable', 'boolean'],
        ]);

        $validated['is_abk'] = $request->boolean('is_abk');

        $student = Student::create($validated);

        // Auto-create parent user account with email @student.sch.id and password "password"
        $parentEmail = self::generateParentEmail($student->nama, $student->id);
        \App\Models\User::create([
            'name' => 'Orang Tua (' . $student->nama . ')',
            'email' => $parentEmail,
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'orang_tua',
            'student_id' => $student->id,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('students.index')->with('success', "Data siswa berhasil ditambahkan dan akun Orang Tua ({$parentEmail}) telah dibuat.");
    }

    /**
     * Generate parent email format (nama_depan.nama_belakang@student.sch.id).
     */
    public static function generateParentEmail(string $nama, int $studentId = 0): string
    {
        $words = array_values(array_filter(explode(' ', trim($nama))));
        $firstName = isset($words[0]) ? strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $words[0])) : 'siswa';
        $lastName = isset($words[count($words) - 1]) && count($words) > 1 ? strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $words[count($words) - 1])) : $firstName;

        $baseEmail = "{$firstName}.{$lastName}@student.sch.id";
        $email = $baseEmail;
        $counter = 1;

        while (\App\Models\User::where('email', $email)->where('student_id', '!=', $studentId)->exists()) {
            $counter++;
            $email = "{$firstName}.{$lastName}{$counter}@student.sch.id";
        }

        return $email;
    }

    /**
     * Display the specified student.
     */
    public function show(Student $student)
    {
        $student->load('attendances');
        return view('students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified student.
     */
    public function edit(Student $student)
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            return redirect()->route('students.index')->with('error', 'Hanya Admin yang berhak mengubah data siswa.');
        }

        return view('students.edit', compact('student'));
    }

    /**
     * Update the specified student in storage.
     */
    public function update(Request $request, Student $student)
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            return redirect()->route('students.index')->with('error', 'Hanya Admin yang berhak mengubah data siswa.');
        }

        $validated = $request->validate([
            'nis' => ['required', 'string', 'max:50', Rule::unique('students', 'nis')->ignore($student->id)],
            'nama' => ['required', 'string', 'max:255'],
            'kelas' => ['required', 'string', 'max:50'],
            'jenis_kelamin' => ['required', Rule::in(['L', 'P'])],
            'alamat' => ['nullable', 'string'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'is_abk' => ['nullable', 'boolean'],
        ]);

        $validated['is_abk'] = $request->boolean('is_abk');

        $student->update($validated);

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Remove the specified student from storage.
     */
    public function destroy(Student $student)
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            return redirect()->route('students.index')->with('error', 'Hanya Admin yang berhak menghapus data siswa.');
        }

        $student->delete();

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil dihapus.');
    }
}
