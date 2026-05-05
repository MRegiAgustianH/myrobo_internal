@extends('layouts.app')

@section('header')
<div class="flex items-center gap-3">
    <div class="p-2 bg-[#8FBFC2]/20 rounded-xl">
        <i data-feather="clipboard" class="w-5 h-5 text-[#6FA9AD]"></i>
    </div>
    <div>
        <h1 class="text-lg font-semibold text-gray-800">Tugas Rapor</h1>
        <p class="text-xs text-gray-500">Daftar tugas rapor yang harus Anda kerjakan</p>
    </div>
</div>
@endsection

@section('content')

{{-- ================= FLASH MESSAGE ================= --}}
@if(session('success'))
<div class="mb-5 flex items-center gap-2 px-4 py-3
            bg-green-50 border border-green-200
            rounded-xl text-sm text-green-700">
    <i data-feather="check-circle" class="w-4 h-4"></i>
    {{ session('success') }}
</div>
@endif

{{-- ================= EMPTY STATE ================= --}}
@if($tugas->isEmpty())
<div class="bg-[#F6FAFB] border border-[#E3EEF0]
            rounded-2xl p-10 text-center text-sm text-gray-500">
    <div class="flex flex-col items-center gap-2">
        <i data-feather="inbox" class="w-8 h-8 text-gray-400"></i>
        <p class="font-medium text-gray-700">
            Tidak ada tugas rapor
        </p>
        <p>
            Saat ini belum ada tugas rapor yang ditugaskan kepada Anda.
        </p>
    </div>
</div>
@else

{{-- ================= GRID TUGAS ================= --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">

@foreach($tugas as $t)

@php
    $total   = $t->rapors_count ?? 0;
    $selesai = $t->rapors_selesai_count ?? 0;
    $persen  = $total > 0 ? round(($selesai / $total) * 100) : 0;

    $adaRevisi = $t->rapors()
        ->where('status', 'revision')
        ->exists();
@endphp

<div class="bg-white border border-[#E3EEF0]
            rounded-2xl shadow-sm hover:shadow-md
            transition flex flex-col overflow-hidden">

    {{-- HEADER --}}
    <div class="px-5 py-4 bg-[#8FBFC2] relative">

        @if($adaRevisi)
        <span class="absolute top-3 right-3
                     flex items-center gap-1
                     bg-red-600 text-white text-[11px]
                     px-2 py-1 rounded-full">
            <i data-feather="alert-circle" class="w-3 h-3"></i>
            Revisi
        </span>
        @endif

        <p class="font-semibold text-sm text-gray-900 truncate">
            {{ $t->sekolah->nama_sekolah }}
        </p>
        <p class="text-xs text-gray-800 opacity-80 flex items-center gap-1">
            <i data-feather="calendar" class="w-3 h-3"></i>
            Semester {{ $t->semester->nama_semester }}
        </p>
    </div>

    {{-- BODY --}}
    <div class="p-5 space-y-4 text-sm text-gray-700 flex-1">

        {{-- PROGRESS --}}
        <div class="space-y-1">
            <div class="flex justify-between items-center">
                <span class="text-gray-500 flex items-center gap-1">
                    <i data-feather="bar-chart-2" class="w-3 h-3"></i>
                    Progress
                </span>
                <span class="font-medium">
                    {{ $selesai }} / {{ $total }}
                </span>
            </div>

            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                <div class="h-2 rounded-full transition-all
                    {{ $persen === 100 ? 'bg-green-500' : 'bg-[#8FBFC2]' }}"
                    style="width: {{ $persen }}%"></div>
            </div>

            <p class="text-xs text-gray-500">
                {{ $persen }}% selesai
            </p>
        </div>

        {{-- DEADLINE --}}
        <div class="flex justify-between items-center">
            <span class="text-gray-500 flex items-center gap-1">
                <i data-feather="clock" class="w-3 h-3"></i>
                Deadline
            </span>
            <span class="text-xs
                {{ $t->deadline && \Carbon\Carbon::parse($t->deadline)->isPast()
                    ? 'text-red-600 font-semibold'
                    : 'text-gray-700' }}">
                {{ $t->deadline
                    ? \Carbon\Carbon::parse($t->deadline)->format('d M Y')
                    : '—' }}
            </span>
        </div>

        {{-- STATUS --}}
        <div class="flex justify-between items-center">
            <span class="text-gray-500 flex items-center gap-1">
                <i data-feather="activity" class="w-3 h-3"></i>
                Status
            </span>
            <span class="px-2 py-1 rounded-full text-xs font-medium
                @switch($t->status)
                    @case('pending') bg-gray-200 text-gray-700 @break
                    @case('in_progress') bg-yellow-100 text-yellow-700 @break
                    @case('completed') bg-green-100 text-green-700 @break
                @endswitch">
                {{ ucfirst(str_replace('_',' ',$t->status)) }}
            </span>
        </div>
    </div>

    {{-- FOOTER / CTA --}}
    <div class="border-t px-4 py-3 bg-gray-50">
        <a href="{{ route('instruktur.rapor-tugas.show', $t->id) }}"
           class="w-full inline-flex justify-center items-center gap-2
                  text-xs font-semibold
                  py-2 rounded-lg transition
                  {{ $adaRevisi
                        ? 'bg-red-600 hover:bg-red-700 text-white'
                        : 'bg-[#8FBFC2] hover:bg-[#6FA9AD] text-gray-900' }}">

            @if($adaRevisi)
                <i data-feather="edit-3" class="w-3 h-3"></i>
                Perbaiki Rapor
            @elseif($persen === 0)
                <i data-feather="play" class="w-3 h-3"></i>
                Mulai Kerjakan
            @elseif($persen < 100)
                <i data-feather="arrow-right" class="w-3 h-3"></i>
                Lanjutkan Rapor
            @else
                <i data-feather="eye" class="w-3 h-3"></i>
                Lihat Detail
            @endif
        </a>
    </div>

</div>

@endforeach
</div>
@endif

@endsection
