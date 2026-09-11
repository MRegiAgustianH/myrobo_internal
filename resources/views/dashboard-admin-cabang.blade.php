@extends('layouts.app')

@section('header')
Dashboard Cabang {{ $cabang?->nama_cabang ?? '' }}
@endsection

@section('content')

@if($cabang)
<div class="bg-gradient-to-r from-[#8FBFC2] to-[#7FB3B8] rounded-xl shadow p-5 mb-6 text-white">
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

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow p-4">
        <div class="text-xs text-gray-500 uppercase tracking-wider">Sekolah</div>
        <div class="text-2xl font-bold text-gray-800 mt-1">{{ $totalSekolah }}</div>
    </div>
    <div class="bg-white rounded-xl shadow p-4">
        <div class="text-xs text-gray-500 uppercase tracking-wider">Peserta</div>
        <div class="text-2xl font-bold text-gray-800 mt-1">{{ $totalPeserta }}</div>
    </div>
    <div class="bg-white rounded-xl shadow p-4">
        <div class="text-xs text-gray-500 uppercase tracking-wider">Instruktur</div>
        <div class="text-2xl font-bold text-gray-800 mt-1">{{ $totalInstruktur }}</div>
    </div>
    <div class="bg-white rounded-xl shadow p-4">
        <div class="text-xs text-gray-500 uppercase tracking-wider">Jadwal</div>
        <div class="text-2xl font-bold text-gray-800 mt-1">{{ $totalJadwal }}</div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl shadow p-4">
        <div class="text-xs text-green-700 uppercase tracking-wider">Uang Masuk ({{ \Carbon\Carbon::create()->month((int) $bulan)->translatedFormat('F') }})</div>
        <div class="text-xl font-bold text-green-800 mt-1">Rp {{ number_format($uangMasuk, 0, ',', '.') }}</div>
    </div>
    <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl shadow p-4">
        <div class="text-xs text-red-700 uppercase tracking-wider">Uang Keluar</div>
        <div class="text-xl font-bold text-red-800 mt-1">Rp {{ number_format($uangKeluar, 0, ',', '.') }}</div>
    </div>
    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow p-4">
        <div class="text-xs text-blue-700 uppercase tracking-wider">Saldo</div>
        <div class="text-xl font-bold text-blue-800 mt-1">Rp {{ number_format($saldo, 0, ',', '.') }}</div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="bg-white rounded-xl shadow p-4">
        <h3 class="font-semibold text-gray-700 mb-3">Status Pembayaran</h3>
        <div class="space-y-2">
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Lunas</span>
                <span class="px-3 py-1 rounded-full text-xs bg-green-100 text-green-700 font-semibold">{{ $pembayaranLunas }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Belum Bayar</span>
                <span class="px-3 py-1 rounded-full text-xs bg-red-100 text-red-700 font-semibold">{{ $pembayaranBelum }}</span>
            </div>
        </div>
    </div>
</div>

@endsection