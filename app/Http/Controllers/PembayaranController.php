<?php

namespace App\Http\Controllers;

use App\Models\HomePrivate;
use App\Models\Peserta;
use App\Models\Sekolah;
use App\Models\Cabang;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\ValidationException;
use App\Models\Keuangan;
use Illuminate\Support\Facades\DB;
use App\Exports\RekapPembayaranExport;
use Maatwebsite\Excel\Facades\Excel;

class PembayaranController extends Controller
{
    /**
     * INPUT PEMBAYARAN
     */
    public function index(Request $request)
    {
        $mode         = $request->mode ?? 'bulanan';
        $user         = auth()->user();
        $sekolahId    = $request->sekolah_id;
        $jenisPeserta = $request->jenis_peserta;
        $statusFilter = $request->status; // lunas | belum | null (semua)

        // ADMIN SEKOLAH: kunci ke sekolahnya, tidak bisa lihat home private
        if ($user->role === 'admin_sekolah') {
            $sekolahId = $user->sekolah_id;
            $jenisPeserta = 'sekolah';
        }

        $cabangs = Cabang::orderBy('nama_cabang')->get();
        $cabangId = null;

        if ($user->role === 'superadmin') {
            $cabangId = $request->cabang_id;
        } elseif (in_array($user->role, ['admin_cabang', 'bendahara', 'sekretaris'])) {
            $cabangId = $user->cabang_id;
        }

        $sekolahQuery = Sekolah::orderBy('nama_sekolah');
        if ($cabangId) {
            $sekolahQuery->where('cabang_id', $cabangId);
        }
        $sekolahs = $sekolahQuery->get();
        $jumlahPeriode = $sekolahs->first()?->jumlah_periode ?? 6;
        $cabangFilter = $cabangId;

        // VARIABEL PERIODE
        $tahunAjaran = $request->tahun_ajaran ?? date('Y') . '/' . (date('Y') + 1);
        $semester    = $request->semester ?? 'ganjil';
        $periode     = $request->periode ?? 1;
        $bulan       = $request->bulan ?? now()->month;
        $tahun       = $request->tahun ?? now()->year;

        // PESERTA SEKOLAH
        $pesertaSekolah = Peserta::with('sekolah')
            ->when($jenisPeserta === 'home_private', fn ($q) => $q->whereRaw('1 = 0'))
            ->when($sekolahId, fn ($q) => $q->where('sekolah_id', $sekolahId))
            ->when($cabangFilter, fn ($q) => $q->whereHas('sekolah', fn($sq) => $sq->where('cabang_id', $cabangFilter)))
            ->orderBy('nama')
            ->get();

        // PESERTA HOME PRIVATE
        $homePrivates = HomePrivate::when($jenisPeserta === 'sekolah', fn ($q) => $q->whereRaw('1 = 0'))
            ->when($cabangFilter, fn ($q) => $q->where('cabang_id', $cabangFilter))
            ->orderBy('nama_peserta')
            ->get();

        // MAP PEMBAYARAN
        $query = Pembayaran::query();

        if ($mode === 'periode') {
            $query->where('tahun_ajaran', $tahunAjaran)
                  ->where('semester', $semester)
                  ->where('periode', $periode);
        } else {
            $query->where('bulan', $bulan)->where('tahun', $tahun);
        }

        $pembayaranMap = $query
            ->when($jenisPeserta, fn ($q) => $q->where('jenis_peserta', $jenisPeserta))
            ->get()
            ->keyBy(function ($p) {
                return $p->jenis_peserta === 'sekolah'
                    ? 'sekolah_' . $p->peserta_id
                    : 'home_' . $p->home_private_id;
            });

        // FILTER PESERTA BY STATUS PEMBAYARAN
        if ($statusFilter) {
            $pesertaSekolah = $pesertaSekolah->filter(function ($p) use ($pembayaranMap, $statusFilter) {
                $pay = $pembayaranMap['sekolah_' . $p->id] ?? null;
                $isLunas = $pay && $pay->status === 'lunas';
                return $statusFilter === 'lunas' ? $isLunas : !$isLunas;
            });
            $homePrivates = $homePrivates->filter(function ($hp) use ($pembayaranMap, $statusFilter) {
                $pay = $pembayaranMap['home_' . $hp->id] ?? null;
                $isLunas = $pay && $pay->status === 'lunas';
                return $statusFilter === 'lunas' ? $isLunas : !$isLunas;
            });
        }

        return view('pembayaran.index', compact(
            'sekolahs', 'pesertaSekolah', 'homePrivates', 'pembayaranMap',
            'sekolahId', 'bulan', 'tahun', 'jenisPeserta',
            'mode', 'tahunAjaran', 'semester', 'periode', 'jumlahPeriode', 'cabangs',
            'statusFilter'
        ));
    }

    /**
     * STORE PEMBAYARAN
     */
    public function store(Request $request)
    {
        $mode = $request->mode ?? 'bulanan';

        if ($mode === 'periode') {
            $request->validate([
                'tahun_ajaran' => 'required|string|max:15',
                'semester'     => 'required|in:ganjil,genap',
                'periode'      => 'required|integer|min:1',
            ]);
        } else {
            $request->validate([
                'bulan' => 'required|integer|min:1|max:12',
                'tahun' => 'required|integer|min:2020',
            ]);
        }

        DB::transaction(function () use ($request, $mode) {

            foreach ($request->pembayaran ?? [] as $id => $data) {

                $jenis   = $data['jenis'] ?? 'sekolah';
                $isLunas = isset($data['status']);
                $status  = $isLunas ? 'lunas' : 'belum';

                // FIX #2: nominal dari sekolah, bukan hardcoded
                if ($jenis === 'home_private') {
                    $default = 450000;
                    $cabangId = $data['cabang_id'] ?? null;
                } else {
                    $sekolah = Sekolah::find($data['sekolah_id'] ?? null);
                    $default = $sekolah?->nominal_pembayaran ?? 150000;
                    $cabangId = $sekolah?->cabang_id;
                }

                $inputJumlah = isset($data['jumlah']) ? (int) $data['jumlah'] : null;
                $jumlah = $isLunas ? ($inputJumlah ?: $default) : null;

                // UNIQUE KEY (ANTI DUPLIKASI)
                $where = [];

                if ($mode === 'periode') {
                    $where['tahun_ajaran'] = $request->tahun_ajaran;
                    $where['semester']     = $request->semester;
                    $where['periode']      = $request->periode;
                } else {
                    $where['bulan'] = $request->bulan;
                    $where['tahun'] = $request->tahun;
                }

                if ($jenis === 'sekolah') {
                    $where['peserta_id'] = $id;
                } else {
                    $where['home_private_id'] = $id;
                }

                // DATA CREATE
                $createData = [
                    'jenis_peserta' => $jenis,
                    'sekolah_id'    => $data['sekolah_id'] ?? null,
                    'status'        => $status,
                    'jumlah'        => $jumlah,
                    'tanggal_bayar' => $isLunas
                        ? ($data['tanggal_bayar'] ?? now()->toDateString())
                        : null,
                ];

                if ($mode === 'periode') {
                    $createData['tahun_ajaran'] = $request->tahun_ajaran;
                    $createData['semester']     = $request->semester;
                    $createData['periode']      = $request->periode;
                } else {
                    $createData['bulan'] = $request->bulan;
                    $createData['tahun'] = $request->tahun;
                }

                $pembayaran = Pembayaran::updateOrCreate($where, $createData);

                // SINKRON KEUANGAN (FIX #5: set cabang_id)
                if ($status === 'lunas') {
                    Keuangan::updateOrCreate(
                        [
                            'sumber_id'    => $pembayaran->id,
                            'sumber_type'  => Pembayaran::class,
                        ],
                        [
                            'cabang_id'  => $cabangId,
                            'tanggal'    => $pembayaran->tanggal_bayar,
                            'tipe'       => 'masuk',
                            'kategori'   => 'Pembayaran Peserta',
                            'deskripsi'  => 'Pembayaran ' . ucfirst(str_replace('_', ' ', $jenis)) . ' ID ' . $id,
                            'jumlah'     => $jumlah,
                            'sekolah_id' => $data['sekolah_id'] ?? null,
                        ]
                    );
                } else {
                    Keuangan::where('sumber_id', $pembayaran->id)
                        ->where('sumber_type', Pembayaran::class)
                        ->delete();
                }
            }
        });

        return back()->with('success', 'Pembayaran berhasil diperbarui');
    }

    /**
     * REKAP PEMBAYARAN
     */
    public function rekap(Request $request)
    {
        $user = auth()->user();

        $mode         = $request->mode ?? 'bulanan';
        $bulan        = $request->bulan ?? now()->month;
        $tahun        = $request->tahun ?? now()->year;
        $tahunAjaran  = $request->tahun_ajaran;
        $semester     = $request->semester;
        $periode      = $request->periode;
        $jenisPeserta = $request->jenis_peserta;

        // ADMIN SEKOLAH: hanya sekolahnya
        if ($user->role === 'admin_sekolah') {
            $jenisPeserta = 'sekolah';
        }

        $cabangs = Cabang::orderBy('nama_cabang')->get();
        $cabangId = null;

        // FIX #6: bendahara & sekretaris juga scoped per cabang
        if ($user->role === 'superadmin') {
            $cabangId = $request->cabang_id;
        } elseif (in_array($user->role, ['admin_cabang', 'bendahara', 'sekretaris'])) {
            $cabangId = $user->cabang_id;
        }

        // SEKOLAH (AKSES & FILTER)
        if ($user->role === 'admin_sekolah') {
            $sekolahs  = Sekolah::where('id', $user->sekolah_id)->get();
            $sekolahId = $user->sekolah_id;
        } else {
            $sekolahQuery = Sekolah::orderBy('nama_sekolah');
            if ($cabangId) {
                $sekolahQuery->where('cabang_id', $cabangId);
            }
            $sekolahs  = $sekolahQuery->get();
            $sekolahId = $request->sekolah_id;
        }

        // QUERY PEMBAYARAN
        $query = Pembayaran::with(['peserta', 'homePrivate', 'sekolah']);

        if ($mode === 'periode') {
            $query->when($tahunAjaran, fn ($q) => $q->where('tahun_ajaran', $tahunAjaran))
                  ->when($semester, fn ($q) => $q->where('semester', $semester))
                  ->when($periode, fn ($q) => $q->where('periode', $periode));
        } else {
            $query->where('bulan', $bulan)->where('tahun', $tahun);
        }

        // TOTAL LUNAS (sebelum paginate)
        $totalLunas = (clone $query)
            ->when($cabangId, fn($q) => $q->whereHas('sekolah', fn($sq) => $sq->where('cabang_id', $cabangId)))
            ->when($jenisPeserta, fn ($q) => $q->where('jenis_peserta', $jenisPeserta))
            ->when($sekolahId && $jenisPeserta !== 'home_private', fn ($q) => $q->where('sekolah_id', $sekolahId))
            ->where('status', 'lunas')
            ->sum('jumlah');

        $pembayarans = $query
            ->when($cabangId, fn($q) => $q->whereHas('sekolah', fn($sq) => $sq->where('cabang_id', $cabangId)))
            ->when($jenisPeserta, fn ($q) => $q->where('jenis_peserta', $jenisPeserta))
            ->when($sekolahId && $jenisPeserta !== 'home_private', fn ($q) => $q->where('sekolah_id', $sekolahId))
            ->orderBy('jenis_peserta')
            ->orderByRaw('COALESCE(peserta_id, home_private_id)')
            ->paginate(15)
            ->withQueryString();

        return view('pembayaran.rekap', compact(
            'sekolahs', 'pembayarans', 'sekolahId', 'bulan', 'tahun',
            'totalLunas', 'jenisPeserta', 'mode', 'tahunAjaran',
            'semester', 'periode', 'cabangs'
        ));
    }

    /**
     * EXPORT PDF REKAP (FIX #3: support mode periode)
     */
    public function exportRekapPdf(Request $request)
    {
        $user = auth()->user();
        $mode = $request->mode ?? 'bulanan';

        $bulan        = $request->bulan;
        $tahun        = $request->tahun;
        $tahunAjaran  = $request->tahun_ajaran;
        $semester     = $request->semester;
        $periode      = $request->periode;
        $jenisPeserta = $request->jenis_peserta;

        if ($user->role === 'admin_sekolah') {
            $jenisPeserta = 'sekolah';
        }

        $cabangId = null;
        if ($user->role === 'superadmin') {
            $cabangId = $request->cabang_id;
        } elseif (in_array($user->role, ['admin_cabang', 'bendahara', 'sekretaris'])) {
            $cabangId = $user->cabang_id;
        }

        if ($user->role === 'admin_sekolah') {
            $sekolahId = $user->sekolah_id;
        } else {
            $sekolahId = $request->sekolah_id;
        }

        $query = Pembayaran::with(['peserta', 'homePrivate', 'sekolah']);

        if ($mode === 'periode') {
            $query->when($tahunAjaran, fn ($q) => $q->where('tahun_ajaran', $tahunAjaran))
                  ->when($semester, fn ($q) => $q->where('semester', $semester))
                  ->when($periode, fn ($q) => $q->where('periode', $periode));
        } else {
            $query->where('bulan', $bulan)->where('tahun', $tahun);
        }

        $pembayarans = $query
            ->when($cabangId, fn($q) => $q->whereHas('sekolah', fn($sq) => $sq->where('cabang_id', $cabangId)))
            ->when($jenisPeserta, fn ($q) => $q->where('jenis_peserta', $jenisPeserta))
            ->when($sekolahId && $jenisPeserta !== 'home_private', fn ($q) => $q->where('sekolah_id', $sekolahId))
            ->orderBy('jenis_peserta')
            ->orderByRaw('COALESCE(peserta_id, home_private_id)')
            ->get();

        $totalLunas = $pembayarans->where('status', 'lunas')->sum(fn ($p) => (float) $p->jumlah);

        return Pdf::loadView('pembayaran.rekap-pdf', [
            'pembayarans' => $pembayarans,
            'bulan'       => $bulan,
            'tahun'       => $tahun,
            'totalLunas'  => $totalLunas,
            'jenisPeserta'=> $jenisPeserta,
            'sekolahId'   => $sekolahId,
            'mode'        => $mode,
            'tahunAjaran' => $tahunAjaran,
            'semester'    => $semester,
            'periode'     => $periode,
        ])->stream('rekap-pembayaran.pdf');
    }

    /**
     * EXPORT EXCEL REKAP
     */
    public function exportRekapExcel(Request $request)
    {
        return Excel::download(new RekapPembayaranExport(
            sekolahId: $request->sekolah_id ? (int) $request->sekolah_id : null,
            jenisPeserta: $request->jenis_peserta,
            bulan: $request->bulan ? (int) $request->bulan : null,
            tahun: $request->tahun ? (int) $request->tahun : null,
            tahunAjaran: $request->tahun_ajaran,
            semester: $request->semester,
            periode: $request->periode ? (int) $request->periode : null,
        ), 'rekap-pembayaran.xlsx');
    }

    /**
     * FORM INVOICE
     */
    public function invoiceForm()
    {
        $user = auth()->user();

        $cabangId = null;
        if (in_array($user->role, ['admin_cabang', 'bendahara', 'sekretaris'])) {
            $cabangId = $user->cabang_id;
        } elseif ($user->role === 'superadmin' && request('cabang_id')) {
            $cabangId = request('cabang_id');
        }

        $sekolahQuery = Sekolah::orderBy('nama_sekolah');
        if ($cabangId) {
            $sekolahQuery->where('cabang_id', $cabangId);
        }
        if ($user->role === 'admin_sekolah') {
            $sekolahQuery->where('id', $user->sekolah_id);
        }

        $hpQuery = HomePrivate::orderBy('nama_peserta');
        if ($cabangId) {
            $hpQuery->where('cabang_id', $cabangId);
        }

        return view('pembayaran.invoice-form', [
            'sekolahs'     => $sekolahQuery->get(),
            'homePrivates' => $hpQuery->get(),
        ]);
    }

    /**
     * CEK DATA INVOICE (AJAX) — FIX #4: support mode periode
     */
    public function checkInvoiceData(Request $request)
    {
        $data = $request->validate([
            'jenis_peserta'   => 'required|in:sekolah,home_private',
            'sekolah_id'      => 'required_if:jenis_peserta,sekolah|nullable|exists:sekolahs,id',
            'home_private_id' => 'required_if:jenis_peserta,home_private|nullable|exists:home_privates,id',
            'mode'            => 'required|in:bulanan,periode',
            'bulan'           => 'nullable|required_if:mode,bulanan|integer|min:1|max:12',
            'tahun'           => 'nullable|required_if:mode,bulanan|integer|min:2020',
            'tahun_ajaran'    => 'nullable|required_if:mode,periode|string|max:15',
            'semester'        => 'nullable|required_if:mode,periode|in:ganjil,genap',
            'periode'         => 'nullable|required_if:mode,periode|integer|min:1',
        ]);

        $query = Pembayaran::where('status', 'lunas')
            ->where('jenis_peserta', $data['jenis_peserta']);

        if ($data['mode'] === 'periode') {
            $query->where('tahun_ajaran', $data['tahun_ajaran'])
                  ->where('semester', $data['semester'])
                  ->where('periode', $data['periode']);
        } else {
            $query->where('bulan', $data['bulan'])
                  ->where('tahun', $data['tahun']);
        }

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

        return response()->json(['status' => 'ok']);
    }

    /**
     * CETAK INVOICE PDF — FIX #4: support mode periode
     */
    public function invoicePdf(Request $request)
    {
        $data = $request->validate([
            'jenis_peserta'   => 'required|in:sekolah,home_private',
            'sekolah_id'      => 'required_if:jenis_peserta,sekolah|nullable|exists:sekolahs,id',
            'home_private_id' => 'required_if:jenis_peserta,home_private|nullable|exists:home_privates,id',
            'mode'            => 'required|in:bulanan,periode',
            'bulan'           => 'nullable|required_if:mode,bulanan|integer|min:1|max:12',
            'tahun'           => 'nullable|required_if:mode,bulanan|integer|min:2020',
            'tahun_ajaran'    => 'nullable|required_if:mode,periode|string|max:15',
            'semester'        => 'nullable|required_if:mode,periode|in:ganjil,genap',
            'periode'         => 'nullable|required_if:mode,periode|integer|min:1',
        ]);

        $query = Pembayaran::where('status', 'lunas')
            ->where('jenis_peserta', $data['jenis_peserta']);

        $label = '';

        if ($data['mode'] === 'periode') {
            $query->where('tahun_ajaran', $data['tahun_ajaran'])
                  ->where('semester', $data['semester'])
                  ->where('periode', $data['periode']);
            $label = 'TA ' . $data['tahun_ajaran'] . ' ' . ucfirst($data['semester']) . ' Periode ' . $data['periode'];
        } else {
            $query->where('bulan', $data['bulan'])
                  ->where('tahun', $data['tahun']);
            $label = \Carbon\Carbon::create()->month((int) $data['bulan'])->translatedFormat('F') . ' ' . $data['tahun'];
        }

        // SEKOLAH
        if ($data['jenis_peserta'] === 'sekolah') {
            $sekolah = Sekolah::findOrFail($data['sekolah_id']);

            $pembayarans = $query->where('sekolah_id', $sekolah->id)
                ->with('peserta')
                ->orderBy('peserta_id')
                ->get();

            abort_if($pembayarans->isEmpty(), 404, 'Invoice tidak ditemukan');

            $total = $pembayarans->sum('jumlah');

            return Pdf::loadView('pembayaran.invoice-pdf', [
                'sekolah'     => $sekolah,
                'pembayarans' => $pembayarans,
                'label'       => $label,
                'total'       => $total,
            ])->stream('invoice-' . str_replace(' ', '-', strtolower($sekolah->nama_sekolah)) . '.pdf');
        }

        // HOME PRIVATE
        $homePrivate = HomePrivate::findOrFail($data['home_private_id']);

        $pembayarans = $query->where('home_private_id', $homePrivate->id)
            ->with('homePrivate')
            ->orderBy('tanggal_bayar')
            ->get();

        abort_if($pembayarans->isEmpty(), 404, 'Invoice Home Private tidak ditemukan');

        $total = $pembayarans->sum('jumlah');

        return Pdf::loadView('pembayaran.invoice-pdf', [
            'homePrivate' => $homePrivate,
            'pembayarans' => $pembayarans,
            'label'       => $label,
            'total'       => $total,
        ])->stream('invoice-home-private-' . str_replace(' ', '-', strtolower($homePrivate->nama_peserta)) . '.pdf');
    }
}