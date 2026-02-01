<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Jadwal;
use App\Models\Absensi;
use App\Models\Peserta;
use App\Models\Sekolah;
use App\Models\Pembayaran;
use App\Models\Keuangan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user  = auth()->user();
        $bulan = now()->month;
        $tahun = now()->year;

        /*
        |==================================================
        | ADMIN SISTEM
        |==================================================
        */
        if ($user->role === 'admin') {

            // ===== MASTER DATA =====
            $totalSekolah    = Sekolah::count();
            $totalPeserta    = Peserta::count();
            $totalInstruktur = User::where('role', 'instruktur')->count();
            $totalJadwal     = Jadwal::count();

            // ===== PEMBAYARAN =====
            $pembayaranBelum = Pembayaran::where('status', 'belum')->count();
            $pembayaranLunas = Pembayaran::where('status', 'lunas')->count();

            // ===== KEUANGAN BULAN INI =====
            $uangMasuk = Keuangan::where('tipe', 'masuk')
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->sum('jumlah');

            $uangKeluar = Keuangan::where('tipe', 'keluar')
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->sum('jumlah');

            $saldo = $uangMasuk - $uangKeluar;

            return view('dashboard', compact(
                'totalSekolah',
                'totalPeserta',
                'totalInstruktur',
                'totalJadwal',
                'pembayaranBelum',
                'pembayaranLunas',
                'uangMasuk',
                'uangKeluar',
                'saldo',
                'bulan',
                'tahun'
            ));
        }

        /*
        |==================================================
        | ADMIN SEKOLAH
        |==================================================
        */
        if ($user->role === 'admin_sekolah') {

            // ===== ABSENSI =====
            $rekapAbsensi = Absensi::whereHas('jadwal', function ($q) use ($user) {
                    $q->where('sekolah_id', $user->sekolah_id);
                })
                ->count();

            // ===== PEMBAYARAN SEKOLAH =====
            $totalPembayaran = Pembayaran::where('sekolah_id', $user->sekolah_id)
                ->where('status', 'lunas')
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->sum('jumlah');

            // ===== KEUANGAN SEKOLAH =====
            $uangMasuk = Keuangan::where('tipe', 'masuk')
                ->where('sekolah_id', $user->sekolah_id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->sum('jumlah');

            $uangKeluar = Keuangan::where('tipe', 'keluar')
                ->where('sekolah_id', $user->sekolah_id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->sum('jumlah');

            $saldo = $uangMasuk - $uangKeluar;

            return view('dashboard-admin-sekolah', compact(
                'rekapAbsensi',
                'totalPembayaran',
                'uangMasuk',
                'uangKeluar',
                'saldo',
                'bulan',
                'tahun'
            ));
        }

        /*
        |==================================================
        | INSTRUKTUR
        |==================================================
        */
        if ($user->role === 'instruktur') {

            $jadwalsHariIni = $user->jadwals()
                ->whereDate('tanggal_mulai', Carbon::today())
                ->with('sekolah')
                ->get();

            $jadwalsMingguan = $user->jadwals()
                ->whereBetween('tanggal_mulai', [
                    Carbon::today(),
                    Carbon::today()->addDays(7)
                ])
                ->with('sekolah')
                ->orderBy('tanggal_mulai')
                ->get();

            return view('dashboard-instruktur', compact(
                'jadwalsHariIni',
                'jadwalsMingguan'
            ));
        }

        /*
        |==================================================
        | ROLE TIDAK VALID
        |==================================================
        */
        abort(403);
    }
}
