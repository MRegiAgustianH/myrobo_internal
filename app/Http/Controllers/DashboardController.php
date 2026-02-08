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
use App\Models\RaporTugas;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user  = auth()->user();
        $bulan = now()->month;
        $tahun = now()->year;

        /* ==================================================
         | ADMIN SISTEM
         |================================================== */
        if ($user->role === 'admin') {

            $totalSekolah    = Sekolah::count();
            $totalPeserta    = Peserta::count();
            $totalInstruktur = User::where('role', 'instruktur')->count();
            $totalJadwal     = Jadwal::count();

            $pembayaranBelum = Pembayaran::where('status', 'belum')->count();
            $pembayaranLunas = Pembayaran::where('status', 'lunas')->count();

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

        /* ==================================================
         | ADMIN SEKOLAH
         |================================================== */
        if ($user->role === 'admin_sekolah') {

            $rekapAbsensi = Absensi::whereHas('jadwal', function ($q) use ($user) {
                $q->where('sekolah_id', $user->sekolah_id);
            })->count();

            $totalPembayaran = Pembayaran::where('sekolah_id', $user->sekolah_id)
                ->where('status', 'lunas')
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->sum('jumlah');

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

        /* ==================================================
         | INSTRUKTUR
         |================================================== */
        if ($user->role === 'instruktur') {

            $today = Carbon::today();

            $jadwalsHariIni = $user->jadwals()
                ->whereDate('tanggal_mulai', $today)
                ->with('sekolah')
                ->orderBy('jam_mulai')
                ->get();

            $jadwalsMingguan = $user->jadwals()
                ->whereBetween('tanggal_mulai', [
                    $today,
                    $today->copy()->addDays(7)
                ])
                ->with('sekolah')
                ->orderBy('tanggal_mulai')
                ->get();

            // ===== TUGAS RAPOR =====
            $tugasRapors = RaporTugas::with(['sekolah', 'semester'])
                ->withCount([
                    'rapors',
                    'rapors as rapors_selesai_count' => function ($q) {
                        $q->whereIn('status', ['submitted', 'approved']);
                    }
                ])
                ->where('instruktur_id', $user->id)
                ->whereIn('status', ['pending', 'in_progress'])
                ->orderBy('deadline')
                ->get();

            return view('dashboard-instruktur', compact(
                'jadwalsHariIni',
                'jadwalsMingguan',
                'tugasRapors'
            ));
        }

        /* ==================================================
         | ROLE TIDAK VALID
         |================================================== */
        abort(403);
    }
}
