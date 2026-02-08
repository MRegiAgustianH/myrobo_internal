<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Pembayaran</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
        }

        h3 {
            margin-bottom: 4px;
        }

        p {
            margin: 2px 0 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        th, td {
            border: 1px solid #333;
            padding: 6px;
            vertical-align: middle;
        }

        th {
            background: #f0f0f0;
            text-align: center;
        }

        td {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .status-lunas {
            font-weight: bold;
            color: #0f766e;
        }

        .status-belum {
            font-weight: bold;
            color: #b91c1c;
        }
    </style>
</head>
<body>

<h3>Rekap Pembayaran</h3>

@php
    $bulanAngka = (int) $bulan;
@endphp

<p>
    <strong>Periode:</strong>
    {{ \Carbon\Carbon::create()->month($bulanAngka)->translatedFormat('F') }}
    {{ $tahun }}
</p>

<table>
<thead>
<tr>
    <th width="4%">No</th>
    <th width="26%">Peserta</th>
    <th width="12%">Jenis</th>
    <th width="22%">Sekolah</th>
    <th width="12%">Tanggal</th>
    <th width="14%">Jumlah</th>
    <th width="10%">Status</th>
</tr>
</thead>

<tbody>
@forelse($pembayarans as $i => $p)

@php
    $namaPeserta = $p->jenis_peserta === 'home_private'
        ? $p->homePrivate?->nama_peserta
        : $p->peserta?->nama;

    $namaSekolah = $p->jenis_peserta === 'home_private'
        ? 'Home Private'
        : ($p->sekolah?->nama_sekolah ?? '-');
@endphp

<tr>
    <td class="text-center">{{ $i + 1 }}</td>

    <td>{{ $namaPeserta ?? '-' }}</td>

    <td class="text-center">
        {{ $p->jenis_peserta === 'home_private' ? 'Home Private' : 'Sekolah' }}
    </td>

    <td>{{ $namaSekolah }}</td>

    <td class="text-center">
        {{ $p->tanggal_bayar
            ? $p->tanggal_bayar->format('d/m/Y')
            : '-' }}
    </td>

    <td class="text-right">
        Rp {{ number_format($p->jumlah ?? 0, 0, ',', '.') }}
    </td>

    <td class="text-center">
        <span class="{{ $p->status === 'lunas' ? 'status-lunas' : 'status-belum' }}">
            {{ strtoupper($p->status) }}
        </span>
    </td>
</tr>

@empty
<tr>
    <td colspan="7" class="text-center">
        Tidak ada data pembayaran
    </td>
</tr>
@endforelse
</tbody>
</table>

<p style="margin-top: 12px;">
    <strong>Total Lunas:</strong>
    Rp {{ number_format($totalLunas, 0, ',', '.') }}
</p>

</body>
</html>
