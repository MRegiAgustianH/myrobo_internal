<table border="1">
    <thead>
        <tr>
            <th colspan="7" style="background:#8FBFC2;font-size:14px;">REKAP ABSENSI PESERTA</th>
        </tr>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Peserta</th>
            <th>Jenis</th>
            <th>Lokasi / Sekolah</th>
            <th>Kegiatan</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @php $no = 1; @endphp
        @foreach($absensis as $a)
        @php
            $namaPeserta = $a->isSekolah() ? $a->peserta?->nama : $a->homePrivate?->nama_peserta;
            $namaSekolah = $a->jadwal?->sekolah?->nama_sekolah ?? 'Home Private';
        @endphp
        <tr>
            <td>{{ $no++ }}</td>
            <td>{{ $a->tanggal?->format('d/m/Y') }}</td>
            <td>{{ $namaPeserta ?? '-' }}</td>
            <td>{{ $a->isSekolah() ? 'Sekolah' : 'Home Private' }}</td>
            <td>{{ $namaSekolah }}</td>
            <td>{{ $a->jadwal?->nama_kegiatan ?? '-' }}</td>
            <td>{{ ucfirst($a->status) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<br><br>

<table border="1">
    <thead>
        <tr>
            <th colspan="6" style="background:#8FBFC2;font-size:14px;">REKAP ABSENSI INSTRUKTUR</th>
        </tr>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Instruktur</th>
            <th>Lokasi / Sekolah</th>
            <th>Kegiatan</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @php $no = 1; @endphp
        @foreach($absensiInstrukturs as $ai)
        <tr>
            <td>{{ $no++ }}</td>
            <td>{{ $ai->tanggal->format('d/m/Y') }}</td>
            <td>{{ $ai->instruktur->name }}</td>
            <td>{{ $ai->jadwal->sekolah->nama_sekolah ?? 'Home Private' }}</td>
            <td>{{ $ai->jadwal->nama_kegiatan ?? '-' }}</td>
            <td>{{ ucfirst($ai->status) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
