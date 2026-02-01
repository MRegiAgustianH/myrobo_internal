<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Absensi;
use App\Models\AbsensiInstruktur;
use App\Models\Keuangan;
use App\Models\Peserta;
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
        // hanya instruktur terkait atau admin
        if (
            auth()->user()->role === 'instruktur' &&
            !$jadwal->instrukturs->contains(auth()->id())
        ) {
            abort(403);
        }

        $pesertas = Peserta::where('sekolah_id', $jadwal->sekolah_id)
            ->where('status', 'aktif')
            ->get();

        $absensiMap = Absensi::where('jadwal_id', $jadwal->id)
            ->get()
            ->keyBy('peserta_id');

        return view('absensi.index', compact(
            'jadwal',
            'pesertas',
            'absensiMap'
        ));
    }

        public function store(Request $request, Jadwal $jadwal)
    {
        DB::transaction(function () use ($request, $jadwal) {

            $tanggalAbsensi = $jadwal->tanggal_mulai;

            foreach ($request->absensi ?? [] as $pesertaId => $data) {
                Absensi::updateOrCreate(
                    [
                        'jadwal_id'  => $jadwal->id,
                        'peserta_id' => $pesertaId,
                        'tanggal'    => $tanggalAbsensi,
                    ],
                    [
                        'status'     => $data['status'] ?? 'alfa',
                        'keterangan' => $data['keterangan'] ?? null,
                    ]
                );
            }

            // ===============================
            // ABSENSI INSTRUKTUR (BARU)
            // ===============================
            foreach ($jadwal->instrukturs as $instruktur) {
                AbsensiInstruktur::updateOrCreate(
                    [
                        'jadwal_id'     => $jadwal->id,
                        'instruktur_id'=> $instruktur->id,
                        'tanggal'      => $tanggalAbsensi,
                    ],
                    [
                        'status' => 'hadir',
                    ]
                );
            }
        });

        return back()->with('success', 'Absensi peserta & instruktur berhasil disimpan');
    }


    public function rekapFilter(Request $request)
    {
        $user = auth()->user();

        // ===============================
        // DATA SEKOLAH UNTUK FILTER
        // ===============================
        if ($user->isAdmin()) {
            // admin → semua sekolah
            $sekolahs = Sekolah::orderBy('nama_sekolah')->get();
        } else {
            // admin_sekolah → hanya sekolahnya sendiri
            $sekolahs = Sekolah::where('id', $user->sekolah_id)->get();
        }

        // ===============================
        // QUERY DASAR
        // ===============================
        $query = Absensi::with([
            'peserta',
            'jadwal.sekolah'
        ]);

        // ===============================
        // FILTER SEKOLAH (WAJIB UNTUK ADMIN SEKOLAH)
        // ===============================
        if ($user->isAdminSekolah()) {
            // PAKSA sekolah sesuai user
            $query->whereHas('jadwal', function ($q) use ($user) {
                $q->where('sekolah_id', $user->sekolah_id);
            });
        } elseif ($request->filled('sekolah_id')) {
            // admin bebas memilih sekolah
            $query->whereHas('jadwal', function ($q) use ($request) {
                $q->where('sekolah_id', $request->sekolah_id);
            });
        }

        // ===============================
        // FILTER TANGGAL (BERDASARKAN TANGGAL JADWAL)
        // ===============================
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $query->whereHas('jadwal', function ($q) use ($request) {
                $q->whereBetween('tanggal_mulai', [
                    $request->tanggal_mulai,
                    $request->tanggal_selesai
                ]);
            });
        }

        // ===============================
        // AMBIL DATA
        // ===============================
        $absensis = $query
            ->orderBy('jadwal_id')
            ->orderBy('peserta_id')
            ->get();

        return view('absensi.rekap-filter', compact(
            'sekolahs',
            'absensis'
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

    public function bayarGaji(Request $request)
    {
        $instruktur = User::findOrFail($request->instruktur_id);

        $hadir = AbsensiInstruktur::where('instruktur_id', $instruktur->id)
            ->where('status', 'hadir')
            ->whereMonth('tanggal', $request->bulan)
            ->whereYear('tanggal', $request->tahun)
            ->count();

        $totalGaji = $hadir * 60000;

        Keuangan::create([
            'tanggal'    => now(),
            'tipe'       => 'keluar',
            'kategori'   => 'Gaji Instruktur',
            'deskripsi'  => "Gaji {$instruktur->name} ({$hadir} pertemuan)",
            'jumlah'     => $totalGaji,
            'sekolah_id' => $instruktur->sekolah_id,
        ]);

        return back()->with('success', 'Gaji instruktur berhasil dibayarkan');
    }



}
