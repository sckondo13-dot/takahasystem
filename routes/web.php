<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\SubcontractorController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AllowanceController;
use App\Http\Controllers\AttendanceTimeController;
use App\Http\Controllers\SiteReportController;

Route::redirect('/', '/login');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('employees', EmployeeController::class);

    Route::resource('clients', ClientController::class);

    Route::resource('subcontractors', SubcontractorController::class);

    Route::resource('sites', SiteController::class);

    Route::resource('daily-reports', DailyReportController::class);

    Route::get('/attendance', [AttendanceController::class, 'index'])
        ->name('attendance.index');

    Route::resource('allowances', AllowanceController::class);
    Route::resource(
        'attendance-times',
        AttendanceTimeController::class
    );

    Route::resource('site-reports', SiteReportController::class)
        ->only(['index']);

    Route::get(
        '/site-reports/monthly',
        [SiteReportController::class, 'monthly']
    )->name('site-reports.monthly');

    Route::get(
        '/site-reports/monthly/pdf',
        [SiteReportController::class, 'monthlyPdf']
    )->name('site-reports.monthly.pdf');

    Route::get(
        '/site-reports/monthly/download',
        [SiteReportController::class, 'monthlyDownload']
    )->name('site-reports.monthly.download');

    Route::get('/attendance/pdf', [
        AttendanceController::class,
        'pdf'
    ])->name('attendance.pdf');

    Route::get('/attendance/pdf/download', [
        AttendanceController::class,
        'downloadPdf'
    ])->name('attendance.pdf.download');

    Route::get(
        '/site-reports/niseko',
        [SiteReportController::class, 'niseko']
    )->name('site-reports.niseko');

    Route::get(
        '/site-reports/niseko/pdf',
        [SiteReportController::class, 'nisekoPdf']
    )->name('site-reports.niseko.pdf');

    Route::get(
        '/site-reports/niseko/download',
        [SiteReportController::class, 'nisekoDownload']
    )->name('site-reports.niseko.download');
});

require __DIR__ . '/auth.php';
