<?php

use App\Http\Controllers\Admin\ActivityController as AdminActivityController;
use App\Http\Controllers\Admin\AdminAnnouncementController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrganizationController as AdminOrganizationController;
use App\Http\Controllers\Admin\RegistrationController as AdminRegistrationController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\StatusCheckController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Website Publik
|--------------------------------------------------------------------------
*/

Route::get('/', HomeController::class)->name('home');

Route::get('/organisasi', [OrganizationController::class, 'index'])->name('organizations.index');
Route::get('/organisasi/{organization:slug}', [OrganizationController::class, 'show'])
    ->name('organizations.show');

Route::get('/alur-pendaftaran', [PageController::class, 'flow'])->name('registration.flow');
Route::get('/tentang', [PageController::class, 'about'])->name('pages.about');
Route::get('/pengumuman', [AnnouncementController::class, 'index'])->name('announcements.index');

Route::get('/pendaftaran/berhasil', [RegistrationController::class, 'success'])
    ->name('registrations.success');

Route::get('/pendaftaran/{organization:slug?}', [RegistrationController::class, 'create'])->name('registrations.create');
Route::post('/pendaftaran/{organization:slug?}', [RegistrationController::class, 'store'])->name('registrations.store');

Route::get('/cek-status', [StatusCheckController::class, 'create'])->name('status.check');
Route::post('/cek-status', [StatusCheckController::class, 'store'])
    ->middleware('throttle:status-check')
    ->name('status.store');
Route::get('/cek-status/hasil', [StatusCheckController::class, 'result'])->name('status.result');

/*
|--------------------------------------------------------------------------
| Autentikasi Admin
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])
        ->middleware('throttle:login')
        ->name('login.attempt');
});

Route::post('/logout', [LogoutController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Dashboard Admin (role-based)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    /*
    | Data pendaftar — dapat diakses Super Admin & Admin Organisasi.
    | Cakupan data dibatasi di controller (admin organisasi hanya melihat
    | organisasinya sendiri) dan ditegakkan lagi oleh RegistrationPolicy.
    */
    Route::prefix('pendaftar')->name('registrations.')->group(function () {
        Route::get('/', [AdminRegistrationController::class, 'index'])->name('index');
        Route::get('/perlu-ditinjau', [AdminRegistrationController::class, 'pending'])->name('pending');
        Route::get('/{registration}', [AdminRegistrationController::class, 'show'])->name('show');
        Route::patch('/{registration}/status', [AdminRegistrationController::class, 'updateStatus'])
            ->name('updateStatus');
    });

    /*
    | Organisasi — daftar hanya super admin; detail bisa diakses admin organisasi
    | atas organisasinya sendiri (dijamin OrganizationPolicy).
    */
    Route::prefix('organisasi')->name('organizations.')->group(function () {
        Route::middleware('super_admin')->get('/', [AdminOrganizationController::class, 'index'])->name('index');
        Route::get('/{organization}', [AdminOrganizationController::class, 'show'])->name('show');
        Route::put('/{organization}', [AdminOrganizationController::class, 'update'])->name('update');
    });

    /*
    | Akun admin — hanya super admin.
    */
    Route::middleware('super_admin')->prefix('akun-admin')->name('admins.')->group(function () {
        Route::get('/', [AdminUserController::class, 'index'])->name('index');
        Route::post('/', [AdminUserController::class, 'store'])->name('store');
        Route::put('/{user}', [AdminUserController::class, 'update'])->name('update');
        Route::delete('/{user}', [AdminUserController::class, 'destroy'])->name('destroy');
    });

    /*
    | Pengumuman — hanya super admin.
    */
    Route::middleware('super_admin')->prefix('pengumuman')->name('announcements.')->group(function () {
        Route::get('/', [AdminAnnouncementController::class, 'index'])->name('index');
        Route::post('/', [AdminAnnouncementController::class, 'store'])->name('store');
        Route::put('/{announcement}', [AdminAnnouncementController::class, 'update'])->name('update');
        Route::delete('/{announcement}', [AdminAnnouncementController::class, 'destroy'])->name('destroy');
    });

    /*
    | Pengaturan umum & log aktivitas — hanya super admin.
    */
    Route::middleware('super_admin')->group(function () {
        Route::get('/pengaturan', AdminSettingController::class)->name('settings.index');
        Route::post('/pengaturan', [AdminSettingController::class, 'update'])->name('settings.update');
        Route::post('/pengaturan/pengumuman-hasil-seleksi', [AdminSettingController::class, 'updateResultAnnouncement'])
            ->name('settings.result-announcement.update');
        Route::post('/pengaturan/pengumuman-hasil-seleksi/buka-sekarang', [AdminSettingController::class, 'openResultAnnouncementNow'])
            ->name('settings.result-announcement.open');
        Route::get('/log-aktivitas', [AdminActivityController::class, 'index'])->name('activities.index');
    });
});
