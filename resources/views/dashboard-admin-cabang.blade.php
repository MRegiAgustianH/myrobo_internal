@extends('layouts.app')

@section('header')
Dashboard Cabang {{ $cabang?->nama_cabang ?? '' }}
@endsection

@section('content')

@if($cabang)
<div class="bg-teal-500 rounded-xl shadow p-5 mb-6 text-white">
    <div class="flex items-center gap-4">
        @if($cabang->logo)
        <img src="{{ asset('storage/' . $cabang->logo) }}" class="w-16 h-16 rounded-xl object-contain bg-white/20 p-1">
        @endif
        <div>
            <h2 class="text-xl font-bold">{{ $cabang->nama_cabang }}</h2>
            <p class="text-sm text-white/80">Kode: {{ $cabang->kode_cabang }} - {{ $cabang->alamat ?? '' }}</p>
        </div>
    </div>
</div>
@endif

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    {{-- SEKOLAH --}}
    <div class="bg-teal-50 rounded-xl p-4 shadow-sm border border-teal-100 hover:shadow-lg hover:border-teal-200 transition-all duration-300 group">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs text-teal-600 font-medium uppercase tracking-wide mb-1">Sekolah</p>
                <p class="text-2xl font-bold text-teal-700 leading-tight">
                    {{ $totalSekolah }}
                </p>
            </div>
            <div class="p-3 rounded-lg bg-teal-500 shadow-lg group-hover:scale-110 transition-transform duration-300 shrink-0">
                <i data-feather="home" class="w-5 h-5 text-white"></i>
            </div>
        </div>
    </div>

    {{-- PESERTA --}}
    <div class="bg-blue-50 rounded-xl p-4 shadow-sm border border-blue-100 hover:shadow-lg hover:border-blue-200 transition-all duration-300 group">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs text-blue-600 font-medium uppercase tracking-wide mb-1">Peserta</p>
                <p class="text-2xl font-bold text-blue-700 leading-tight">
                    {{ $totalPeserta }}
                </p>
            </div>
            <div class="p-3 rounded-lg bg-blue-500 shadow-lg group-hover:scale-110 transition-transform duration-300 shrink-0">
                <i data-feather="users" class="w-5 h-5 text-white"></i>
            </div>
        </div>
    </div>

    {{-- INSTRUKTUR --}}
    <div class="bg-emerald-50 rounded-xl p-4 shadow-sm border border-emerald-100 hover:shadow-lg hover:border-emerald-200 transition-all duration-300 group">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs text-emerald-600 font-medium uppercase tracking-wide mb-1">Instruktur</p>
                <p class="text-2xl font-bold text-emerald-700 leading-tight">
                    {{ $totalInstruktur }}
                </p>
            </div>
            <div class="p-3 rounded-lg bg-emerald-500 shadow-lg group-hover:scale-110 transition-transform duration-300 shrink-0">
                <i data-feather="user-check" class="w-5 h-5 text-white"></i>
            </div>
        </div>
    </div>

    {{-- JADWAL --}}
    <div class="bg-violet-50 rounded-xl p-4 shadow-sm border border-violet-100 hover:shadow-lg hover:border-violet-200 transition-all duration-300 group">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs text-violet-600 font-medium uppercase tracking-wide mb-1">Jadwal</p>
                <p class="text-2xl font-bold text-violet-700 leading-tight">
                    {{ $totalJadwal }}
                </p>
            </div>
            <div class="p-3 rounded-lg bg-violet-500 shadow-lg group-hover:scale-110 transition-transform duration-300 shrink-0">
                <i data-feather="calendar" class="w-5 h-5 text-white"></i>
            </div>
        </div>
    </div>
</div>

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
                    Bulan {{ $tahun }}
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

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mb-8">
    {{-- LUNAS --}}
    <div class="bg-green-50 rounded-xl shadow-sm border border-green-100 hover:shadow-lg hover:border-green-200 transition-all duration-300 group">
        <div class="p-5">
            <h3 class="font-semibold text-gray-700 mb-4 flex items-center gap-3">
                <div class="p-2 rounded-lg bg-green-500 shadow-md">
                    <i data-feather="check-circle" class="w-4 h-4 text-white"></i>
                </div>
                Status Pembayaran - Lunas
            </h3>
            <div class="flex items-center justify-between gap-3">
                <span class="text-lg font-bold text-green-700">{{ $pembayaranLunas }}</span>
                <a href="{{ route('pembayaran.index') }}?status=lunas" class="text-sm text-green-600 hover:text-green-700 font-medium flex items-center gap-1">
                    Lihat Detail <i data-feather="chevron-right" class="w-4 h-4"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- BELUM LUNAS --}}
    <div class="bg-red-50 rounded-xl shadow-sm border border-red-100 hover:shadow-lg hover:border-red-200 transition-all duration-300 group">
        <div class="p-5">
            <h3 class="font-semibold text-gray-700 mb-4 flex items-center gap-3">
                <div class="p-2 rounded-lg bg-red-500 shadow-md">
                    <i data-feather="alert-circle" class="w-4 h-4 text-white"></i>
                </div>
                Status Pembayaran - Belum Lunas
            </h3>
            <div class="flex items-center justify-between gap-3">
                <span class="text-lg font-bold text-red-700">{{ $pembayaranBelum }}</span>
                <a href="{{ route('pembayaran.index') }}?status=belum" class="text-sm text-red-600 hover:text-red-700 font-medium flex items-center gap-1">
                    Lihat Detail <i data-feather="chevron-right" class="w-4 h-4"></i>
                </a>
            </div>
        </div>
    </div>
</div>

@endsection