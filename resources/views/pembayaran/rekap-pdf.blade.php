<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width:100%; border-collapse: collapse; }
        th, td { border:1px solid #333; padding:6px; }
        th { background:#eee; }
    </style>
</head>
<body>

<h3>Rekap Pembayaran</h3>
@php
    $bulanAngka = (int) $bulan;
@endphp

<p>
    Periode:
    {{ \Carbon\Carbon::create()->month($bulanAngka)->translatedFormat('F') }}
    {{ $tahun }}
</p>


<table>
<thead>
<tr>
    <th>No</th>
    <th>Peserta</th>
    <th>Sekolah</th>
    <th>Tanggal</th>
    <th>Jumlah</th>
    <th>Status</th>
</tr>
</thead>
<tbody>
@foreach($pembayarans as $i => $p)
<tr>
    <td>{{ $i+1 }}</td>
    <td>{{ $p->peserta->nama }}</td>
    <td>{{ $p->sekolah->nama_sekolah }}</td>
    <td>{{ \Carbon\Carbon::parse($p->tanggal_bayar)->format('d/m/Y') }}</td>
    <td>Rp {{ number_format($p->jumlah,0,',','.') }}</td>
    <td>{{ ucfirst($p->status) }}</td>
</tr>
@endforeach
</tbody>
</table>

<p><strong>Total Lunas:</strong>
Rp {{ number_format($totalLunas,0,',','.') }}</p>

</body>
</html>
