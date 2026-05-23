<?php

use App\Http\Controllers\BatchController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\IncomingTransactionController;
use App\Http\Controllers\InventoryCardController;
use App\Http\Controllers\OutgoingTransactionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RejectItemController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ── Auth (Breeze) ──────────────────────────────────────────────────────────
require __DIR__ . '/auth.php';

// ── Authenticated routes ───────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard — all roles
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // ── Master Barang ──────────────────────────────────────────────────────
    Route::middleware('role:super_admin,admin_gudang')->group(function () {
        Route::resource('products', ProductController::class);
        Route::get('products/{product}/label', [ProductController::class, 'printLabel'])
             ->name('products.label');
    });

    // Barcode lookup — all stock roles
    Route::get('api/products/by-barcode', [ProductController::class, 'findByBarcode'])
         ->middleware('role:super_admin,admin_gudang')
         ->name('api.products.by-barcode');

    // ── Kategori ──────────────────────────────────────────────────────────
    Route::middleware('role:super_admin,admin_gudang')->group(function () {
        Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
    });

    // ── Supplier ──────────────────────────────────────────────────────────
    Route::middleware('role:super_admin,admin_gudang')->group(function () {
        Route::resource('suppliers', SupplierController::class)->only(['index', 'store', 'update', 'destroy']);
    });

    // ── Barang Masuk ──────────────────────────────────────────────────────
    Route::middleware('role:super_admin,admin_gudang')->group(function () {
        Route::resource('incoming', IncomingTransactionController::class)
             ->only(['index', 'create', 'store', 'show'])
             ->names([
                'index'  => 'incoming.index',
                'create' => 'incoming.create',
                'store'  => 'incoming.store',
                'show'   => 'incoming.show',
             ]);
    });

    // ── Barang Keluar ─────────────────────────────────────────────────────
    Route::middleware('role:super_admin,admin_gudang')->group(function () {
        Route::resource('outgoing', OutgoingTransactionController::class)
             ->only(['index', 'create', 'store', 'show'])
             ->names([
                'index'  => 'outgoing.index',
                'create' => 'outgoing.create',
                'store'  => 'outgoing.store',
                'show'   => 'outgoing.show',
             ]);
    });

    // ── Batch FIFO ────────────────────────────────────────────────────────
    Route::middleware('role:super_admin,admin_gudang,owner')->group(function () {
        Route::get('batches', [BatchController::class, 'index'])->name('batches.index');
        Route::get('batches/{batch}', [BatchController::class, 'show'])->name('batches.show');
        Route::get('api/products/{product}/batches', [BatchController::class, 'forProduct'])
             ->name('api.batches.for-product');
    });

    // ── Barang Reject ─────────────────────────────────────────────────────
    Route::middleware('role:super_admin,admin_gudang')->group(function () {
        Route::resource('reject', RejectItemController::class)
             ->only(['index', 'create', 'store'])
             ->names([
                'index'  => 'reject.index',
                'create' => 'reject.create',
                'store'  => 'reject.store',
             ]);
    });

    // ── Kartu Persediaan ──────────────────────────────────────────────────
    Route::middleware('role:super_admin,admin_gudang,admin_keuangan,owner')->group(function () {
        Route::get('inventory-card', [InventoryCardController::class, 'index'])->name('inventory-card.index');
        Route::get('inventory-card/{product}', [InventoryCardController::class, 'show'])->name('inventory-card.show');
    });

    // ── Keuangan ──────────────────────────────────────────────────────────
    Route::middleware('role:super_admin,admin_keuangan')->group(function () {
        Route::get('financial', [FinancialController::class, 'index'])->name('financial.index');
        Route::post('financial', [FinancialController::class, 'store'])->name('financial.store');
        Route::delete('financial/{financial}', [FinancialController::class, 'destroy'])->name('financial.destroy');
        Route::get('financial/profit-loss', [FinancialController::class, 'profitLoss'])->name('financial.profit-loss');
    });

    // ── Laporan ───────────────────────────────────────────────────────────
    Route::middleware('role:super_admin,admin_keuangan,owner')->group(function () {
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/stock', [ReportController::class, 'stock'])->name('reports.stock');
        Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('reports/fifo', [ReportController::class, 'fifo'])->name('reports.fifo');
        Route::get('reports/reject', [ReportController::class, 'reject'])->name('reports.reject');
        Route::get('reports/financial', [ReportController::class, 'financial'])->name('reports.financial');
    });

    // ── User Management (Super Admin only) ────────────────────────────────
    Route::middleware('role:super_admin')->group(function () {
        Route::resource('users', UserController::class)
             ->only(['index', 'store', 'update', 'destroy']);
    });
});
