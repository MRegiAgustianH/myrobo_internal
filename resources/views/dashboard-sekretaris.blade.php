@extends('layouts.app')

@section('header')
Dashboard Sekretaris
@endsection

@section('content')

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
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

    {{-- TOTAL TUGAS --}}
    <div class="bg-blue-50 rounded-xl p-4 shadow-sm border border-blue-100 hover:shadow-lg hover:border-blue-200 transition-all duration-300 group">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs text-blue-600 font-medium uppercase tracking-wide mb-1">Tugas Rapor</p>
                <p class="text-2xl font-bold text-blue-700 leading-tight">
                    {{ $totalTugas }}
                </p>
            </div>
            <div class="p-3 rounded-lg bg-blue-500 shadow-lg group-hover:scale-110 transition-transform duration-300 shrink-0">
                <i data-feather="file-text" class="w-5 h-5 text-white"></i>
            </div>
        </div>
    </div>

    {{-- TUGAS PENDING --}}
    <div class="bg-yellow-50 rounded-xl p-4 shadow-sm border border-yellow-100 hover:shadow-lg hover:border-yellow-200 transition-all duration-300 group">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs text-yellow-600 font-medium uppercase tracking-wide mb-1">Tugas Pending</p>
                <p class="text-2xl font-bold text-yellow-700 leading-tight">
                    {{ $tugasPending }}
                </p>
            </div>
            <div class="p-3 rounded-lg bg-yellow-500 shadow-lg group-hover:scale-110 transition-transform duration-300 shrink-0">
                <i data-feather="clock" class="w-5 h-5 text-white"></i>
            </div>
        </div>
    </div>
</div>

{{-- JADWAL & RAPOR STATS --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    {{-- TOTAL JADWAL --}}
    <div class="bg-violet-50 rounded-xl p-4 shadow-sm border border-violet-100 hover:shadow-lg hover:border-violet-200 transition-all duration-300 group">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs text-violet-600 font-medium uppercase tracking-wide mb-1">Total Jadwal</p>
                <p class="text-2xl font-bold text-violet-700 leading-tight">
                    {{ $totalJadwal }}
                </p>
            </div>
            <div class="p-3 rounded-lg bg-violet-500 shadow-lg group-hover:scale-110 transition-transform duration-300 shrink-0">
                <i data-feather="calendar" class="w-5 h-5 text-white"></i>
            </div>
        </div>
    </div>

    {{-- JADWAL HARI INI --}}
    <div class="bg-teal-50 rounded-xl p-4 shadow-sm border border-teal-100 hover:shadow-lg hover:border-teal-200 transition-all duration-300 group">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs text-teal-600 font-medium uppercase tracking-wide mb-1">Jadwal Hari Ini</p>
                <p class="text-2xl font-bold text-teal-700 leading-tight">
                    {{ $jadwalHariIni }}
                </p>
            </div>
            <div class="p-3 rounded-lg bg-teal-500 shadow-lg group-hover:scale-110 transition-transform duration-300 shrink-0">
                <i data-feather="sun" class="w-5 h-5 text-white"></i>
            </div>
        </div>
    </div>

    {{-- JADWAL MINGGU INI --}}
    <div class="bg-purple-50 rounded-xl p-4 shadow-sm border border-purple-100 hover:shadow-lg hover:border-purple-200 transition-all duration-300 group">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs text-purple-600 font-medium uppercase tracking-wide mb-1">Jadwal Minggu Ini</p>
                <p class="text-2xl font-bold text-purple-700 leading-tight">
                    {{ $jadwalMingguIni }}
                </p>
            </div>
            <div class="p-3 rounded-lg bg-purple-500 shadow-lg group-hover:scale-110 transition-transform duration-300 shrink-0">
                <i data-feather="layers" class="w-5 h-5 text-white"></i>
            </div>
        </div>
    </div>

    {{-- MENUNGGU VALIDASI --}}
    <div class="bg-orange-50 rounded-xl p-4 shadow-sm border border-orange-100 hover:shadow-lg hover:border-orange-200 transition-all duration-300 group">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs text-orange-600 font-medium uppercase tracking-wide mb-1">Rapor Menunggu Validasi</p>
                <p class="text-2xl font-bold text-orange-700 leading-tight">
                    {{ $raporMenungguValidasi }}
                </p>
            </div>
            <div class="p-3 rounded-lg bg-orange-500 shadow-lg group-hover:scale-110 transition-transform duration-300 shrink-0">
                <i data-feather="alert-circle" class="w-5 h-5 text-white"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
    {{-- RAPOR DISETUJUI --}}
    <div class="bg-green-50 rounded-xl p-4 shadow-sm border border-green-100 hover:shadow-lg hover:border-green-200 transition-all duration-300 group">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs text-green-600 font-medium uppercase tracking-wide mb-1">Rapor Disetujui</p>
                <p class="text-2xl font-bold text-green-700 leading-tight">
                    {{ $raporDisetujui }}
                </p>
            </div>
            <div class="p-3 rounded-lg bg-green-500 shadow-lg group-hover:scale-110 transition-transform duration-300 shrink-0">
                <i data-feather="check-circle" class="w-5 h-5 text-white"></i>
            </div>
        </div>
    </div>

    {{-- TUGAS IN PROGRESS --}}
    <div class="bg-indigo-50 rounded-xl p-4 shadow-sm border border-indigo-100 hover:shadow-lg hover:border-indigo-200 transition-all duration-300 group">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs text-indigo-600 font-medium uppercase tracking-wide mb-1">Tugas In Progress</p>
                <p class="text-2xl font-bold text-indigo-700 leading-tight">
                    {{ $tugasInProgress }}
                </p>
            </div>
            <div class="p-3 rounded-lg bg-indigo-500 shadow-lg group-hover:scale-110 transition-transform duration-300 shrink-0">
                <i data-feather="loader" class="w-5 h-5 text-white"></i>
            </div>
        </div>
    </div>
</div>

{{-- QUICK ACCESS / MANAGEMENT --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- PENJADWALAN --}}
    <div class="bg-teal-50 rounded-xl shadow-sm border border-teal-200 hover:shadow-xl hover:border-teal-300 transition-all duration-300 group">
        <div class="p-5 sm:p-6">
            <h3 class="font-bold text-base sm:text-lg text-teal-700 mb-4 flex items-center gap-3">
                <div class="p-2 rounded-lg bg-teal-500 shadow-md">
                    <i data-feather="calendar" class="w-4 h-4 text-white"></i>
                </div>
                Penjadwalan
            </h3>

            <div class="space-y-2">
                <a href="{{ route('jadwal.index') }}"
                   class="flex justify-between items-center p-3 rounded-lg bg-white hover:bg-teal-500 hover:text-white transition-all duration-300 group/item">
                    <span class="font-medium">Kelola Jadwal</span>
                    <i data-feather="chevron-right" class="w-4 h-4 group-hover/item:translate-x-1 transition-transform"></i>
                </a>

                <a href="{{ route('absensi.rekap.filter') }}"
                   class="flex justify-between items-center p-3 rounded-lg bg-white hover:bg-teal-500 hover:text-white transition-all duration-300 group/item">
                    <span class="font-medium">Rekap Absensi</span>
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
                <a href="{{ route('admin.rapor-tugas.index') }}"
                   class="flex justify-between items-center p-3 rounded-lg bg-white hover:bg-blue-500 hover:text-white transition-all duration-300 group/item">
                    <span class="font-medium">Penugasan Rapor</span>
                    <i data-feather="chevron-right" class="w-4 h-4 group-hover/item:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

