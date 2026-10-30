<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\StrukController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.process');

Route::get('/register', [AuthController::class, 'showRegistration'])
    ->middleware('guest')
    ->name('register');

Route::post('/register', [AuthController::class, 'storeRegistration'])
    ->middleware(['guest', 'throttle:5,1'])
    ->name('register.store');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

Route::put('/profile/name', [AuthController::class, 'updateName'])
    ->middleware('auth')
    ->name('profile.name.update');

/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

/*
|--------------------------------------------------------------------------
| TRANSAKSI BARU / KASIR
|--------------------------------------------------------------------------
*/

Route::get('/transaction', [CashierController::class, 'createTransaction'])
    ->middleware('auth')
    ->name('transaction.create');

Route::post('/transaction', [CashierController::class, 'storeTransaction'])
    ->middleware('auth')
    ->name('transaction.store');

Route::post('/transaction/{transaction}/cancel', [CashierController::class, 'cancelHeldTransaction'])
    ->middleware('auth')
    ->name('transaction.cancel');

Route::post('/transaction/products', [CashierController::class, 'storeProduct'])
    ->middleware('auth')
    ->name('transaction.products.store');

Route::get('/transaction/products/import-template', [CashierController::class, 'downloadProductImportTemplate'])
    ->middleware('auth')
    ->name('transaction.products.import-template');

Route::post('/transaction/products/import', [CashierController::class, 'importProducts'])
    ->middleware('auth')
    ->name('transaction.products.import');

Route::get('/transaction/history', [CashierController::class, 'transactionHistory'])
    ->middleware('auth')
    ->name('transaction.history');

Route::get('/transaction/{transaction}/receipt', [StrukController::class, 'show'])
    ->middleware('auth')
    ->name('transaction.receipt');

/*
|--------------------------------------------------------------------------
| DOWNLOAD INVOICE PDF
|--------------------------------------------------------------------------
*/

Route::get('/transaction/{transaction}/invoice/pdf', [StrukController::class, 'downloadPdf'])
    ->middleware('auth')
    ->name('transaction.invoice.pdf');

Route::get('/transaction/{transaction}/refund', [CashierController::class, 'showRefundForm'])
    ->middleware('auth')
    ->name('transaction.refund.create');

Route::post('/transaction/{transaction}/refund', [CashierController::class, 'storeRefund'])
    ->middleware('auth')
    ->name('transaction.refund.store');

/*
|--------------------------------------------------------------------------
| HALAMAN UTAMA
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});
