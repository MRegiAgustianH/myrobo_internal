@extends('layouts.app')

@section('header')
Dashboard Bendahara
@endsection

@section('content')

{{-- ================= RINGKASAN KEUANGAN ================= --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 mb-8">

    {{-- UANG MASUK --}}
    <div class="bg-emerald-50 rounded-xl p-5 sm:p-6 shadow-sm border border-emerald-200 hover:shadow-xl hover:border-emerald-300 transition-all duration-300 group">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-xs text-emerald-600 font-semibold uppercase tracking-wide">
                    Uang Masuk
                </p>
                <p class="text-[10px] text-emerald-500 mt-1">
                    {{ \Carbon\Carbon::create()->month((int) $bulan)->translatedFormat('F') }} {{ $tahun }}
                </p>
            </div>
            <div class="p-3 rounded-lg bg-emerald-500 shadow-lg group-hover:scale-110 transition-transform duration-300 shrink-0">
                <i data-feather="arrow-down" class="w-5 h-5 text-white"></i>
            </div>
        </div>
        <p class="text-2xl md:text-3xl font-bold text-emerald-600">
            Rp {{ number_format($uangMasuk, 0, ',', '.') }}
        </p>
    </div>

    {{-- UANG KELUAR --}}
    <div class="bg-red-50 rounded-xl p-5 sm:p-6 shadow-sm border border-red-200 hover:shadow-xl hover:border-red-300 transition-all duration-300 group">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-xs text-red-600 font-semibold uppercase tracking-wide">
                    Uang Keluar
                </p>
                <p class="text-[10px] text-red-500 mt-1">
                    {{ \Carbon\Carbon::create()->month((int) $bulan)->translatedFormat('F') }} {{ $tahun }}
                </p>
            </div>
            <div class="p-3 rounded-lg bg-red-500 shadow-lg group-hover:scale-110 transition-transform duration-300 shrink-0">
                <i data-feather="arrow-up" class="w-5 h-5 text-white"></i>
            </div>
        </div>
        <p class="text-2xl md:text-3xl font-bold text-red-600">
            Rp {{ number_format($uangKeluar, 0, ',', '.') }}
        </p>
    </div>

    {{-- SALDO --}}
    <div class="bg-blue-50 rounded-xl p-5 sm:p-6 shadow-sm border border-blue-200 hover:shadow-xl hover:border-blue-300 transition-all duration-300 group">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-xs text-blue-600 font-semibold uppercase tracking-wide">
                    Saldo Bersih
                </p>
            </div>
            <div class="p-3 rounded-lg bg-blue-500 shadow-lg group-hover:scale-110 transition-transform duration-300 shrink-0">
                <i data-feather="dollar-sign" class="w-5 h-5 text-white"></i>
            </div>
        </div>
        <p class="text-2xl md:text-3xl font-bold {{ $saldo >= 0 ? 'text-blue-600' : 'text-red-600' }}">
            Rp {{ number_format($saldo, 0, ',', '.') }}
        </p>
    </div>

</div>

{{-- ================= STATUS PEMBAYARAN ================= --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-8">

    <a href="{{ route('pembayaran.index') }}?status=belum"
       class="bg-red-50 rounded-xl p-5 sm:p-6 shadow-sm border border-red-100 hover:shadow-lg hover:border-red-200 transition-all duration-300 group/item">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs text-red-600 font-medium uppercase tracking-wide mb-1">Belum Lunas</p>
                <p class="text-3xl font-bold text-red-700">
                    {{ $pembayaranBelum }}
                </p>
            </div>
            <div class="p-4 rounded-lg bg-red-500 shadow-lg group-hover/item:scale-110 transition-transform duration-300 shrink-0">
                <i data-feather="alert-circle" class="w-6 h-6 text-white"></i>
            </div>
        </div>
        <p class="text-xs text-red-500 mt-3 flex items-center gap-1">
            <i data-feather="chevron-right" class="w-3 h-3"></i> Klik untuk melihat detail
        </p>
    </a>

    <a href="{{ route('pembayaran.index') }}?status=lunas"
       class="bg-green-50 rounded-xl p-5 sm:p-6 shadow-sm border border-green-100 hover:shadow-lg hover:border-green-200 transition-all duration-300 group/item">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs text-green-600 font-medium uppercase tracking-wide mb-1">Lunas</p>
                <p class="text-3xl font-bold text-green-700">
                    {{ $pembayaranLunas }}
                </p>
            </div>
            <div class="p-4 rounded-lg bg-green-500 shadow-lg group-hover/item:scale-110 transition-transform duration-300 shrink-0">
                <i data-feather="check-circle" class="w-6 h-6 text-white"></i>
            </div>
        </div>
        <p class="text-xs text-green-500 mt-3 flex items-center gap-1">
            <i data-feather="chevron-right" class="w-3 h-3"></i> Klik untuk melihat detail
        </p>
    </a>

</div>

{{-- ================= QUICK ACCESS ================= --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <div class="bg-violet-50 rounded-xl shadow-sm border border-violet-200 hover:shadow-xl hover:border-violet-300 transition-all duration-300 group">
        <div class="p-5 sm:p-6">
            <h3 class="font-bold text-base sm:text-lg text-violet-700 mb-4 flex items-center gap-3">
                <div class="p-2 rounded-lg bg-violet-500 shadow-md">
                    <i data-feather="credit-card" class="w-4 h-4 text-white"></i>
                </div>
                Keuangan & Pembayaran
            </h3>

            <div class="space-y-2">
                <a href="{{ route('keuangan.cashflow') }}"
                   class="flex justify-between items-center p-3 rounded-lg bg-white hover:bg-violet-500 hover:text-white transition-all duration-300 group/item">
                    <span class="font-medium">Arus Kas (Cashflow)</span>
                    <i data-feather="chevron-right" class="w-4 h-4 group-hover/item:translate-x-1 transition-transform"></i>
                </a>

                <a href="{{ route('pembayaran.index') }}"
                   class="flex justify-between items-center p-3 rounded-lg bg-white hover:bg-violet-500 hover:text-white transition-all duration-300 group/item">
                    <span class="font-medium">Pembayaran</span>
                    <i data-feather="chevron-right" class="w-4 h-4 group-hover/item:translate-x-1 transition-transform"></i>
                </a>

                <a href="{{ route('pembayaran.invoice.form') }}"
                   class="flex justify-between items-center p-3 rounded-lg bg-white hover:bg-violet-500 hover:text-white transition-all duration-300 group/item">
                    <span class="font-medium">Cetak Invoice</span>
                    <i data-feather="chevron-right" class="w-4 h-4 group-hover/item:translate-x-1 transition-transform"></i>
                </a>

                <a href="{{ route('pembayaran.rekap') }}"
                   class="flex justify-between items-center p-3 rounded-lg bg-white hover:bg-violet-500 hover:text-white transition-all duration-300 group/item">
                    <span class="font-medium">Rekap Pembayaran</span>
                    <i data-feather="chevron-right" class="w-4 h-4 group-hover/item:translate-x-1 transition-transform"></i>
                </a>

                <a href="{{ route('keuangan.index') }}"
                   class="flex justify-between items-center p-3 rounded-lg bg-white hover:bg-violet-500 hover:text-white transition-all duration-300 group/item">
                    <span class="font-medium">Pengeluaran</span>
                    <i data-feather="chevron-right" class="w-4 h-4 group-hover/item:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </div>
    </div>

</div>

@endsection