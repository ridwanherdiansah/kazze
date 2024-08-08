<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

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

});