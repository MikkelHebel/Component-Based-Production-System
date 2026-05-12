<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

Route::get('/', [AuthController::class, 'show'])->name('login');

Route::post('/', [AuthController::class, 'authenticate'])->name('login.post');

Route::middleware('auth')->group(function() {
// Put dashboard routes in here!
    Route::get('/dashboard', [DashboardController::class, 'show'])->name('dashboard');
});
