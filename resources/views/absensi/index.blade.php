@extends('layouts.app')

@section('header')
Absensi – {{ $jadwal->sekolah->nama_sekolah }} / {{ $jadwal->tanggal_mulai }}
@endsection

@section('content')

@if(session('success'))
<div class="mb-4 p-3 rounded-lg bg-green-100 text-green-700 border border-green-200">
    {{ session('success') }}
</div>
@endif

<form action="{{ route('absensi.store', $jadwal->id) }}" method="POST">
@csrf

<div class="bg-white border border-[#E3EEF0]
            rounded-2xl shadow-sm overflow-x-auto">

<table class="min-w-full text-sm">
<thead class="bg-[#F6FAFB] border-b border-[#E3EEF0]">
<tr class="text-gray-600">
    <th class="px-4 py-3 w-12 text-left">No</th>
    <th class="px-4 py-3 text-left">Nama Peserta</th>
    <th class="px-4 py-3 text-center">Hadir</th>
    <th class="px-4 py-3 text-center">Sakit</th>
    <th class="px-4 py-3 text-center">Izin</th>
    <th class="px-4 py-3 text-center">Alfa</th>
    <th class="px-4 py-3 text-left">Keterangan</th>
</tr>
</thead>

<tbody class="divide-y divide-[#E3EEF0]">
@foreach($pesertas as $i => $p)

@php
    $absen = $absensiMap[$p->id] ?? null;
@endphp

<tr class="hover:bg-[#F6FAFB] transition">
    <td class="px-4 py-2">{{ $i + 1 }}</td>

    <td class="px-4 py-2 font-medium text-gray-800">
        {{ $p->nama }}
    </td>

    {{-- HADIR --}}
    <td class="text-center">
        <input type="checkbox"
            name="absensi[{{ $p->id }}][status]"
            value="hadir"
            class="status-checkbox rounded border-gray-300
                   text-[#8FBFC2] focus:ring-[#8FBFC2]"
            data-peserta="{{ $p->id }}"
            {{ $absen?->status === 'hadir' ? 'checked' : '' }}>
    </td>

    {{-- SAKIT --}}
    <td class="text-center">
        <input type="checkbox"
            name="absensi[{{ $p->id }}][status]"
            value="sakit"
            class="status-checkbox rounded border-gray-300
                   text-[#8FBFC2] focus:ring-[#8FBFC2]"
            data-peserta="{{ $p->id }}"
            {{ $absen?->status === 'sakit' ? 'checked' : '' }}>
    </td>

    {{-- IZIN --}}
    <td class="text-center">
        <input type="checkbox"
            name="absensi[{{ $p->id }}][status]"
            value="izin"
            class="status-checkbox rounded border-gray-300
                   text-[#8FBFC2] focus:ring-[#8FBFC2]"
            data-peserta="{{ $p->id }}"
            {{ $absen?->status === 'izin' ? 'checked' : '' }}>
    </td>

    {{-- ALFA --}}
    <td class="text-center">
        <input type="checkbox"
            name="absensi[{{ $p->id }}][status]"
            value="alfa"
            class="status-checkbox rounded border-gray-300
                   text-[#8FBFC2] focus:ring-[#8FBFC2]"
            data-peserta="{{ $p->id }}"
            {{ $absen?->status === 'alfa' ? 'checked' : '' }}>
    </td>

    {{-- KETERANGAN --}}
    <td class="px-4 py-2">
        <input type="text"
            name="absensi[{{ $p->id }}][keterangan]"
            value="{{ $absen->keterangan ?? '' }}"
            class="w-full bg-white border border-[#E3EEF0]
                   rounded-lg px-3 py-1.5 text-sm
                   focus:ring-2 focus:ring-[#8FBFC2]/60
                   focus:border-[#8FBFC2]"
            placeholder="Opsional">
    </td>
</tr>

@endforeach
</tbody>
</table>
</div>

<div class="mt-5 flex justify-end">
    <button
        class="inline-flex items-center gap-2
               bg-gradient-to-r from-[#8FBFC2] to-[#7AAEB1]
               hover:from-[#7AAEB1] hover:to-[#6FA9AD]
               text-gray-900 font-semibold
               px-6 py-2.5 rounded-xl
               shadow-sm transition">
        <i data-feather="save" class="w-4 h-4"></i>
        Simpan Absensi
    </button>
</div>

</form>

{{-- SCRIPT EKSKLUSIF CHECKBOX (TETAP) --}}
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
