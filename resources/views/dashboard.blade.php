@extends('layouts.app')

@section('header')
Dashboard Admin
@endsection

@section('content')


{{-- ================= SUMMARY CARDS ================= --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">

    {{-- SEKOLAH --}}
    <div class="bg-teal-50 rounded-xl p-4 shadow-sm border border-teal-100 hover:shadow-lg hover:border-teal-200 transition-all duration-300 group">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs text-teal-600 font-medium uppercase tracking-wide mb-1">Sekolah</p>
                <p class="text-2xl md:text-3xl font-bold text-teal-700 leading-tight">
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
                <p class="text-2xl md:text-3xl font-bold text-blue-700 leading-tight">
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
                <p class="text-2xl md:text-3xl font-bold text-emerald-700 leading-tight">
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
                <p class="text-2xl md:text-3xl font-bold text-violet-700 leading-tight">
                    {{ $totalJadwal }}
                </p>
            </div>
            <div class="p-3 rounded-lg bg-violet-500 shadow-lg group-hover:scale-110 transition-transform duration-300 shrink-0">
                <i data-feather="calendar" class="w-5 h-5 text-white"></i>
            </div>
        </div>
    </div>

    {{-- BELUM LUNAS --}}
    <div class="bg-red-50 rounded-xl p-4 shadow-sm border border-red-100 hover:shadow-lg hover:border-red-200 transition-all duration-300 group">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs text-red-600 font-medium uppercase tracking-wide mb-1">Belum Lunas</p>
                <p class="text-2xl md:text-3xl font-bold text-red-700 leading-tight">
                    {{ $pembayaranBelum }}
                </p>
            </div>
            <div class="p-3 rounded-lg bg-red-500 shadow-lg group-hover:scale-110 transition-transform duration-300 shrink-0">
                <i data-feather="alert-circle" class="w-5 h-5 text-white"></i>
            </div>
        </div>
    </div>

    {{-- LUNAS --}}
    <div class="bg-green-50 rounded-xl p-4 shadow-sm border border-green-100 hover:shadow-lg hover:border-green-200 transition-all duration-300 group">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs text-green-600 font-medium uppercase tracking-wide mb-1">Lunas</p>
                <p class="text-2xl md:text-3xl font-bold text-green-700 leading-tight">
                    {{ $pembayaranLunas }}
                </p>
            </div>
            <div class="p-3 rounded-lg bg-green-500 shadow-lg group-hover:scale-110 transition-transform duration-300 shrink-0">
                <i data-feather="check-circle" class="w-5 h-5 text-white"></i>
            </div>
        </div>
    </div>

</div>

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

{{-- ================= QUICK ACCESS / MANAGEMENT ================= --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

    {{-- MANAJEMEN SEKOLAH --}}
    <div class="bg-teal-50 rounded-xl shadow-sm border border-teal-200 hover:shadow-xl hover:border-teal-300 transition-all duration-300 group">
        <div class="p-5 sm:p-6">
            <h3 class="font-bold text-base sm:text-lg text-teal-700 mb-4 flex items-center gap-3">
                <div class="p-2 rounded-lg bg-teal-500 shadow-md">
                    <i data-feather="grid" class="w-4 h-4 text-white"></i>
                </div>
                Manajemen Pelatihan
            </h3>

            <div class="space-y-2">
                <a href="{{ route('sekolah.index') }}"
                   class="flex justify-between items-center p-3 rounded-lg bg-white hover:bg-teal-500 hover:text-white transition-all duration-300 group/item">
                    <span class="font-medium">Kelola Sekolah</span>
                    <i data-feather="chevron-right" class="w-4 h-4 group-hover/item:translate-x-1 transition-transform"></i>
                </a>
                <a href="{{ route('home-private.index') }}"
                   class="flex justify-between items-center p-3 rounded-lg bg-white hover:bg-teal-500 hover:text-white transition-all duration-300 group/item">
                    <span class="font-medium">Kelola Home Private</span>
                    <i data-feather="chevron-right" class="w-4 h-4 group-hover/item:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- AKADEMIK --}}
    <div class="bg-blue-50 rounded-xl shadow-sm border border-blue-200 hover:shadow-xl hover:border-blue-300 transition-all duration-300 group">
        <div class="p-5 sm:p-6">
            <h3 class="font-bold text-base sm:text-lg text-blue-700 mb-4 flex items-center gap-3">
                <div class="p-2 rounded-lg bg-blue-500 shadow-md">
                    <i data-feather="book-open" class="w-4 h-4 text-white"></i>
                </div>
                Akademik
            </h3>

            <div class="space-y-2">
                <a href="{{ route('admin.materi.index') }}"
                   class="flex justify-between items-center p-3 rounded-lg bg-white hover:bg-blue-500 hover:text-white transition-all duration-300 group/item">
                    <span class="font-medium">Materi</span>
                    <i data-feather="chevron-right" class="w-4 h-4 group-hover/item:translate-x-1 transition-transform"></i>
                </a>

                <a href="{{ route('jadwal.index') }}"
                   class="flex justify-between items-center p-3 rounded-lg bg-white hover:bg-blue-500 hover:text-white transition-all duration-300 group/item">
                    <span class="font-medium">Jadwal</span>
                    <i data-feather="chevron-right" class="w-4 h-4 group-hover/item:translate-x-1 transition-transform"></i>
                </a>

                <a href="{{ route('admin.rapor-tugas.index') }}"
                   class="flex justify-between items-center p-3 rounded-lg bg-white hover:bg-blue-500 hover:text-white transition-all duration-300 group/item">
                    <span class="font-medium">Rapor</span>
                    <i data-feather="chevron-right" class="w-4 h-4 group-hover/item:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- KEUANGAN --}}
    <div class="bg-violet-50 rounded-xl shadow-sm border border-violet-200 hover:shadow-xl hover:border-violet-300 transition-all duration-300 group">
        <div class="p-5 sm:p-6">
            <h3 class="font-bold text-base sm:text-lg text-violet-700 mb-4 flex items-center gap-3">
                <div class="p-2 rounded-lg bg-violet-500 shadow-md">
                    <i data-feather="credit-card" class="w-4 h-4 text-white"></i>
                </div>
                Keuangan
            </h3>

            <div class="space-y-2">
                <a href="{{ route('pembayaran.index') }}"
                   class="flex justify-between items-center p-3 rounded-lg bg-white hover:bg-violet-500 hover:text-white transition-all duration-300 group/item">
                    <span class="font-medium">Pembayaran</span>
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
