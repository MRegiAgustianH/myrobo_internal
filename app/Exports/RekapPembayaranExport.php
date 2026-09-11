<?php

namespace App\Exports;

use App\Models\Pembayaran;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RekapPembayaranExport implements FromView
{
    public function __construct(
        public ?int $sekolahId = null,
        public ?string $jenisPeserta = null,
        public ?int $bulan = null,
        public ?int $tahun = null,
        public ?string $tahunAjaran = null,
        public ?string $semester = null,
        public ?int $periode = null,
    ) {}

    public function view(): View
    {
        $query = Pembayaran::with(['peserta', 'homePrivate', 'sekolah']);

        // Mode periode vs bulanan
        if ($this->tahunAjaran) {
            $query->where('tahun_ajaran', $this->tahunAjaran);
            if ($this->semester) {
                $query->where('semester', $this->semester);
            }
            if ($this->periode) {
                $query->where('periode', $this->periode);
            }
        } else {
            if ($this->bulan) {
                $query->where('bulan', $this->bulan);
            }
            if ($this->tahun) {
                $query->where('tahun', $this->tahun);
            }
        }

        if ($this->jenisPeserta) {
            $query->where('jenis_peserta', $this->jenisPeserta);
        }

        if ($this->sekolahId && $this->jenisPeserta !== 'home_private') {
            $query->where('sekolah_id', $this->sekolahId);
        }

        $pembayarans = $query
            ->orderBy('jenis_peserta')
            ->orderByRaw('COALESCE(peserta_id, home_private_id)')
            ->get();

        $totalLunas = $pembayarans->where('status', 'lunas')->sum(fn($p) => (float) $p->jumlah);

        return view('exports.rekap-pembayaran-excel', compact('pembayarans', 'totalLunas'));
    }
}
