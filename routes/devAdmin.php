<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DevAdmin\AuthController;
use App\Http\Controllers\DevAdmin\DashboardController;
use App\Http\Controllers\DevAdmin\SettingsController;

// ----------------------
// devAdmin Panel Routes
// ----------------------

Route::middleware('web')->name('devAdmin.')->group(function () {
    Route::get('/', fn() => redirect()->route('devAdmin.login'))->name('home');
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.submit');

    Route::middleware(['auth:admin', 'role:developer'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });
});

