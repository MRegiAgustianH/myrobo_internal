<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rapor Ekstrakurikuler</title>

    <style>
        @page {
            size: 210mm 330mm; /* F4 */
            margin: 15mm;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            line-height: 1.4;
        }

        .container {
            padding: 5px;
        }

        /* ===== HEADER ===== */
        table.header {
            width: 100%;
            border-collapse: collapse;
        }

        .title {
            font-weight: bold;
            font-size: 14pt;
            text-align: center;
        }

        .subtitle {
            font-size: 11pt;
            text-align: center;
        }

        .logo {
            width: 65px;
        }

        .header-hr {
            border: 0;
            border-top: 2px solid #000;
            margin: 8px 0 12px 0;
        }

        /* ===== NILAI BOX ===== */
        .nilai-box {
            border: 2px solid #000;
            width: 70px;
            height: 70px;
            border-collapse: collapse;
        }

        .nilai-title {
            border-bottom: 1px solid #000;
            font-size: 10pt;
            font-weight: bold;
            text-align: center;
            height: 20px;
            background-color: #2f75b5;
            color: #fff;
        }

        .nilai-huruf {
            font-size: 30pt;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }

        /* ===== INFO ===== */
        .info table {
            width: 100%;
            border-collapse: collapse;
        }

        .info td {
            padding: 3px 4px;
            vertical-align: top;
        }

        /* ===== BOX ===== */
        .box {
            border: 1px solid #000;
            padding: 6px;
            margin-bottom: 8px;
        }

        /* ===== TABEL PENILAIAN ===== */
        table.penilaian {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            page-break-inside: auto;
        }

        table.penilaian tr {
            page-break-inside: avoid;
        }

        table.penilaian th,
        table.penilaian td {
            border: 1px solid #000;
            padding: 4px;
            font-size: 10pt;
        }

        table.penilaian th {
            background: #2f75b5;
            color: #fff;
            text-align: center;
        }

        .center {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            font-family: DejaVu Sans;
        }

        .kesimpulan {
            border: 1px solid #000;
            padding: 6px;
        }

        /* ===== FOOTER ===== */
        .footer {
            margin-top: 30px;
            width: 100%;
        }

        .footer td {
            font-size: 11pt;
        }

        .ttd {
            width: 90px;
            margin-bottom: 4px;
        }
    </style>
</head>
<body>

<div class="container">

    {{-- ================= HEADER ================= --}}
    <table class="header">
        <tr>
            <td width="15%" valign="top">
                @php
                    $namaSekolah = strtolower($rapor->sekolah->nama_sekolah ?? '');
                @endphp

                @if(str_contains($namaSekolah, 'al azhar'))
                    <img src="{{ public_path('images/alazlogo.jpg') }}" class="logo">
                @elseif(str_contains($namaSekolah, 'islam kreatif'))
                    <img src="{{ public_path('images/iklogo.jpg') }}" class="logo">
                @endif
            </td>

            <td width="70%" valign="top" align="center">
                <div class="title">LAPORAN KEGIATAN EKSTRAKURIKULER</div>
                @php
                    $tahunSekarang = now()->year;
                    $tahunDepan = $tahunSekarang + 1;
                @endphp
                <div class="subtitle">
                    SEMESTER {{ $rapor->semester->nama_semester }}<br>
                    TAHUN AJARAN {{ $tahunSekarang }} - {{ $tahunDepan }}
                </div>
            </td>

            <td width="15%" valign="top" align="right">
                <img src="{{ public_path('images/applogo.png') }}" class="logo">
            </td>
        </tr>
    </table>

    <hr class="header-hr">

    {{-- ================= INFO SISWA ================= --}}
    <div class="info">
        <table>
            <tr>
                <td width="70%">
                    <table>
                        <tr>
                            <td width="30%">Nama</td>
                            <td width="5%">:</td>
                            <td width="65%">{{ $rapor->peserta->nama }}</td>
                        </tr>
                        <tr>
                            <td>Kelas</td>
                            <td>:</td>
                            <td>{{ $rapor->peserta->kelas }}</td>
                        </tr>
                        <tr>
                            <td>Ekstrakurikuler</td>
                            <td>:</td>
                            <td>{{ $rapor->ekstrakurikuler ?? 'Robotik' }}</td>
                        </tr>
                    </table>
                </td>

                <td width="15%" align="right" valign="top">
                    <table class="nilai-box">
                        <tr>
                            <td class="nilai-title">NILAI</td>
                        </tr>
                        <tr>
                            <td class="nilai-huruf">
                                {{ $rapor->nilai_akhir }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    {{-- ================= MATERI ================= --}}
    <div class="box">
        <strong>Materi yang diajarkan</strong><br>
        {{ $rapor->materi }}
    </div>

    {{-- ================= PENILAIAN ================= --}}
    <div class="box">
        <strong>Kemampuan Siswa</strong>

        <table class="penilaian">
            <thead>
                <tr>
                    <th rowspan="2" width="25%">Kompetensi</th>
                    <th rowspan="2" width="45%">Indikator</th>
                    <th colspan="3">Nilai</th>
                </tr>
                <tr>
                    <th>C</th>
                    <th>B</th>
                    <th>SB</th>
                </tr>
            </thead>
            <tbody>

            @php
                $grouped = $rapor->nilaiRapors->groupBy(fn($n) =>
                    $n->indikatorKompetensi->kompetensi->nama_kompetensi
                );
            @endphp

            @foreach($grouped as $kompetensi => $items)
                @php $rowspan = count($items); @endphp

                @foreach($items as $i => $item)
                @php $nilai = strtoupper($item->nilai); @endphp
                <tr>
                    @if($i === 0)
                    <td rowspan="{{ $rowspan }}" style="font-weight:bold;">
                        {{ $kompetensi }}
                    </td>
                    @endif

                    <td>{{ $item->indikatorKompetensi->nama_indikator }}</td>

                    <td class="center">{!! $nilai === 'C' ? '&#10003;' : '' !!}</td>
                    <td class="center">{!! $nilai === 'B' ? '&#10003;' : '' !!}</td>
                    <td class="center">{!! $nilai === 'SB' ? '&#10003;' : '' !!}</td>
                </tr>
                @endforeach
            @endforeach

            </tbody>
        </table>
    </div>

    {{-- ================= KESIMPULAN ================= --}}
    <div class="kesimpulan">
        <strong>Kesimpulan</strong><br>
        {{ $rapor->kesimpulan }}
    </div>

    {{-- ================= FOOTER ================= --}}
    <table class="footer">
        <tr>
            <td width="60%"></td>
            <td width="40%" align="center">
                Cianjur, {{ now()->translatedFormat('d F Y') }}<br><br>
                <img src="{{ public_path('images/ttd_gina.png') }}" class="ttd"><br>
                <strong>Gina Sunandar</strong>
            </td>
        </tr>
    </table>

</div>

</body>
</html>
