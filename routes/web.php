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
use App\Http\Controllers\IndikatorKompetensiController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\KompetensiController;
use App\Http\Controllers\MateriModulController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\RaporController;
use App\Models\Rapor;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('sekolah', SekolahController::class);
    
    Route::get('/sekolah/{sekolah}/peserta', [PesertaController::class, 'bySekolah'])
    ->name('peserta.bySekolah');

    Route::get('/rapor/manajemen',[RaporController::class, 'manajemen'])->name('rapor.manajemen');

    Route::resource('rapor', RaporController::class)->except(['index']);

    Route::resource('kompetensi.indikator',IndikatorKompetensiController::class);

    Route::get('/rapor/{rapor}/cetak', [RaporController::class, 'cetak'])->name('rapor.cetak');

    Route::get('/rapor/peserta/{sekolah}',[RaporController::class, 'pesertaBySekolah'])->name('rapor.peserta.bySekolah');

});

Route::middleware(['auth','role:admin'])->group(function () {

   Route::get('/sekolah/{sekolah}/peserta',
            [PesertaController::class, 'index'])
            ->name('sekolah.peserta.index');

        Route::post('/sekolah/{sekolah}/peserta',
            [PesertaController::class, 'store'])
            ->name('sekolah.peserta.store');

        Route::patch('/peserta/{peserta}',
            [PesertaController::class, 'update'])
            ->name('peserta.update');

        Route::delete('/peserta/{peserta}',
            [PesertaController::class, 'destroy'])
            ->name('peserta.destroy');

        Route::post('/sekolah/{sekolah}/peserta/import',
            [PesertaController::class, 'import'])
            ->name('sekolah.peserta.import');

        Route::get('/peserta/template/download',
            [PesertaController::class, 'downloadTemplate'])
            ->name('peserta.template.download');

        Route::post(
            '/sekolah/{sekolah}/peserta/import',
            [PesertaController::class, 'import']
        )->name('peserta.import');

        Route::resource('keuangan', KeuanganController::class)->except(['show']);
        Route::get('keuangan/gaji-instruktur', [KeuanganController::class, 'gajiInstruktur'])->name('keuangan.gaji.instruktur');
        Route::post('keuangan/gaji-instruktur/bayar', [KeuanganController::class, 'bayarGajiInstruktur'])->name('keuangan.gaji.bayar');

});



Route::middleware(['auth','role:admin'])->group(function () {

    Route::resource('users', UserController::class);
    Route::resource('materi', MateriController::class)->except(['show']);
    Route::resource('kompetensi', KompetensiController::class);

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

// Route::get('/materi/modul/{modul}/download',[MateriModulController::class, 'download'])->name('materi.modul.download')->middleware('auth');
// Route::get('/materi/{materi}/modul', [MateriModulController::class, 'index'])->name('materi.modul.index');


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

Route::middleware('auth')->group(function () {

    Route::get('/materi/{materi}/modul', 
        [MateriModulController::class, 'index']
    )->name('materi.modul.index');

    Route::post('/materi/{materi}/modul', 
        [MateriModulController::class, 'store']
    )->name('materi.modul.store');

    Route::put('/materi/modul/{modul}', 
        [MateriModulController::class, 'update']
    )->name('materi.modul.update');

    Route::delete('/materi/modul/{modul}', 
        [MateriModulController::class, 'destroy']
    )->name('materi.modul.destroy');

    Route::get('/materi/modul/{modul}/download', 
        [MateriModulController::class, 'download']
    )->name('materi.modul.download');

    Route::get('/materi/modul/{modul}/preview',
        [MateriModulController::class, 'preview']
    )->name('materi.modul.preview')
    ->middleware('auth');

});




require __DIR__.'/auth.php';