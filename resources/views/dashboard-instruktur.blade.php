@extends('layouts.app')

@section('header')
Dashboard Instruktur
@endsection

@section('content')

{{-- ========================= --}}
{{-- PROFIL RINGKAS --}}
{{-- ========================= --}}
<div
    class="bg-[#F6FAFB] border border-[#E3EEF0]
           rounded-2xl shadow-sm p-5 mb-6">

    <p class="text-sm text-gray-500">Selamat datang</p>

    <p class="text-lg font-semibold text-gray-800">
        {{ Auth::user()->name }}
    </p>

    <p class="text-xs text-gray-500 capitalize">
        Instruktur
    </p>
</div>

{{-- ========================= --}}
{{-- JADWAL HARI INI --}}
{{-- ========================= --}}
<div class="mb-8">

    <h2
        class="text-sm font-semibold text-gray-800 mb-3
               flex items-center gap-2">
        <i data-feather="calendar" class="w-4 h-4 text-gray-500"></i>
        Jadwal Hari Ini
    </h2>

    @forelse($jadwalsHariIni as $j)
        <div
            class="bg-white border border-[#E3EEF0]
                   rounded-2xl shadow-sm p-4 mb-3
                   hover:bg-[#F6FAFB] transition">

            <p class="font-semibold text-sm text-gray-800">
                {{ $j->nama_kegiatan }}
            </p>

            <div class="mt-2 text-xs text-gray-600 space-y-1">

                <p class="flex items-center gap-2">
                    <i data-feather="home" class="w-3.5 h-3.5"></i>
                    {{ $j->sekolah->nama_sekolah }}
                </p>

                <p class="flex items-center gap-2">
                    <i data-feather="clock" class="w-3.5 h-3.5"></i>
                    {{ $j->jam_mulai }} – {{ $j->jam_selesai }}
                </p>

            </div>

            <a
                href="{{ route('absensi.index', $j->id) }}"
                class="inline-flex items-center gap-2 mt-4
                       bg-[#8FBFC2] hover:bg-[#6FA9AD]
                       text-gray-900 text-xs font-medium
                       px-4 py-2 rounded-lg transition">

                <i data-feather="edit-3" class="w-3.5 h-3.5"></i>
                Isi Absensi
            </a>
        </div>
    @empty
        <div
            class="bg-[#F6FAFB] border border-[#E3EEF0]
                   text-center text-sm text-gray-500
                   py-6 rounded-xl">

            Tidak ada jadwal hari ini
        </div>
    @endforelse
</div>

{{-- ========================= --}}
{{-- JADWAL 7 HARI KE DEPAN --}}
{{-- ========================= --}}
<div>

    <h2
        class="text-sm font-semibold text-gray-800 mb-3
               flex items-center gap-2">
        <i data-feather="calendar-range" class="w-4 h-4 text-gray-500"></i>
        Jadwal Minggu Ini
    </h2>

    <div class="space-y-3">
        @forelse($jadwalsMingguan as $j)
            <div
                class="bg-white border border-[#E3EEF0]
                       rounded-xl shadow-sm p-4
                       flex flex-col sm:flex-row
                       sm:justify-between sm:items-center gap-3">

                <div>
                    <p class="font-medium text-sm text-gray-800">
                        {{ $j->nama_kegiatan }}
                    </p>

                    <p class="text-xs text-gray-500 mt-0.5">
                        {{ \Carbon\Carbon::parse($j->tanggal_mulai)->format('d M Y') }}
                        • {{ $j->jam_mulai }} – {{ $j->jam_selesai }}
                    </p>
                </div>

                <span
                    class="text-xs bg-[#F6FAFB]
                           border border-[#E3EEF0]
                           px-3 py-1 rounded-full
                           text-gray-700">

                    {{ $j->sekolah->nama_sekolah }}
                </span>
            </div>
        @empty
            <div
                class="bg-[#F6FAFB] border border-[#E3EEF0]
                       text-center text-sm text-gray-500
                       py-5 rounded-xl">

                Tidak ada jadwal minggu ini
            </div>
        @endforelse
    </div>
</div>

@endsection
