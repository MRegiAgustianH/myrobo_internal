@extends('layouts.app')

@section('header')
<div class="flex items-center justify-between flex-wrap gap-4">

    <div class="flex items-center gap-3">
        <div class="p-2 bg-[#8FBFC2]/20 rounded-xl">
            <i data-feather="file-text" class="w-5 h-5 text-[#6FA9AD]"></i>
        </div>
        <div>
            <h1 class="text-lg font-semibold text-gray-800">
                Detail Tugas Rapor
            </h1>
            <p class="text-xs text-gray-500">
                Monitoring progres dan verifikasi rapor peserta
            </p>
        </div>
    </div>

    {{-- BACK --}}
    <a href="{{ route('admin.rapor-tugas.index') }}"
       class="inline-flex items-center gap-2
              px-4 py-2 rounded-xl
              border border-gray-300
              text-sm font-medium text-gray-700
              hover:bg-gray-50 transition">
        <i data-feather="arrow-left" class="w-4 h-4"></i>
        Kembali
    </a>

</div>
@endsection

@section('content')

{{-- ================= INFO TUGAS ================= --}}
<div class="bg-white rounded-3xl shadow-sm border mb-8">
    <div class="p-6 grid grid-cols-1 sm:grid-cols-3 gap-6 text-sm">

        <div>
            <p class="text-xs text-gray-500 flex items-center gap-1">
                <i data-feather="home" class="w-3 h-3"></i>
                Sekolah
            </p>
            <p class="font-semibold text-gray-800">
                {{ $raporTugas->sekolah->nama_sekolah }}
            </p>
        </div>

        <div>
            <p class="text-xs text-gray-500 flex items-center gap-1">
                <i data-feather="calendar" class="w-3 h-3"></i>
                Semester
            </p>
            <p class="font-semibold text-gray-800">
                {{ $raporTugas->semester->nama_semester }}
            </p>
        </div>

        <div>
            <p class="text-xs text-gray-500 flex items-center gap-1">
                <i data-feather="user" class="w-3 h-3"></i>
                Instruktur
            </p>
            <p class="font-semibold text-gray-800">
                {{ $raporTugas->instruktur->name }}
            </p>
        </div>

    </div>

    <div class="px-6 pb-6 flex flex-wrap gap-3 text-xs">
        <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-700 flex items-center gap-1">
            <i data-feather="activity" class="w-3 h-3"></i>
            Status:
            <strong>{{ ucfirst(str_replace('_',' ',$raporTugas->status)) }}</strong>
        </span>

        @if($raporTugas->deadline)
        <span class="px-3 py-1 rounded-full flex items-center gap-1
            {{ \Carbon\Carbon::parse($raporTugas->deadline)->isPast()
                ? 'bg-red-100 text-red-700'
                : 'bg-yellow-100 text-yellow-700' }}">
            <i data-feather="clock" class="w-3 h-3"></i>
            Deadline:
            {{ \Carbon\Carbon::parse($raporTugas->deadline)->format('d M Y') }}
        </span>
        @endif
    </div>
</div>

{{-- ================= PROGRESS ================= --}}
@php
    $total   = $raporTugas->rapors->count();
    $selesai = $raporTugas->rapors
        ->whereIn('status', ['submitted','approved'])
        ->count();
    $persen  = $total > 0 ? round(($selesai / $total) * 100) : 0;
@endphp

<div class="bg-white rounded-3xl shadow-sm border mb-8">
    <div class="p-6">
        <div class="flex items-center gap-2 mb-3">
            <i data-feather="bar-chart-2" class="w-4 h-4 text-gray-600"></i>
            <h3 class="font-semibold text-gray-800 text-sm">
                Progress Rapor
            </h3>
        </div>

        <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
            <div class="bg-[#8FBFC2] h-2 transition-all"
                 style="width: {{ $persen }}%"></div>
        </div>

        <p class="text-xs text-gray-600 mt-2">
            {{ $selesai }} dari {{ $total }} rapor selesai ({{ $persen }}%)
        </p>
    </div>
</div>

{{-- ================= DAFTAR RAPOR ================= --}}
<div class="bg-white rounded-3xl shadow-sm border overflow-hidden mb-8">

    <div class="p-6 border-b flex items-center gap-2">
        <i data-feather="users" class="w-4 h-4 text-gray-600"></i>
        <h3 class="font-semibold text-gray-800 text-sm">
            Daftar Rapor Peserta
        </h3>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="px-6 py-3 text-left">Peserta</th>
                <th class="px-6 py-3 text-center">Status</th>
                <th class="px-6 py-3 text-center">Aksi</th>
            </tr>
            </thead>

            <tbody class="divide-y">
            @forelse($raporTugas->rapors as $r)
            <tr class="hover:bg-gray-50">

                <td class="px-6 py-4 font-medium text-gray-800">
                    {{ $r->peserta->nama }}
                </td>

                <td class="px-6 py-4 text-center">
                    <span class="px-3 py-1 rounded-full text-xs font-medium
                        @switch($r->status)
                            @case('draft') bg-gray-100 text-gray-700 @break
                            @case('submitted') bg-yellow-100 text-yellow-700 @break
                            @case('revision') bg-red-100 text-red-700 @break
                            @case('approved') bg-green-100 text-green-700 @break
                        @endswitch">
                        {{ ucfirst($r->status) }}
                    </span>
                </td>

                <td class="px-6 py-4 text-center">
                    <div class="flex items-center justify-center gap-2">

                        <a href="{{ route('admin.rapor.verifikasi.show', $r->id) }}"
                           class="inline-flex items-center gap-1
                                  px-3 py-1.5 rounded-lg
                                  text-xs font-medium
                                  border border-indigo-200
                                  text-indigo-600 hover:bg-indigo-50">
                            <i data-feather="eye" class="w-3 h-3"></i>
                            Lihat
                        </a>

                        @if(in_array($r->status, ['submitted','revision']))
                        <a href="{{ route('admin.rapor.verifikasi.show', $r->id) }}"
                           class="inline-flex items-center gap-1
                                  px-3 py-1.5 rounded-lg
                                  text-xs font-medium
                                  bg-green-600 hover:bg-green-700
                                  text-white">
                            <i data-feather="check" class="w-3 h-3"></i>
                            Verifikasi
                        </a>
                        @elseif($r->status === 'approved')
                        <span class="inline-flex items-center gap-1
                                     text-xs text-green-600 font-medium">
                            <i data-feather="check-circle" class="w-3 h-3"></i>
                            Disetujui
                        </span>
                        @endif

                    </div>
                </td>

            </tr>
            @empty
            <tr>
                <td colspan="3" class="px-6 py-12 text-center text-gray-500">
                    <i data-feather="inbox" class="w-8 h-8 mx-auto mb-2"></i>
                    Belum ada rapor dibuat
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ================= VERIFIKASI SEMUA ================= --}}
@php
    $jumlahSubmitted = $raporTugas->rapors
        ->where('status', 'submitted')
        ->count();
@endphp

@if($jumlahSubmitted > 0)
<div class="bg-white rounded-3xl shadow-sm border p-6
            flex flex-col sm:flex-row
            justify-between items-start sm:items-center gap-4">

    <p class="text-sm text-gray-700 flex items-center gap-2">
        <i data-feather="alert-circle" class="w-4 h-4"></i>
        {{ $jumlahSubmitted }} rapor siap diverifikasi
    </p>

    <form method="POST"
          action="{{ route('admin.rapor.verifikasi.approveAll', $raporTugas->id) }}"
          onsubmit="return confirm('Setujui SEMUA rapor yang sudah disubmit?')">
        @csrf
        @method('PATCH')

        <button
            class="flex items-center gap-2
                   bg-green-600 hover:bg-green-700
                   text-white px-6 py-2.5 rounded-xl
                   text-sm font-semibold">
            <i data-feather="check-square" class="w-4 h-4"></i>
            Verifikasi Semua
        </button>
    </form>

</div>
@endif

@endsection
