<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ScannerController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

// ============================
// Public Routes (No Auth)
// ============================

// Landing Page
Route::get('/', function () {
    return view('pages.landing');
})->name('home');

// Login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// ============================
// Protected Routes (Auth Required)
// ============================

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Scanner QR
    Route::get('/scanner', [ScannerController::class, 'index'])->name('scanner');
    Route::post('/api/scan', [ScannerController::class, 'scan'])->name('api.scan');
    Route::post('/api/scan-out', [ScannerController::class, 'scanOut'])->name('api.scan_out');

    // Data Siswa (CRUD)
    Route::post('/classes', [StudentController::class, 'storeClass'])->name('classes.store');
    Route::post('/students/import', [StudentController::class, 'import'])->name('students.import');
    Route::get('/students/template', [StudentController::class, 'downloadTemplate'])->name('students.template');
    Route::get('/students/{student}/qr', [StudentController::class, 'downloadQr'])->name('students.qr');
    Route::resource('students', StudentController::class)->except(['create', 'show', 'edit']);

    // Rekap Absensi
    Route::get('/attendances', [\App\Http\Controllers\AttendanceController::class, 'index'])->name('attendances.index');
    Route::post('/attendances/status', [\App\Http\Controllers\AttendanceController::class, 'updateStatus'])->name('attendances.update_status');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
