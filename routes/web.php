<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransaksiController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('auth.login');
});

// Login
Route::get('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('auth.authenticate');
Route::get('/logout', [AuthController::class, 'logout'])->name('auth.logout');

// Registrasi
Route::get('/registrasi', [AuthController::class, 'registrasi'])->name('auth.registrasi');
Route::post('/registrasi', [AuthController::class, 'store'])->name('auth.store');

// Group
Route::group(['middleware' => ['auth']],function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard.index');
    
    // Product
    Route::get('/product', [ProductController::class, 'index'])->name('product.index');
    Route::get('/product/search', [ProductController::class, 'search'])->name('product.search');
    Route::post('/product', [ProductController::class, 'store'])->name('product.store');
    Route::get('/product/filter', [ProductController::class, 'filter'])->name('product.filter');
    Route::get('/product/export', [ProductController::class, 'export'])->name('product.export');
    Route::post('/product/update/{id}', [ProductController::class, 'update'])->name('product.update');
    Route::post('/product/destroy/{id}', [ProductController::class, 'destroy'])->name('product.destroy');
    

    //Transaksi
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('/transaksi/search', [TransaksiController::class, 'search'])->name('transaksi.search');
    Route::post('/transaksi', [TransaksiController::class, 'store'])->name('transaksi.store');
    Route::get('/transaksi/filter', [TransaksiController::class, 'filter'])->name('transaksi.filter');
    Route::get('/transaksi/export', [TransaksiController::class, 'export'])->name('transaksi.export');
    Route::post('/transaksi/update/{id}', [TransaksiController::class, 'update'])->name('transaksi.update');
    Route::post('/transaksi/destroy/{id}', [TransaksiController::class, 'destroy'])->name('transaksi.destroy');
});