@extends('layouts.app')

@section('header')
Dashboard Superadmin - Monitoring Semua Cabang
@endsection

@section('content')

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-[#E3EEF0] shadow-sm p-5">
        <div class="text-xs text-gray-500 uppercase tracking-wider">Total Cabang</div>
        <div class="text-2xl font-bold text-gray-800 mt-1">{{ $totalCabang }}</div>
    </div>
    <div class="bg-white rounded-2xl border border-[#E3EEF0] shadow-sm p-5">
        <div class="text-xs text-gray-500 uppercase tracking-wider">Total Sekolah</div>
        <div class="text-2xl font-bold text-gray-800 mt-1">{{ $totalSekolah }}</div>
    </div>
    <div class="bg-white rounded-2xl border border-[#E3EEF0] shadow-sm p-5">
        <div class="text-xs text-gray-500 uppercase tracking-wider">Total Peserta</div>
        <div class="text-2xl font-bold text-gray-800 mt-1">{{ $totalPeserta }}</div>
    </div>
    <div class="bg-white rounded-2xl border border-[#E3EEF0] shadow-sm p-5">
        <div class="text-xs text-gray-500 uppercase tracking-wider">Total Instruktur</div>
        <div class="text-2xl font-bold text-gray-800 mt-1">{{ $totalInstruktur }}</div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-[#F2F9F3] border border-[#D4EDDA] rounded-2xl p-5 shadow-sm">
        <div class="text-xs font-semibold text-[#28A745] uppercase tracking-wider">Uang Masuk ({{ \Carbon\Carbon::create()->month((int) $bulan)->translatedFormat('F') }})</div>
        <div class="text-2xl font-bold text-[#1E7E34] mt-1.5 font-mono">Rp {{ number_format($uangMasuk, 0, ',', '.') }}</div>
    </div>
    <div class="bg-[#FDF3F3] border border-[#F8D7DA] rounded-2xl p-5 shadow-sm">
        <div class="text-xs font-semibold text-[#DC3545] uppercase tracking-wider">Uang Keluar</div>
        <div class="text-2xl font-bold text-[#BD2130] mt-1.5 font-mono">Rp {{ number_format($uangKeluar, 0, ',', '.') }}</div>
    </div>
    <div class="bg-[#F0F6FC] border border-[#D1E3F6] rounded-2xl p-5 shadow-sm">
        <div class="text-xs font-semibold text-[#0066CC] uppercase tracking-wider">Saldo Bersih</div>
        <div class="text-2xl font-bold text-[#004C99] mt-1.5 font-mono">Rp {{ number_format($saldo, 0, ',', '.') }}</div>
    </div>
</div>

<h3 class="text-lg font-semibold text-gray-700 mb-4">Monitoring per Cabang</h3>

<div class="overflow-x-auto bg-white rounded-xl shadow">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr class="text-gray-600 uppercase text-xs tracking-wider">
                <th class="px-4 py-3 text-left">Cabang</th>
                <th class="px-4 py-3 text-center">Kode</th>
                <th class="px-4 py-3 text-center">Sekolah</th>
                <th class="px-4 py-3 text-center">User</th>
                <th class="px-4 py-3 text-center">Jadwal</th>
                <th class="px-4 py-3 text-center">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y">
        @foreach($cabangs as $cb)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium">
                    <div class="flex items-center gap-2">
                        @if($cb->logo)
                        <img src="{{ asset('storage/' . $cb->logo) }}" class="w-8 h-8 rounded object-contain">
                        @endif
                        {{ $cb->nama_cabang }}
                    </div>
                </td>
                <td class="px-4 py-3 text-center font-mono">{{ $cb->kode_cabang }}</td>
                <td class="px-4 py-3 text-center">{{ $cb->sekolahs_count }}</td>
                <td class="px-4 py-3 text-center">{{ $cb->users_count }}</td>
                <td class="px-4 py-3 text-center">{{ $cb->jadwals_count }}</td>
                <td class="px-4 py-3 text-center">
                    @if($cb->is_aktif)
                    <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">Aktif</span>
                    @else
                    <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700">Nonaktif</span>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

@endsection