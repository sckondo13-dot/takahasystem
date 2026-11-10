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
use App\Http\Controllers\SubcontractorAttendanceController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MonthlySalesRecalculationController;

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

    Route::get('/clients/{client}/monthly-prices', [ClientController::class, 'monthlyPrices'])
        ->name('clients.monthly-prices');

    Route::post('/clients/{client}/monthly-prices', [ClientController::class, 'updateMonthlyPrices'])
        ->name('clients.monthly-prices.update');

    Route::resource('subcontractors', SubcontractorController::class);

    Route::resource('sites', SiteController::class);

    Route::get('/sites/{site}/monthly-prices', [SiteController::class, 'monthlyPrices'])
        ->name('sites.monthly-prices');

    Route::post('/sites/{site}/monthly-prices', [SiteController::class, 'updateMonthlyPrices'])
        ->name('sites.monthly-prices.update');

    Route::get(
        '/daily-reports/employees-by-date',
        [DailyReportController::class, 'employeesByDate']
    )->name('daily-reports.employees-by-date');

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

    Route::get('/site-reports/truck', [SiteReportController::class, 'truck'])
        ->name('site-reports.truck');

    Route::get(
        '/site-reports/niseko/pdf',
        [SiteReportController::class, 'nisekoPdf']
    )->name('site-reports.niseko.pdf');

    Route::get(
        '/site-reports/niseko/download',
        [SiteReportController::class, 'nisekoDownload']
    )->name('site-reports.niseko.download');

    Route::get('/site-reports/truck/pdf', [SiteReportController::class, 'truckPdf'])
        ->name('site-reports.truck.pdf');

    Route::get('/site-reports/truck/download', [SiteReportController::class, 'truckDownload'])
        ->name('site-reports.truck.download');

    Route::get(
        '/subcontractor-attendance',
        [SubcontractorAttendanceController::class, 'index']
    )->name('subcontractor-attendance.index');

    Route::get(
        '/subcontractor-attendance/pdf',
        [SubcontractorAttendanceController::class, 'pdf']
    )->name('subcontractor-attendance.pdf');

    Route::get(
        '/subcontractor-attendance/download',
        [SubcontractorAttendanceController::class, 'downloadPdf']
    )->name('subcontractor-attendance.download');

    Route::get(
        '/invoices/{invoice}/pdf',
        [InvoiceController::class, 'pdf']
    )->name('invoices.pdf');

    Route::get(
        '/invoices/{invoice}/download',
        [InvoiceController::class, 'downloadPdf']
    )->name('invoices.pdf.download');

    Route::get('/invoices/sites', [InvoiceController::class, 'getSites'])
        ->name('invoices.sites');

    Route::get('/invoices/site-details', [InvoiceController::class, 'getSiteDetails'])
        ->name('invoices.site-details');

    Route::resource('invoices', InvoiceController::class);

    // 月別売上の一括再計算
    Route::get('/monthly-sales-recalculation', [MonthlySalesRecalculationController::class, 'index'])
        ->name('monthly-sales-recalculation.index');

    Route::post('/monthly-sales-recalculation', [MonthlySalesRecalculationController::class, 'store'])
        ->name('monthly-sales-recalculation.store');
});

require __DIR__ . '/auth.php';
