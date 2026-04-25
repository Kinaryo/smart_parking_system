<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\All\ProfileController;
use App\Http\Controllers\QRParkirController;
use Illuminate\Http\Request;
use App\Services\SlotService;
use App\Events\SlotUpdated;

/* ================= USER ================= */
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\User\UserKendaraanController;
use App\Http\Controllers\User\UserParkirController;
use App\Http\Controllers\User\UserProfileController;

/* ================= PETUGAS ================= */
use App\Http\Controllers\Petugas\PetugasDashboardController;
use App\Http\Controllers\Petugas\PetugasRiwayatTransaksiController;

/* ================= ADMIN ================= */
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminKelolaPetugasController;
use App\Http\Controllers\Admin\AdminKelolaUserController;
use App\Http\Controllers\Admin\AdminLaporanPetugasController;
use App\Http\Controllers\Admin\AdminRiwayatTransaksiController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminTarifController;
use App\Http\Controllers\Admin\CetakNotaController;
use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/

Route::get('/', [LandingController::class, 'index']);


/*
|--------------------------------------------------------------------------
| QR
|--------------------------------------------------------------------------
*/
Route::prefix('qr')->group(function () {
    Route::get('/generate', [QRParkirController::class, 'generate']);
    Route::get('/aktif', [QRParkirController::class, 'getQR']);
});

Route::get('/qr-show', [QRParkirController::class, 'showQR']);
Route::get('/ajax-qr-show', [QRParkirController::class, 'ajaxQRShow']);


/*
|--------------------------------------------------------------------------
| GUEST (BELUM LOGIN)
|--------------------------------------------------------------------------
*/
Route::middleware('guest.custom')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.process');

    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'store'])->name('register.process');


    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});


/*
|--------------------------------------------------------------------------
| AUTH (SUDAH LOGIN)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');
   
    Route::get('nota/{id}', [CetakNotaController::class, 'show'])->name('cetakNota');
   
   
    /*
    |--------------------------------------------------------------------------
    | USER
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:user')->group(function () {

        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');

        // PROFILE
        Route::get('/user-profile', [UserProfileController::class, 'index'])->name('user.profile');
        Route::post('/user-profile/update', [UserProfileController::class, 'updateProfile'])->name('user.profile.update');
        Route::post('user-profile/update-password', [UserProfileController::class, 'updatePassword'])->name('user.profile.update.password');

        // KENDARAAN
        Route::get('/kendaraan', [UserKendaraanController::class, 'index'])->name('user.kendaraan.index');
        Route::get('/kendaraan/{id}', [UserKendaraanController::class, 'detail'])->name('user.kendaraan.detail');
        Route::put('/kendaraan/{id}/update', [UserKendaraanController::class, 'update'])->name('user.kendaraan.update');
        Route::get('/kendaraan/{id}/riwayat', [UserKendaraanController::class, 'detailHistory'])->name('user.kendaraan.detail-history');
        Route::post('/kendaraan', [UserKendaraanController::class, 'store'])->name('user.kendaraan.store');

        // PARKIR
        Route::post('/parkir-store', [UserKendaraanController::class, 'storeToParkir'])->name('parkir.store');
        Route::get('/parkir/scanqr/{kendaraanId}', [UserParkirController::class, 'scanIndex'])->name('parkir.scan');
        Route::post('/parkir/scanqr/store', [UserParkirController::class, 'scanStore'])->name('scan.qr');
        Route::get('/parkir/detail/{id}', [UserParkirController::class, 'detail'])->name('user.dashboard.parkir.detail');
        Route::get('/transaksi/status/{id}', [UserParkirController::class, 'getStatusApi']);
    });


    /*
    |--------------------------------------------------------------------------
    | PETUGAS
    |--------------------------------------------------------------------------
    */
    Route::prefix('petugas')
        ->middleware('role:petugas')
        ->group(function () {

            Route::get('/dashboard', [PetugasDashboardController::class, 'index'])->name('petugas.dashboard');
            Route::get('/slots', [PetugasDashboardController::class, 'slots'])->name('petugas.slots');
            Route::post('/keluar/{id}', [PetugasDashboardController::class, 'keluar'])->name('petugas.parkir.keluar');

            Route::get('/riwayat-transaksi', [PetugasRiwayatTransaksiController::class, 'index'])->name('petugas.riwayat');
            Route::get('/riwayat-transaksi/data', [PetugasRiwayatTransaksiController::class, 'data'])->name('petugas.transaksi.data');
            Route::get('/riwayat-transaksi/summary', [PetugasRiwayatTransaksiController::class, 'summary'])->name('petugas.transaksi.summary');
        });


    /*
    |--------------------------------------------------------------------------
    | ADMIN
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')
        ->middleware('role:admin')
        ->group(function () {

            Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

            Route::get('/slots', [AdminDashboardController::class, 'slots'])->name('admin.slots');
            Route::post('/keluar/{id}', [AdminDashboardController::class, 'keluar'])->name('admin.parkir.keluar');

            // RIWAYAT
            Route::get('/riwayat-transaksi', [AdminRiwayatTransaksiController::class, 'index'])->name('admin.riwayat');
            Route::get('/riwayat-transaksi/data', [AdminRiwayatTransaksiController::class, 'data'])->name('admin.transaksi.data');
            Route::get('/riwayat-transaksi/summary', [AdminRiwayatTransaksiController::class, 'summary'])->name('admin.transaksi.summary');

            // PETUGAS (FULL)
            Route::get('/petugas', [AdminKelolaPetugasController::class, 'index'])->name('admin.petugas.index');
            Route::get('/petugas/data', [AdminKelolaPetugasController::class, 'data'])->name('admin.petugas.data');
            Route::get('/petugas/{id}/transaksi-data', [AdminKelolaPetugasController::class, 'transaksiData'])->name('admin.petugas.transaksi.data');
            Route::get('/petugas/create', [AdminKelolaPetugasController::class, 'create'])->name('admin.petugas.create');
            Route::post('/petugas/store', [AdminKelolaPetugasController::class, 'store'])->name('admin.petugas.store');
            Route::get('/petugas/{id}', [AdminKelolaPetugasController::class, 'show'])->name('admin.petugas.show');
            Route::get('/petugas/edit/{id}', [AdminKelolaPetugasController::class, 'edit'])->name('admin.petugas.edit');
            Route::put('/petugas/update/{id}', [AdminKelolaPetugasController::class, 'update'])->name('admin.petugas.update');
            Route::delete('/petugas/delete/{id}', [AdminKelolaPetugasController::class, 'destroy'])->name('admin.petugas.destroy');
            Route::put('/petugas/toggle/{id}', [AdminKelolaPetugasController::class, 'toggleStatus'])->name('admin.petugas.toggle');

            // LAPORAN
            Route::get('laporan/rekap-petugas', [AdminLaporanPetugasController::class, 'exportRekap'])->name('admin.laporan.rekap');
            Route::get('laporan/detail-semua-petugas', [AdminLaporanPetugasController::class, 'exportAllDetail'])->name('admin.laporan.all-detail');

            // USER
            Route::get('/user', [AdminKelolaUserController::class, 'index'])->name('admin.user.index');
            Route::get('/user/data', [AdminKelolaUserController::class, 'data'])->name('admin.user.data');
            Route::post('/user', [AdminKelolaUserController::class, 'store'])->name('admin.user.store');
            Route::put('/user/{id}', [AdminKelolaUserController::class, 'update'])->name('admin.user.update');
            Route::delete('/user/{id}', [AdminKelolaUserController::class, 'destroy'])->name('admin.user.delete');
            Route::put('/user/toggle/{id}', [AdminKelolaUserController::class, 'toggleStatus'])->name('admin.user.toggle');

            // TARIF
            Route::get('/tarif', [AdminTarifController::class, 'index'])->name('admin.tarif.index');
            Route::get('/tarif/data', [AdminTarifController::class, 'data'])->name('admin.tarif.data');
            Route::post('/tarif', [AdminTarifController::class, 'store'])->name('admin.tarif.store');
            Route::put('/tarif/{id}', [AdminTarifController::class, 'update'])->name('admin.tarif.update');
            Route::delete('/tarif/{id}', [AdminTarifController::class, 'destroy'])->name('admin.tarif.destroy');

            // SETTING 
            Route::get('/setting', [AdminSettingController::class, 'index'])->name('admin.setting.index');
            Route::get('/setting/data', [AdminSettingController::class, 'data'])->name('admin.setting.data');
            Route::post('/setting/update', [AdminSettingController::class, 'update'])->name('admin.setting.update');
        });
});
