@extends('layouts.app')

@section('header')
Rekap Pembayaran
@endsection

@section('content')

<form method="GET" class="bg-white p-4 rounded-lg shadow mb-6">
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
                            {{ $sekolahId == $s->id ? 'selected' : '' }}>
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

        {{-- BULAN --}}
        <div>
            <label class="block text-sm font-medium mb-1">Bulan</label>
            <select name="bulan"
                class="w-full border rounded px-3 py-2 text-sm">
                @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month((int)$i)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
        </div>

        {{-- TAHUN --}}
        <div>
            <label class="block text-sm font-medium mb-1">Tahun</label>
            <input type="number"
                   name="tahun"
                   value="{{ $tahun }}"
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>

        {{-- BUTTON --}}
        <div>
            <button
                class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                🔍 Tampilkan
            </button>
        </div>

    </div>
</form>


@if($pembayarans->count())
<form method="GET" action="{{ route('pembayaran.rekap.export-pdf') }}" target="_blank">
    <input type="hidden" name="sekolah_id" value="{{ $sekolahId }}">
    <input type="hidden" name="bulan" value="{{ $bulan }}">
    <input type="hidden" name="tahun" value="{{ $tahun }}">

    <button class="mb-4 bg-red-600 text-white px-4 py-2 rounded">
        📄 Export PDF
    </button>
</form>
@endif

<div class="bg-white rounded shadow overflow-x-auto">
<table class="min-w-full text-sm border">
<thead class="bg-gray-100">
<tr>
    <th class="px-3 py-2">No</th>
    <th class="px-3 py-2 text-left">Peserta</th>
    <th class="px-3 py-2 text-left">Sekolah</th>
    <th class="px-3 py-2 text-center">Tanggal</th>
    <th class="px-3 py-2 text-right">Jumlah</th>
    <th class="px-3 py-2 text-center">Status</th>
</tr>
</thead>

<tbody class="divide-y">
@foreach($pembayarans as $i => $p)

<tr
    class="
        @if($p->status === 'belum')
            bg-red-50
        @endif
    "
>
    <td class="px-3 py-2">{{ $i+1 }}</td>

    <td class="px-3 py-2 font-medium">
        {{ $p->peserta->nama }}
    </td>

    <td class="px-3 py-2">
        {{ $p->sekolah->nama_sekolah }}
    </td>

    <td class="px-3 py-2 text-center">
        {{ \Carbon\Carbon::parse($p->tanggal_bayar)->format('d/m/Y') ?? '-' }}
    </td>

    <td class="px-3 py-2 text-right">
        Rp {{ number_format($p->jumlah,0,',','.') }}
    </td>

    <td class="px-3 py-2 text-center font-semibold
        @if($p->status === 'lunas')
            text-green-600
        @else
            text-red-600
        @endif
    ">
        {{ strtoupper($p->status) }}
    </td>
</tr>

@endforeach
</tbody>

</table>
</div>

<div class="mt-4 font-semibold text-right">
    Total Lunas: Rp {{ number_format($totalLunas,0,',','.') }}
</div>

@endsection
