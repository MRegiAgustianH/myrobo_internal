@extends('layouts.app')

@section('header')
Dashboard Admin
@endsection

@section('content')

{{-- ================= SUMMARY CARDS ================= --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-5 mb-8">

    {{-- SEKOLAH --}}
    <div class="bg-white rounded-xl p-5 shadow-sm border hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 uppercase">Sekolah</p>
                <p class="text-3xl font-semibold text-gray-800">
                    {{ $totalSekolah }}
                </p>
            </div>
            <div class="p-3 rounded-lg bg-[#8FBFC2]/20">
                <i data-feather="home" class="w-6 h-6 text-[#5a8f94]"></i>
            </div>
        </div>
    </div>

    {{-- PESERTA --}}
    <div class="bg-white rounded-xl p-5 shadow-sm border hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 uppercase">Peserta</p>
                <p class="text-3xl font-semibold text-gray-800">
                    {{ $totalPeserta }}
                </p>
            </div>
            <div class="p-3 rounded-lg bg-blue-100">
                <i data-feather="users" class="w-6 h-6 text-blue-600"></i>
            </div>
        </div>
    </div>

    {{-- INSTRUKTUR --}}
    <div class="bg-white rounded-xl p-5 shadow-sm border hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 uppercase">Instruktur</p>
                <p class="text-3xl font-semibold text-gray-800">
                    {{ $totalInstruktur }}
                </p>
            </div>
            <div class="p-3 rounded-lg bg-emerald-100">
                <i data-feather="user-check" class="w-6 h-6 text-emerald-600"></i>
            </div>
        </div>
    </div>

    {{-- JADWAL --}}
    <div class="bg-white rounded-xl p-5 shadow-sm border hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 uppercase">Jadwal</p>
                <p class="text-3xl font-semibold text-gray-800">
                    {{ $totalJadwal }}
                </p>
            </div>
            <div class="p-3 rounded-lg bg-purple-100">
                <i data-feather="calendar" class="w-6 h-6 text-purple-600"></i>
            </div>
        </div>
    </div>

    {{-- PEMBAYARAN BELUM --}}
    <div class="bg-white rounded-xl p-5 shadow-sm border hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 uppercase">Belum Lunas</p>
                <p class="text-3xl font-semibold text-red-600">
                    {{ $pembayaranBelum }}
                </p>
            </div>
            <div class="p-3 rounded-lg bg-red-100">
                <i data-feather="alert-circle" class="w-6 h-6 text-red-600"></i>
            </div>
        </div>
    </div>

    {{-- PEMBAYARAN LUNAS --}}
    <div class="bg-white rounded-xl p-5 shadow-sm border hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 uppercase">Lunas</p>
                <p class="text-3xl font-semibold text-emerald-600">
                    {{ $pembayaranLunas }}
                </p>
            </div>
            <div class="p-3 rounded-lg bg-emerald-100">
                <i data-feather="check-circle" class="w-6 h-6 text-emerald-600"></i>
            </div>
        </div>
    </div>

</div>

{{-- ================= QUICK ACCESS / MANAGEMENT ================= --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

    {{-- MANAJEMEN SEKOLAH --}}
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h3 class="font-semibold text-gray-700 mb-4 flex items-center gap-2">
            <i data-feather="grid" class="w-4 h-4"></i>
            Manajemen Sekolah
        </h3>

        <div class="space-y-2 text-sm">
            <a href="{{ route('sekolah.index') }}"
               class="flex justify-between items-center p-3 rounded-lg hover:bg-gray-50">
                <span>Kelola Sekolah</span>
                <i data-feather="chevron-right" class="w-4 h-4"></i>
            </a>
        </div>
    </div>

    {{-- AKADEMIK --}}
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h3 class="font-semibold text-gray-700 mb-4 flex items-center gap-2">
            <i data-feather="book-open" class="w-4 h-4"></i>
            Akademik
        </h3>

        <div class="space-y-2 text-sm">
            <a href="{{ route('materi.index') }}"
               class="flex justify-between items-center p-3 rounded-lg hover:bg-gray-50">
                <span>Materi</span>
                <i data-feather="chevron-right" class="w-4 h-4"></i>
            </a>

            <a href="{{ route('jadwal.index') }}"
               class="flex justify-between items-center p-3 rounded-lg hover:bg-gray-50">
                <span>Jadwal</span>
                <i data-feather="chevron-right" class="w-4 h-4"></i>
            </a>

            <a href="{{ route('rapor.manajemen') }}"
               class="flex justify-between items-center p-3 rounded-lg hover:bg-gray-50">
                <span>Rapor</span>
                <i data-feather="chevron-right" class="w-4 h-4"></i>
            </a>
        </div>
    </div>

    {{-- KEUANGAN --}}
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h3 class="font-semibold text-gray-700 mb-4 flex items-center gap-2">
            <i data-feather="credit-card" class="w-4 h-4"></i>
            Keuangan
        </h3>

        <div class="space-y-2 text-sm">
            <a href="{{ route('pembayaran.index') }}"
               class="flex justify-between items-center p-3 rounded-lg hover:bg-gray-50">
                <span>Pembayaran</span>
                <i data-feather="chevron-right" class="w-4 h-4"></i>
            </a>

            <a href="{{ route('pembayaran.rekap') }}"
               class="flex justify-between items-center p-3 rounded-lg hover:bg-gray-50">
                <span>Rekap Pembayaran</span>
                <i data-feather="chevron-right" class="w-4 h-4"></i>
            </a>
        </div>
    </div>

</div>

@endsection
