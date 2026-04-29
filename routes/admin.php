<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\productVariantsController;
use App\Http\Controllers\Admin\ComboItemController;
use App\Http\Controllers\Admin\ComplementaryItemController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\MembershipController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\RestaurantSetup\BranchController;
use App\Http\Controllers\Admin\RestaurantSetup\ResDepartmentController;
use App\Http\Controllers\Admin\RestaurantSetup\StaffDepartmentController;
use App\Http\Controllers\Admin\RestaurantSetup\TableController;
use App\Http\Controllers\Admin\Inventory\StockController;
use App\Http\Controllers\Admin\Inventory\PurchaseController;
use App\Http\Controllers\Admin\Inventory\IngredientController;
use App\Http\Controllers\Admin\Inventory\SupplierController;
use App\Http\Controllers\Admin\Inventory\UnitController;
use App\Http\Controllers\Admin\Inventory\ProductionController;
use App\Http\Controllers\Admin\RecipeController;
use App\Http\Controllers\Admin\ReservationController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\BusinessConfigController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\PosController;
use App\Http\Controllers\Admin\KdsController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ReportController;

// ----------------------------
// Software Admin Panel Routes
// ----------------------------

Route::middleware('web')->name('admin.')->group(function () {
    Route::middleware('guest:web')->group(function () {
        Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->name('login.submit');
    });

    Route::get('/', function () {
        $guard = Auth::guard('web');

        return $guard->check() ? redirect()->to($guard->user()->redirectToDashboard()) : redirect()->route('admin.login');
    })->name('home');

    Route::middleware(['auth:web', 'role:admin,manager,cashier,staff', 'permission'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Users & Roles
        Route::resource('users', UserController::class);
        Route::get('users/{user}/permissions', [UserController::class, 'permissions'])->name('users.permissions');
        Route::post('users/{user}/permissions', [UserController::class, 'updatePermissions'])->name('users.permissions.update');

        // Menu management
        Route::prefix('product')->name('product.')->group(function () {
            Route::resource('categories', ProductCategoryController::class)->names('categories');
            Route::resource('items', ProductController::class)->names('items');
            Route::resource('variants', ProductVariantsController::class)->names('variants');
        });

        //Inventory & Purchases
        Route::resource('unit', UnitController::class);
        Route::resource('ingredients', IngredientController::class);
        Route::resource('suppliers', SupplierController::class);
        Route::resource('purchase', PurchaseController::class);
        Route::resource('production', ProductionController::class);

        Route::get('stock/adjust', [StockController::class, 'adjust'])->name('stock.adjust');
        Route::post('stock/adjust', [StockController::class, 'processAdjustment'])->name('stock.adjust.process');
        Route::resource('stock', StockController::class)->only(['index', 'show']);

        // Recipes
        Route::resource('recipes', RecipeController::class);

        // Orders
        Route::resource('orders', OrderController::class)->only(['index', 'show', 'create', 'store', 'edit', 'update']);
        Route::post('orders/{order}/payments', [PaymentController::class, 'store'])->name('orders.payments.store');
        Route::get('orders/{order}/invoice', [InvoiceController::class, 'show'])->name('orders.invoice');
        Route::post('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status.update');

        // Customers
        Route::resource('customers', CustomerController::class);

        // Memberships
        Route::resource('memberships', MembershipController::class);

        // Promotions
        Route::resource('promotions', PromotionController::class);

        // Reservations & Events
        Route::resource('reservations', ReservationController::class);
        Route::post('reservations/{reservation}/status', [ReservationController::class, 'updateStatus'])->name('reservations.status');

        Route::resource('events', EventController::class);
        Route::post('events/{event}/approve', [EventController::class, 'approve'])->name('events.approve');

        // Profile
        Route::get('profile', [UserController::class, 'profile'])->name('profile');
        Route::put('profile', [UserController::class, 'updateProfile'])->name('profile.update');
        Route::put('profile/password', [UserController::class, 'updatePassword'])->name('profile.password.update');

        // POS (Point of Sale)
        Route::get('pos', [PosController::class, 'index'])->name('pos.index');
        Route::post('pos/submit', [PosController::class, 'submitOrder'])->name('pos.submit');

        // Kitchen Display System
        Route::prefix('kds')->name('kds.')->group(function () {
            Route::get('/', [KdsController::class, 'index'])->name('index');
            Route::post('item/{item}', [KdsController::class, 'updateItemStatus'])->name('item.status');
            Route::post('{order}/ready', [KdsController::class, 'readyOrder'])->name('order.ready');
        });

        // Reports
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');

        // Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingsController::class, 'index'])->name('index');

            // Restaurant Setup
            Route::prefix('restaurant-setup')->name('restaurant-setup.')->group(function () {
                Route::resource('branches', BranchController::class);
                Route::resource('res-departments', ResDepartmentController::class);
                Route::resource('staff-departments', StaffDepartmentController::class);
                Route::resource('tables', TableController::class);
            });

            // Roles & Permissions
            Route::resource('roles', RoleController::class);
            Route::get('roles/{role}/permissions', [RoleController::class, 'permissions'])->name('roles.permissions');
            Route::post('roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.permissions.update');

            // Business Config
            Route::get('business-config', [BusinessConfigController::class, 'index'])->name('business-config.index');
            Route::post('business-config', [BusinessConfigController::class, 'update'])->name('business-config.update');
        });
    });
});
