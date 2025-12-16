<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Absensi</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #000; padding: 6px; }
        th { background-color: #f2f2f2; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    

    
<h2 class="text-center">REKAP ABSENSI PESERTA</h2>

<p>
    <strong>Periode Absensi:</strong>
    @if($tanggal_mulai && $tanggal_selesai)
        {{ \Carbon\Carbon::parse($tanggal_mulai)->format('d/m/Y') }}
        s/d
        {{ \Carbon\Carbon::parse($tanggal_selesai)->format('d/m/Y') }}
    @else
        -
    @endif
</p>



<hr>
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Peserta</th>
            <th>Sekolah</th>
            <th>Kegiatan</th>
            <th>Tanggal Absensi</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
    @foreach($absensis as $i => $a)
        <tr>
            <td class="text-center">{{ $i + 1 }}</td>
            <td>{{ $a->peserta->nama ?? '-' }}</td>
            <td>{{ $a->jadwal->sekolah->nama_sekolah ?? '-' }}</td>
            <td>{{ $a->jadwal->nama_kegiatan ?? '-' }}</td>
            <td class="text-center">
                {{ \Carbon\Carbon::parse($a->jadwal->tanggal_mulai)->format('d/m/Y') }}
            </td>
            <td class="text-center">
                {{ ucfirst($a->status) }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
<br><br>
<p>
    Dicetak pada: {{ now()->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}
</p>

</body>
</html>
