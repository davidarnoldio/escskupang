<?php

use App\Http\Controllers\AdminPasswordController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeworkController;
use App\Http\Controllers\ImpersonateController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QRController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StudentController;
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
    Route::post('/parent/upload-letter', [ParentController::class, 'uploadLetter'])->name('parent.upload-letter');
    Route::post('/parent/update-account', [ParentController::class, 'updateAccount'])->name('parent.update-account');

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
    Route::delete('/homeworks/{homework}', [HomeworkController::class, 'destroy'])->name('homeworks.destroy');
    Route::get('/parent/homeworks', [HomeworkController::class, 'parentIndex'])->name('parent.homeworks');
    Route::post('/parent/homeworks/{homework}/submit', [HomeworkController::class, 'submitHomework'])->name('parent.submit-homework');

    // Student CRUD & QR Card
    Route::resource('students', StudentController::class);
    Route::get('/students/{student}/qr-card', [QRController::class, 'card'])->name('students.qr-card');

    // Teacher Management (Admin Only)
    Route::resource('teachers', TeacherController::class);

    // QR Code Scanner & Process
    Route::get('/scan-qr', [QRController::class, 'index'])->name('qr.scan');
    Route::post('/scan-qr/process', [QRController::class, 'process'])->name('qr.process');

    // Attendance CRUD, Rekap & Print
    Route::get('/attendances/print-rekap', [AttendanceController::class, 'printRekap'])->name('attendances.print-rekap');
    Route::get('/attendances/letters', [AttendanceController::class, 'letters'])->name('attendances.letters');
    Route::resource('attendances', AttendanceController::class);
    Route::get('/rekap-absensi', [AttendanceController::class, 'rekap'])->name('attendances.rekap');

    // School Operating Hours Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});

// Direct Storage Serving fallback (ensures static files, proof photos, and letters always load reliably)
Route::get('/storage/{path}', function ($path) {
    $cleanPath = ltrim(str_replace(['public/', 'storage/'], '', $path), '/');
    $fullPath = storage_path('app/public/' . $cleanPath);

    if (file_exists($fullPath) && !is_dir($fullPath)) {
        return response()->file($fullPath);
    }

    if (file_exists(public_path('uploads/' . $cleanPath)) && !is_dir(public_path('uploads/' . $cleanPath))) {
        return response()->file(public_path('uploads/' . $cleanPath));
    }

    abort(404);
})->where('path', '.*')->name('storage.fallback');

require __DIR__.'/auth.php';
