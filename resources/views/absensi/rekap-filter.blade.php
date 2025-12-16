@extends('layouts.app')

@section('header')
Rekap Absensi
@endsection

@section('content')

{{-- FILTER --}}
<form method="GET"
      action="{{ route('absensi.rekap.filter') }}"
      class="bg-[#F6FAFB] border border-[#E3EEF0]
             rounded-2xl shadow-sm mb-6 p-5">

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">

        {{-- SEKOLAH --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Sekolah
            </label>

            @if(auth()->user()->isAdmin())
                <select name="sekolah_id"
                        class="w-full bg-white border border-[#E3EEF0]
                               rounded-lg px-3 py-2 text-sm
                               focus:ring-2 focus:ring-[#8FBFC2]/60 focus:border-[#8FBFC2]">
                    <option value="">-- Semua Sekolah --</option>
                    @foreach($sekolahs as $s)
                        <option value="{{ $s->id }}"
                            {{ request('sekolah_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->nama_sekolah }}
                        </option>
                    @endforeach
                </select>
            @endif

            @if(auth()->user()->isAdminSekolah())
                <input type="hidden" name="sekolah_id"
                       value="{{ auth()->user()->sekolah_id }}">

                <div
                    class="px-3 py-2 bg-white border border-[#E3EEF0]
                           rounded-lg text-sm text-gray-700">
                    {{ auth()->user()->sekolah->nama_sekolah }}
                </div>
            @endif
        </div>

        {{-- TANGGAL MULAI --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Tanggal Mulai
            </label>
            <input type="date"
                   name="tanggal_mulai"
                   value="{{ request('tanggal_mulai') }}"
                   class="w-full bg-white border border-[#E3EEF0]
                          rounded-lg px-3 py-2 text-sm
                          focus:ring-2 focus:ring-[#8FBFC2]/60 focus:border-[#8FBFC2]">
        </div>

        {{-- TANGGAL SELESAI --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Tanggal Selesai
            </label>
            <input type="date"
                   name="tanggal_selesai"
                   value="{{ request('tanggal_selesai') }}"
                   class="w-full bg-white border border-[#E3EEF0]
                          rounded-lg px-3 py-2 text-sm
                          focus:ring-2 focus:ring-[#8FBFC2]/60 focus:border-[#8FBFC2]">
        </div>

        {{-- ACTION --}}
        <div class="flex gap-2">
            <button type="submit"
                class="flex-1 inline-flex items-center justify-center gap-2
                       bg-[#8FBFC2] hover:bg-[#6FA9AD]
                       text-gray-900 font-medium
                       px-4 py-2 rounded-lg transition">
                <i data-feather="search" class="w-4 h-4"></i>
                Tampilkan
            </button>

            <a href="{{ route('absensi.rekap.filter') }}"
               class="flex-1 inline-flex items-center justify-center gap-2
                      bg-white border border-[#E3EEF0]
                      hover:bg-[#F6FAFB]
                      text-gray-700 px-4 py-2 rounded-lg transition">
                <i data-feather="rotate-ccw" class="w-4 h-4"></i>
                Reset
            </a>
        </div>

    </div>
</form>

{{-- EXPORT --}}
@if(request()->filled('tanggal_mulai') && request()->filled('tanggal_selesai'))
<form method="GET"
      action="{{ route('absensi.rekap.export-pdf') }}"
      target="_blank"
      class="mb-4">

    <input type="hidden" name="sekolah_id" value="{{ request('sekolah_id') }}">
    <input type="hidden" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}">
    <input type="hidden" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}">

    <button
        class="inline-flex items-center gap-2
               bg-white border border-[#E3EEF0]
               hover:bg-[#F6FAFB]
               text-gray-800
               px-4 py-2 rounded-lg shadow-sm transition">
        <i data-feather="file-text" class="w-4 h-4"></i>
        Export PDF
    </button>
</form>
@endif

{{-- TABLE --}}
<div class="bg-white border border-[#E3EEF0]
            rounded-2xl shadow-sm overflow-x-auto">

<table class="min-w-full text-sm">
<thead class="bg-[#F6FAFB] border-b border-[#E3EEF0]">
<tr>
    <th class="px-4 py-2 text-left">No</th>
    <th class="px-4 py-2 text-left">Peserta</th>
    <th class="px-4 py-2 text-left">Sekolah</th>
    <th class="px-4 py-2 text-left">Kegiatan</th>
    <th class="px-4 py-2 text-center">Tanggal</th>
    <th class="px-4 py-2 text-center">Status</th>
</tr>
</thead>

<tbody class="divide-y divide-[#E3EEF0]">
@forelse($absensis as $i => $a)
<tr>
    <td class="px-4 py-2">{{ $i + 1 }}</td>

    <td class="px-4 py-2 font-medium">
        {{ $a->peserta->nama ?? '-' }}
    </td>

    <td class="px-4 py-2">
        {{ $a->jadwal->sekolah->nama_sekolah ?? '-' }}
    </td>

    <td class="px-4 py-2">
        {{ $a->jadwal->nama_kegiatan ?? '-' }}
    </td>

    <td class="px-4 py-2 text-center">
        {{ \Carbon\Carbon::parse($a->jadwal->tanggal_mulai)->format('d/m/Y') }}
    </td>

    <td class="px-4 py-2 text-center">
        <span class="px-2 py-1 rounded-full text-xs font-semibold
            @if($a->status === 'hadir')
                bg-green-100 text-green-700
            @elseif($a->status === 'sakit')
                bg-yellow-100 text-yellow-700
            @elseif($a->status === 'izin')
                bg-blue-100 text-blue-700
            @else
                bg-red-100 text-red-700
            @endif">
            {{ ucfirst($a->status) }}
        </span>
    </td>
</tr>
@empty
<tr>
    <td colspan="6" class="text-center py-6 text-gray-500">
        Tidak ada data sesuai filter
    </td>
</tr>
@endforelse
</tbody>
</table>

</div>

@endsection
