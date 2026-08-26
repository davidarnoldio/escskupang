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
        foreach (Student::OFFICIAL_CLASSES as $class) {
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

        $officialClasses = Student::OFFICIAL_CLASSES;
        return view('teachers.create', compact('officialClasses'));
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
            'assigned_class' => ['nullable', 'string', Rule::in(Student::OFFICIAL_CLASSES)],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'guru',
            'assigned_class' => $validated['assigned_class'] ?? null,
        ]);

        return redirect()->route('teachers.index')->with('success', 'Akun Guru / Wali Kelas berhasil ditambahkan.');
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

        $officialClasses = Student::OFFICIAL_CLASSES;
        return view('teachers.edit', compact('teacher', 'officialClasses'));
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
            'assigned_class' => ['nullable', 'string', Rule::in(Student::OFFICIAL_CLASSES)],
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'assigned_class' => $validated['assigned_class'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $teacher->update($data);

        return redirect()->route('teachers.index')->with('success', 'Data Guru / Wali Kelas berhasil diperbarui.');
    }

    /**
     * Remove the specified teacher from storage.
     */
    public function destroy(User $teacher)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if (!$user->isAdmin() && !session()->has('impersonated_by')) {
            abort(403, 'Akses khusus Administrator.');
        }

        $isCurrentlyImpersonated = (auth()->id() === $teacher->id);
        $adminId = session('impersonated_by');

        $teacher->delete();

        if ($isCurrentlyImpersonated && $adminId) {
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
