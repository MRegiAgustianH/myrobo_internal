<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AdminRaporVerifikasiController;
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SekolahController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomePrivateController;
use App\Http\Controllers\IndikatorKompetensiController;
use App\Http\Controllers\InstrukturRaporController;
use App\Http\Controllers\InstrukturRaporTugasController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\KompetensiController;
use App\Http\Controllers\MateriModulController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\RaporController;
use App\Http\Controllers\RaporTugasController;
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


});

Route::middleware('auth')->group(function () {

    // ================= AJAX LOAD KOMPETENSI & INDIKATOR =================
    Route::get(
        '/rapor/materi/{materi}/kompetensi',
        [RaporController::class, 'loadKompetensi']
    )->name('rapor.materi.kompetensi');

    // ================= PESERTA BY SEKOLAH =================
    Route::get(
        '/rapor/peserta/{sekolah}',
        [RaporController::class, 'pesertaBySekolah']
    )->name('rapor.peserta.bySekolah');

    // ================= RAPOR =================
    Route::get('/rapor/manajemen',
        [RaporController::class, 'manajemen']
    )->name('rapor.manajemen');

    Route::resource('rapor', RaporController::class)
        ->except(['index']);

    Route::get('/rapor/{rapor}/cetak',
        [RaporController::class, 'cetak']
    )->name('rapor.cetak');
});

Route::prefix('admin')->middleware(['auth'])->group(function () {

        Route::get('/rapor-tugas', 
            [RaporTugasController::class, 'index']
        )->name('admin.rapor-tugas.index');

        Route::post('/rapor-tugas', 
            [RaporTugasController::class, 'store']
        )->name('admin.rapor-tugas.store');

        Route::get('/rapor-tugas/{raporTugas}', 
            [RaporTugasController::class, 'show']
        )->name('admin.rapor-tugas.show');

        Route::post('/rapor-tugas/{raporTugas}/verifikasi',
            [RaporTugasController::class, 'verifikasi']
        )->name('admin.rapor-tugas.verifikasi');
    });

    Route::prefix('instruktur')->middleware(['auth','role:instruktur'])->group(function () {

        // Daftar tugas rapor
        Route::get('/rapor-tugas',
            [InstrukturRaporTugasController::class, 'index']
        )->name('instruktur.rapor-tugas.index');

        // Detail tugas rapor (list peserta)
        Route::get('/rapor-tugas/{raporTugas}',
            [InstrukturRaporTugasController::class, 'show']
        )->name('instruktur.rapor-tugas.show');

        // Form isi rapor (reuse _form.blade)
        Route::get(
            '/rapor-tugas/{raporTugas}/peserta/{peserta}/rapor',
            [InstrukturRaporController::class, 'edit']
        )->name('instruktur.rapor.edit');

        // Simpan / update rapor
        Route::post(
            '/rapor-tugas/{raporTugas}/peserta/{peserta}/rapor',
            [InstrukturRaporController::class, 'store']
        )->name('instruktur.rapor.store');

        // Submit rapor (ubah status ke submitted)
        Route::post(
            '/rapor/{rapor}/submit',
            [InstrukturRaporController::class, 'submit']
        )->name('instruktur.rapor.submit');

        Route::get(
            '/rapor/{rapor}/cetak',
            [RaporController::class, 'cetakInstruktur']
        )->name('instruktur.rapor.cetak');
});

Route::prefix('admin')
    ->middleware(['auth','role:admin'])
    ->group(function () {

        Route::get(
            '/rapor/{rapor}/verifikasi',
            [AdminRaporVerifikasiController::class, 'show']
        )->name('admin.rapor.verifikasi.show');

        Route::patch(
            '/rapor/{rapor}/verifikasi',
            [AdminRaporVerifikasiController::class, 'approve']
        )->name('admin.rapor.verifikasi.approve');

        Route::patch(
            '/rapor/{rapor}/revisi',
            [AdminRaporVerifikasiController::class, 'revision']
        )->name('admin.rapor.verifikasi.revision');

        Route::patch(
            '/admin/rapor-tugas/{raporTugas}/verifikasi-semua',
            [AdminRaporVerifikasiController::class, 'approveAll']
        )->name('admin.rapor.verifikasi.approveAll');


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

Route::middleware(['auth', 'role:admin'])->group(function () {

        Route::resource('tarif-gaji', \App\Http\Controllers\TarifGajiController::class)
            ->except(['show']);
            
    });

    Route::middleware(['auth', 'role:admin'])
    ->post('tarif-gaji/quick-store',
        [\App\Http\Controllers\TarifGajiController::class, 'quickStore']
    )->name('tarif-gaji.quick-store');



Route::middleware(['auth','role:admin'])->group(function () {

    Route::resource('users', UserController::class);
    Route::resource('home-private', HomePrivateController::class);

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
            
        Route::post('/jadwal/recurring', [JadwalController::class, 'storeRecurring'])
            ->name('jadwal.recurring');
    });
});

Route::middleware(['auth'])->group(function () {

    Route::get('/absensi/jadwal/{jadwal}', [AbsensiController::class, 'index'])
        ->name('absensi.index');

    Route::post('/absensi/jadwal/{jadwal}', [AbsensiController::class, 'store'])
        ->name('absensi.store');

    Route::post('jadwal/{jadwal}/absensi-instruktur', [\App\Http\Controllers\AbsensiInstrukturController::class, 'store'])->name('instruktur.absensi.store');

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

Route::prefix('admin')
    ->middleware(['auth'])
    ->group(function () {

    /* ================= MATERI ================= */
    Route::get('materi', [MateriController::class, 'index'])
        ->name('admin.materi.index');

    Route::post('materi', [MateriController::class, 'store'])
        ->name('admin.materi.store');

    Route::put('materi/{materi}', [MateriController::class, 'update'])
        ->name('admin.materi.update');

    Route::delete('materi/{materi}', [MateriController::class, 'destroy'])
        ->name('admin.materi.destroy');

    /* ================= MODUL ================= */
    Route::get('materi/{materi}/modul', [MateriModulController::class, 'index'])
        ->name('materi.modul.index');

    Route::post('materi/{materi}/modul', [MateriModulController::class, 'store'])
        ->name('materi.modul.store');

    Route::put('materi/modul/{modul}', [MateriModulController::class, 'update'])
        ->name('materi.modul.update');

    Route::delete('materi/modul/{modul}', [MateriModulController::class, 'destroy'])
        ->name('materi.modul.destroy');

    Route::get('materi/modul/{modul}/download', [MateriModulController::class, 'download'])
        ->name('materi.modul.download');

    Route::get('materi/modul/{modul}/preview', [MateriModulController::class, 'preview'])
        ->name('materi.modul.preview');

    /* ================= KOMPETENSI ================= */
    Route::prefix('materi/{materi}/kompetensi')->group(function () {

        Route::get('/', [KompetensiController::class, 'index'])
            ->name('materi.kompetensi.index');

        Route::post('/', [KompetensiController::class, 'store'])
            ->name('materi.kompetensi.store');


        Route::put('{kompetensi}', [KompetensiController::class, 'update'])
            ->name('materi.kompetensi.update');

        Route::delete('{kompetensi}', [KompetensiController::class, 'destroy'])
            ->name('materi.kompetensi.destroy');

        /* ========== INDIKATOR (NESTED PALING DALAM) ========== */
        Route::get('{kompetensi}/indikator', [IndikatorKompetensiController::class, 'index'])
            ->name('materi.kompetensi.indikator.index');

        Route::get('{kompetensi}/indikator/create', [IndikatorKompetensiController::class, 'create'])
            ->name('materi.kompetensi.indikator.create');

        Route::post('{kompetensi}/indikator', [IndikatorKompetensiController::class, 'store'])
            ->name('materi.kompetensi.indikator.store');

        Route::get('{kompetensi}/indikator/{indikator}/edit', [IndikatorKompetensiController::class, 'edit'])
            ->name('materi.kompetensi.indikator.edit');

        Route::put('{kompetensi}/indikator/{indikator}', [IndikatorKompetensiController::class, 'update'])
            ->name('materi.kompetensi.indikator.update');

        Route::delete('{kompetensi}/indikator/{indikator}', [IndikatorKompetensiController::class, 'destroy'])
            ->name('materi.kompetensi.indikator.destroy');
    });

});






require __DIR__.'/auth.php';