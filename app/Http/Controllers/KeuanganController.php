<?php

namespace App\Http\Controllers;

use App\Models\Keuangan;
use App\Models\User;
use App\Models\AbsensiInstruktur;
use App\Models\HomePrivate;
use App\Models\Sekolah;
use App\Models\TarifGaji;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KeuanganController extends Controller
{
    /*
    |==================================================
    | LIST PENGELUARAN
    |==================================================
    */
    public function index()
    {
        $user = auth()->user();

        $query = Keuangan::where('tipe', 'keluar')
            ->orderBy('tanggal', 'desc');

        if ($user->role === 'admin_sekolah') {
            $query->where('sekolah_id', $user->sekolah_id);
        }

        $pengeluarans = $query->paginate(15);

        return view('keuangan.index', compact('pengeluarans'));
    }

    /*
    |==================================================
    | TAMBAH PENGELUARAN MANUAL
    |==================================================
    */
    public function create()
    {
        return view('keuangan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal'  => 'required|date',
            'kategori' => 'required|string',
            'jumlah'   => 'required|numeric|min:0',
        ]);

        Keuangan::create([
            'tanggal'    => $request->tanggal,
            'tipe'       => 'keluar',
            'kategori'   => $request->kategori,
            'deskripsi'  => $request->deskripsi,
            'jumlah'     => $request->jumlah,
            'sekolah_id' => auth()->user()->sekolah_id,
        ]);

        return redirect()
            ->route('keuangan.index')
            ->with('success', 'Pengeluaran berhasil ditambahkan');
    }

    /*
    |==================================================
    | EDIT & UPDATE
    |==================================================
    */
    public function edit(Keuangan $keuangan)
    {
        return view('keuangan.edit', compact('keuangan'));
    }

    public function update(Request $request, Keuangan $keuangan)
    {
        $request->validate([
            'tanggal'  => 'required|date',
            'kategori' => 'required|string',
            'jumlah'   => 'required|numeric|min:0',
        ]);

        $keuangan->update([
            'tanggal'   => $request->tanggal,
            'kategori'  => $request->kategori,
            'deskripsi' => $request->deskripsi,
            'jumlah'    => $request->jumlah,
        ]);

        return redirect()
            ->route('keuangan.index')
            ->with('success', 'Pengeluaran berhasil diperbarui');
    }

    /*
    |==================================================
    | HAPUS
    |==================================================
    */
    public function destroy(Keuangan $keuangan)
    {
        $keuangan->delete();

        return back()->with('success', 'Pengeluaran berhasil dihapus');
    }

    /*
    |==================================================
    | HALAMAN GAJI INSTRUKTUR
    |==================================================
    */

    public function gajiInstruktur(Request $request)
    {
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        $periode = sprintf('%04d-%02d', $tahun, $bulan);

        $user = auth()->user();

        // ===============================
        // QUERY INSTRUKTUR (SCOPE CABANG)
        // ===============================
        $instrukturQuery = User::where('role', 'instruktur');

        // admin sekolah hanya lihat instruktur cabangnya
        if ($user->isAdminSekolah()) {
            $instrukturQuery->where('sekolah_id', $user->sekolah_id);
        }

        $instrukturs = $instrukturQuery->get()
        ->map(function ($instruktur) use ($bulan, $tahun) {

            $absensis = AbsensiInstruktur::with('jadwal')
                ->where('instruktur_id', $instruktur->id)
                ->where('status', 'hadir')
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->get();

            $totalGaji = 0;
            $adaTarifKosong = false;

            foreach ($absensis as $absen) {
                $tarif = $absen->jadwal->tarifInstruktur();

                if ($tarif === 0) {
                    $adaTarifKosong = true;
                }

                $totalGaji += $tarif;
            }

            $instruktur->total_hadir = $absensis->count();
            $instruktur->total_gaji  = $totalGaji;
            $instruktur->tarif_valid = !$adaTarifKosong;

            return $instruktur;
        });


        // ===============================
        // CEK SUDAH DIBAYAR
        // ===============================
        $sudahDibayarIds = Keuangan::where([
                'tipe'        => 'keluar',
                'kategori'    => 'Gaji Instruktur',
                'periode'     => $periode,
                'sumber_type' => User::class,
            ])
            ->pluck('sumber_id')
            ->toArray();

        // ===============================
        // DATA UNTUK MODAL SET GAJI
        // ===============================
        // admin pusat -> semua
        // admin sekolah -> hanya cabangnya
        $sekolahQuery = Sekolah::orderBy('nama_sekolah');
        $homePrivateQuery = HomePrivate::orderBy('nama_peserta');

        if ($user->isAdminSekolah()) {
            $sekolahQuery->where('id', $user->sekolah_id);
            $homePrivateQuery->where('sekolah_id', $user->sekolah_id);
        }

        $sekolahs = $sekolahQuery->get();
        $homePrivates = $homePrivateQuery->get();

        // ===============================
        // MAP TARIF (AUTO LOAD KE MODAL)
        // ===============================
        $tarifMap = TarifGaji::all()
            ->keyBy(fn ($t) =>
                $t->jenis_jadwal.'-'.$t->sekolah_id.'-'.$t->home_private_id
            );

        return view('keuangan.gaji-instruktur', compact(
            'instrukturs',
            'bulan',
            'tahun',
            'periode',
            'sudahDibayarIds',
            'sekolahs',
            'homePrivates',
            'tarifMap'
        ));
    }


    /*
    |==================================================
    | BAYAR GAJI INSTRUKTUR (ANTI DOUBLE)
    |==================================================
    */
    public function bayarGajiInstruktur(Request $request)
    {
        $request->validate([
            'instruktur_id' => 'required|exists:users,id',
            'bulan'         => 'required|numeric|min:1|max:12',
            'tahun'         => 'required|numeric|min:2000',
        ]);

        $periode = sprintf('%04d-%02d', $request->tahun, $request->bulan);

        // ===============================
        // CEK SUDAH DIBAYAR?
        // ===============================
        $sudahDibayar = Keuangan::where([
            'tipe'        => 'keluar',
            'kategori'    => 'Gaji Instruktur',
            'sumber_id'   => $request->instruktur_id,
            'sumber_type' => User::class,
            'periode'     => $periode,
        ])->exists();

        if ($sudahDibayar) {
            return back()->with('error', 'Gaji instruktur untuk periode ini sudah dibayarkan.');
        }

        DB::transaction(function () use ($request, $periode) {

            $absensis = AbsensiInstruktur::with('jadwal')
                ->where('instruktur_id', $request->instruktur_id)
                ->where('status', 'hadir')
                ->whereMonth('tanggal', $request->bulan)
                ->whereYear('tanggal', $request->tahun)
                ->get();

            $totalGaji = 0;
            $detail = [];

            foreach ($absensis as $absen) {
                $tarif = $absen->jadwal->tarifInstruktur();
                $totalGaji += $tarif;

                $detail[] = "{$absen->jadwal->jenis_jadwal}: Rp".number_format($tarif);
            }

            Keuangan::create([
                'tanggal'     => now(),
                'tipe'        => 'keluar',
                'kategori'    => 'Gaji Instruktur',
                'periode'     => $periode,
                'deskripsi'   => "Gaji {$absensis->count()} pertemuan ({$periode})",
                'jumlah'      => $totalGaji,
                'sekolah_id'  => auth()->user()->sekolah_id,
                'sumber_id'   => $request->instruktur_id,
                'sumber_type' => User::class,
            ]);
        });


        return back()->with('success', 'Gaji instruktur berhasil dibayarkan');
    }
}
