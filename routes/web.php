<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SekolahController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PembayaranController;


Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('sekolah', SekolahController::class);
    
    Route::get('/sekolah/{sekolah}/peserta', [PesertaController::class, 'bySekolah'])
    ->name('peserta.bySekolah');

    Route::resource('rapot', RapotController::class);
});

Route::middleware(['auth','role:admin'])->group(function () {

    Route::get('/sekolah/{sekolah}/peserta',
        [PesertaController::class,'bySekolah'])
        ->name('peserta.bySekolah');

    Route::post('/peserta',
        [PesertaController::class,'store'])
        ->name('peserta.store');

    Route::put('/peserta/{peserta}',
        [PesertaController::class,'update'])
        ->name('peserta.update');

    Route::delete('/peserta/{peserta}',
        [PesertaController::class,'destroy'])
        ->name('peserta.destroy');

    Route::post(
        '/sekolah/{sekolah}/peserta/import',
        [PesertaController::class, 'import']
    )->name('peserta.import');

    Route::get(
        '/peserta/template/download',
        [PesertaController::class, 'downloadTemplate']
    )->name('peserta.template.download');
    
});



Route::middleware(['auth','role:admin'])->group(function () {

    Route::resource('users', UserController::class);

});

Route::middleware(['auth','role:admin'])->group(function () {
    Route::resource('materi', MateriController::class)->except(['show']);
});

Route::middleware(['auth'])->group(function () {

    Route::get('/jadwal', [JadwalController::class, 'index'])
        ->name('jadwal.index');

    Route::middleware('role:admin')->group(function () {

        Route::post('/jadwal', [JadwalController::class, 'store'])
            ->name('jadwal.store');

        Route::put('/jadwal/{jadwal}', [JadwalController::class, 'update'])
            ->name('jadwal.update');

        Route::delete('/jadwal/{jadwal}', [JadwalController::class, 'destroy'])
            ->name('jadwal.destroy');
    });
});

Route::middleware(['auth'])->group(function () {

    Route::get('/absensi/jadwal/{jadwal}', [AbsensiController::class, 'index'])
        ->name('absensi.index');

    Route::post('/absensi/jadwal/{jadwal}', [AbsensiController::class, 'store'])
        ->name('absensi.store');

});


Route::get('/rekap-absensi/filter', [AbsensiController::class, 'rekapFilter'])
    ->name('absensi.rekap.filter');

Route::get('/absensi/rekap/export-pdf', 
    [AbsensiController::class, 'exportRekapPdf']
)->name('absensi.rekap.export-pdf');

Route::middleware(['auth'])->group(function () {

    // Input pembayaran bulanan
    Route::get('/pembayaran', [PembayaranController::class, 'index'])
        ->name('pembayaran.index');

    Route::post('/pembayaran', [PembayaranController::class, 'store'])
        ->name('pembayaran.store');

    // Rekap pembayaran
    Route::get('/pembayaran/rekap', [PembayaranController::class, 'rekap'])
        ->name('pembayaran.rekap');

    // Export PDF rekap
    Route::get('/pembayaran/rekap/export-pdf', [PembayaranController::class, 'exportRekapPdf'])
        ->name('pembayaran.rekap.export-pdf');

    Route::get('/pembayaran/invoice', [PembayaranController::class, 'invoiceForm'])
        ->name('pembayaran.invoice.form');

    Route::post('/pembayaran/invoice/check', [PembayaranController::class, 'checkInvoiceData'])
        ->name('pembayaran.invoice.check');

    Route::get('/pembayaran/invoice/pdf', [PembayaranController::class, 'invoicePdf'])
        ->name('pembayaran.invoice.pdf');
});




require __DIR__.'/auth.php';
