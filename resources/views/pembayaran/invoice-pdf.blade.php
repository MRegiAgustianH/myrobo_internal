<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice</title>
    {{-- CSS di sini --}}
    <style>
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 12px;
        color: #000;
        margin: 0;
        padding: 0;
    }

    .page {
        padding: 40px 50px;
        position: relative;
    }

    /* HEADER */
    .header {
        position: relative;
        margin-bottom: 30px;
    }

    .ink-splash {
        position: absolute;
        top: -80px;          /* geser ke atas */
        left: -90px;         /* geser ke kiri */
        width: 360px;        /* ukuran splash */
        height: auto;
        z-index: 1;
    }

    /* Jika perlu sedikit transparansi */
    .ink-splash {
        opacity: 0.95;
    }


    .logo {
        position: absolute;
        top: 10px;
        right: 20px;
        z-index: 10;
    }

    .logo img {
        height: 140px;
        max-width: 280px;
    }


    h1 {
        text-align: center;
        letter-spacing: 2px;
        margin: 30px 0 10px;
    }

    .meta {
        margin-bottom: 25px;
        line-height: 1.6;
    }

    /* TABLE */
    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        border: 1px solid #999;
        padding: 10px;
        text-align: center;
    }

    th {
        background: #f2f2f2;
        font-weight: bold;
    }

    .text-left {
        text-align: left;
    }

    .terbilang {
        border: 1px solid #999;
        border-top: none;
        padding: 8px;
        font-size: 11px;
        font-style: italic;
    }

    /* FOOTER INFO */
    .info {
        margin-top: 25px;
        line-height: 1.7;
    }

    .signature {
        margin-top: 50px;
        width: 200px;
        text-align: left;
    }

    .signature img {
        width: 120px;
        margin-bottom: 5px;
    }

    .footer-line {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 12px;
        width: 100%;
        background: linear-gradient(to right, #3b1fa3, #1ccad8);
    }
</style>

</head>
<body>

<div class="page">

    <img
    src="{{ public_path('images/ink.png') }}"
    class="ink-splash"
    alt="Ink Splash"
/>





    {{-- LOGO --}}
    <div class="logo">
        <img src="{{ public_path('images/applogo.png') }}">
    </div>
<br>
<br>
<br>
    <h1>INVOICE</h1>

    {{-- META --}}
    <div class="meta">
        <strong>Tanggal invoice:</strong>
        {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br><br>

        <strong>Ditagihkan kepada:</strong><br>
        {{ $sekolah->nama_sekolah }}
    </div>

    {{-- TABLE --}}
    <table>
        <thead>
            <tr>
                <th>Deskripsi</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $pembayarans->count() }} Orang</td>
                <td>Rp. {{ number_format($total, 0, ',', '.') }},-</td>
            </tr>
        </tbody>
    </table>

    {{-- TERBILANG --}}
    <div class="terbilang">
        <strong>Terbilang :</strong>
        {{ \App\Helpers\Terbilang::convert($total) }} Rupiah
    </div>

    {{-- INFO --}}
    <div class="info">
        <p><strong>Transfer via Bank SeaBank</strong></p>
        <p>Nomor Rekening : 901139158045 / Gina Sunandar</p>

        <p style="margin-top:15px;">
            No Kontak konfirmasi : 085797234126
        </p>
    </div>

    {{-- SIGNATURE --}}
    <div class="signature">
        <p><strong>Penanggung Jawab</strong></p>
        <img src="{{ public_path('images/signature.png') }}">
        <p><strong>Ray Pribadi</strong></p>
    </div>

</div>

<div class="footer-line"></div>

</body>
</html>
