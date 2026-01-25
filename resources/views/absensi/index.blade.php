@extends('layouts.app')

@section('header')
Absensi – {{ $jadwal->sekolah->nama_sekolah }} / {{ $jadwal->tanggal_mulai }}
@endsection

@section('content')

@if(session('success'))
<div class="mb-4 p-3 rounded-xl bg-green-100 text-green-700 border border-green-200 text-sm">
    {{ session('success') }}
</div>
@endif


<form action="{{ route('absensi.store', $jadwal->id) }}" method="POST">
@csrf


<div class="space-y-4 md:hidden">
@foreach($pesertas as $p)
@php $absen = $absensiMap[$p->id] ?? null; @endphp

<div class="bg-white border border-[#E3EEF0] rounded-2xl p-4 shadow-sm">
    <div class="font-semibold text-gray-800 mb-3">
        {{ $p->nama }}
    </div>

    <div class="grid grid-cols-2 gap-3 text-sm mb-3">
        @foreach(['hadir','sakit','izin','alfa'] as $status)
        <label class="flex items-center gap-2">
            <input type="checkbox"
                name="absensi[{{ $p->id }}][status]"
                value="{{ $status }}"
                data-peserta="{{ $p->id }}"
                class="status-checkbox w-5 h-5 rounded
                       border-gray-300 text-[#8FBFC2]
                       focus:ring-[#8FBFC2]"
                {{ $absen?->status === $status ? 'checked' : '' }}>
            <span class="capitalize">{{ $status }}</span>
        </label>
        @endforeach
    </div>

    <input type="text"
        name="absensi[{{ $p->id }}][keterangan]"
        value="{{ $absen->keterangan ?? '' }}"
        placeholder="Keterangan (opsional)"
        class="w-full border border-[#E3EEF0]
               rounded-lg px-3 py-2 text-sm
               focus:ring-2 focus:ring-[#8FBFC2]/60
               focus:border-[#8FBFC2]">
</div>
@endforeach
</div>
<div class="hidden md:block bg-white border border-[#E3EEF0]
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
@php $absen = $absensiMap[$p->id] ?? null; @endphp

<tr class="hover:bg-[#F6FAFB] transition">
    <td class="px-4 py-2">{{ $i + 1 }}</td>
    <td class="px-4 py-2 font-medium text-gray-800">{{ $p->nama }}</td>

    @foreach(['hadir','sakit','izin','alfa'] as $status)
    <td class="text-center">
        <input type="checkbox"
            name="absensi[{{ $p->id }}][status]"
            value="{{ $status }}"
            data-peserta="{{ $p->id }}"
            class="status-checkbox rounded border-gray-300
                   text-[#8FBFC2] focus:ring-[#8FBFC2]"
            {{ $absen?->status === $status ? 'checked' : '' }}>
    </td>
    @endforeach

    <td class="px-4 py-2">
        <input type="text"
            name="absensi[{{ $p->id }}][keterangan]"
            value="{{ $absen->keterangan ?? '' }}"
            class="w-full border border-[#E3EEF0]
                   rounded-lg px-3 py-1.5 text-sm">
    </td>
</tr>
@endforeach
</tbody>
</table>
</div>


<div class="mt-6">
    <button
        class="w-full md:w-auto
               inline-flex justify-center items-center gap-2
               bg-gradient-to-r from-[#8FBFC2] to-[#7AAEB1]
               hover:from-[#7AAEB1] hover:to-[#6FA9AD]
               text-gray-900 font-semibold
               px-6 py-3 rounded-xl
               shadow-sm transition">
        <i data-feather="save" class="w-4 h-4"></i>
        Simpan Absensi
    </button>
</div>
</form>


</form>

{{-- SCRIPT EKSKLUSIF CHECKBOX (TETAP) --}}
<script>
document.querySelectorAll('.status-checkbox').forEach(cb => {
    cb.addEventListener('change', function () {
        const pesertaId = this.dataset.peserta;
        if (this.checked) {
            document
                .querySelectorAll(`.status-checkbox[data-peserta="${pesertaId}"]`)
                .forEach(other => {
                    if (other !== this) other.checked = false;
                });
        }
    });
});
</script>


@endsection
