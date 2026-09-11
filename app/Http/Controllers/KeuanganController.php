<?php

namespace App\Http\Controllers;

use App\Models\Keuangan;
use App\Models\Cabang;
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

        if ($user->role === 'admin_cabang') {
            $query->where('cabang_id', $user->cabang_id);
        } elseif ($user->role === 'superadmin' && request('cabang_id')) {
            $query->where('cabang_id', request('cabang_id'));
        }

        $pengeluarans = $query->paginate(15);
        $cabangs = Cabang::orderBy('nama_cabang')->get();

        return view('keuangan.index', compact('pengeluarans', 'cabangs'));
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
            'tanggal'   => 'required|date',
            'tipe'      => 'required|in:masuk,keluar',
            'kategori'  => 'required|string',
            'jumlah'    => 'required|numeric|min:0',
            'cabang_id' => 'nullable|exists:cabangs,id',
            'sekolah_id'=> 'nullable|exists:sekolahs,id',
        ]);

        $user = auth()->user();
        $cabangId = $request->cabang_id;
        
        if ($user->role === 'admin_cabang' || $user->role === 'bendahara' || $user->role === 'sekretaris') {
            $cabangId = $user->cabang_id;
        }

        Keuangan::create([
            'cabang_id'  => $cabangId ?? $user->cabang_id,
            'tanggal'    => $request->tanggal,
            'tipe'       => $request->tipe,
            'kategori'   => $request->kategori,
            'deskripsi'  => $request->deskripsi,
            'jumlah'     => $request->jumlah,
            'sekolah_id' => $request->sekolah_id,
        ]);

        $msg = $request->tipe === 'masuk' ? 'Pemasukan / Saldo awal berhasil disimpan.' : 'Pengeluaran berhasil ditambahkan.';

        return back()->with('success', $msg);
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
            'cabang_id'  => $request->cabang_id ?? $keuangan->cabang_id,
            'tanggal'    => $request->tanggal,
            'tipe'       => $request->tipe ?? $keuangan->tipe,
            'kategori'   => $request->kategori,
            'deskripsi'  => $request->deskripsi,
            'jumlah'     => $request->jumlah,
            'sekolah_id' => $request->sekolah_id ?? $keuangan->sekolah_id,
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

            $rincian = [];
            
            foreach ($absensis as $absen) {
                // Tarif sekarang spesifik per sekolah/home private
                $tarif = $absen->jadwal->tarifInstruktur();

                if ($tarif === 0) {
                    $adaTarifKosong = true;
                }

                $totalGaji += $tarif;

                // Buat key untuk grouping rincian
                // Contoh: "Sekolah (SDN 1)" atau "Home Private (Budi)"
                if ($absen->jadwal->jenis_jadwal === 'sekolah') {
                    $nama = $absen->jadwal->sekolah->nama_sekolah ?? 'Sekolah';
                    $key  = "Sekolah: $nama";
                } else {
                    $nama = $absen->jadwal->homePrivate->nama_peserta ?? 'Home Private';
                    $key  = "Private: $nama";
                }

                if (!isset($rincian[$key])) {
                    $rincian[$key] = ['count' => 0, 'subtotal' => 0];
                }
                $rincian[$key]['count']++;
                $rincian[$key]['subtotal'] += $tarif;
            }

            // Format rincian menjadi string untuk view
            // Contoh strings: ["Sekolah: SDN 1 (2x) = Rp 300.000", ...]
            $rincianStrings = [];
            foreach ($rincian as $k => $v) {
                $sub = number_format($v['subtotal'], 0, ',', '.');
                $rincianStrings[] = "$k ({$v['count']}x) : Rp $sub";
            }

            $instruktur->total_hadir = $absensis->count();
            $instruktur->total_gaji  = $totalGaji;
            $instruktur->tarif_valid = !$adaTarifKosong;
            $instruktur->rincian     = $rincianStrings; // Array of strings

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
    /*
    |==================================================
    | CASHFLOW (ARUS KAS MASUK & KELUAR)
    |==================================================
    */
    public function cashflow(Request $request)
    {
        $user = auth()->user();
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        $cabangs = Cabang::orderBy('nama_cabang')->get();
        $cabangId = null;

        if ($user->role === 'superadmin') {
            $cabangId = $request->cabang_id;
        } elseif (in_array($user->role, ['admin_cabang', 'bendahara', 'sekretaris'])) {
            $cabangId = $user->cabang_id;
        }

        // Base Query untuk total saldo & hitungan page ini
        $queryBase = Keuangan::query()
            ->when($cabangId, fn($q) => $q->where('cabang_id', $cabangId));

        // Hitung total saldo secara keseluruhan (all time)
        $totalMasukAll = (clone $queryBase)->where('tipe', 'masuk')->sum('jumlah');
        $totalKeluarAll = (clone $queryBase)->where('tipe', 'keluar')->sum('jumlah');
        $saldoAll = $totalMasukAll - $totalKeluarAll;

        // Query transaksi untuk bulan & tahun terpilih
        $query = Keuangan::with('sekolah')
            ->when($cabangId, fn($q) => $q->where('cabang_id', $cabangId))
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc');

        $transaksis = $query->paginate(15)->withQueryString();

        // Hitung total rekap bulanan
        $queryBulan = Keuangan::when($cabangId, fn($q) => $q->where('cabang_id', $cabangId))
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun);

        $totalMasukBulan = (clone $queryBulan)->where('tipe', 'masuk')->sum('jumlah');
        $totalKeluarBulan = (clone $queryBulan)->where('tipe', 'keluar')->sum('jumlah');
        $saldoBulan = $totalMasukBulan - $totalKeluarBulan;

        // Breakdown pemasukan per kategori (bulan ini)
        $masukDetail = (clone $queryBulan)
            ->where('tipe', 'masuk')
            ->selectRaw('kategori, SUM(jumlah) as total')
            ->groupBy('kategori')
            ->pluck('total', 'kategori')
            ->toArray();

        // Breakdown pengeluaran per kategori (bulan ini)
        $keluarDetail = (clone $queryBulan)
            ->where('tipe', 'keluar')
            ->selectRaw('kategori, SUM(jumlah) as total')
            ->groupBy('kategori')
            ->pluck('total', 'kategori')
            ->toArray();

        return view('keuangan.cashflow', compact(
            'transaksis', 'cabangs', 'cabangId', 'bulan', 'tahun',
            'totalMasukAll', 'totalKeluarAll', 'saldoAll',
            'totalMasukBulan', 'totalKeluarBulan', 'saldoBulan',
            'masukDetail', 'keluarDetail'
        ));
    }
}