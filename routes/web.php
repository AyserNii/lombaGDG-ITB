<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Customer\BerandaController as CustomerBeranda;
use App\Http\Controllers\Customer\BarengMakanController as CustomerBarengMakan;
use App\Http\Controllers\Customer\BarengBelanjaController as CustomerBarengBelanja;
use App\Http\Controllers\Customer\BarengKirimController as CustomerBarengKirim;
use App\Http\Controllers\Customer\SesiAktifController as CustomerSesiAktif;
use App\Http\Controllers\Customer\KomunitasController as CustomerKomunitas;
use App\Http\Controllers\Customer\PengaturanController as CustomerPengaturan;
use Illuminate\Support\Facades\Route;

// ─── Auth routes (tidak perlu login) ─────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [LoginController::class, 'showLoginForm'])->name('login');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');

    Route::prefix('auth')->name('auth.')->group(function () {
        Route::get('/login',     [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login',    [LoginController::class, 'authenticate'])->name('authenticate');
        Route::get('/register',  [RegisterController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [RegisterController::class, 'register'])->name('store');
    });
});

// Logout bisa diakses kapan saja (perlu POST)
Route::post('/auth/logout', [LoginController::class, 'logout'])->name('auth.logout');
Route::post('/logout',      [LoginController::class, 'logout'])->name('logout');

// ─── Customer routes (wajib login) ───────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::get('/', fn () => redirect()->route('dashboard'));

    // Flat routes (dipakai sidebar & semua link)
    Route::get('/dashboard',   [CustomerBeranda::class, 'index'])->name('dashboard');
    Route::get('/makan',       [CustomerBarengMakan::class, 'index'])->name('makan');
    Route::get('/belanja',     [CustomerBarengBelanja::class, 'index'])->name('belanja');
    Route::get('/kirim',       [CustomerBarengKirim::class, 'index'])->name('kirim');
    Route::get('/sesi',        [CustomerSesiAktif::class, 'index'])->name('sesi');
    Route::get('/komunitas',   [CustomerKomunitas::class, 'index'])->name('community');
    Route::get('/pengaturan',  [CustomerPengaturan::class, 'index'])->name('settings');
    Route::post('/pengaturan', [CustomerPengaturan::class, 'update'])->name('settings.update');

    // Prefixed routes (alias /customer/*)
    Route::prefix('customer')->name('customer.')->group(function () {
        Route::prefix('beranda')->name('beranda.')->group(function () {
            Route::get('/', [CustomerBeranda::class, 'index'])->name('index');
        });
        Route::prefix('makan')->name('makan.')->group(function () {
            Route::get('/', [CustomerBarengMakan::class, 'index'])->name('index');
        });
        Route::prefix('belanja')->name('belanja.')->group(function () {
            Route::get('/', [CustomerBarengBelanja::class, 'index'])->name('index');
        });
        Route::prefix('kirim')->name('kirim.')->group(function () {
            Route::get('/', [CustomerBarengKirim::class, 'index'])->name('index');
        });
        Route::prefix('sesi')->name('sesi.')->group(function () {
            Route::get('/', [CustomerSesiAktif::class, 'index'])->name('index');
        });
        Route::prefix('komunitas')->name('komunitas.')->group(function () {
            Route::get('/', [CustomerKomunitas::class, 'index'])->name('index');
        });
        Route::prefix('pengaturan')->name('pengaturan.')->group(function () {
            Route::get('/',  [CustomerPengaturan::class, 'index'])->name('index');
            Route::post('/', [CustomerPengaturan::class, 'update'])->name('update');
        });
    });
});
