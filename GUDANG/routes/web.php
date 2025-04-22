<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\userloginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AdminManagementController;
use App\Http\Controllers\MemberController;

// Public routes
Route::get('/', [userloginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [userloginController::class, 'login'])->name('login.submit');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

// Protected routes
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [userloginController::class, 'logout'])->name('logout');
    
    // Dashboard route
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    
    // Routes untuk staff dan admin
    Route::get('/home', [HomeController::class, 'index'])->name('home.index');
    Route::resource('home', HomeController::class);
    Route::get('/export', [HomeController::class, 'export'])->name('home.export');
    Route::resource('barangMasuk', BarangMasukController::class);
    Route::resource('barangKeluar', BarangKeluarController::class);
    Route::resource('peminjaman', PeminjamanController::class);
    Route::post('/peminjaman/{id}/return', [PeminjamanController::class, 'return'])->name('peminjaman.return');
    Route::get('/peminjaman/invoice/{id}', [PeminjamanController::class, 'invoice'])->name('peminjaman.invoice');
    Route::get('/peminjaman/{id}/invoice', [PeminjamanController::class, 'printInvoice'])->name('peminjaman.invoice');

    Route::get('/search-barang', [HomeController::class, 'searchBarang'])->name('search.barang');
    Route::get('/search', [GlobalSearchController::class, 'search'])->name('global.search');
    Route::get('/home/{id}/detail', [HomeController::class, 'detail'])->name('home.detail');
    Route::put('/home/{id}/stok-minimal', [HomeController::class, 'updateStokMinimal'])->name('home.updateStokMinimal');
    // Route::post('/home/{id}/variants', [HomeController::class, 'saveVariants'])->name('home.saveVariants');
    // Route::delete('/home/variants/{id}', [HomeController::class, 'deleteVariant'])->name('home.deleteVariant');
    // Route::put('/home/variants/{id}/quantity', [HomeController::class, 'updateVariantQuantity'])->name('home.updateVariantQuantity');
    // Route::put('/home/variants/{id}', [HomeController::class, 'updateVariant'])->name('home.updateVariant');
    // Route::post('/home/{id}/variants/enable', [HomeController::class, 'enableVariants'])->name('home.enableVariants');
    // Route::post('/home/{id}/variants/disable', [HomeController::class, 'disableVariants'])->name('home.disableVariants');

    Route::get('/barang-keluar/surat-jalan-manual', [BarangKeluarController::class, 'barangKeluarManual'])->name('barangKeluar.surat.manual');
    Route::get('/barang-keluar/surat-jalan/{id}', [BarangKeluarController::class, 'barangKeluar'])->name('barangKeluar.surat'); 
    Route::resource('/member', MemberController::class)->names('member');
    Route::get('/search-member', [PeminjamanController::class, 'searchMember'])->name('search.member');
    Route::get('/peminjaman/search-member', [PeminjamanController::class, 'searchMember'])->name('peminjaman.searchMember');
    Route::get('/peminjaman/search-barang', [PeminjamanController::class, 'searchBarang'])->name('peminjaman.searchBarang');
});

// Route untuk admin
Route::group(['prefix' => 'admin', 'middleware' => ['auth', \App\Http\Middleware\AdminMiddleware::class]], function () {
    Route::get('/manage', [AdminManagementController::class, 'index'])->name('admin.manage');
    Route::get('/create', [AdminManagementController::class, 'create'])->name('admin.create');
    Route::post('/', [AdminManagementController::class, 'store'])->name('admin.store');
    Route::get('/{user}/edit', [AdminManagementController::class, 'edit'])->name('admin.edit');
    Route::put('/{user}', [AdminManagementController::class, 'update'])->name('admin.update');
    Route::delete('/{user}', [AdminManagementController::class, 'destroy'])->name('admin.destroy');
});