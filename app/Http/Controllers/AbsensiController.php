<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Absensi;
use App\Models\AbsensiInstruktur;
use App\Models\HomePrivate;
use App\Models\Keuangan;
use App\Models\Peserta;
use App\Models\PesertaHomePrivate;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class AbsensiController extends Controller
{
    //
   public function index(Jadwal $jadwal)
{
    // ===============================
    // AUTH GUARD
    // ===============================
    if (
        auth()->user()->role === 'instruktur' &&
        !$jadwal->instrukturs->contains(auth()->id())
    ) {
        abort(403);
    }

    // ===============================
    // SEKOLAH
    // ===============================
    if ($jadwal->jenis_jadwal === 'sekolah') {

        $pesertas = Peserta::where('sekolah_id', $jadwal->sekolah_id)
            ->where('status', 'aktif')
            ->get();

        $absensiMap = Absensi::where('jadwal_id', $jadwal->id)
            ->whereNotNull('peserta_id')
            ->get()
            ->keyBy('peserta_id');

    }
    // ===============================
    // HOME PRIVATE (1 HOME = 1 PESERTA)
    // ===============================
    else {

        $pesertas = HomePrivate::where('id', $jadwal->home_private_id)->get();

        $absensiMap = Absensi::where('jadwal_id', $jadwal->id)
            ->whereNotNull('home_private_id')
            ->get()
            ->keyBy('home_private_id');
    }

    // ===============================
    // ABSENSI INSTRUKTUR (JIKA INSTRUKTUR)
    // ===============================
    $absensiInstruktur = null;

    if (auth()->user()->isInstruktur()) {
        $absensiInstruktur = AbsensiInstruktur::where('jadwal_id', $jadwal->id)
            ->where('instruktur_id', auth()->id())
            ->whereDate('tanggal', $jadwal->tanggal_mulai)
            ->first();
    }

    return view('absensi.index', compact(
        'jadwal',
        'pesertas',
        'absensiMap',
        'absensiInstruktur'
    ));

}





    public function store(Request $request, Jadwal $jadwal)
{

        // ===============================
        // BATAS WAKTU ABSENSI
        // ===============================
        if (
            auth()->user()->isInstruktur() &&
            !$jadwal->isDalamJamAbsensi()
        ) {
            return back()->with('error', 'Absensi hanya bisa diisi saat jam pelajaran.');
        }
    DB::transaction(function () use ($request, $jadwal) {
        

        $tanggal = $jadwal->tanggal_mulai;

        foreach ($request->absensi ?? [] as $key => $data) {

            $status = $data['status'] ?? 'alfa';

            // ===============================
            // SEKOLAH
            // ===============================
            if ($jadwal->jenis_jadwal === 'sekolah') {

                Absensi::updateOrCreate(
                    [
                        'jadwal_id'  => $jadwal->id,
                        'peserta_id' => $key,
                        'tanggal'    => $tanggal,
                    ],
                    [
                        'status'           => $status,
                        'keterangan'       => $data['keterangan'] ?? null,
                        'home_private_id'  => null,
                    ]
                );

            }
            // ===============================
            // HOME PRIVATE
            // ===============================
            else {

                Absensi::updateOrCreate(
                    [
                        'jadwal_id'       => $jadwal->id,
                        'home_private_id' => $key,
                        'tanggal'         => $tanggal,
                    ],
                    [
                        'status'       => $status,
                        'keterangan'   => $data['keterangan'] ?? null,
                        'peserta_id'   => null,
                    ]
                );
            }
        }
    });

    return back()->with('success', 'Absensi peserta berhasil disimpan');
}



    public function rekapFilter(Request $request)
    {
        $user = auth()->user();

        // ===============================
        // DATA SEKOLAH UNTUK FILTER
        // ===============================
        if ($user->isAdmin()) {
            $sekolahs = Sekolah::orderBy('nama_sekolah')->get();
            $sekolahId = $request->sekolah_id;
        } else {
            // admin sekolah → terkunci
            $sekolahs = Sekolah::where('id', $user->sekolah_id)->get();
            $sekolahId = $user->sekolah_id;
        }

        // ===============================
        // QUERY DASAR ABSENSI
        // ===============================
        $query = Absensi::with([
            'peserta',        // peserta sekolah
            'homePrivate',    // peserta home private
            'jadwal.sekolah',
        ]);

        // ===============================
        // FILTER JENIS PESERTA
        // ===============================
        if ($request->filled('jenis_peserta')) {

            if ($request->jenis_peserta === 'sekolah') {
                $query->whereNotNull('peserta_id');
            }

            if ($request->jenis_peserta === 'home_private') {
                $query->whereNotNull('home_private_id');
            }
        }

        // ===============================
        // FILTER SEKOLAH
        // ===============================
        if ($sekolahId) {
            $query->whereHas('jadwal', function ($q) use ($sekolahId) {
                $q->where('sekolah_id', $sekolahId);
            });
        }

        // ===============================
        // FILTER TANGGAL ABSENSI
        // ===============================
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $query->whereBetween('tanggal', [
                $request->tanggal_mulai,
                $request->tanggal_selesai,
            ]);
        }

        // ===============================
        // SORTING AMAN
        // ===============================
        $absensis = $query
            ->orderBy('tanggal')
            ->orderBy('jadwal_id')
            ->orderByRaw('COALESCE(peserta_id, home_private_id)')
            ->get();

        return view('absensi.rekap-filter', compact(
            'sekolahs',
            'absensis',
            'sekolahId'
        ));
    }




    public function exportRekapPdf(Request $request)
    {
        $sekolahId      = $request->sekolah_id;
        $tanggalMulai   = $request->tanggal_mulai;
        $tanggalSelesai = $request->tanggal_selesai;

        $absensis = Absensi::with(['peserta', 'jadwal.sekolah'])
            ->when($sekolahId, function ($q) use ($sekolahId) {
                $q->whereHas('jadwal', function ($j) use ($sekolahId) {
                    $j->where('sekolah_id', $sekolahId);
                });
            })
            ->when($tanggalMulai && $tanggalSelesai, function ($q) use ($tanggalMulai, $tanggalSelesai) {
                $q->whereBetween('tanggal', [
                    $tanggalMulai,
                    $tanggalSelesai
                ]);
            })
            ->orderBy('tanggal')
            ->orderBy('peserta_id')
            ->get();

        return Pdf::loadView('absensi.rekap-filter-pdf', [
            'absensis'        => $absensis,
            'tanggal_mulai'   => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
        ])->stream('rekap-absensi.pdf');
    }

}
