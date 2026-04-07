<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DevAdmin\AuthController;
use App\Http\Controllers\DevAdmin\DashboardController;
use App\Http\Controllers\DevAdmin\SettingsController;
use App\Http\Controllers\DevAdmin\SoftwareMenuController;
use App\Http\Controllers\DevAdmin\SoftwareInternalLinkController;
use App\Http\Controllers\DevAdmin\SoftwareMenuSortingController;
use App\Http\Controllers\DevAdmin\SystemController;
use App\Http\Controllers\DevAdmin\UserController;
// use App\Http\Controllers\DevAdmin\AdminController;
use App\Http\Controllers\DevAdmin\AdminMenuController;
use App\Http\Controllers\DevAdmin\AdminInternalLinkController;
use App\Http\Controllers\DevAdmin\AdminMenuSortingController;

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

        // Route::resource('admins', AdminController::class);
        Route::resource('users', UserController::class);
        Route::get('users/{user}/permissions', [UserController::class, 'permissions'])->name('users.permissions');
        Route::post('users/{user}/permissions', [UserController::class, 'updatePermissions'])->name('users.permissions.update');



        // System routes
        Route::prefix('system-config')->name('systemConfig.')->group(function () {
            Route::prefix('admin-panel')->name('adminPanel.')->group(function () {
                Route::resource('menu', AdminMenuController::class);
                Route::resource('internal-link', AdminInternalLinkController::class);
                Route::get('menu-sorting', [AdminMenuSortingController::class, 'index'])->name('menuSorting');
                Route::post('menu-sorting/update-order', [AdminMenuSortingController::class, 'updateOrder'])->name('menuSorting.updateOrder');
            });

            Route::prefix('software')->name('software.')->group(function () {
                Route::resource('menu', SoftwareMenuController::class);
                Route::resource('internal-link', SoftwareInternalLinkController::class);
                Route::get('internal-link/{internal_link}/permissions', [SoftwareInternalLinkController::class, 'permissions'])->name('internal-link.permissions');
                Route::post('internal-link/{internal_link}/permissions', [SoftwareInternalLinkController::class, 'updatePermissions'])->name('internal-link.permissions.update');
                Route::get('menu-sorting', [SoftwareMenuSortingController::class, 'index'])->name('menuSorting');
                Route::post('menu-sorting/update-order', [SoftwareMenuSortingController::class, 'updateOrder'])->name('menuSorting.updateOrder');
            });
        });

        Route::get('clear-cache', [SystemController::class, 'clearCache'])->name('cache.clear');
        Route::get('logs', [SystemController::class, 'viewLogs'])->name('logs.view');
        Route::get('database', [SystemController::class, 'databaseInfo'])->name('database.info');
        Route::get('settings', [SettingsController::class, 'index'])->name('settings');
    });
});
