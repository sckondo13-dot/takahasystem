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
});

require __DIR__ . '/auth.php';
