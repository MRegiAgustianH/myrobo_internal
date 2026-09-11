<table border="1">
    <thead>
        <tr>
            <th colspan="8" style="background:#8FBFC2;font-size:14px;">REKAP PEMBAYARAN</th>
        </tr>
        <tr>
            <th>No</th>
            <th>Peserta</th>
            <th>Jenis</th>
            <th>Sekolah</th>
            <th>Periode</th>
            <th>Tanggal Bayar</th>
            <th>Jumlah</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @php $no = 1; @endphp
        @foreach($pembayarans as $p)
        @php
            $namaPeserta = $p->jenis_peserta === 'home_private'
                ? $p->homePrivate?->nama_peserta
                : $p->peserta?->nama;
            $namaSekolah = $p->jenis_peserta === 'home_private'
                ? 'Home Private'
                : ($p->sekolah?->nama_sekolah ?? '-');
            $periodeLabel = $p->isPeriode()
                ? $p->labelPeriode()
                : \Carbon\Carbon::create()->month((int) $p->bulan)->translatedFormat('F') . ' ' . $p->tahun;
        @endphp
        <tr>
            <td>{{ $no++ }}</td>
            <td>{{ $namaPeserta ?? '-' }}</td>
            <td>{{ str_replace('_', ' ', $p->jenis_peserta) }}</td>
            <td>{{ $namaSekolah }}</td>
            <td>{{ $periodeLabel }}</td>
            <td>{{ $p->tanggal_bayar?->format('d/m/Y') ?? '-' }}</td>
            <td>{{ $p->jumlah ? 'Rp ' . number_format($p->jumlah, 0, ',', '.') : '-' }}</td>
            <td>{{ strtoupper($p->status) }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="6" style="font-weight:bold;text-align:right;">TOTAL LUNAS</td>
            <td style="font-weight:bold;">Rp {{ number_format($totalLunas, 0, ',', '.') }}</td>
            <td></td>
        </tr>
    </tbody>
</table>
