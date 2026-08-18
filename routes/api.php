<?php

use App\Http\Controllers\Api\SiteApiController;
use App\Http\Controllers\Api\InvoiceApiController;
use Illuminate\Support\Facades\Route;

Route::get('/sites', [SiteApiController::class, 'index']);

Route::get(
    '/invoice-summary',
    [InvoiceApiController::class, 'summary']
);
