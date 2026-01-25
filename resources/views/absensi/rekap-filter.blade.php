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

                <div class="px-3 py-2 bg-white border border-[#E3EEF0]
                            rounded-lg text-sm text-gray-700">
                    {{ auth()->user()->sekolah->nama_sekolah }}
                </div>
            @endif
        </div>

        {{-- TANGGAL --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Tanggal Mulai
            </label>
            <input type="date" name="tanggal_mulai"
                   value="{{ request('tanggal_mulai') }}"
                   class="w-full bg-white border border-[#E3EEF0]
                          rounded-lg px-3 py-2 text-sm">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Tanggal Selesai
            </label>
            <input type="date" name="tanggal_selesai"
                   value="{{ request('tanggal_selesai') }}"
                   class="w-full bg-white border border-[#E3EEF0]
                          rounded-lg px-3 py-2 text-sm">
        </div>

        {{-- ACTION --}}
        <div class="flex gap-2">
            <button type="submit"
                class="flex-1 bg-[#8FBFC2] hover:bg-[#6FA9AD]
                       text-gray-900 px-4 py-2 rounded-lg">
                Tampilkan
            </button>

            <a href="{{ route('absensi.rekap.filter') }}"
               class="flex-1 bg-white border border-[#E3EEF0]
                      px-4 py-2 rounded-lg text-center">
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
        class="w-full sm:w-auto inline-flex items-center justify-center gap-2
               bg-white border border-[#E3EEF0]
               hover:bg-[#F6FAFB]
               px-4 py-2 rounded-lg">
        <i data-feather="file-text" class="w-4 h-4"></i>
        Export PDF
    </button>
</form>
@endif


{{-- TABLE --}}
<div class="space-y-4 md:hidden">

@forelse($absensis as $a)
<div class="bg-white border border-[#E3EEF0]
            rounded-xl p-4 shadow-sm text-sm">

    <div class="font-semibold text-gray-800">
        {{ $a->peserta->nama ?? '-' }}
    </div>

    <div class="text-gray-600 text-xs mt-1">
        {{ $a->jadwal->sekolah->nama_sekolah ?? '-' }}
    </div>

    <div class="mt-2">
        <span class="text-xs font-medium">Kegiatan:</span>
        {{ $a->jadwal->nama_kegiatan ?? '-' }}
    </div>

    <div class="mt-1 text-xs text-gray-600">
        {{ \Carbon\Carbon::parse($a->jadwal->tanggal_mulai)->format('d/m/Y') }}
    </div>

    <div class="mt-3">
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
    </div>
</div>
@empty
<div class="text-center text-gray-500 py-6">
    Tidak ada data sesuai filter
</div>
@endforelse

</div>
<div class="hidden md:block bg-white border border-[#E3EEF0]
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
@foreach($absensis as $i => $a)
<tr>
    <td class="px-4 py-2">{{ $i + 1 }}</td>
    <td class="px-4 py-2 font-medium">{{ $a->peserta->nama ?? '-' }}</td>
    <td class="px-4 py-2">{{ $a->jadwal->sekolah->nama_sekolah ?? '-' }}</td>
    <td class="px-4 py-2">{{ $a->jadwal->nama_kegiatan ?? '-' }}</td>
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
@endforeach
</tbody>
</table>
</div>


@endsection
