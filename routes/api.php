<?php

use App\Http\Controllers\Api\SiteApiController;
use Illuminate\Support\Facades\Route;

Route::get('/sites', [SiteApiController::class, 'index']);
