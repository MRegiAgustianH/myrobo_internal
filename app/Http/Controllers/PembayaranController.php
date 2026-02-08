<?php

namespace App\Http\Controllers;

use App\Models\HomePrivate;
use App\Models\Peserta;
use App\Models\Sekolah;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\ValidationException;
use App\Models\Keuangan;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{
    /**
     * ===============================
     * INPUT PEMBAYARAN BULANAN
     * ===============================
     */
   public function index(Request $request)
    {
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        $sekolahId    = $request->sekolah_id;
        $jenisPeserta = $request->jenis_peserta; // sekolah | home_private | null

        $sekolahs = Sekolah::orderBy('nama_sekolah')->get();

        // ===============================
        // PESERTA SEKOLAH
        // ===============================
        $pesertaSekolah = Peserta::when($jenisPeserta === 'sekolah', fn ($q) => $q)
            ->when($jenisPeserta === 'home_private', fn ($q) => $q->whereRaw('1 = 0'))
            ->when($sekolahId, fn ($q) => $q->where('sekolah_id', $sekolahId))
            ->orderBy('nama')
            ->get();

        // ===============================
        // PESERTA HOME PRIVATE
        // ===============================
        $homePrivates = HomePrivate::when($jenisPeserta === 'home_private', fn ($q) => $q)
            ->when($jenisPeserta === 'sekolah', fn ($q) => $q->whereRaw('1 = 0'))
            ->orderBy('nama_peserta')
            ->get();

        // ===============================
        // MAP PEMBAYARAN (GABUNGAN)
        // ===============================
        $pembayaranMap = Pembayaran::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->when($jenisPeserta, fn ($q) =>
                $q->where('jenis_peserta', $jenisPeserta)
            )
            ->get()
            ->keyBy(function ($p) {
                return $p->jenis_peserta === 'sekolah'
                    ? 'sekolah_' . $p->peserta_id
                    : 'home_' . $p->home_private_id;
            });

        return view('pembayaran.index', compact(
            'sekolahs',
            'pesertaSekolah',
            'homePrivates',
            'pembayaranMap',
            'sekolahId',
            'bulan',
            'tahun',
            'jenisPeserta'
        ));
    }


    /**
     * ===============================
     * STORE
     * ===============================
     */
    public function store(Request $request)
    {
        // ===============================
        // VALIDASI DASAR
        // ===============================
        $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020',
        ]);

        DB::transaction(function () use ($request) {

            foreach ($request->pembayaran ?? [] as $id => $data) {

                // ===============================
                // JENIS PESERTA
                // ===============================
                $jenis   = $data['jenis'] ?? 'sekolah';
                $isLunas = isset($data['status']);

                $status = $isLunas ? 'lunas' : 'belum';
                $jumlah = $isLunas
                    ? ($jenis === 'home_private' ? 450000 : 150000)
                    : null;

                // ===============================
                // UNIQUE KEY QUERY (ANTI DUPLIKASI)
                // ===============================
                $where = [
                    'bulan' => $request->bulan,
                    'tahun' => $request->tahun,
                ];

                if ($jenis === 'sekolah') {
                    $where['peserta_id'] = $id;
                } else {
                    $where['home_private_id'] = $id;
                }

                // ===============================
                // SIMPAN / UPDATE PEMBAYARAN
                // ===============================
                $pembayaran = Pembayaran::updateOrCreate(
                    $where,
                    [
                        'jenis_peserta' => $jenis,
                        'sekolah_id'    => $data['sekolah_id'] ?? null,
                        'status'        => $status,
                        'jumlah'        => $jumlah,
                        'tanggal_bayar' => $isLunas
                            ? ($data['tanggal_bayar'] ?? now()->toDateString())
                            : null,
                    ]
                );

                // ===============================
                // SINKRON KEUANGAN
                // ===============================
                if ($status === 'lunas') {

                    Keuangan::updateOrCreate(
                        [
                            'sumber_id'   => $pembayaran->id,
                            'sumber_type'=> Pembayaran::class,
                        ],
                        [
                            'tanggal'    => $pembayaran->tanggal_bayar,
                            'tipe'       => 'masuk',
                            'kategori'   => 'Pembayaran Peserta',
                            'deskripsi'  => 'Pembayaran ' . ucfirst(str_replace('_',' ', $jenis)) . ' ID ' . $id,
                            'jumlah'     => $jumlah,
                            'sekolah_id' => $data['sekolah_id'] ?? null,
                        ]
                    );

                } else {
                    // jika batal lunas → hapus uang masuk
                    Keuangan::where('sumber_id', $pembayaran->id)
                        ->where('sumber_type', Pembayaran::class)
                        ->delete();
                }
            }
        });

        return back()->with('success', 'Pembayaran berhasil diperbarui');
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
        // BULAN & TAHUN
        // ===============================
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        // ===============================
        // FILTER JENIS PESERTA
        // ===============================
        $jenisPeserta = $request->jenis_peserta; // sekolah | home_private | null

        // ===============================
        // SEKOLAH (AKSES & FILTER)
        // ===============================
        if ($user->isAdmin()) {
            $sekolahs  = Sekolah::orderBy('nama_sekolah')->get();
            $sekolahId = $request->sekolah_id;
        } else {
            $sekolahs  = Sekolah::where('id', $user->sekolah_id)->get();
            $sekolahId = $user->sekolah_id;
        }

        // ===============================
        // QUERY PEMBAYARAN
        // ===============================
        $pembayarans = Pembayaran::with([
                'peserta',       // peserta sekolah
                'homePrivate',   // peserta home private
                'sekolah',
            ])
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)

            // filter jenis peserta
            ->when($jenisPeserta, function ($q) use ($jenisPeserta) {
                $q->where('jenis_peserta', $jenisPeserta);
            })

            // filter sekolah (hanya untuk sekolah)
            ->when($sekolahId && $jenisPeserta !== 'home_private', function ($q) use ($sekolahId) {
                $q->where('sekolah_id', $sekolahId);
            })

            // urutan rapi
            ->orderBy('jenis_peserta') // sekolah dulu, lalu home_private
            ->orderByRaw('COALESCE(peserta_id, home_private_id)')
            ->get();

        // ===============================
        // TOTAL PEMBAYARAN LUNAS
        // ===============================
        $totalLunas = $pembayarans
            ->where('status', 'lunas')
            ->sum(fn ($p) => (float) $p->jumlah);

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
        $user = auth()->user();

        // ===============================
        // PARAMETER
        // ===============================
        $bulan        = $request->bulan;
        $tahun        = $request->tahun;
        $jenisPeserta = $request->jenis_peserta; // sekolah | home_private | null

        // ===============================
        // SEKOLAH (AKSES)
        // ===============================
        if ($user->isAdmin()) {
            $sekolahId = $request->sekolah_id;
        } else {
            $sekolahId = $user->sekolah_id;
        }

        // ===============================
        // QUERY PEMBAYARAN
        // ===============================
        $pembayarans = Pembayaran::with([
                'peserta',
                'homePrivate',
                'sekolah',
            ])
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)

            // filter jenis peserta
            ->when($jenisPeserta, function ($q) use ($jenisPeserta) {
                $q->where('jenis_peserta', $jenisPeserta);
            })

            // filter sekolah (hanya sekolah, bukan home private)
            ->when($sekolahId && $jenisPeserta !== 'home_private', function ($q) use ($sekolahId) {
                $q->where('sekolah_id', $sekolahId);
            })

            // urutan rapi
            ->orderBy('jenis_peserta')
            ->orderByRaw('COALESCE(peserta_id, home_private_id)')
            ->get();

        // ===============================
        // TOTAL LUNAS
        // ===============================
        $totalLunas = $pembayarans
            ->where('status', 'lunas')
            ->sum(fn ($p) => (float) $p->jumlah);

        // ===============================
        // PDF
        // ===============================
        return Pdf::loadView('pembayaran.rekap-pdf', [
            'pembayarans' => $pembayarans,
            'bulan'       => $bulan,
            'tahun'       => $tahun,
            'totalLunas'  => $totalLunas,
            'jenisPeserta'=> $jenisPeserta,
            'sekolahId'   => $sekolahId,
        ])->stream('rekap-pembayaran.pdf');
    }


    /* =====================================================
     | FORM INVOICE (SEKOLAH & HOME PRIVATE)
     ===================================================== */
    public function invoiceForm()
    {
        return view('pembayaran.invoice-form', [
            'sekolahs'     => Sekolah::orderBy('nama_sekolah')->get(),
            'homePrivates' => HomePrivate::orderBy('nama_peserta')->get(),
        ]);
    }

    /* =====================================================
     | 🔎 CEK DATA INVOICE (AJAX)
     ===================================================== */
    public function checkInvoiceData(Request $request)
    {
        $data = $request->validate([
            'jenis_peserta'   => 'required|in:sekolah,home_private',
            'sekolah_id'      => 'required_if:jenis_peserta,sekolah|nullable|exists:sekolahs,id',
            'home_private_id' => 'required_if:jenis_peserta,home_private|nullable|exists:home_privates,id',
            'bulan'           => 'required|integer|min:1|max:12',
            'tahun'           => 'required|integer|min:2020',
        ]);

        $query = Pembayaran::where('bulan', $data['bulan'])
            ->where('tahun', $data['tahun'])
            ->where('status', 'lunas')
            ->where('jenis_peserta', $data['jenis_peserta']);

        if ($data['jenis_peserta'] === 'sekolah') {
            $query->where('sekolah_id', $data['sekolah_id']);
        } else {
            $query->where('home_private_id', $data['home_private_id']);
        }

        if ($query->count() === 0) {
            throw ValidationException::withMessages([
                'invoice' => ['Data pembayaran lunas tidak tersedia untuk periode tersebut.']
            ]);
        }

        return response()->json([
            'status' => 'ok'
        ]);
    }


    /* =====================================================
     | 🧾 CETAK INVOICE PDF
     ===================================================== */
    public function invoicePdf(Request $request)
    {
        $data = $request->validate([
            'jenis_peserta'   => 'required|in:sekolah,home_private',
            'sekolah_id'      => 'required_if:jenis_peserta,sekolah|nullable|exists:sekolahs,id',
            'home_private_id' => 'required_if:jenis_peserta,home_private|nullable|exists:home_privates,id',
            'bulan'           => 'required|integer|min:1|max:12',
            'tahun'           => 'required|integer|min:2020',
        ]);

        $bulan = (int) $data['bulan'];
        $tahun = (int) $data['tahun'];

        $query = Pembayaran::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('status', 'lunas')
            ->where('jenis_peserta', $data['jenis_peserta']);

        /* ================= SEKOLAH ================= */
        if ($data['jenis_peserta'] === 'sekolah') {

            $sekolah = Sekolah::findOrFail($data['sekolah_id']);

            $pembayarans = $query
                ->where('sekolah_id', $sekolah->id)
                ->with('peserta')
                ->orderBy('peserta_id')
                ->get();

            abort_if($pembayarans->isEmpty(), 404, 'Invoice tidak ditemukan');

            $total = $pembayarans->sum('jumlah');

            return Pdf::loadView('pembayaran.invoice-pdf', [
                'sekolah'     => $sekolah,
                'pembayarans' => $pembayarans,
                'bulan'       => $bulan,
                'tahun'       => $tahun,
                'total'       => $total,
            ])->stream(
                'invoice-' . str_replace(' ', '-', strtolower($sekolah->nama_sekolah)) .
                "-{$bulan}-{$tahun}.pdf"
            );
        }

        /* ================= HOME PRIVATE ================= */
        $homePrivate = HomePrivate::findOrFail($data['home_private_id']);

        $pembayarans = $query
            ->where('home_private_id', $homePrivate->id)
            ->with('homePrivate')
            ->orderBy('tanggal_bayar')
            ->get();

        abort_if($pembayarans->isEmpty(), 404, 'Invoice Home Private tidak ditemukan');

        $total = $pembayarans->sum('jumlah');

        return Pdf::loadView('pembayaran.invoice-pdf', [
            'homePrivate' => $homePrivate,
            'pembayarans' => $pembayarans,
            'bulan'       => $bulan,
            'tahun'       => $tahun,
            'total'       => $total,
        ])->stream(
            'invoice-home-private-' .
            str_replace(' ', '-', strtolower($homePrivate->nama_peserta)) .
            "-{$bulan}-{$tahun}.pdf"
        );
    }

}




