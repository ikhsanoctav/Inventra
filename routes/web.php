<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryNoteController;
use App\Http\Controllers\GudangRequestController;
use App\Http\Controllers\InboundController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ManagerApprovalController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OutboundController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\PurchasingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RuleBuilderController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\TransactionHistoryController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->middleware('throttle:5,1');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');
    Route::get('/kasir/chart-data', [DashboardController::class, 'kasirChartData'])->name('kasir.chart');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Notifications
    Route::get('/notifications/unread', [NotificationController::class, 'unread'])->name('notifications.unread');
    Route::get('/notifications/all', [NotificationController::class, 'all'])->name('notifications.all');
    Route::post('/notifications/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    // Master Data
    Route::middleware('role:manager,admin_gudang,purchasing,super_admin')->group(function () {
        Route::get('/master/items', [ItemController::class, 'index'])->name('master.items');
        Route::post('/master/items', [ItemController::class, 'store']);
        Route::put('/master/items/{id}', [ItemController::class, 'update'])->name('master.items.update');
        Route::delete('/master/items/{id}', [ItemController::class, 'destroy'])->name('master.items.destroy');

        Route::get('/master/categories', [CategoryController::class, 'index'])->name('master.categories');
        Route::post('/master/categories', [CategoryController::class, 'store']);
        Route::put('/master/categories/{id}', [CategoryController::class, 'update'])->name('master.categories.update');
        Route::delete('/master/categories/{id}', [CategoryController::class, 'destroy'])->name('master.categories.destroy');

        Route::get('/master/units', [UnitController::class, 'index'])->name('master.units');
        Route::post('/master/units', [UnitController::class, 'store']);
        Route::put('/master/units/{id}', [UnitController::class, 'update'])->name('master.units.update');
        Route::delete('/master/units/{id}', [UnitController::class, 'destroy'])->name('master.units.destroy');
    });

    Route::middleware('role:manager,purchasing,super_admin')->group(function () {
        Route::get('/master/suppliers', [SupplierController::class, 'index'])->name('master.suppliers');
        Route::post('/master/suppliers', [SupplierController::class, 'store']);
        Route::put('/master/suppliers/{id}', [SupplierController::class, 'update'])->name('master.suppliers.update');
        Route::delete('/master/suppliers/{id}', [SupplierController::class, 'destroy'])->name('master.suppliers.destroy');
    });

    // Transactions
    Route::middleware('role:manager,admin_gudang,super_admin')->group(function () {
        Route::get('/transactions/inbound', [InboundController::class, 'index'])->name('transactions.inbound');
        Route::post('/transactions/inbound', [InboundController::class, 'store']);
        Route::get('/transactions/inbound/{id}', [InboundController::class, 'show'])->name('transactions.inbound.show');
        Route::post('/transactions/inbound/{id}/complete', [InboundController::class, 'complete'])->name('transactions.inbound.complete');
        Route::delete('/transactions/inbound/{id}', [InboundController::class, 'destroy'])->name('transactions.inbound.destroy');
        Route::post('/transactions/inbound/{id}/lines', [InboundController::class, 'addLine'])->name('transactions.inbound.addLine');
        Route::delete('/transactions/lines/{id}', [InboundController::class, 'destroyLine'])->name('transactions.lines.destroy');

        Route::get('/transactions/outbound', [OutboundController::class, 'index'])->name('transactions.outbound');
        Route::post('/transactions/outbound', [OutboundController::class, 'store']);
        Route::get('/transactions/outbound/{id}', [OutboundController::class, 'show'])->name('transactions.outbound.show');
        Route::post('/transactions/outbound/{id}/complete', [OutboundController::class, 'complete'])->name('transactions.outbound.complete');
        Route::delete('/transactions/outbound/{id}', [OutboundController::class, 'destroy'])->name('transactions.outbound.destroy');
        Route::post('/transactions/outbound/{id}/lines', [OutboundController::class, 'addLine'])->name('transactions.outbound.addLine');
        Route::delete('/transactions/outbound/lines/{id}', [OutboundController::class, 'destroyLine'])->name('transactions.outbound.lines.destroy');

        Route::get('/transactions/opname', [StockOpnameController::class, 'index'])->name('transactions.opname.index');
        Route::post('/transactions/opname', [StockOpnameController::class, 'store'])->name('transactions.opname');
        Route::get('/transactions/opname/{id}', [StockOpnameController::class, 'show'])->name('transactions.opname.show');
        Route::post('/transactions/opname/{id}/complete', [StockOpnameController::class, 'complete'])->name('transactions.opname.complete');
        Route::delete('/transactions/opname/{id}', [StockOpnameController::class, 'destroy'])->name('transactions.opname.destroy');
        Route::post('/transactions/opname/{id}/lines', [StockOpnameController::class, 'addLine'])->name('transactions.opname.addLine');
        Route::delete('/transactions/opname/lines/{id}', [StockOpnameController::class, 'destroyLine'])->name('transactions.opname.lines.destroy');
    });

    Route::middleware('role:manager,purchasing,super_admin')->group(function () {
        Route::get('/transactions/po', [PurchaseOrderController::class, 'index'])->name('transactions.po.index');
        Route::post('/transactions/po', [PurchaseOrderController::class, 'store'])->name('transactions.po');
        Route::get('/transactions/po/{id}', [PurchaseOrderController::class, 'show'])->name('transactions.po.show');
        Route::post('/transactions/po/{id}/approve', [PurchaseOrderController::class, 'approve'])->name('transactions.po.approve');
        Route::delete('/transactions/po/{id}', [PurchaseOrderController::class, 'destroy'])->name('transactions.po.destroy');
        Route::post('/transactions/po/{id}/lines', [PurchaseOrderController::class, 'addLine'])->name('transactions.po.addLine');
        Route::delete('/transactions/po/lines/{id}', [PurchaseOrderController::class, 'destroyLine'])->name('transactions.po.lines.destroy');
    });

    Route::middleware('role:manager,admin_gudang,purchasing,super_admin')->group(function () {
        Route::get('/transactions/history', [TransactionHistoryController::class, 'index'])->name('transactions.history');
        Route::get('/reports/stock', [ReportController::class, 'stock'])->name('reports.stock');
        Route::get('/reports/transactions', [ReportController::class, 'transactions'])->name('reports.transactions');
    });

    // System Configs - Super Admin Only
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/system/settings', [SystemSettingController::class, 'index'])->name('system.settings');
        Route::post('/system/settings', [SystemSettingController::class, 'store']);

        Route::get('/system/rbl', [RuleBuilderController::class, 'index'])->name('system.rbl');
        Route::post('/system/rbl', [RuleBuilderController::class, 'store']);
        Route::get('/system/rbl/{id}', [RuleBuilderController::class, 'show'])->name('system.rbl.show');
        Route::delete('/system/rbl/{id}', [RuleBuilderController::class, 'destroy'])->name('system.rbl.destroy');
        Route::post('/system/rbl/{id}/conditions', [RuleBuilderController::class, 'storeCondition'])->name('system.rbl.condition.store');
        Route::delete('/system/rbl/conditions/{id}', [RuleBuilderController::class, 'destroyCondition'])->name('system.rbl.condition.destroy');
        Route::post('/system/rbl/{id}/actions', [RuleBuilderController::class, 'storeAction'])->name('system.rbl.action.store');
        Route::delete('/system/rbl/actions/{id}', [RuleBuilderController::class, 'destroyAction'])->name('system.rbl.action.destroy');

        Route::get('/system/users', [UserController::class, 'index'])->name('system.users');
        Route::post('/system/users', [UserController::class, 'store']);
        Route::put('/system/users/{id}', [UserController::class, 'update'])->name('system.users.update');
        Route::delete('/system/users/{id}', [UserController::class, 'destroy'])->name('system.users.destroy');
        Route::post('/system/users/{id}/restore', [UserController::class, 'restore'])->name('system.users.restore');
        Route::post('/system/users/{id}/toggle-active', [UserController::class, 'toggleActive'])->name('system.users.toggle_active');

        // Schedules & Shifts
        Route::get('/system/schedules', [ScheduleController::class, 'index'])->name('system.schedules.index');
        Route::post('/system/schedules', [ScheduleController::class, 'storeSchedule'])->name('system.schedules.store');
        Route::delete('/system/schedules/{id}', [ScheduleController::class, 'destroySchedule'])->name('system.schedules.destroy');

        Route::get('/system/schedules/shifts', [ScheduleController::class, 'shifts'])->name('system.schedules.shifts');
        Route::post('/system/schedules/shifts', [ScheduleController::class, 'storeShift'])->name('system.schedules.shifts.store');
        Route::put('/system/schedules/shifts/{id}', [ScheduleController::class, 'updateShift'])->name('system.schedules.shifts.update');
        Route::delete('/system/schedules/shifts/{id}', [ScheduleController::class, 'destroyShift'])->name('system.schedules.shifts.destroy');

        Route::get('/system/audit', [AuditLogController::class, 'index'])->name('system.audit');
        Route::post('/system/audit', [AuditLogController::class, 'store']);
    });

    // POS - Kasir Only
    Route::middleware('role:kasir,super_admin')->group(function () {
        Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
        Route::get('/pos/items', [PosController::class, 'items'])->name('pos.items');
        Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
        Route::get('/pos/receipt/{ref}', [PosController::class, 'receipt'])->name('pos.receipt');
    });

    // ----------------------------------------------------------------------
    // Placeholder Routes for PRD Features
    // ----------------------------------------------------------------------
    $placeholder = function ($title) {
        return function () use ($title) {
            return view('placeholder', ['title' => $title]);
        };
    };

    Route::middleware('role:super_admin,manager')->group(function () {
        Route::get('/master/branches', [BranchController::class, 'index'])->name('master.branches');
        Route::post('/master/branches', [BranchController::class, 'store']);
        Route::put('/master/branches/{branch}', [BranchController::class, 'update'])->name('master.branches.update');
        Route::delete('/master/branches/{branch}', [BranchController::class, 'destroy'])->name('master.branches.destroy');
    });
    // Transfer Gudang
    Route::middleware('role:manager,admin_gudang,super_admin')->group(function () {
        Route::get('/transactions/transfer', [TransferController::class, 'index'])->name('transactions.transfer');
        Route::get('/transactions/transfer/create', [TransferController::class, 'create'])->name('transactions.transfer.create');
        Route::post('/transactions/transfer', [TransferController::class, 'store'])->name('transactions.transfer.store');
        Route::get('/transactions/transfer/{id}', [TransferController::class, 'show'])->name('transactions.transfer.show');
        Route::post('/transactions/transfer/{id}/lines', [TransferController::class, 'addLine'])->name('transactions.transfer.addLine');
        Route::delete('/transactions/transfer/lines/{id}', [TransferController::class, 'destroyLine'])->name('transactions.transfer.lines.destroy');
        Route::post('/transactions/transfer/{id}/complete', [TransferController::class, 'complete'])->name('transactions.transfer.complete');
        Route::delete('/transactions/transfer/{id}', [TransferController::class, 'destroy'])->name('transactions.transfer.destroy');
    });

    // Reports
    Route::middleware('role:manager,admin_gudang,purchasing,kasir,super_admin')->group(function () {
        Route::get('/reports/mutation', [ReportController::class, 'mutation'])->name('reports.mutation');
        Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('/reports/profit-loss', [ReportController::class, 'profitLoss'])->name('reports.profit_loss');
        Route::get('/reports/purchasing', [ReportController::class, 'purchasing'])->name('reports.purchasing');
        Route::get('/reports/stock-valuation', [ReportController::class, 'stockValuation'])->name('reports.stock_valuation');
        Route::get('/reports/stock-card', [ReportController::class, 'stockCard'])->name('reports.stock_card');
        Route::get('/reports/low-stock', [ReportController::class, 'lowStock'])->name('reports.low_stock');
        Route::get('/reports/po', [ReportController::class, 'purchasing'])->name('reports.po');
        Route::get('/reports/reorder', [ReportController::class, 'lowStock'])->name('reports.reorder');
        Route::get('/reports/sales-branch', [ReportController::class, 'salesBranch'])->name('reports.sales_branch');
    });

    // Manager
    Route::middleware('role:manager,super_admin')->group(function () {
        Route::get('/manager/audit/opname', [ManagerApprovalController::class, 'auditOpname'])->name('manager.audit.opname');
        Route::get('/manager/approvals', [ManagerApprovalController::class, 'index'])->name('manager.approvals');
        Route::get('/manager/trends', [ReportController::class, 'trends'])->name('manager.trends');
    });

    // Gudang
    Route::middleware('role:admin_gudang,manager,super_admin')->group(function () {
        Route::get('/gudang/requests', [GudangRequestController::class, 'index'])->name('gudang.requests');
        Route::post('/gudang/requests', [GudangRequestController::class, 'store'])->name('gudang.requests.store');
        Route::get('/gudang/delivery-note', [DeliveryNoteController::class, 'index'])->name('gudang.delivery_note');
        Route::get('/gudang/delivery-note/{id}/print', [DeliveryNoteController::class, 'print'])->name('gudang.delivery_note.print');
    });

    // Purchasing
    Route::middleware('role:purchasing,manager,super_admin')->group(function () {
        Route::get('/purchasing/history', [PurchasingController::class, 'history'])->name('purchasing.history');
        Route::get('/purchasing/performance', [PurchasingController::class, 'performance'])->name('purchasing.performance');
        Route::get('/purchasing/pending-approval', [PurchasingController::class, 'pendingApproval'])->name('purchasing.pending_approval');
        Route::get('/transactions/po/create', [PurchaseOrderController::class, 'create'])->name('transactions.po.create');
    });

    // POS Additional
    Route::middleware('role:kasir,super_admin')->group(function () {
        Route::get('/pos/mode/service', [PosController::class, 'index'])->name('pos.service');
        Route::get('/pos/terminal', [PosController::class, 'index'])->name('pos.terminal');
        Route::get('/pos/history/today', [PosController::class, 'historyToday'])->name('pos.history.today');
        Route::get('/pos/shifts', [PosController::class, 'shifts'])->name('pos.shifts');
        Route::post('/pos/shifts/open', [PosController::class, 'openShift'])->name('pos.shifts.open');
        Route::post('/pos/shifts/close', [PosController::class, 'closeShift'])->name('pos.shifts.close');
        Route::get('/pos/returns', [PosController::class, 'returns'])->name('pos.returns');
        Route::post('/pos/returns/{id}', [PosController::class, 'processReturn'])->name('pos.returns.process');
    });
});
Route::get('/search', [SearchController::class, 'index'])->name('search.index')->middleware('auth');
