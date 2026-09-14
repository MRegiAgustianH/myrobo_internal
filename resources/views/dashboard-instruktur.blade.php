@extends('layouts.app')

@section('header')
Dashboard Instruktur
@endsection

@section('content')

{{-- ========================= --}}
{{-- PROFIL RINGKAS --}}
{{-- ========================= --}}
<div
    class="bg-teal-50 border border-teal-200
           rounded-xl shadow-sm p-5 mb-6">

    <div class="flex items-center justify-between gap-3">
        <div>
            <p class="text-xs text-teal-600 font-medium uppercase tracking-wide">Selamat datang</p>

            <p class="text-lg font-bold text-teal-700">
                {{ Auth::user()->name }}
            </p>

            <p class="text-xs text-teal-500 capitalize">
                Instruktur
            </p>
        </div>
        <div class="p-3 rounded-lg bg-teal-500 shadow-lg">
            <i data-feather="user-check" class="w-5 h-5 text-white"></i>
        </div>
    </div>
</div>

{{-- ========================= --}}
{{-- JADWAL HARI INI --}}
{{-- ========================= --}}
<div class="mb-8">

    <h2
        class="text-sm font-bold text-gray-800 mb-3
               flex items-center gap-2">
        <span class="p-1.5 rounded-lg bg-teal-500">
            <i data-feather="calendar" class="w-3.5 h-3.5 text-white"></i>
        </span>
        Jadwal Hari Ini
    </h2>

    @forelse($jadwalsHariIni as $j)
        <div
            class="bg-blue-50 border border-blue-200
                   rounded-xl shadow-sm p-4 mb-3
                   hover:shadow-lg hover:border-blue-300
                   transition-all duration-300">

            <p class="font-semibold text-sm text-blue-700">
                {{ $j->nama_kegiatan }}
            </p>

            <div class="mt-2 text-xs text-blue-600 space-y-1">

                <p class="flex items-center gap-2">
                    <i data-feather="home" class="w-3.5 h-3.5"></i>
                    {{ $j->sekolah?->nama_sekolah ?? 'Home Private' }}
                </p>


                <p class="flex items-center gap-2">
                    <i data-feather="clock" class="w-3.5 h-3.5"></i>
                    {{ $j->jam_mulai }} – {{ $j->jam_selesai }}
                </p>

            </div>

            <a
                href="{{ route('absensi.index', $j->id) }}"
                class="inline-flex items-center gap-2 mt-4
                       bg-blue-500 hover:bg-blue-600
                       text-white text-xs font-medium
                       px-4 py-2 rounded-lg transition">

                <i data-feather="edit-3" class="w-3.5 h-3.5"></i>
                Isi Absensi
            </a>
        </div>
    @empty
        <div
            class="bg-gray-50 border border-gray-200
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
        class="text-sm font-bold text-gray-800 mb-3
               flex items-center gap-2">
        <span class="p-1.5 rounded-lg bg-violet-500">
            <i data-feather="calendar-range" class="w-3.5 h-3.5 text-white"></i>
        </span>
        Jadwal Minggu Ini
    </h2>

    <div class="space-y-3">
        @forelse($jadwalsMingguan as $j)
            <div
                class="bg-violet-50 border border-violet-200
                       rounded-xl shadow-sm p-4
                       flex flex-col sm:flex-row
                       sm:justify-between sm:items-center gap-3
                       hover:shadow-lg hover:border-violet-300
                       transition-all duration-300">

                <div>
                    <p class="font-semibold text-sm text-violet-700">
                        {{ $j->nama_kegiatan }}
                    </p>

                    <p class="text-xs text-violet-500 mt-0.5">
                        {{ \Carbon\Carbon::parse($j->tanggal_mulai)->format('d M Y') }}
                        • {{ $j->jam_mulai }} – {{ $j->jam_selesai }}
                    </p>
                </div>

                <span class="text-xs bg-white
                    border border-violet-200
                    px-3 py-1 rounded-full
                    text-violet-700 font-medium whitespace-nowrap self-start sm:self-auto">

                    {{ $j->sekolah?->nama_sekolah ?? 'Home Private' }}
                </span>

            </div>
        @empty
            <div
                class="bg-gray-50 border border-gray-200
                       text-center text-sm text-gray-500
                       py-5 rounded-xl">

                Tidak ada jadwal minggu ini
            </div>
        @endforelse
    </div>
</div>

@endsection
