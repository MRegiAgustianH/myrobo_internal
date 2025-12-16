@extends('layouts.app')

@section('header')
Absensi – {{ $jadwal->sekolah->nama_sekolah }} / {{$jadwal->tanggal_mulai}}
@endsection

@section('content')

@if(session('success'))
<div class="mb-4 p-3 rounded bg-green-100 text-green-700">
    {{ session('success') }}
</div>
@endif

<form action="{{ route('absensi.store', $jadwal->id) }}" method="POST">
@csrf

<div class="bg-white rounded-lg shadow overflow-x-auto">

<table class="min-w-full text-sm border">
<thead class="bg-gray-100">
<tr>
    <th class="px-4 py-3 w-12">No</th>
    <th class="px-4 py-3 text-left">Nama Peserta</th>
    <th class="px-4 py-3 text-center">Hadir</th>
    <th class="px-4 py-3 text-center">Sakit</th>
    <th class="px-4 py-3 text-center">Izin</th>
    <th class="px-4 py-3 text-center">Alfa</th>
    <th class="px-4 py-3">Keterangan</th>
</tr>
</thead>

<tbody class="divide-y">
@foreach($pesertas as $i => $p)

@php
    $absen = $absensiMap[$p->id] ?? null;
@endphp

<tr class="hover:bg-gray-50">
    <td class="px-4 py-2">{{ $i + 1 }}</td>

    <td class="px-4 py-2 font-medium">
        {{ $p->nama }}
    </td>

    {{-- HADIR --}}
    <td class="text-center">
        <input type="checkbox"
            name="absensi[{{ $p->id }}][status]"
            value="hadir"
            class="status-checkbox"
            data-peserta="{{ $p->id }}"
            {{ $absen?->status === 'hadir' ? 'checked' : '' }}>
    </td>

    {{-- SAKIT --}}
    <td class="text-center">
        <input type="checkbox"
            name="absensi[{{ $p->id }}][status]"
            value="sakit"
            class="status-checkbox"
            data-peserta="{{ $p->id }}"
            {{ $absen?->status === 'sakit' ? 'checked' : '' }}>
    </td>

    {{-- IZIN --}}
    <td class="text-center">
        <input type="checkbox"
            name="absensi[{{ $p->id }}][status]"
            value="izin"
            class="status-checkbox"
            data-peserta="{{ $p->id }}"
            {{ $absen?->status === 'izin' ? 'checked' : '' }}>
    </td>

    {{-- ALFA --}}
    <td class="text-center">
        <input type="checkbox"
            name="absensi[{{ $p->id }}][status]"
            value="alfa"
            class="status-checkbox"
            data-peserta="{{ $p->id }}"
            {{ $absen?->status === 'alfa' ? 'checked' : '' }}>
    </td>

    {{-- KETERANGAN --}}
    <td class="px-4 py-2">
        <input type="text"
            name="absensi[{{ $p->id }}][keterangan]"
            value="{{ $absen->keterangan ?? '' }}"
            class="w-full px-3 py-1 border rounded text-sm"
            placeholder="Opsional">
    </td>
</tr>

@endforeach
</tbody>
</table>
</div>

<div class="mt-4 flex justify-end">
    <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">
        💾 Simpan Absensi
    </button>
</div>

</form>

{{-- SCRIPT EKSKLUSIF CHECKBOX --}}
<script>
document.querySelectorAll('.status-checkbox').forEach(cb => {
    cb.addEventListener('change', function () {

        const pesertaId = this.dataset.peserta;

        if (this.checked) {
            document
                .querySelectorAll(
                    `.status-checkbox[data-peserta="${pesertaId}"]`
                )
                .forEach(other => {
                    if (other !== this) other.checked = false;
                });
        }
    });
});
</script>

@endsection
