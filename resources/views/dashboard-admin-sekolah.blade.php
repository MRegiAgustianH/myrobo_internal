@extends('layouts.app')

@section('header')
Dashboard Admin Sekolah
@endsection

@section('content')

{{-- INFO SEKOLAH --}}
<div class="bg-teal-50 border border-teal-200
            rounded-xl shadow-sm p-5 mb-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <p class="text-xs text-teal-600 font-medium uppercase tracking-wide">Sekolah</p>
            <p class="text-lg font-bold text-teal-700">
                {{ Auth::user()->sekolah->nama_sekolah ?? '-' }}
            </p>
            <p class="text-xs text-teal-500 mt-1">
                Periode
                {{ \Carbon\Carbon::create()->month((int) $bulan)->translatedFormat('F') }}
                {{ $tahun }}
            </p>
        </div>
        <div class="p-3 rounded-lg bg-teal-500 shadow-lg">
            <i data-feather="home" class="w-5 h-5 text-white"></i>
        </div>
    </div>
</div>

{{-- RINGKASAN --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">

    {{-- ABSENSI --}}
    <div
        class="bg-blue-50 border border-blue-200
               rounded-xl shadow-sm p-5
               hover:shadow-lg hover:border-blue-300
               transition-all duration-300 group">

        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs text-blue-600 font-medium uppercase tracking-wide mb-1">Total Absensi</p>
                <p class="text-2xl font-bold text-blue-700">
                    {{ $rekapAbsensi }}
                </p>
            </div>
            <div
                class="p-3 rounded-lg bg-blue-500 shadow-lg
                       group-hover:scale-110 transition-transform duration-300 shrink-0">
                <i data-feather="clipboard" class="w-5 h-5 text-white"></i>
            </div>
        </div>

        <a href="{{ route('absensi.rekap.filter', ['sekolah_id' => Auth::user()->sekolah_id]) }}"
           class="inline-flex items-center gap-1 mt-3 text-xs text-blue-600 hover:text-blue-700 font-medium hover:underline">
            Lihat Rekap Absensi
            <i data-feather="arrow-right" class="w-3 h-3"></i>
        </a>
    </div>

    {{-- PEMBAYARAN --}}
    <div
        class="bg-emerald-50 border border-emerald-200
               rounded-xl shadow-sm p-5
               hover:shadow-lg hover:border-emerald-300
               transition-all duration-300 group">

        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs text-emerald-600 font-medium uppercase tracking-wide mb-1">Total Pembayaran</p>
                <p class="text-2xl font-bold text-emerald-700">
                    Rp {{ number_format($totalPembayaran, 0, ',', '.') }}
                </p>
            </div>
            <div
                class="p-3 rounded-lg bg-emerald-500 shadow-lg
                       group-hover:scale-110 transition-transform duration-300 shrink-0">
                <i data-feather="credit-card" class="w-5 h-5 text-white"></i>
            </div>
        </div>

        <a href="{{ route('pembayaran.rekap', ['sekolah_id' => Auth::user()->sekolah_id]) }}"
           class="inline-flex items-center gap-1 mt-3 text-xs text-emerald-600 hover:text-emerald-700 font-medium hover:underline">
            Lihat Rekap Pembayaran
            <i data-feather="arrow-right" class="w-3 h-3"></i>
        </a>
    </div>

</div>

{{-- CETAK LAPORAN --}}
<div class="bg-violet-50 border border-violet-200
            rounded-xl shadow-sm p-5">

    <h3 class="font-bold text-lg text-violet-700 mb-4 flex items-center gap-3">
        <div class="p-2 rounded-lg bg-violet-500 shadow-md">
            <i data-feather="file-text" class="w-4 h-4 text-white"></i>
        </div>
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
                  bg-violet-500 hover:bg-violet-600
                  text-white text-sm font-medium
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
                  bg-white border border-violet-200
                  hover:bg-violet-100
                  text-violet-700 text-sm font-medium
                  px-4 py-2.5 rounded-lg transition">
            <i data-feather="printer" class="w-4 h-4"></i>
            Cetak Rekap Pembayaran
        </a>

    </div>
</div>

@endsection
