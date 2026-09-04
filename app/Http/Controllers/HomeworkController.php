<?php

namespace App\Http\Controllers;

use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HomeworkController extends Controller
{
    /**
     * Display homework list for Teachers.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user->isTeacher() && !$user->isAdmin()) {
            abort(403, 'Akses khusus Guru / Wali Kelas.');
        }

        $assignedClass = $user->getAssignedClass();

        $query = Homework::with(['teacher', 'submissions.student'])->latest();

        if ($user->isTeacher() && $assignedClass) {
            $query->where('kelas', $assignedClass);
        } elseif ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

        if ($request->filled('mata_pelajaran')) {
            $query->where('mata_pelajaran', 'like', "%{$request->mata_pelajaran}%");
        }

        $homeworks = $query->paginate(10)->withQueryString();
        $classes = Student::OFFICIAL_CLASSES;
        $studentsInClass = Student::when($assignedClass, fn($q) => $q->where('kelas', $assignedClass))->orderBy('nama')->get();

        return view('homeworks.teacher-index', compact('homeworks', 'assignedClass', 'classes', 'studentsInClass'));
    }

    /**
     * Store new Homework announcement by Teacher.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isTeacher() && !$user->isAdmin()) {
            abort(403, 'Akses khusus Guru / Wali Kelas.');
        }

        $assignedClass = $user->getAssignedClass();
        $defaultClass = $assignedClass ?? Student::OFFICIAL_CLASSES[0];

        $validated = $request->validate([
            'target_type' => 'required|in:all,student',
            'student_id' => 'nullable|required_if:target_type,student|exists:students,id',
            'kelas' => 'required|in:' . implode(',', Student::OFFICIAL_CLASSES),
            'mata_pelajaran' => 'required|string|max:100',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'deadline' => 'required|date',
            'lampiran_guru' => 'nullable|file|mimes:jpeg,jpg,png,pdf,doc,docx|max:3072',
        ]);

        if ($user->isTeacher() && $assignedClass && strtolower($validated['kelas']) !== strtolower($assignedClass)) {
            return redirect()->back()->with('error', "Anda hanya dapat membuat PR untuk kelas {$assignedClass}.");
        }

        $lampiranPath = null;
        if ($request->hasFile('lampiran_guru')) {
            $lampiranPath = $request->file('lampiran_guru')->store('pr_lampiran', 'public');
        }

        $targetStudentId = $validated['target_type'] === 'student' ? $validated['student_id'] : null;

        Homework::create([
            'teacher_id' => $user->id,
            'student_id' => $targetStudentId,
            'kelas' => $validated['kelas'],
            'mata_pelajaran' => $validated['mata_pelajaran'],
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'],
            'deadline' => $validated['deadline'],
            'lampiran_guru' => $lampiranPath,
        ]);

        $targetText = $targetStudentId ? "siswa spesifik" : "seluruh siswa kelas {$validated['kelas']}";
        return redirect()->route('homeworks.index')->with('success', "Pemberitahuan PR baru berhasil dibuat dan dikirim ke {$targetText}.");
    }

    /**
     * View homework submissions and grade recap table for a specific assignment.
     */
    public function submissions(Homework $homework)
    {
        $user = Auth::user();
        $assignedClass = $user->getAssignedClass();

        if ($user->isTeacher() && $assignedClass && strtolower($homework->kelas) !== strtolower($assignedClass)) {
            abort(403, "Anda hanya dapat melihat dan menilai PR kelas {$assignedClass}.");
        }

        $studentsQuery = Student::where('kelas', $homework->kelas);
        if ($homework->student_id) {
            $studentsQuery->where('id', $homework->student_id);
        }
        $students = $studentsQuery->orderBy('nama')->get();
        $submissions = HomeworkSubmission::where('homework_id', $homework->id)->with('student')->get()->keyBy('student_id');

        $gradedSubmissions = $submissions->whereNotNull('nilai');
        $scores = $gradedSubmissions->pluck('nilai');

        $recap = [
            'total_siswa' => $students->count(),
            'sudah_kumpul' => $submissions->count(),
            'belum_kumpul' => max(0, $students->count() - $submissions->count()),
            'sudah_dinilai' => $gradedSubmissions->count(),
            'belum_dinilai' => max(0, $submissions->count() - $gradedSubmissions->count()),
            'rata_rata' => $scores->count() > 0 ? round($scores->avg(), 1) : 0,
            'nilai_tertinggi' => $scores->count() > 0 ? $scores->max() : 0,
            'nilai_terendah' => $scores->count() > 0 ? $scores->min() : 0,
        ];

        return view('homeworks.teacher-submissions', compact('homework', 'students', 'submissions', 'recap'));
    }

    /**
     * Teacher inputs grade and feedback notes for a student submission.
     */
    public function gradeSubmission(Request $request, HomeworkSubmission $submission)
    {
        $user = Auth::user();
        $homework = $submission->homework;
        $assignedClass = $user->getAssignedClass();

        if ($user->isTeacher() && $assignedClass && strtolower($homework->kelas) !== strtolower($assignedClass)) {
            abort(403, "Anda tidak memiliki akses untuk menilai PR kelas {$homework->kelas}.");
        }

        $validated = $request->validate([
            'nilai' => 'required|integer|min:0|max:100',
            'catatan_guru' => 'nullable|string|max:500',
        ]);

        $submission->update([
            'nilai' => $validated['nilai'],
            'catatan_guru' => $validated['catatan_guru'] ?? null,
            'graded_at' => now(),
        ]);

        return redirect()->back()->with('success', "Nilai {$validated['nilai']} berhasil disimpan untuk siswa {$submission->student->nama}.");
    }

    /**
     * Delete homework.
     */
    public function destroy(Homework $homework)
    {
        $user = Auth::user();
        $assignedClass = $user->getAssignedClass();

        if ($user->isTeacher() && $assignedClass && strtolower($homework->kelas) !== strtolower($assignedClass)) {
            abort(403, "Anda tidak memiliki akses untuk menghapus PR kelas {$homework->kelas}.");
        }

        if ($homework->lampiran_guru && Storage::disk('public')->exists($homework->lampiran_guru)) {
            Storage::disk('public')->delete($homework->lampiran_guru);
        }

        foreach ($homework->submissions as $sub) {
            if ($sub->foto_pr && Storage::disk('public')->exists($sub->foto_pr)) {
                Storage::disk('public')->delete($sub->foto_pr);
            }
        }

        $homework->delete();

        return redirect()->route('homeworks.index')->with('success', 'PR berhasil dihapus.');
    }

    /**
     * Display homework list for Parent/Student.
     */
    public function parentIndex()
    {
        $user = Auth::user();
        if (!$user->isParent() || !$user->student_id) {
            return redirect()->route('dashboard')->with('error', 'Akun ini belum terhubung dengan data siswa.');
        }

        $student = $user->student;
        $homeworks = Homework::where('kelas', $student->kelas)
            ->where(function ($q) use ($student) {
                $q->whereNull('student_id')->orWhere('student_id', $student->id);
            })
            ->with(['teacher', 'submissions' => function ($q) use ($student) {
                $q->where('student_id', $student->id);
            }])
            ->latest()
            ->get();

        $stats = [
            'total_pr' => $homeworks->count(),
            'sudah_kumpul' => $homeworks->filter(fn($hw) => $hw->submissions->isNotEmpty())->count(),
            'belum_kumpul' => $homeworks->filter(fn($hw) => $hw->submissions->isEmpty())->count(),
            'sudah_dinilai' => $homeworks->filter(fn($hw) => $hw->submissions->whereNotNull('nilai')->isNotEmpty())->count(),
        ];

        return view('homeworks.parent-homeworks', compact('student', 'homeworks', 'stats'));
    }

    /**
     * Parent/Student uploads photo submission of completed PR.
     */
    public function submitHomework(Request $request, Homework $homework)
    {
        $user = Auth::user();
        if (!$user->isParent() || !$user->student_id) {
            abort(403, 'Anda tidak memiliki akses untuk mengirim PR.');
        }

        $student = $user->student;

        if (strtolower($homework->kelas) !== strtolower($student->kelas)) {
            abort(403, 'PR ini bukan ditujukan untuk kelas siswa Anda.');
        }

        $request->validate([
            'foto_pr' => 'required|image|mimes:jpeg,jpg,png,pdf|max:3072',
            'catatan_siswa' => 'nullable|string|max:500',
        ]);

        $existing = HomeworkSubmission::where('homework_id', $homework->id)
            ->where('student_id', $student->id)
            ->first();

        if ($existing && $existing->foto_pr && Storage::disk('public')->exists($existing->foto_pr)) {
            Storage::disk('public')->delete($existing->foto_pr);
        }

        $fotoPath = $request->file('foto_pr')->store('pr_submissions', 'public');

        HomeworkSubmission::updateOrCreate(
            [
                'homework_id' => $homework->id,
                'student_id' => $student->id,
            ],
            [
                'foto_pr' => $fotoPath,
                'catatan_siswa' => $request->catatan_siswa ?? null,
                'submitted_at' => now(),
            ]
        );

        return redirect()->back()->with('success', 'Foto pengerjaan PR berhasil diunggah!');
    }
}
