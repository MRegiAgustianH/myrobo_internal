<?php

namespace App\Http\Controllers;

use App\Models\Peserta;
use App\Models\Sekolah;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PembayaranController extends Controller
{
    /**
     * ===============================
     * INPUT PEMBAYARAN BULANAN
     * ===============================
     */
    public function index(Request $request)
    {
        $sekolahId = $request->sekolah_id;
        $bulan     = $request->bulan ?? now()->month;
        $tahun     = $request->tahun ?? now()->year;

        $sekolahs = Sekolah::all();

        $pesertas = Peserta::when($sekolahId, fn ($q) =>
            $q->where('sekolah_id', $sekolahId)
        )->get();

        $pembayaranMap = Pembayaran::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->when($sekolahId, fn ($q) =>
                $q->where('sekolah_id', $sekolahId)
            )
            ->get()
            ->keyBy('peserta_id');

        return view('pembayaran.index', compact(
            'sekolahs',
            'pesertas',
            'pembayaranMap',
            'sekolahId',
            'bulan',
            'tahun'
        ));
    }

    /**
     * ===============================
     * SIMPAN PEMBAYARAN
     * ===============================
     */
    public function store(Request $request)
    {
        foreach ($request->pembayaran ?? [] as $pesertaId => $data) {

            $status = isset($data['status']) ? 'lunas' : 'belum';

            Pembayaran::updateOrCreate(
                [
                    'peserta_id' => $pesertaId,
                    'bulan'      => $request->bulan,
                    'tahun'      => $request->tahun,
                ],
                [
                    'sekolah_id'    => $data['sekolah_id'],
                    'status'        => $status,
                    'jumlah'        => $status === 'lunas'
                                        ? ($data['jumlah'] ?? 0)
                                        : null,
                    'tanggal_bayar' => $status === 'lunas'
                                        ? ($data['tanggal_bayar'] ?? now()->toDateString())
                                        : null,
                ]
            );
        }

        return back()->with('success', 'Pembayaran berhasil disimpan');
    }


    /**
     * ===============================
     * REKAP PEMBAYARAN
     * ===============================
     */
    public function rekap(Request $request)
    {
        $user = auth()->user();

        // ===============================
        // BULAN & TAHUN DEFAULT
        // ===============================
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        // ===============================
        // SEKOLAH (UNTUK FILTER)
        // ===============================
        if ($user->isAdmin()) {
            $sekolahs = Sekolah::orderBy('nama_sekolah')->get();
            $sekolahId = $request->sekolah_id; // bebas pilih
        } else {
            // admin_sekolah → kunci sekolah
            $sekolahs = Sekolah::where('id', $user->sekolah_id)->get();
            $sekolahId = $user->sekolah_id;
        }

        // ===============================
        // QUERY PEMBAYARAN
        // ===============================
        $pembayarans = Pembayaran::with(['peserta', 'sekolah'])
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->when($sekolahId, function ($q) use ($sekolahId) {
                $q->where('sekolah_id', $sekolahId);
            })
            ->orderBy('peserta_id')
            ->get();

        // ===============================
        // TOTAL LUNAS
        // ===============================
        $totalLunas = $pembayarans
            ->where('status', 'lunas')
            ->sum('jumlah');

        return view('pembayaran.rekap', compact(
            'sekolahs',
            'pembayarans',
            'sekolahId',
            'bulan',
            'tahun',
            'totalLunas'
        ));
    }

    /**
     * ===============================
     * EXPORT PDF REKAP
     * ===============================
     */
    public function exportRekapPdf(Request $request)
    {
        $sekolahId = $request->sekolah_id;
        $bulan     = $request->bulan;
        $tahun     = $request->tahun;

        $pembayarans = Pembayaran::with(['peserta', 'sekolah'])
            ->when($sekolahId, fn ($q) =>
                $q->where('sekolah_id', $sekolahId)
            )
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->orderBy('tanggal_bayar')
            ->get();

        $totalLunas = $pembayarans
            ->where('status', 'lunas')
            ->sum('jumlah');

        return Pdf::loadView('pembayaran.rekap-pdf', [
            'pembayarans' => $pembayarans,
            'bulan'       => $bulan,
            'tahun'       => $tahun,
            'totalLunas'  => $totalLunas,
        ])->stream('rekap-pembayaran.pdf');
    }



    public function invoiceForm()
    {
        return view('pembayaran.invoice-form', [
            'sekolahs' => Sekolah::all(),
        ]);
    }

    public function invoicePdf(Request $request)
    {
        $request->validate([
            'sekolah_id' => 'required|exists:sekolahs,id',
            'bulan'      => 'required|integer|min:1|max:12',
            'tahun'      => 'required|integer',
        ]);

        // ===============================
        // CAST AMAN UNTUK CARBON
        // ===============================
        $bulan = (int) $request->bulan;
        $tahun = (int) $request->tahun;

        $sekolah = Sekolah::findOrFail($request->sekolah_id);

        // ===============================
        // AMBIL PEMBAYARAN (LUNAS SAJA)
        // ===============================
        $pembayarans = Pembayaran::with('peserta')
            ->where('sekolah_id', $sekolah->id)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('status', 'lunas')
            ->orderBy('peserta_id')
            ->get();

        // ===============================
        // HITUNG TOTAL & JUMLAH PESERTA
        // ===============================
        $total = $pembayarans->sum('jumlah');
        $jumlahPeserta = $pembayarans->count();

        // ===============================
        // NAMA FILE AMAN
        // ===============================
        $filename = 'invoice-' .
            str_replace(' ', '-', strtolower($sekolah->nama_sekolah)) .
            "-{$bulan}-{$tahun}.pdf";

        return Pdf::loadView('pembayaran.invoice-pdf', [
            'sekolah'        => $sekolah,
            'pembayarans'    => $pembayarans,
            'bulan'          => $bulan,
            'tahun'          => $tahun,
            'total'          => $total,
            'jumlahPeserta'  => $jumlahPeserta,
        ])->stream($filename);
    }
}
