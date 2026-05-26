<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MonitorController;
use App\Http\Controllers\ScannerController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ActivityLogController;
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

// Web Scanner
Route::get('/scanner', [ScannerController::class, 'index'])->name('scanner');
Route::post('/api/launch-scanner', [ScannerController::class, 'launchScanner'])->name('api.launch_scanner');

// ============================
// Scanner API (No Session Auth, Requires API Key)
// ============================
Route::post('/api/scan', [ScannerController::class, 'scan'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class])
    ->name('api.scan');
Route::post('/api/scan-out', [ScannerController::class, 'scanOut'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class])
    ->name('api.scan_out');

// ============================
// Protected Routes (Auth Required)
// ============================

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Live Monitor
    Route::get('/monitor', [MonitorController::class, 'index'])->name('monitor');
    Route::get('/api/monitor/latest', [MonitorController::class, 'fetchLatest'])->name('api.monitor.latest');
    
    // Log Aktivitas
    Route::get('/logs', [ActivityLogController::class, 'index'])->name('logs.index');

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
