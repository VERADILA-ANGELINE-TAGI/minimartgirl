<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;

// Tes API
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API MINIMARTGIRL berhasil!',
        'application' => 'MINIMARTGIRL'
    ]);
});

// Melihat semua produk aktif
Route::get('/products', [ProductController::class, 'index']);

// Melihat detail satu produk
Route::get('/products/{id}', [ProductController::class, 'show']);

// Menambahkan produk
Route::post('/products', [ProductController::class, 'store']);

// Mengubah produk
Route::put('/products/{id}', [ProductController::class, 'update']);
Route::patch('/products/{id}', [ProductController::class, 'update']);

// Menghapus produk
Route::delete('/products/{id}', [ProductController::class, 'destroy']);