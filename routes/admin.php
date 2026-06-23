<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\ProductVariantsController;
use App\Http\Controllers\Admin\ComboItemController;
use App\Http\Controllers\Admin\ComplementaryItemController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\MembershipController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\MarketingCampaignController;
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
use App\Http\Controllers\Admin\PosRegisterController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\Account\ChartOfAccountController;
use App\Http\Controllers\Admin\Account\VoucherController;
use App\Http\Controllers\Admin\Account\AccountReportController;

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

        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::resource('waste', \App\Http\Controllers\Admin\Inventory\WasteManagementController::class)->only(['index', 'create', 'store']);

            Route::prefix('transfers')->name('transfers.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\Inventory\StockTransferController::class, 'index'])->name('index');
                Route::get('create', [\App\Http\Controllers\Admin\Inventory\StockTransferController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Admin\Inventory\StockTransferController::class, 'store'])->name('store');
                Route::post('{transfer}/dispatch', [\App\Http\Controllers\Admin\Inventory\StockTransferController::class, 'dispatchAction'])->name('dispatch');
                Route::post('{transfer}/receive', [\App\Http\Controllers\Admin\Inventory\StockTransferController::class, 'receiveAction'])->name('receive');
            });
        });

        Route::get('stock/adjust', [StockController::class, 'adjust'])->name('stock.adjust');
        Route::post('stock/adjust', [StockController::class, 'processAdjustment'])->name('stock.adjust.process');
        Route::get('stock/{type}/{id}', [StockController::class, 'show'])->name('stock.show');
        Route::resource('stock', StockController::class)->only(['index']);

        // Recipes
        Route::resource('recipes', RecipeController::class);

        // Orders
        Route::resource('orders', OrderController::class)->only(['index', 'show', 'create', 'store', 'edit', 'update']);
        Route::post('orders/{order}/payments', [PaymentController::class, 'store'])->name('orders.payments.store');
        Route::get('orders/{order}/invoice', [InvoiceController::class, 'show'])->name('orders.invoice');
        Route::post('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status.update');
        Route::post('orders/{order}/assign-driver', [OrderController::class, 'assignDriver'])->name('orders.assign-driver');
        Route::post('orders/item/{orderDetailId}/refund', [OrderController::class, 'refundItem'])->name('orders.item.refund');


        // Customers
        Route::resource('customers', CustomerController::class);

        // Memberships
        Route::resource('memberships', MembershipController::class);

        // Promotions
        Route::resource('promotions', PromotionController::class);

        // Marketing Campaigns
        Route::resource('marketing-campaigns', MarketingCampaignController::class);

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
        Route::prefix('pos')->name('pos.')->group(function () {
            Route::get('/', [PosController::class, 'index'])->name('index');
            Route::post('submit', [PosController::class, 'submitOrder'])->name('submit');

            // Register Shifts
            Route::get('register/open', [PosRegisterController::class, 'showOpen'])->name('register.open');
            Route::post('register/open', [PosRegisterController::class, 'open'])->name('register.open.submit');
            Route::get('register/close', [PosRegisterController::class, 'showClose'])->name('register.close');
            Route::post('register/close', [PosRegisterController::class, 'close'])->name('register.close.submit');
        });

        // Kitchen Display System
        Route::prefix('kds')->name('kds.')->group(function () {
            Route::get('/', [KdsController::class, 'index'])->name('index');
            Route::get('expo', [KdsController::class, 'expo'])->name('expo');
            Route::get('ready-items', [KdsController::class, 'fetchReadyItems'])->name('ready-items');
            Route::post('item/{item}', [KdsController::class, 'updateItemStatus'])->name('item.status');
            Route::post('{order}/ready', [KdsController::class, 'readyOrder'])->name('order.ready');
        });

        // Accounting Module
        Route::prefix('accounts')->name('accounts.')->group(function () {
            // Chart of Accounts
            Route::post('coa/post-opening', [ChartOfAccountController::class, 'postOpeningBalances'])->name('coa.post-opening');
            Route::resource('coa', ChartOfAccountController::class);

            // Vouchers
            Route::resource('vouchers', VoucherController::class);
            Route::post('vouchers/{voucher}/approve', [VoucherController::class, 'approve'])->name('vouchers.approve');

            // Reports
            Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('ledger', [AccountReportController::class, 'generalLedger'])->name('ledger');
                Route::get('trial-balance', [AccountReportController::class, 'trialBalance'])->name('trial-balance');
                Route::get('profit-loss', [AccountReportController::class, 'profitAndLoss'])->name('profit-loss');
            });

            // Supplier Ledger
            Route::get('supplier-ledger', [\App\Http\Controllers\Admin\Account\SupplierLedgerController::class, 'index'])->name('supplier-ledger.index');
            Route::get('supplier-ledger/{supplier}', [\App\Http\Controllers\Admin\Account\SupplierLedgerController::class, 'show'])->name('supplier-ledger.show');
            Route::post('supplier-ledger/{supplier}/payment', [\App\Http\Controllers\Admin\Account\SupplierLedgerController::class, 'storePayment'])->name('supplier-ledger.payment');
        });

        // Reports
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/abc-analysis', [ReportController::class, 'abcAnalysis'])->name('reports.abc-analysis');
        Route::get('reports/heatmap', [ReportController::class, 'hourlyHeatmap'])->name('reports.heatmap');
        Route::get('reports/staff-performance', [ReportController::class, 'staffPerformance'])->name('reports.staff-performance');

        // HR Module
        Route::prefix('hr')->name('hr.')->group(function () {
            Route::resource('employees', \App\Http\Controllers\Admin\HR\EmployeeController::class);
            Route::get('attendance', [\App\Http\Controllers\Admin\HR\AttendanceController::class, 'index'])->name('attendance.index');
            Route::post('attendance', [\App\Http\Controllers\Admin\HR\AttendanceController::class, 'mark'])->name('attendance.mark');
            Route::get('leaves', [\App\Http\Controllers\Admin\HR\LeaveController::class, 'index'])->name('leaves.index');
            Route::get('leaves/create', [\App\Http\Controllers\Admin\HR\LeaveController::class, 'create'])->name('leaves.create');
            Route::post('leaves', [\App\Http\Controllers\Admin\HR\LeaveController::class, 'store'])->name('leaves.store');
            Route::post('leaves/{leave}/status', [\App\Http\Controllers\Admin\HR\LeaveController::class, 'updateStatus'])->name('leaves.status');
            Route::get('payroll', [\App\Http\Controllers\Admin\HR\PayrollController::class, 'index'])->name('payroll.index');
            Route::post('payroll', [\App\Http\Controllers\Admin\HR\PayrollController::class, 'store'])->name('payroll.store');
        });

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
            Route::post('switch-branch', [BusinessConfigController::class, 'switchBranch'])->name('settings.switch-branch');
        });
    });
});
