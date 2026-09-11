<?php

namespace App\Exports;

use App\Models\Absensi;
use App\Models\AbsensiInstruktur;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RekapAbsensiExport implements FromView
{
    public function __construct(
        public ?int $sekolahId = null,
        public ?string $tanggalMulai = null,
        public ?string $tanggalSelesai = null,
        public ?string $jenisPeserta = null,
    ) {}

    public function view(): View
    {
        $queryPeserta = Absensi::with(['peserta', 'homePrivate', 'jadwal.sekolah']);

        if ($this->jenisPeserta === 'sekolah') {
            $queryPeserta->whereNotNull('peserta_id');
        } elseif ($this->jenisPeserta === 'home_private') {
            $queryPeserta->whereNotNull('home_private_id');
        }

        if ($this->sekolahId) {
            $queryPeserta->whereHas('jadwal', fn($q) => $q->where('sekolah_id', $this->sekolahId));
        }

        if ($this->tanggalMulai && $this->tanggalSelesai) {
            $queryPeserta->whereBetween('tanggal', [$this->tanggalMulai, $this->tanggalSelesai]);
        }

        $absensis = $queryPeserta->orderBy('tanggal')->orderByRaw('COALESCE(peserta_id, home_private_id)')->get();

        // Absensi instruktur
        $queryInstruktur = AbsensiInstruktur::with(['instruktur', 'jadwal.sekolah']);

        if ($this->jenisPeserta === 'sekolah') {
            $queryInstruktur->whereHas('jadwal', fn($q) => $q->where('jenis_jadwal', 'sekolah'));
        } elseif ($this->jenisPeserta === 'home_private') {
            $queryInstruktur->whereHas('jadwal', fn($q) => $q->where('jenis_jadwal', 'home_private'));
        }

        if ($this->sekolahId) {
            $queryInstruktur->whereHas('jadwal', fn($q) => $q->where('sekolah_id', $this->sekolahId));
        }

        if ($this->tanggalMulai && $this->tanggalSelesai) {
            $queryInstruktur->whereBetween('tanggal', [$this->tanggalMulai, $this->tanggalSelesai]);
        }

        $absensiInstrukturs = $queryInstruktur->orderBy('tanggal')->orderBy('jadwal_id')->get();

        return view('exports.rekap-absensi-excel', compact('absensis', 'absensiInstrukturs'));
    }
}
