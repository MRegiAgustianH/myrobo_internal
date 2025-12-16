@extends('layouts.app')

@section('header')
Dashboard Admin Sekolah
@endsection

@section('content')

{{-- INFO SEKOLAH --}}
<div class="bg-white rounded-xl shadow p-4 mb-4">
    <p class="text-xs text-gray-500">Sekolah</p>
    <p class="text-lg font-semibold">
        {{ Auth::user()->sekolah->nama_sekolah ?? '-' }}
    </p>
    <p class="text-xs text-gray-500">
        Periode {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }}
        {{ $tahun }}
    </p>
</div>

{{-- RINGKASAN --}}
<div class="grid grid-cols-1 gap-4 mb-6">

    {{-- ABSENSI --}}
    <div class="bg-blue-50 border-l-4 border-blue-600 rounded-lg p-4">
        <p class="text-sm text-blue-700">Total Absensi</p>
        <p class="text-2xl font-bold text-blue-900">
            {{ $rekapAbsensi }}
        </p>

        <a href="{{ route('absensi.rekap.filter', ['sekolah_id' => Auth::user()->sekolah_id]) }}"
           class="inline-block mt-2 text-xs text-blue-700 underline">
            Lihat Rekap Absensi
        </a>
    </div>

    {{-- PEMBAYARAN --}}
    <div class="bg-green-50 border-l-4 border-green-600 rounded-lg p-4">
        <p class="text-sm text-green-700">Total Pembayaran</p>
        <p class="text-2xl font-bold text-green-900">
            Rp {{ number_format($totalPembayaran, 0, ',', '.') }}
        </p>

        <a href="{{ route('pembayaran.rekap', ['sekolah_id' => Auth::user()->sekolah_id]) }}"
           class="inline-block mt-2 text-xs text-green-700 underline">
            Lihat Rekap Pembayaran
        </a>
    </div>

</div>

{{-- CETAK LAPORAN --}}
<div class="bg-white rounded-xl shadow p-4">
    <p class="font-semibold mb-3">📄 Laporan Bulanan</p>

    <div class="flex flex-col gap-2">
        <a href="{{ route('absensi.rekap.export-pdf', [
                'sekolah_id' => Auth::user()->sekolah_id,
                'tanggal_mulai' => now()->startOfMonth()->toDateString(),
                'tanggal_selesai' => now()->endOfMonth()->toDateString()
            ]) }}"
           target="_blank"
           class="bg-blue-600 text-white text-sm px-4 py-2 rounded text-center">
            Cetak Rekap Absensi
        </a>

        <a href="{{ route('pembayaran.rekap.export-pdf', [
                'sekolah_id' => Auth::user()->sekolah_id,
                'bulan' => $bulan,
                'tahun' => $tahun
            ]) }}"
           target="_blank"
           class="bg-green-600 text-white text-sm px-4 py-2 rounded text-center">
            Cetak Rekap Pembayaran
        </a>
    </div>
</div>

@endsection
