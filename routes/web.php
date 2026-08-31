<?php

use App\Http\Controllers\AdminPasswordController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImpersonateController;
use App\Http\Controllers\ParentController;
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

require __DIR__.'/auth.php';
