@extends('layouts.app')

@section('header')
Dashboard Instruktur
@endsection

@section('content')

{{-- ========================= --}}
{{-- PROFIL RINGKAS --}}
{{-- ========================= --}}
<div class="bg-white rounded-xl shadow p-4 mb-4">
    <p class="text-sm text-gray-500">Selamat datang</p>
    <p class="text-lg font-semibold">{{ Auth::user()->name }}</p>
    <p class="text-xs text-gray-500 capitalize">Instruktur</p>
</div>

{{-- ========================= --}}
{{-- JADWAL HARI INI --}}
{{-- ========================= --}}
<div class="mb-6">
    <h2 class="text-sm font-semibold mb-2">
        📅 Jadwal Hari Ini
    </h2>

    @forelse($jadwalsHariIni as $j)
    <div class="bg-white rounded-lg shadow p-4 mb-3 border-l-4 border-blue-500">
        <p class="font-semibold text-sm">
            {{ $j->nama_kegiatan }}
        </p>

        <p class="text-xs text-gray-600 mt-1">
            🏫 {{ $j->sekolah->nama_sekolah }}
        </p>

        <p class="text-xs text-gray-600">
            ⏰ {{ $j->jam_mulai }} – {{ $j->jam_selesai }}
        </p>

        <a href="{{ route('absensi.index', $j->id) }}"
           class="inline-block mt-3 bg-blue-600 text-white text-xs px-4 py-2 rounded">
            📝 Isi Absensi
        </a>
    </div>
    @empty
    <div class="bg-gray-100 text-center text-sm text-gray-500 py-6 rounded">
        Tidak ada jadwal hari ini
    </div>
    @endforelse
</div>

{{-- ========================= --}}
{{-- JADWAL MINGGU INI --}}
{{-- ========================= --}}
<div>
    <h2 class="text-sm font-semibold mb-2">
        📆 Jadwal Minggu Ini
    </h2>

    <div class="space-y-2">
        @forelse($jadwalsMingguan as $j)
        <div class="bg-white rounded-lg shadow p-3 text-sm flex justify-between items-center">
            <div>
                <p class="font-medium">{{ $j->nama_kegiatan }}</p>
                <p class="text-xs text-gray-500">
                    {{ \Carbon\Carbon::parse($j->tanggal_mulai)->format('d M') }}
                    | {{ $j->jam_mulai }}
                </p>
            </div>

            <span class="text-xs bg-gray-100 px-2 py-1 rounded">
                {{ $j->sekolah->nama_sekolah }}
            </span>
        </div>
        @empty
        <div class="bg-gray-100 text-center text-sm text-gray-500 py-4 rounded">
            Tidak ada jadwal
        </div>
        @endforelse
    </div>
</div>

@endsection
