<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DevAdmin\AuthController;
use App\Http\Controllers\DevAdmin\DashboardController;
use App\Http\Controllers\DevAdmin\SettingsController;

// ----------------------
// devAdmin Panel Routes
// ----------------------

Route::prefix('devAdmin')->name('devAdmin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('devAdmin.login');
    })->name('home');
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.submit');

    Route::middleware(['web', 'auth:admin', 'role:developer'])->group(function () {
        // Dashboard
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');


        // Settings
        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    });
});
