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
use App\Models\Cabang;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\RekapAbsensiExport;
use Maatwebsite\Excel\Facades\Excel;
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
        $cabangs = Cabang::orderBy('nama_cabang')->get();
        $cabangId = null;

        if ($user->role === 'superadmin') {
            $cabangId = $request->cabang_id;
        } elseif ($user->role === 'admin_cabang' || $user->role === 'bendahara' || $user->role === 'sekretaris') {
            $cabangId = $user->cabang_id;
        }

        if (in_array($user->role, ['superadmin', 'admin', 'admin_cabang']) || $user->role === 'sekretaris' || $user->role === 'superadmin' || $user->role === 'admin_cabang' || $user->role === 'bendahara') {
            $sekolahQuery = Sekolah::orderBy('nama_sekolah');
            if ($cabangId) {
                $sekolahQuery->where('cabang_id', $cabangId);
            }
            $sekolahs = $sekolahQuery->get();
            $sekolahId = $request->sekolah_id;
        } else {
            // admin sekolah Ã¢â€ â€™ terkunci
            $sekolahs = Sekolah::where('id', $user->sekolah_id)->get();
            $sekolahId = $user->sekolah_id;
        }

        // ===============================
        // QUERY DASAR ABSENSI PESERTA
        // ===============================
        $queryPeserta = Absensi::with([
            'peserta',        // peserta sekolah
            'homePrivate',    // peserta home private
            'jadwal.sekolah',
        ]);

        // ===============================
        // QUERY DASAR ABSENSI INSTRUKTUR
        // ===============================
        $queryInstruktur = AbsensiInstruktur::with([
            'instruktur',
            'jadwal.sekolah'
        ]);


        // ===============================
        // FILTER JENIS PESERTA
        // ===============================
        if ($request->filled('jenis_peserta')) {

            if ($request->jenis_peserta === 'sekolah') {
                $queryPeserta->whereNotNull('peserta_id');
                // Untuk instruktur, filter berdasarkan jenis jadwal
                $queryInstruktur->whereHas('jadwal', function($q) {
                    $q->where('jenis_jadwal', 'sekolah');
                });
            }

            if ($request->jenis_peserta === 'home_private') {
                $queryPeserta->whereNotNull('home_private_id');
                 // Untuk instruktur, filter berdasarkan jenis jadwal
                 $queryInstruktur->whereHas('jadwal', function($q) {
                    $q->where('jenis_jadwal', 'home_private');
                });
            }
        }

        // ===============================
        // FILTER SEKOLAH
        // ===============================
        if ($sekolahId) {
            $queryPeserta->whereHas('jadwal', function ($q) use ($sekolahId) {
                $q->where('sekolah_id', $sekolahId);
            });
            $queryInstruktur->whereHas('jadwal', function ($q) use ($sekolahId) {
                $q->where('sekolah_id', $sekolahId);
            });
        }

        // ===============================
        // FILTER CABANG
        // ===============================
        if ($cabangId) {
            $queryPeserta->whereHas('jadwal', fn($q) => $q->where('cabang_id', $cabangId));
            $queryInstruktur->whereHas('jadwal', fn($q) => $q->where('cabang_id', $cabangId));
        }

        // ===============================
        // FILTER TANGGAL ABSENSI
        // ===============================
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $range = [$request->tanggal_mulai, $request->tanggal_selesai];
            $queryPeserta->whereBetween('tanggal', $range);
            $queryInstruktur->whereBetween('tanggal', $range);
        }

        // ===============================
        // SORTING & GET
        // ===============================
        $absensis = $queryPeserta
            ->orderBy('tanggal')
            ->orderBy('jadwal_id')
            ->orderByRaw('COALESCE(peserta_id, home_private_id)')
            ->paginate(15)
            ->withQueryString();

        $absensiInstrukturs = $queryInstruktur
            ->orderBy('tanggal')
            ->orderBy('jadwal_id')
            ->paginate(15)
            ->withQueryString();

        return view('absensi.rekap-filter', compact(
            'sekolahs',
            'absensis',
            'absensiInstrukturs',
            'sekolahId',
            'cabangs'
        ));
    }




    /* =====================================================
     | CRUD ABSENSI (ADMIN & SEKRETARIS)
     | Admin/sekretaris dapat mengubah / hapus absensi
     | ketika instruktur lupa atau salah input.
     ===================================================== */

    public function update(Request $request, Absensi $absensi)
    {
        if (!in_array(auth()->user()->role, ['superadmin', 'admin', 'admin_cabang', 'sekretaris'])) {
            abort(403);
        }

        $data = $request->validate([
            'status'     => 'required|in:hadir,izin,sakit,alfa',
            'keterangan' => 'nullable|string|max:255',
            'tanggal'    => 'required|date',
        ]);

        $absensi->update($data);

        return back()->with('success', 'Absensi peserta berhasil diperbarui.');
    }

    public function destroy(Absensi $absensi)
    {
        if (!in_array(auth()->user()->role, ['superadmin', 'admin', 'admin_cabang', 'sekretaris'])) {
            abort(403);
        }

        $absensi->delete();

        return back()->with('success', 'Absensi peserta berhasil dihapus.');
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


