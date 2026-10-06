<?php

namespace App\Http\Controllers;

use App\Models\SchoolAnnouncement;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SchoolAnnouncementController extends Controller
{
    /**
     * Admin view: List all school announcements and upload form.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Akses ditolak. Hanya administrator yang dapat mengelola berkas pengumuman.');
        }

        $query = SchoolAnnouncement::with('creator')->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->where('description', 'like', "%{$search}%")
                  ->orWhere('file_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('target_class') && $request->input('target_class') !== 'all') {
            $query->where('target_class', $request->input('target_class'));
        }

        $announcements = $query->paginate(12)->withQueryString();

        // Get unique class list for filter
        $availableClasses = Student::distinct()->pluck('kelas')->filter()->values();

        return view('admin.announcements.index', compact('announcements', 'availableClasses'));
    }

    /**
     * Admin: Store and upload a new PDF announcement.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'target_class' => ['required', 'string', 'max:50'],
            'pdf_file' => ['required', 'file', 'mimes:pdf', 'max:15360'], // 15MB limit
        ], [
            'title.required' => 'Judul surat/pengumuman wajib diisi.',
            'target_class.required' => 'Target kelas penerima wajib dipilih.',
            'pdf_file.required' => 'File dokumen PDF wajib diunggah.',
            'pdf_file.mimes' => 'Format berkas harus berupa dokumen PDF (.pdf).',
            'pdf_file.max' => 'Ukuran berkas PDF maksimal 15 Megabytes (MB).',
        ]);

        $file = $request->file('pdf_file');
        $originalFileName = $file->getClientOriginalName();
        $fileSize = $file->getSize();

        // Store file in public storage disk under announcements directory
        $filePath = $file->store('announcements', 'public');

        SchoolAnnouncement::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'file_path' => $filePath,
            'file_name' => $originalFileName,
            'file_size' => $fileSize,
            'target_class' => $validated['target_class'],
            'created_by' => $user->id,
        ]);

        return redirect()->route('school-announcements.index')->with('success', 'Dokumen PDF surat edaran / pengumuman berhasil dipublikasikan ke seluruh orang tua!');
    }

    /**
     * Admin: Delete an announcement and its associated PDF file.
     */
    public function destroy(SchoolAnnouncement $announcement)
    {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        // Delete physical file from public storage disk if exists
        if ($announcement->file_path && Storage::disk('public')->exists($announcement->file_path)) {
            Storage::disk('public')->delete($announcement->file_path);
        }

        $announcement->delete();

        return redirect()->route('school-announcements.index')->with('success', 'Dokumen PDF pengumuman berhasil dihapus.');
    }

    /**
     * Download or view PDF file safely.
     */
    public function download(SchoolAnnouncement $announcement)
    {
        $user = Auth::user();
        if (!$user) {
            abort(401, 'Silakan login terlebih dahulu.');
        }

        // Parent authorization: ensure target_class is either 'Semua Kelas' or matching student's class
        if ($user->isParent()) {
            $student = $user->student;
            if ($announcement->target_class !== 'Semua Kelas' && (!$student || $student->kelas !== $announcement->target_class)) {
                abort(403, 'Dokumen ini tidak ditujukan untuk kelas siswa Anda.');
            }
        }

        if (!Storage::disk('public')->exists($announcement->file_path)) {
            abort(404, 'File dokumen PDF tidak ditemukan di server.');
        }

        return Storage::disk('public')->download($announcement->file_path, $announcement->file_name);
    }

    /**
     * Parent Portal: View circular letters and PDF announcements.
     */
    public function parentIndex(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isParent()) {
            return redirect()->route('dashboard');
        }

        $student = $user->student;
        $studentClass = $student ? $student->kelas : null;

        $query = SchoolAnnouncement::query()->latest();

        // Scope to "Semua Kelas" and student's current class
        $query->where(function ($q) use ($studentClass) {
            $q->where('target_class', 'Semua Kelas');
            if ($studentClass) {
                $q->orWhere('target_class', $studentClass);
            }
        });

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('file_name', 'like', "%{$search}%");
            });
        }

        $announcements = $query->paginate(10)->withQueryString();

        return view('parent.announcements', compact('announcements', 'student'));
    }
}
