<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentDailyNote;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentDailyNoteController extends Controller
{
    /**
     * Display daily notes management for Teachers.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user || (!$user->isTeacher() && !$user->isAdmin())) {
            abort(403, 'Akses khusus Guru / Wali Kelas.');
        }

        $assignedClass = $user->getAssignedClass();
        $selectedClass = $request->input('kelas', $assignedClass);

        $query = StudentDailyNote::with(['student', 'teacher'])
            ->latest('tanggal')
            ->latest('id');

        // Restrict to teacher's class if assigned, or by requested class filter
        if ($user->isTeacher() && $assignedClass) {
            $query->whereHas('student', fn($q) => $q->where('kelas', $assignedClass));
        } elseif ($selectedClass) {
            $query->whereHas('student', fn($q) => $q->where('kelas', $selectedClass));
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('tanggal')) {
            $query->where('tanggal', $request->tanggal);
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('catatan', 'like', "%{$search}%")
                  ->orWhere('pesan_untuk_orangtua', 'like', "%{$search}%")
                  ->orWhereHas('student', fn($sq) => $sq->where('nama', 'like', "%{$search}%"));
            });
        }

        $notes = $query->paginate(10)->withQueryString();

        // Students selectable for note creation
        $studentsQuery = Student::aktif();
        if ($user->isTeacher() && $assignedClass) {
            $studentsQuery->where('kelas', $assignedClass);
        } elseif ($selectedClass) {
            $studentsQuery->where('kelas', $selectedClass);
        }
        $students = $studentsQuery->orderBy('nama')->get();

        $classes = Student::getAllClasses();
        $categories = StudentDailyNote::KATEGORI_OPTIONS;

        // Statistics for the teacher
        $today = now()->format('Y-m-d');
        $statQuery = StudentDailyNote::query();
        if ($user->isTeacher() && $assignedClass) {
            $statQuery->whereHas('student', fn($q) => $q->where('kelas', $assignedClass));
        }

        $todayNotesCount = (clone $statQuery)->where('tanggal', $today)->count();
        $todayStudentsCount = (clone $statQuery)->where('tanggal', $today)->distinct('student_id')->count('student_id');
        $monthNotesCount = (clone $statQuery)->whereMonth('tanggal', now()->month)->whereYear('tanggal', now()->year)->count();
        $readCount = (clone $statQuery)->where('is_read', true)->count();

        return view('teachers.daily_notes.index', compact(
            'notes',
            'students',
            'classes',
            'categories',
            'assignedClass',
            'selectedClass',
            'todayNotesCount',
            'todayStudentsCount',
            'monthNotesCount',
            'readCount'
        ));
    }

    /**
     * Store new daily note for a student (Teacher only).
     */
    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user || (!$user->isTeacher() && !$user->isAdmin())) {
            abort(403, 'Akses khusus Guru / Wali Kelas.');
        }

        $validated = $request->validate([
            'student_id'           => ['required', 'exists:students,id'],
            'tanggal'              => ['required', 'date'],
            'kategori'             => ['required', 'string', 'max:50'],
            'judul'                => ['required', 'string', 'max:150'],
            'catatan'              => ['required', 'string'],
            'pesan_untuk_orangtua' => ['nullable', 'string', 'max:1000'],
        ]);

        $student = Student::findOrFail($validated['student_id']);

        // Check if teacher is assigned to this student's class
        $assignedClass = $user->getAssignedClass();
        if ($user->isTeacher() && $assignedClass && strtolower($student->kelas) !== strtolower($assignedClass)) {
            return redirect()->back()
                ->withErrors(['student_id' => "Anda hanya dapat memberikan catatan untuk siswa {$assignedClass}."])
                ->withInput();
        }

        $note = StudentDailyNote::create([
            'student_id'           => $student->id,
            'teacher_id'           => $user->id,
            'tanggal'              => $validated['tanggal'],
            'kategori'             => $validated['kategori'],
            'judul'                => $validated['judul'],
            'catatan'              => $validated['catatan'],
            'pesan_untuk_orangtua' => $validated['pesan_untuk_orangtua'] ?? null,
            'is_read'              => false,
            'read_at'              => null,
        ]);

        return redirect()->back()->with('success', "Catatan harian untuk {$student->nama} berhasil disimpan dan otomatis tampil di portal Orang Tua!");
    }

    /**
     * Update an existing daily note.
     */
    public function update(Request $request, StudentDailyNote $dailyNote)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user || (!$user->isTeacher() && !$user->isAdmin())) {
            abort(403, 'Akses khusus Guru / Wali Kelas.');
        }

        // Authorize: creator teacher or admin
        if (!$user->isAdmin() && $dailyNote->teacher_id !== $user->id) {
            abort(403, 'Anda hanya dapat mengedit catatan yang Anda buat sendiri.');
        }

        $validated = $request->validate([
            'tanggal'              => ['required', 'date'],
            'kategori'             => ['required', 'string', 'max:50'],
            'judul'                => ['required', 'string', 'max:150'],
            'catatan'              => ['required', 'string'],
            'pesan_untuk_orangtua' => ['nullable', 'string', 'max:1000'],
        ]);

        $dailyNote->update($validated);

        return redirect()->back()->with('success', "Catatan harian berhasil diperbarui!");
    }

    /**
     * Delete a daily note.
     */
    public function destroy(StudentDailyNote $dailyNote)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user || (!$user->isTeacher() && !$user->isAdmin())) {
            abort(403, 'Akses khusus Guru / Wali Kelas.');
        }

        if (!$user->isAdmin() && $dailyNote->teacher_id !== $user->id) {
            abort(403, 'Anda hanya dapat menghapus catatan yang Anda buat sendiri.');
        }

        $studentName = $dailyNote->student?->nama ?? 'Siswa';
        $dailyNote->delete();

        return redirect()->back()->with('success', "Catatan harian untuk {$studentName} berhasil dihapus.");
    }

    /**
     * Display all daily notes for the Parent Portal.
     */
    public function parentIndex(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user || !$user->isParent() || !$user->student) {
            abort(403, 'Akses khusus akun Orang Tua yang terhubung dengan data siswa.');
        }

        $student = $user->student;

        // Auto-mark any unread daily notes as read
        StudentDailyNote::where('student_id', $student->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        $query = StudentDailyNote::with('teacher')
            ->where('student_id', $student->id)
            ->latest('tanggal')
            ->latest('id');

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('tanggal')) {
            $query->where('tanggal', $request->tanggal);
        }

        if ($request->filled('bulan')) {
            $month = Carbon::parse($request->bulan . '-01');
            $query->whereMonth('tanggal', $month->month)->whereYear('tanggal', $month->year);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('catatan', 'like', "%{$search}%")
                  ->orWhere('pesan_untuk_orangtua', 'like', "%{$search}%");
            });
        }

        $notes = $query->paginate(8)->withQueryString();
        $categories = StudentDailyNote::KATEGORI_OPTIONS;

        // Note summary statistics for parent
        $totalNotes = StudentDailyNote::where('student_id', $student->id)->count();
        $todayNote = StudentDailyNote::where('student_id', $student->id)->where('tanggal', now()->format('Y-m-d'))->first();
        $categoriesCount = StudentDailyNote::where('student_id', $student->id)
            ->selectRaw('kategori, count(*) as count')
            ->groupBy('kategori')
            ->pluck('count', 'kategori');

        return view('parent.daily_notes.index', compact(
            'student',
            'notes',
            'categories',
            'totalNotes',
            'todayNote',
            'categoriesCount'
        ));
    }
}
