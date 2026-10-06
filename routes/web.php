<?php

use App\Http\Controllers\AdminPasswordController;
use App\Http\Controllers\AlumniController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeworkController;
use App\Http\Controllers\ImpersonateController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QRController;
use App\Http\Controllers\SchoolAnnouncementController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentDailyNoteController;
use App\Http\Controllers\TeacherAttendanceController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Impersonate Teacher & Return Routes
    Route::post('/impersonate/leave', [ImpersonateController::class, 'leave'])->name('impersonate.leave');
    Route::post('/impersonate/{teacher}', [ImpersonateController::class, 'switch'])->name('impersonate.switch');

    // Admin Password Management & Requests
    Route::get('/admin/password-requests', [AdminPasswordController::class, 'index'])->name('admin.password-requests.index');
    Route::post('/admin/reset-password/{user}', [AdminPasswordController::class, 'reset'])->name('admin.reset-password');
    Route::post('/admin/password-requests/{resetRequest}/resolve', [AdminPasswordController::class, 'resolveRequest'])->name('admin.password-requests.resolve');

    // Parent Portal
    Route::get('/parent/dashboard', [ParentController::class, 'dashboard'])->name('parent.dashboard');
    Route::get('/parent/announcements', [SchoolAnnouncementController::class, 'parentIndex'])->name('parent.announcements');
    Route::get('/parent/daily-notes', [StudentDailyNoteController::class, 'parentIndex'])->name('parent.daily-notes');
    Route::post('/parent/upload-letter', [ParentController::class, 'uploadLetter'])->name('parent.upload-letter');
    Route::post('/parent/update-account', [ParentController::class, 'updateAccount'])->name('parent.update-account');

    // School Announcements & PDF Circular Broadcast (Admin & Shared Download)
    Route::get('/school-announcements', [SchoolAnnouncementController::class, 'index'])->name('school-announcements.index');
    Route::post('/school-announcements', [SchoolAnnouncementController::class, 'store'])->name('school-announcements.store');
    Route::delete('/school-announcements/{announcement}', [SchoolAnnouncementController::class, 'destroy'])->name('school-announcements.destroy');
    Route::get('/school-announcements/{announcement}/download', [SchoolAnnouncementController::class, 'download'])->name('school-announcements.download');

    // Student Daily Notes (Buku Penghubung Guru untuk Siswa)
    Route::get('/daily-notes', [StudentDailyNoteController::class, 'index'])->name('daily-notes.index');
    Route::post('/daily-notes', [StudentDailyNoteController::class, 'store'])->name('daily-notes.store');
    Route::put('/daily-notes/{dailyNote}', [StudentDailyNoteController::class, 'update'])->name('daily-notes.update');
    Route::delete('/daily-notes/{dailyNote}', [StudentDailyNoteController::class, 'destroy'])->name('daily-notes.destroy');

    // Payments Module (Admin & Parent)
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::post('/payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
    Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
    Route::get('/parent/payments', [PaymentController::class, 'parentIndex'])->name('parent.payments');
    Route::post('/parent/payments/{payment}/upload-proof', [PaymentController::class, 'uploadProof'])->name('parent.upload-proof');

    // Homework Module (Teacher & Parent)
    Route::get('/homeworks', [HomeworkController::class, 'index'])->name('homeworks.index');
    Route::post('/homeworks', [HomeworkController::class, 'store'])->name('homeworks.store');
    Route::get('/homeworks/{homework}/submissions', [HomeworkController::class, 'submissions'])->name('homeworks.submissions');
    Route::get('/homeworks/{homework}/print-recap', [HomeworkController::class, 'printSubmissions'])->name('homeworks.print-recap');
    Route::post('/homework-submissions/{submission}/grade', [HomeworkController::class, 'gradeSubmission'])->name('homeworks.grade');
    Route::delete('/homework-submissions/{submission}', [HomeworkController::class, 'destroySubmission'])->name('homeworks.destroy-submission');
    Route::delete('/homeworks/{homework}', [HomeworkController::class, 'destroy'])->name('homeworks.destroy');
    Route::get('/parent/homeworks', [HomeworkController::class, 'parentIndex'])->name('parent.homeworks');
    Route::post('/parent/homeworks/{homework}/submit', [HomeworkController::class, 'submitHomework'])->name('parent.submit-homework');

    // Student CRUD & QR Card
    Route::post('/students/bulk-graduate', [AlumniController::class, 'bulkGraduate'])->name('students.bulk-graduate');
    Route::post('/students/{student}/graduate', [AlumniController::class, 'graduate'])->name('students.graduate');
    Route::resource('students', StudentController::class);
    Route::get('/students/{student}/qr-card', [QRController::class, 'card'])->name('students.qr-card');

    // Alumni Management & Print Recap / SKL
    Route::get('/alumni', [AlumniController::class, 'index'])->name('alumni.index');
    Route::get('/alumni/print-rekap', [AlumniController::class, 'printRekap'])->name('alumni.print-rekap');
    Route::get('/alumni/{student}/print-skl', [AlumniController::class, 'printSkl'])->name('alumni.print-skl');
    Route::post('/alumni/{student}/revert', [AlumniController::class, 'revert'])->name('alumni.revert');
    Route::put('/alumni/{student}', [AlumniController::class, 'update'])->name('alumni.update');

    // Teacher Management (Admin Only) & Teacher QR Card
    Route::resource('teachers', TeacherController::class);
    Route::get('/teachers/{teacher}/qr-card', [QRController::class, 'teacherCard'])->name('teachers.qr-card');

    // Teacher Daily Attendance & Monthly Recap (Admin Only)
    Route::get('/teacher-attendances', [TeacherAttendanceController::class, 'index'])->name('teacher-attendances.index');
    Route::post('/teacher-attendances/status', [TeacherAttendanceController::class, 'updateStatus'])->name('teacher-attendances.update-status');
    Route::get('/teacher-attendances/rekap', [TeacherAttendanceController::class, 'rekap'])->name('teacher-attendances.rekap');
    Route::get('/teacher-attendances/print-rekap', [TeacherAttendanceController::class, 'printRekap'])->name('teacher-attendances.print-rekap');

    // QR Code Scanner & Process
    Route::get('/scan-qr', [QRController::class, 'index'])->name('qr.scan');
    Route::match(['GET', 'POST'], '/scan-qr/process', [QRController::class, 'process'])->name('qr.process');

    // Attendance CRUD, Rekap & Print
    Route::get('/attendances/print-rekap', [AttendanceController::class, 'printRekap'])->name('attendances.print-rekap');
    Route::get('/attendances/letters', [AttendanceController::class, 'letters'])->name('attendances.letters');
    Route::post('/attendances/{attendance}/verify-letter', [AttendanceController::class, 'verifyLetter'])->name('attendances.verify-letter');
    Route::delete('/attendances/{attendance}/letter', [AttendanceController::class, 'destroyLetter'])->name('attendances.destroy-letter');
    Route::resource('attendances', AttendanceController::class);
    Route::get('/rekap-absensi', [AttendanceController::class, 'rekap'])->name('attendances.rekap');

    // School Operating Hours Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

    // Utility: Clear Cache for Admin (Bila route/view di hosting belum ter-refresh)
    Route::get('/admin/clear-cache', function () {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403);
        }
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        return redirect()->route('dashboard')->with('success', 'Cache route, config, dan views berhasil dibersihkan!');
    })->name('admin.clear-cache');
});

// Direct Storage Serving fallback (ensures static files, proof photos, and letters always load reliably)
Route::get('/storage/{path}', function ($path) {
    // Prevent directory traversal and null-byte injection attacks
    if (str_contains($path, '..') || str_contains($path, "\0")) {
        abort(404);
    }

    $cleanPath = ltrim(str_replace(['public/', 'storage/'], '', $path), '/');
    if (empty($cleanPath) || str_starts_with(basename($cleanPath), '.')) {
        abort(404);
    }

    $storagePublicBase = realpath(storage_path('app/public'));
    $uploadsBase = realpath(public_path('uploads'));

    $fullStoragePath = storage_path('app/public/' . $cleanPath);
    $realStoragePath = realpath($fullStoragePath);

    if ($realStoragePath && $storagePublicBase && str_starts_with($realStoragePath, $storagePublicBase) && is_file($realStoragePath)) {
        return response()->file($realStoragePath);
    }

    $fullUploadsPath = public_path('uploads/' . $cleanPath);
    $realUploadsPath = realpath($fullUploadsPath);

    if ($realUploadsPath && $uploadsBase && str_starts_with($realUploadsPath, $uploadsBase) && is_file($realUploadsPath)) {
        return response()->file($realUploadsPath);
    }

    abort(404);
})->where('path', '.*')->name('storage.fallback');

require __DIR__.'/auth.php';
