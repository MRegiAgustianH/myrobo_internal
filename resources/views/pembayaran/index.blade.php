@extends('layouts.app')

@section('header')
Pembayaran Bulanan
@endsection

@section('content')

@if(session('success'))
<div class="mb-4 p-3 rounded bg-green-100 text-green-700">
    {{ session('success') }}
</div>
@endif

{{-- FILTER --}}
<form method="GET" class="bg-white p-4 rounded shadow mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

        <div>
            <label class="text-sm font-medium">Sekolah</label>
            <select name="sekolah_id" class="w-full border rounded px-3 py-2">
                <option value="">-- Pilih Sekolah --</option>
                @foreach($sekolahs as $s)
                <option value="{{ $s->id }}"
                    {{ $sekolahId == $s->id ? 'selected' : '' }}>
                    {{ $s->nama_sekolah }}
                </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-sm font-medium">Bulan</label>
            <select name="bulan" class="w-full border rounded px-3 py-2">
                @for($i=1;$i<=12;$i++)
                <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                </option>
                @endfor
            </select>
        </div>

        <div>
            <label class="text-sm font-medium">Tahun</label>
            <input type="number" name="tahun" value="{{ $tahun }}"
                class="w-full border rounded px-3 py-2">
        </div>

        <div class="flex items-end">
            <button class="bg-blue-600 text-white px-4 py-2 rounded w-full">
                🔍 Tampilkan
            </button>
        </div>

    </div>
</form>

<form action="{{ route('pembayaran.store') }}" method="POST">
@csrf

<div class="bg-white rounded shadow overflow-x-auto">

<table class="min-w-full text-sm border">
<thead class="bg-gray-100">
<tr>
    <th class="px-3 py-2">No</th>
    <th class="px-3 py-2 text-left">Peserta</th>
    <th class="px-3 py-2 text-center">Lunas</th>
    <th class="px-3 py-2">Jumlah</th>
    <th class="px-3 py-2">Tanggal Bayar</th>
</tr>
</thead>

<tbody class="divide-y">
@foreach($pesertas as $i => $p)

@php
$pembayaran = $pembayaranMap[$p->id] ?? null;
@endphp

<tr>
    <td class="px-3 py-2">{{ $i+1 }}</td>

    <td class="px-3 py-2 font-medium">
        {{ $p->nama }}
        <input type="hidden"
            name="pembayaran[{{ $p->id }}][sekolah_id]"
            value="{{ $p->sekolah_id }}">
    </td>

    {{-- STATUS --}}
    <td class="text-center">
        <input type="checkbox"
            name="pembayaran[{{ $p->id }}][status]"
            value="lunas"
            {{ $pembayaran?->status === 'lunas' ? 'checked' : '' }}>
    </td>

    {{-- JUMLAH --}}
    <td class="px-3 py-2">
        <input type="number"
            name="pembayaran[{{ $p->id }}][jumlah]"
            value="{{ $pembayaran->jumlah ?? '' }}"
            class="w-full border rounded px-2 py-1 text-sm"
            placeholder="0">
    </td>

    {{-- TANGGAL --}}
    <td class="px-3 py-2">
        <input type="date"
        name="pembayaran[{{ $p->id }}][tanggal_bayar]"
        value="{{ $pembayaran?->tanggal_bayar?->format('Y-m-d') }}"
        class="w-full border rounded px-2 py-1 text-sm"
        {{ $pembayaran?->status !== 'lunas' ? 'disabled' : '' }}>

    </td>
</tr>

@endforeach
</tbody>
</table>

</div>

<input type="hidden" name="bulan" value="{{ $bulan }}">
<input type="hidden" name="tahun" value="{{ $tahun }}">

<div class="mt-4 flex justify-end">
    <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">
        💾 Simpan Pembayaran
    </button>
</div>

</form>

@endsection
