<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DevAdmin\AuthController;
use App\Http\Controllers\DevAdmin\DashboardController;
use App\Http\Controllers\DevAdmin\SettingsController;
use App\Http\Controllers\DevAdmin\SoftwareMenuController;
use App\Http\Controllers\DevAdmin\SystemController;
use App\Http\Controllers\DevAdmin\UserController;

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

        Route::resource('users', UserController::class);


        // System routes
        Route::prefix('system-config')->name('systemConfig.')->group(function () {
            Route::prefix('admin-panel')->name('adminPanel.')->group(function () {
                Route::get('/menu', [SettingsController::class, 'adminMenu'])->name('menu');
                Route::get('/internal-link', [SettingsController::class, 'adminInternalLink'])->name('internalLink');
                Route::get('/menu-sorting', [SettingsController::class, 'adminMenuSorting'])->name('menuSorting');
            });

            Route::prefix('software')->name('software.')->group(function () {
                Route::resource('/menu', SoftwareMenuController::class);
                Route::get('/internal-link', [SettingsController::class, 'softwareInternalLink'])->name('internalLink');
                Route::get('/menu-sorting', [SettingsController::class, 'softwareMenuSorting'])->name('menuSorting');
            });
        });

        Route::get('clear-cache', [SystemController::class, 'clearCache'])->name('cache.clear');
        Route::get('logs', [SystemController::class, 'viewLogs'])->name('logs.view');
        Route::get('database', [SystemController::class, 'databaseInfo'])->name('database.info');
        Route::get('settings', [SettingsController::class, 'index'])->name('settings');
    });
});
