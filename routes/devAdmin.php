<?php

use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Admin\DashboardController;
// use App\Http\Controllers\Admin\MenuController;
// use App\Http\Controllers\Admin\MenuCategoryController;
// use App\Http\Controllers\Admin\MenuVariantsController;
// use App\Http\Controllers\Admin\ComboItemController;
// use App\Http\Controllers\Admin\ComplementaryItemController;
// use App\Http\Controllers\Admin\StockController;
// use App\Http\Controllers\Admin\PurchaseController;
// use App\Http\Controllers\Admin\OrderController;
// use App\Http\Controllers\Admin\CustomerController;
// use App\Http\Controllers\Admin\MembershipController;
// use App\Http\Controllers\Admin\PromotionController;
// use App\Http\Controllers\Admin\UserController;
// use App\Http\Controllers\Admin\SettingsController;

// ----------------------
// Admin Panel Routes
// ----------------------
Route::middleware(['web', 'auth', 'role:admin'])->name('admin.')->group(function () {

    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Users & Roles
    Route::resource('users', UserController::class);

    // Menu management
    Route::prefix('menu')->name('menu.')->group(function () {
        Route::resource('categories', MenuCategoryController::class)->names('categories');
        Route::resource('items', MenuController::class)->names('items');
        Route::resource('variants', MenuVariantsController::class)->names('variants');
    });
    // Combo Items
    // Route::resource('combo-items', ComboItemController::class);

    // // Complementary Items
    // Route::resource('complementary-items', ComplementaryItemController::class);

    // Stock management
    Route::resource('stock', StockController::class)->only(['index','show','create','store','edit','update']);

    // Purchases
    Route::resource('purchase', PurchaseController::class);

    // Orders
    Route::resource('orders', OrderController::class)->only(['index','show','create','store','edit','update']);

    // Customers
    Route::resource('customers', CustomerController::class);

    // Memberships
    Route::resource('memberships', MembershipController::class);

    // Promotions
    Route::resource('promotions', PromotionController::class);

    // Settings
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
});
