@extends('layouts.app')

@section('header')
Dashboard Admin Sekolah
@endsection

@section('content')

{{-- INFO SEKOLAH --}}
<div class="bg-[#F6FAFB] border border-[#E3EEF0]
            rounded-2xl shadow-sm p-5 mb-6">
    <p class="text-xs text-gray-500">Sekolah</p>
    <p class="text-lg font-semibold text-gray-800">
        {{ Auth::user()->sekolah->nama_sekolah ?? '-' }}
    </p>
    <p class="text-xs text-gray-500 mt-1">
        Periode
        {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }}
        {{ $tahun }}
    </p>
</div>

{{-- RINGKASAN --}}
<div class="grid grid-cols-1 gap-4 mb-6">

    {{-- ABSENSI --}}
    <div
        class="bg-white border border-[#E3EEF0]
               rounded-2xl shadow-sm p-5">

        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Absensi</p>
                <p class="text-2xl font-bold text-gray-900">
                    {{ $rekapAbsensi }}
                </p>
            </div>
            <div
                class="w-10 h-10 rounded-full
                       bg-[#F6FAFB] border border-[#E3EEF0]
                       flex items-center justify-center">
                <i data-feather="clipboard" class="w-5 h-5 text-gray-500"></i>
            </div>
        </div>

        <a href="{{ route('absensi.rekap.filter', ['sekolah_id' => Auth::user()->sekolah_id]) }}"
           class="inline-flex items-center gap-1 mt-3 text-xs text-gray-600 hover:underline">
            Lihat Rekap Absensi
            <i data-feather="arrow-right" class="w-3 h-3"></i>
        </a>
    </div>

    {{-- PEMBAYARAN --}}
    <div
        class="bg-white border border-[#E3EEF0]
               rounded-2xl shadow-sm p-5">

        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Pembayaran</p>
                <p class="text-2xl font-bold text-gray-900">
                    Rp {{ number_format($totalPembayaran, 0, ',', '.') }}
                </p>
            </div>
            <div
                class="w-10 h-10 rounded-full
                       bg-[#F6FAFB] border border-[#E3EEF0]
                       flex items-center justify-center">
                <i data-feather="credit-card" class="w-5 h-5 text-gray-500"></i>
            </div>
        </div>

        <a href="{{ route('pembayaran.rekap', ['sekolah_id' => Auth::user()->sekolah_id]) }}"
           class="inline-flex items-center gap-1 mt-3 text-xs text-gray-600 hover:underline">
            Lihat Rekap Pembayaran
            <i data-feather="arrow-right" class="w-3 h-3"></i>
        </a>
    </div>

</div>

{{-- CETAK LAPORAN --}}
<div class="bg-[#F6FAFB] border border-[#E3EEF0]
            rounded-2xl shadow-sm p-5">

    <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
        <i data-feather="file-text" class="w-4 h-4 text-gray-500"></i>
        Laporan Bulanan
    </h3>

    <div class="flex flex-col gap-3">

        <a href="{{ route('absensi.rekap.export-pdf', [
                'sekolah_id' => Auth::user()->sekolah_id,
                'tanggal_mulai' => now()->startOfMonth()->toDateString(),
                'tanggal_selesai' => now()->endOfMonth()->toDateString()
            ]) }}"
           target="_blank"
           class="inline-flex items-center justify-center gap-2
                  bg-[#8FBFC2] hover:bg-[#6FA9AD]
                  text-gray-900 text-sm font-medium
                  px-4 py-2.5 rounded-lg transition">
            <i data-feather="printer" class="w-4 h-4"></i>
            Cetak Rekap Absensi
        </a>

        <a href="{{ route('pembayaran.rekap.export-pdf', [
                'sekolah_id' => Auth::user()->sekolah_id,
                'bulan' => $bulan,
                'tahun' => $tahun
            ]) }}"
           target="_blank"
           class="inline-flex items-center justify-center gap-2
                  bg-white border border-[#E3EEF0]
                  hover:bg-[#F6FAFB]
                  text-gray-800 text-sm font-medium
                  px-4 py-2.5 rounded-lg transition">
            <i data-feather="printer" class="w-4 h-4"></i>
            Cetak Rekap Pembayaran
        </a>

    </div>
</div>

@endsection
