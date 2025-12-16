<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Jadwal;
use App\Models\Absensi;
use App\Models\Peserta;
use App\Models\Sekolah;
use App\Models\Pembayaran;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // ======================
        // ADMIN
        // ======================
        if ($user->role === 'admin') {
            return view('dashboard', [
                'totalSekolah'    => Sekolah::count(),
                'totalPeserta'    => Peserta::count(),
                'totalInstruktur' => User::where('role', 'instruktur')->count(),
                'totalJadwal'     => Jadwal::count(),
                'pembayaranBelum' => Pembayaran::where('status', 'belum')->count(),
                'pembayaranLunas' => Pembayaran::where('status', 'lunas')->count(),
            ]);
        }elseif ($user->role === 'admin_sekolah') {

        $bulan = now()->month;
        $tahun = now()->year;

        $rekapAbsensi = Absensi::whereHas('jadwal', function ($q) use ($user) {
                $q->where('sekolah_id', $user->sekolah_id);
            })
            ->count();

        $totalPembayaran = Pembayaran::where('sekolah_id', $user->sekolah_id)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->sum('jumlah');

        return view('dashboard-admin-sekolah', compact(
            'rekapAbsensi',
            'totalPembayaran',
            'bulan',
            'tahun'
        ));
    }

        // ======================
        // INSTRUKTUR
        // ======================
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

        

    abort(403);
    }
    
}
