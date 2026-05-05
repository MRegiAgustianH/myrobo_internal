@extends('layouts.app')

@php
    $bolehAbsen =
        auth()->user()->isAdmin()
        || auth()->user()->isAdminSekolah()
        || (auth()->user()->isInstruktur() && $jadwal->isDalamJamAbsensi());
@endphp

@section('header')
Absensi –
{{ $jadwal->jenis_jadwal === 'sekolah'
    ? $jadwal->sekolah->nama_sekolah
    : $jadwal->homePrivate->nama_kegiatan
}}
/ {{ $jadwal->tanggal_mulai }}
@endsection

@section('content')

{{-- ================= FLASH MESSAGE ================= --}}
@if(session('success'))
<div class="mb-4 p-3 rounded-xl bg-green-100 text-green-700 border text-sm">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-4 p-3 rounded-xl bg-red-100 text-red-700 border text-sm">
    {{ session('error') }}
</div>
@endif

{{-- ================================================= --}}
{{-- ABSENSI INSTRUKTUR --}}
{{-- ================================================= --}}
@if(auth()->user()->isInstruktur())
<form
    action="{{ route('instruktur.absensi.store', $jadwal) }}"
    method="POST"
    class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-2xl"
>
@csrf

<h3 class="font-semibold mb-3">Absensi Instruktur</h3>

@if(!empty($absensiInstruktur))
    <p class="text-sm text-gray-600 mb-2">
        Status saat ini:
        <strong class="capitalize">{{ $absensiInstruktur->status }}</strong>
    </p>
@endif

<div class="flex flex-wrap gap-4 mb-3">
@foreach(['hadir','sakit','izin','alfa'] as $status)
<label class="flex items-center gap-2">
    <input
        type="radio"
        name="status"
        value="{{ $status }}"
        {{ !$bolehAbsen ? 'disabled' : '' }}
        {{ $absensiInstruktur?->status === $status ? 'checked' : '' }}
        required
    >
    <span class="capitalize">{{ $status }}</span>
</label>
@endforeach
</div>

<textarea
    name="keterangan"
    class="w-full border rounded-lg px-3 py-2 text-sm"
    placeholder="Keterangan (opsional)"
    {{ !$bolehAbsen ? 'disabled' : '' }}
>{{ $absensiInstruktur->keterangan ?? '' }}</textarea>

@if($bolehAbsen)
<button
    type="submit"
    class="mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg"
>
    Simpan Absensi Instruktur
</button>
@else
<div class="mt-2 text-sm text-red-600">
    Absensi instruktur terkunci. Hanya dapat diedit oleh admin.
</div>
@endif
</form>
@endif

{{-- ================================================= --}}
{{-- ABSENSI PESERTA --}}
{{-- ================================================= --}}
<form action="{{ route('absensi.store', $jadwal) }}" method="POST">
@csrf

{{-- ================= MOBILE ================= --}}
<div class="space-y-4 md:hidden">
@foreach($pesertas as $p)
@php
    $key = $p->id;
    $absen = $absensiMap[$key] ?? null;
    $namaPeserta = $jadwal->jenis_jadwal === 'sekolah'
        ? $p->nama
        : $p->nama_peserta;
@endphp

<div class="bg-white border rounded-2xl p-4 shadow-sm">
<input type="hidden" name="absensi[{{ $key }}][__present]" value="1">

<div class="font-semibold mb-3">{{ $namaPeserta }}</div>

<div class="grid grid-cols-2 gap-3 mb-3">
@foreach(['hadir','sakit','izin','alfa'] as $status)
<label class="flex items-center gap-2">
<input
    type="radio"
    name="absensi[{{ $key }}][status]"
    value="{{ $status }}"
    {{ !$bolehAbsen ? 'disabled' : '' }}
    {{ $absen?->status === $status ? 'checked' : '' }}
>
<span class="capitalize">{{ $status }}</span>
</label>
@endforeach
</div>

<input
    type="text"
    name="absensi[{{ $key }}][keterangan]"
    value="{{ $absen->keterangan ?? '' }}"
    placeholder="Keterangan"
    class="w-full border rounded-lg px-3 py-2 text-sm"
    {{ !$bolehAbsen ? 'disabled' : '' }}
>
</div>
@endforeach
</div>

{{-- ================= DESKTOP ================= --}}
<div class="hidden md:block bg-white border rounded-2xl shadow-sm mt-4 overflow-x-auto">
<table class="min-w-full text-sm">
<thead>
<tr class="bg-gray-50">
    <th class="px-3 py-2">No</th>
    <th class="px-3 py-2 text-left">Nama Peserta</th>
    <th class="px-3 py-2 text-center">H</th>
    <th class="px-3 py-2 text-center">S</th>
    <th class="px-3 py-2 text-center">I</th>
    <th class="px-3 py-2 text-center">A</th>
    <th class="px-3 py-2 text-left">Keterangan</th>
</tr>
</thead>
<tbody>
@foreach($pesertas as $i => $p)
@php
    $key = $p->id;
    $absen = $absensiMap[$key] ?? null;
    $namaPeserta = $jadwal->jenis_jadwal === 'sekolah'
        ? $p->nama
        : $p->nama_peserta;
@endphp
<tr class="border-t">
<input type="hidden" name="absensi[{{ $key }}][__present]" value="1">

<td class="px-3 py-2">{{ $i + 1 }}</td>
<td class="px-3 py-2 font-medium">{{ $namaPeserta }}</td>

@foreach(['hadir','sakit','izin','alfa'] as $status)
<td class="text-center">
<input
    type="radio"
    name="absensi[{{ $key }}][status]"
    value="{{ $status }}"
    {{ !$bolehAbsen ? 'disabled' : '' }}
    {{ $absen?->status === $status ? 'checked' : '' }}
>
</td>
@endforeach

<td class="px-3 py-2">
<input
    type="text"
    name="absensi[{{ $key }}][keterangan]"
    value="{{ $absen->keterangan ?? '' }}"
    class="w-full border rounded px-2 py-1 text-sm"
    {{ !$bolehAbsen ? 'disabled' : '' }}
>
</td>
</tr>
@endforeach
</tbody>
</table>
</div>

@if($bolehAbsen)
<div class="mt-6">
<button
    type="submit"
    class="px-6 py-3 bg-[#8FBFC2] rounded-xl font-semibold text-gray-900"
>
    Simpan Absensi Peserta
</button>
</div>
@else
<div class="mt-4 text-sm text-red-600">
    Absensi peserta terkunci. Hanya dapat diedit oleh admin.
</div>
@endif

</form>
@endsection
