@extends('layouts.app')

@section('header')
Dashboard Sekretaris
@endsection

@section('content')

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
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

    {{-- TOTAL TUGAS --}}
    <div class="bg-white rounded-xl p-5 shadow-sm border hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 uppercase">Tugas Rapor</p>
                <p class="text-3xl font-semibold text-gray-800">
                    {{ $totalTugas }}
                </p>
            </div>
            <div class="p-3 rounded-lg bg-blue-100">
                <i data-feather="file-text" class="w-6 h-6 text-blue-600"></i>
            </div>
        </div>
    </div>

    {{-- TUGAS PENDING --}}
    <div class="bg-white rounded-xl p-5 shadow-sm border hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 uppercase">Tugas Pending</p>
                <p class="text-3xl font-semibold text-yellow-600">
                    {{ $tugasPending }}
                </p>
            </div>
            <div class="p-3 rounded-lg bg-yellow-100">
                <i data-feather="clock" class="w-6 h-6 text-yellow-600"></i>
            </div>
        </div>
    </div>
</div>

{{-- JADWAL & RAPOR STATS --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    {{-- TOTAL JADWAL --}}
    <div class="bg-white rounded-xl p-5 shadow-sm border hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 uppercase">Total Jadwal</p>
                <p class="text-3xl font-semibold text-gray-800">
                    {{ $totalJadwal }}
                </p>
            </div>
            <div class="p-3 rounded-lg bg-indigo-100">
                <i data-feather="calendar" class="w-6 h-6 text-indigo-600"></i>
            </div>
        </div>
    </div>

    {{-- JADWAL HARI INI --}}
    <div class="bg-white rounded-xl p-5 shadow-sm border hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 uppercase">Jadwal Hari Ini</p>
                <p class="text-3xl font-semibold text-[#5a8f94]">
                    {{ $jadwalHariIni }}
                </p>
            </div>
            <div class="p-3 rounded-lg bg-[#8FBFC2]/20">
                <i data-feather="sun" class="w-6 h-6 text-[#5a8f94]"></i>
            </div>
        </div>
    </div>

    {{-- JADWAL MINGGU INI --}}
    <div class="bg-white rounded-xl p-5 shadow-sm border hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 uppercase">Jadwal Minggu Ini</p>
                <p class="text-3xl font-semibold text-purple-600">
                    {{ $jadwalMingguIni }}
                </p>
            </div>
            <div class="p-3 rounded-lg bg-purple-100">
                <i data-feather="layers" class="w-6 h-6 text-purple-600"></i>
            </div>
        </div>
    </div>

    {{-- MENUNGGU VALIDASI --}}
    <div class="bg-white rounded-xl p-5 shadow-sm border hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 uppercase">Rapor Menunggu Validasi</p>
                <p class="text-3xl font-semibold text-orange-600">
                    {{ $raporMenungguValidasi }}
                </p>
            </div>
            <div class="p-3 rounded-lg bg-orange-100">
                <i data-feather="alert-circle" class="w-6 h-6 text-orange-600"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-8">
    {{-- RAPOR DISETUJUI --}}
    <div class="bg-white rounded-xl p-5 shadow-sm border hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 uppercase">Rapor Disetujui</p>
                <p class="text-3xl font-semibold text-emerald-600">
                    {{ $raporDisetujui }}
                </p>
            </div>
            <div class="p-3 rounded-lg bg-emerald-100">
                <i data-feather="check-circle" class="w-6 h-6 text-emerald-600"></i>
            </div>
        </div>
    </div>

    {{-- TUGAS IN PROGRESS --}}
    <div class="bg-white rounded-xl p-5 shadow-sm border hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 uppercase">Tugas In Progress</p>
                <p class="text-3xl font-semibold text-blue-600">
                    {{ $tugasInProgress }}
                </p>
            </div>
            <div class="p-3 rounded-lg bg-blue-100">
                <i data-feather="loader" class="w-6 h-6 text-blue-600"></i>
            </div>
        </div>
    </div>
</div>

{{-- QUICK ACCESS / MANAGEMENT --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h3 class="font-semibold text-gray-700 mb-4 flex items-center gap-2">
            <i data-feather="calendar" class="w-4 h-4"></i>
            Penjadwalan
        </h3>

        <div class="space-y-2 text-sm">
            <a href="{{ route('jadwal.index') }}"
               class="flex justify-between items-center p-3 rounded-lg hover:bg-gray-50 transition">
                <span>Kelola Jadwal</span>
                <i data-feather="chevron-right" class="w-4 h-4"></i>
            </a>

            <a href="{{ route('absensi.rekap.filter') }}"
               class="flex justify-between items-center p-3 rounded-lg hover:bg-gray-50 transition">
                <span>Rekap Absensi</span>
                <i data-feather="chevron-right" class="w-4 h-4"></i>
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h3 class="font-semibold text-gray-700 mb-4 flex items-center gap-2">
            <i data-feather="book-open" class="w-4 h-4"></i>
            Akademik
        </h3>

        <div class="space-y-2 text-sm">
            <a href="{{ route('admin.rapor-tugas.index') }}"
               class="flex justify-between items-center p-3 rounded-lg hover:bg-gray-50 transition">
                <span>Penugasan Rapor</span>
                <i data-feather="chevron-right" class="w-4 h-4"></i>
            </a>
        </div>
    </div>
</div>

@endsection

