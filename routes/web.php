<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\CashbookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExchangeRateController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PayableController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ReceivableController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StorefrontController::class, 'index'])->name('storefront.home');
Route::get('/products', [StorefrontController::class, 'catalog'])->name('storefront.products');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/inventory', [InventoryController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('inventory.index');

Route::post('/inventory/adjust', [InventoryController::class, 'adjust'])
    ->middleware(['auth', 'verified'])
    ->name('inventory.adjust');

Route::post('/inventory/pack-sizes', [InventoryController::class, 'updatePackSizes'])
    ->middleware(['auth', 'verified'])
    ->name('inventory.pack-sizes');

Route::get('/inventory/packaging', [InventoryController::class, 'packaging'])
    ->middleware(['auth', 'verified'])
    ->name('inventory.packaging');

Route::get('/customers', [CustomerController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('customers.index');

Route::get('/customers/create', [CustomerController::class, 'create'])
    ->middleware(['auth', 'verified'])
    ->name('customers.create');

Route::post('/customers', [CustomerController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('customers.store');

Route::get('/orders/create', [OrderController::class, 'create'])
    ->middleware(['auth', 'verified'])
    ->name('orders.create');

Route::post('/orders/store', [OrderController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('orders.store');

Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('purchase-orders.index');

Route::get('/purchase-orders/create', [PurchaseOrderController::class, 'create'])
    ->middleware(['auth', 'verified'])
    ->name('purchase-orders.create');

Route::post('/purchase-orders', [PurchaseOrderController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('purchase-orders.store');

Route::get('/vendors', [VendorController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('vendors.index');

Route::get('/vendors/create', [VendorController::class, 'create'])
    ->middleware(['auth', 'verified'])
    ->name('vendors.create');

Route::post('/vendors', [VendorController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('vendors.store');

Route::get('/expenses', [ExpenseController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('expenses.index');

Route::get('/expenses/create', [ExpenseController::class, 'create'])
    ->middleware(['auth', 'verified'])
    ->name('expenses.create');

Route::post('/expenses', [ExpenseController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('expenses.store');

Route::get('/payments', [PaymentController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('payments.index');

Route::get('/payments/create', [PaymentController::class, 'create'])
    ->middleware(['auth', 'verified'])
    ->name('payments.create');

Route::post('/payments', [PaymentController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('payments.store');

Route::get('/returns', [ReturnController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('returns.index');

Route::get('/returns/create', [ReturnController::class, 'create'])
    ->middleware(['auth', 'verified'])
    ->name('returns.create');

Route::post('/returns', [ReturnController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('returns.store');

Route::get('/cashbook', [CashbookController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('cashbook.index');

Route::get('/cashbook/create', [CashbookController::class, 'create'])
    ->middleware(['auth', 'verified'])
    ->name('cashbook.create');

Route::post('/cashbook', [CashbookController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('cashbook.store');

Route::get('/ledger', [LedgerController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('ledger.index');

Route::get('/ledger/create', [LedgerController::class, 'create'])
    ->middleware(['auth', 'verified'])
    ->name('ledger.create');

Route::post('/ledger', [LedgerController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('ledger.store');

Route::get('/payroll', [PayrollController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('payroll.index');

Route::get('/payroll/create', [PayrollController::class, 'create'])
    ->middleware(['auth', 'verified'])
    ->name('payroll.create');

Route::post('/payroll', [PayrollController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('payroll.store');

Route::get('/invoices', [InvoiceController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('invoices.index');

Route::get('/invoices/create', [InvoiceController::class, 'create'])
    ->middleware(['auth', 'verified'])
    ->name('invoices.create');

Route::post('/invoices', [InvoiceController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('invoices.store');

Route::get('/quotations', [QuotationController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('quotations.index');

Route::get('/quotations/create', [QuotationController::class, 'create'])
    ->middleware(['auth', 'verified'])
    ->name('quotations.create');

Route::post('/quotations', [QuotationController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('quotations.store');

Route::get('/receivables', [ReceivableController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('receivables.index');

Route::get('/receivables/create', [ReceivableController::class, 'create'])
    ->middleware(['auth', 'verified'])
    ->name('receivables.create');

Route::post('/receivables', [ReceivableController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('receivables.store');

Route::get('/payables', [PayableController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('payables.index');

Route::get('/payables/create', [PayableController::class, 'create'])
    ->middleware(['auth', 'verified'])
    ->name('payables.create');

Route::post('/payables', [PayableController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('payables.store');

Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/items', [ProductController::class, 'index'])->name('products.index');
    Route::get('/items/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/items', [ProductController::class, 'store'])->name('products.store');
    Route::get('/items/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/items/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/items/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::get('/items/bulk/create', [ProductController::class, 'bulkCreate'])->name('products.bulk-create');
    Route::post('/items/bulk', [ProductController::class, 'bulkStore'])->name('products.bulk-store');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/brands', [BrandController::class, 'index'])->name('brands.index');
    Route::get('/brands/create', [BrandController::class, 'create'])->name('brands.create');
    Route::post('/brands', [BrandController::class, 'store'])->name('brands.store');
    Route::get('/brands/{brand}/edit', [BrandController::class, 'edit'])->name('brands.edit');
    Route::put('/brands/{brand}', [BrandController::class, 'update'])->name('brands.update');
    Route::delete('/brands/{brand}', [BrandController::class, 'destroy'])->name('brands.destroy');

    Route::get('/exchange-rates', [ExchangeRateController::class, 'index'])->name('exchange-rates.index');
    Route::get('/exchange-rates/create', [ExchangeRateController::class, 'create'])->name('exchange-rates.create');
    Route::post('/exchange-rates', [ExchangeRateController::class, 'store'])->name('exchange-rates.store');
    Route::post('/exchange-rates/{exchangeRate}/activate', [ExchangeRateController::class, 'activate'])->name('exchange-rates.activate');
    Route::delete('/exchange-rates/{exchangeRate}', [ExchangeRateController::class, 'destroy'])->name('exchange-rates.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
