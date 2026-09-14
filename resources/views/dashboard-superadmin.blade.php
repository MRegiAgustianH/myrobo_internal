@extends('layouts.app')

@section('header')
Dashboard Superadmin - Monitoring Semua Cabang
@endsection

@section('content')

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    {{-- TOTAL CABANG --}}
    <div class="bg-purple-50 rounded-xl p-4 sm:p-4 sm:p-5 shadow-sm border border-purple-100 hover:shadow-lg hover:border-purple-200 transition-all duration-300 group">
        <div class="flex items-center justify-between gap-3">
            <div class="min-w-0">
                <p class="text-xs text-purple-600 font-medium uppercase tracking-wide mb-1">Total Cabang</p>
                <p class="text-2xl font-bold text-purple-700 leading-tight">
                    {{ $totalCabang }}
                </p>
            </div>
            <div class="p-3 rounded-lg bg-purple-500 shadow-lg group-hover:scale-110 transition-transform duration-300 shrink-0">
                <i data-feather="grid" class="w-5 h-5 text-white"></i>
            </div>
        </div>
    </div>

    {{-- TOTAL SEKOLAH --}}
    <div class="bg-teal-50 rounded-xl p-4 sm:p-5 shadow-sm border border-teal-100 hover:shadow-lg hover:border-teal-200 transition-all duration-300 group">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs text-teal-600 font-medium uppercase tracking-wide mb-1">Total Sekolah</p>
                <p class="text-2xl font-bold text-teal-700 leading-tight">
                    {{ $totalSekolah }}
                </p>
            </div>
            <div class="p-3 rounded-lg bg-teal-500 shadow-lg group-hover:scale-110 transition-transform duration-300 shrink-0">
                <i data-feather="home" class="w-5 h-5 text-white"></i>
            </div>
        </div>
    </div>

    {{-- TOTAL PESERTA --}}
    <div class="bg-blue-50 rounded-xl p-4 sm:p-5 shadow-sm border border-blue-100 hover:shadow-lg hover:border-blue-200 transition-all duration-300 group">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs text-blue-600 font-medium uppercase tracking-wide mb-1">Total Peserta</p>
                <p class="text-2xl font-bold text-blue-700 leading-tight">
                    {{ $totalPeserta }}
                </p>
            </div>
            <div class="p-3 rounded-lg bg-blue-500 shadow-lg group-hover:scale-110 transition-transform duration-300 shrink-0">
                <i data-feather="users" class="w-5 h-5 text-white"></i>
            </div>
        </div>
    </div>

    {{-- TOTAL INSTRUKTUR --}}
    <div class="bg-emerald-50 rounded-xl p-4 sm:p-5 shadow-sm border border-emerald-100 hover:shadow-lg hover:border-emerald-200 transition-all duration-300 group">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs text-emerald-600 font-medium uppercase tracking-wide mb-1">Total Instruktur</p>
                <p class="text-2xl font-bold text-emerald-700 leading-tight">
                    {{ $totalInstruktur }}
                </p>
            </div>
            <div class="p-3 rounded-lg bg-emerald-500 shadow-lg group-hover:scale-110 transition-transform duration-300 shrink-0">
                <i data-feather="user-check" class="w-5 h-5 text-white"></i>
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

<h3 class="text-base sm:text-lg font-semibold text-gray-700 mb-4">Monitoring per Cabang</h3>

<div class="overflow-x-auto bg-white rounded-xl shadow">
    <table class="w-full text-sm min-w-[640px]">
        <thead class="bg-gray-50 border-b">
            <tr class="text-gray-600 uppercase text-xs tracking-wider">
                <th class="px-3 sm:px-4 py-3 text-left">Cabang</th>
                <th class="px-3 sm:px-4 py-3 text-center">Kode</th>
                <th class="px-3 sm:px-4 py-3 text-center">Sekolah</th>
                <th class="px-3 sm:px-4 py-3 text-center">User</th>
                <th class="px-3 sm:px-4 py-3 text-center">Jadwal</th>
                <th class="px-3 sm:px-4 py-3 text-center">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y">
        @foreach($cabangs as $cb)
            <tr class="hover:bg-gray-50">
                <td class="px-3 sm:px-4 py-3 font-medium">
                    <div class="flex items-center gap-2">
                        @if($cb->logo)
                        <img src="{{ asset('storage/' . $cb->logo) }}" class="w-8 h-8 rounded object-contain shrink-0">
                        @endif
                        <span class="whitespace-nowrap">{{ $cb->nama_cabang }}</span>
                    </div>
                </td>
                <td class="px-3 sm:px-4 py-3 text-center font-mono">{{ $cb->kode_cabang }}</td>
                <td class="px-3 sm:px-4 py-3 text-center">{{ $cb->sekolahs_count }}</td>
                <td class="px-3 sm:px-4 py-3 text-center">{{ $cb->users_count }}</td>
                <td class="px-3 sm:px-4 py-3 text-center">{{ $cb->jadwals_count }}</td>
                <td class="px-3 sm:px-4 py-3 text-center">
                    @if($cb->is_aktif)
                    <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700 whitespace-nowrap">Aktif</span>
                    @else
                    <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700 whitespace-nowrap">Nonaktif</span>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

@endsection