@extends('layouts.app')

@section('header')
Rekap Absensi 
@endsection

@section('content')

{{-- @dump(request()->all()) --}}


<form method="GET"
      action="{{ route('absensi.rekap.filter') }}"
      class="bg-white p-4 rounded-lg shadow mb-6">

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">

        {{-- SEKOLAH --}}
        <div>
            <label class="block text-sm font-medium mb-1">Sekolah</label>

            @if(auth()->user()->isAdmin())
                <select name="sekolah_id"
                    class="w-full border rounded px-3 py-2 text-sm">
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

                <div class="px-3 py-2 border rounded bg-gray-100 text-sm">
                    {{ auth()->user()->sekolah->nama_sekolah }}
                </div>
            @endif
        </div>

        {{-- TANGGAL MULAI --}}
        <div>
            <label class="block text-sm font-medium mb-1">Tanggal Mulai</label>
            <input type="date"
                   name="tanggal_mulai"
                   value="{{ request('tanggal_mulai') }}"
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>

        {{-- TANGGAL SELESAI --}}
        <div>
            <label class="block text-sm font-medium mb-1">Tanggal Selesai</label>
            <input type="date"
                   name="tanggal_selesai"
                   value="{{ request('tanggal_selesai') }}"
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>

        {{-- ACTION --}}
        <div class="flex gap-2">
            <button type="submit"
                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                🔍 Tampilkan
            </button>

            <a href="{{ route('absensi.rekap.filter') }}"
               class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm text-center">
                Reset
            </a>
        </div>

    </div>
</form>



@if(request()->filled('tanggal_mulai') && request()->filled('tanggal_selesai'))
<form method="GET"
      action="{{ route('absensi.rekap.export-pdf') }}"
      target="_blank"
      class="mb-4">

    <input type="hidden" name="sekolah_id" value="{{ request('sekolah_id') }}">
    <input type="hidden" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}">
    <input type="hidden" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}">

    <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
        📄 Export PDF
    </button>
</form>
@endif


{{-- HASIL --}}
<div class="bg-white rounded shadow overflow-x-auto">

<table class="min-w-full text-sm border">
<thead class="bg-gray-100">
<tr>
    <th class="px-4 py-2">No</th>
    <th class="px-4 py-2 text-left">Peserta</th>
    <th class="px-4 py-2 text-left">Sekolah</th>
    <th class="px-4 py-2 text-left">Kegiatan</th>
    <th class="px-4 py-2 text-center">Tanggal</th>
    <th class="px-4 py-2 text-center">Status</th>
</tr>
</thead>

<tbody class="divide-y">
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

    <td class="px-4 py-2 text-center font-semibold
        @if($a->status === 'hadir') text-green-600
        @elseif($a->status === 'sakit') text-yellow-600
        @elseif($a->status === 'izin') text-blue-600
        @else text-red-600 @endif
    ">
        {{ ucfirst($a->status) }}
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
