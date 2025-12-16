@extends('layouts.app')

@section('header')
Dashboard Admin
@endsection

@section('content')

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

    {{-- SEKOLAH --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Sekolah</p>
                <p class="text-3xl font-semibold text-gray-800">
                    {{ $totalSekolah }}
                </p>
            </div>
            <i data-feather="home" class="w-8 h-8 text-gray-400"></i>
        </div>
    </div>

    {{-- PESERTA --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Peserta</p>
                <p class="text-3xl font-semibold text-gray-800">
                    {{ $totalPeserta }}
                </p>
            </div>
            <i data-feather="users" class="w-8 h-8 text-gray-400"></i>
        </div>
    </div>

    {{-- INSTRUKTUR --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Instruktur</p>
                <p class="text-3xl font-semibold text-gray-800">
                    {{ $totalInstruktur }}
                </p>
            </div>
            <i data-feather="user-check" class="w-8 h-8 text-gray-400"></i>
        </div>
    </div>

    {{-- JADWAL --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Jadwal</p>
                <p class="text-3xl font-semibold text-gray-800">
                    {{ $totalJadwal }}
                </p>
            </div>
            <i data-feather="calendar" class="w-8 h-8 text-gray-400"></i>
        </div>
    </div>

    {{-- PEMBAYARAN BELUM --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Pembayaran Belum Lunas</p>
                <p class="text-3xl font-semibold text-gray-800">
                    {{ $pembayaranBelum }}
                </p>
            </div>
            <i data-feather="alert-circle" class="w-8 h-8 text-gray-400"></i>
        </div>
    </div>

    {{-- PEMBAYARAN LUNAS --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Pembayaran Lunas</p>
                <p class="text-3xl font-semibold text-gray-800">
                    {{ $pembayaranLunas }}
                </p>
            </div>
            <i data-feather="check-circle" class="w-8 h-8 text-gray-400"></i>
        </div>
    </div>

</div>



@endsection
