@extends('layouts.app')

@section('header')
Dashboard Admin
@endsection

@section('content')

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

    {{-- SEKOLAH --}}
    <div class="bg-white shadow rounded-xl p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Sekolah</p>
                <p class="text-3xl font-bold">{{ $totalSekolah }}</p>
            </div>
            <div class="text-4xl">🏫</div>
        </div>
    </div>

    {{-- PESERTA --}}
    <div class="bg-white shadow rounded-xl p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Peserta</p>
                <p class="text-3xl font-bold">{{ $totalPeserta }}</p>
            </div>
            <div class="text-4xl">🎓</div>
        </div>
    </div>

    {{-- INSTRUKTUR --}}
    <div class="bg-white shadow rounded-xl p-6 border-l-4 border-indigo-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Instruktur</p>
                <p class="text-3xl font-bold">{{ $totalInstruktur }}</p>
            </div>
            <div class="text-4xl">👨‍🏫</div>
        </div>
    </div>

    {{-- JADWAL --}}
    <div class="bg-white shadow rounded-xl p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Jadwal</p>
                <p class="text-3xl font-bold">{{ $totalJadwal }}</p>
            </div>
            <div class="text-4xl">🗓️</div>
        </div>
    </div>

    {{-- PEMBAYARAN BELUM --}}
    <div class="bg-white shadow rounded-xl p-6 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Pembayaran Belum Lunas</p>
                <p class="text-3xl font-bold text-red-600">
                    {{ $pembayaranBelum }}
                </p>
            </div>
            <div class="text-4xl">💰</div>
        </div>
    </div>

    {{-- PEMBAYARAN LUNAS --}}
    <div class="bg-white shadow rounded-xl p-6 border-l-4 border-green-600">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Pembayaran Lunas</p>
                <p class="text-3xl font-bold text-green-600">
                    {{ $pembayaranLunas }}
                </p>
            </div>
            <div class="text-4xl">✅</div>
        </div>
    </div>

</div>

@endsection
