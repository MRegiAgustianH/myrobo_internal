@extends('layouts.app')

@section('header')
Rekap Absensi
@endsection

@section('content')

{{-- ================= FILTER ================= --}}
<form method="GET"
      action="{{ route('absensi.rekap.filter') }}"
      class="bg-[#F6FAFB] border border-[#E3EEF0]
             rounded-2xl shadow-sm mb-6 p-5">

    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">

        {{-- JENIS PESERTA --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Jenis Peserta
            </label>
            <select name="jenis_peserta"
                    id="jenis_peserta"
                    class="w-full bg-white border border-[#E3EEF0]
                           rounded-lg px-3 py-2 text-sm">
                <option value="">Semua</option>
                <option value="sekolah"
                    {{ request('jenis_peserta') === 'sekolah' ? 'selected' : '' }}>
                    Sekolah
                </option>
                <option value="home_private"
                    {{ request('jenis_peserta') === 'home_private' ? 'selected' : '' }}>
                    Home Private
                </option>
            </select>
        </div>

        {{-- SEKOLAH --}}
        <div id="sekolah-wrapper">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Sekolah
            </label>

            @if(auth()->user()->isAdmin())
                <select name="sekolah_id"
                        id="sekolah_id"
                        class="w-full bg-white border border-[#E3EEF0]
                               rounded-lg px-3 py-2 text-sm">
                    <option value="">-- Semua Sekolah --</option>
                    @foreach($sekolahs as $s)
                        <option value="{{ $s->id }}"
                            {{ (string)request('sekolah_id') === (string)$s->id ? 'selected' : '' }}>
                            {{ $s->nama_sekolah }}
                        </option>
                    @endforeach
                </select>
            @else
                <input type="hidden"
                       id="sekolah_id"
                       name="sekolah_id"
                       value="{{ auth()->user()->sekolah_id }}">
                <div class="px-3 py-2 bg-white border border-[#E3EEF0]
                            rounded-lg text-sm text-gray-700">
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
                   class="w-full bg-white border border-[#E3EEF0]
                          rounded-lg px-3 py-2 text-sm">
        </div>

        {{-- TANGGAL SELESAI --}}
        <div>
            <label class="block text-sm font-medium mb-1">Tanggal Selesai</label>
            <input type="date"
                   name="tanggal_selesai"
                   value="{{ request('tanggal_selesai') }}"
                   class="w-full bg-white border border-[#E3EEF0]
                          rounded-lg px-3 py-2 text-sm">
        </div>

        {{-- ACTION --}}
        <div class="flex gap-2">
            <button
                class="flex-1 bg-[#8FBFC2] hover:bg-[#6FA9AD]
                       px-4 py-2 rounded-lg font-semibold">
                Tampilkan
            </button>

            <a href="{{ route('absensi.rekap.filter') }}"
               class="flex-1 border border-[#E3EEF0]
                      px-4 py-2 rounded-lg text-center text-sm">
                Reset
            </a>
        </div>

    </div>
</form>



{{-- ================= EXPORT ================= --}}
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
               border border-[#E3EEF0]
               px-4 py-2 rounded-lg text-sm
               hover:bg-[#F6FAFB]">
        Export PDF
    </button>
</form>
@endif


{{-- ================= MOBILE ================= --}}
<div class="space-y-4 md:hidden">

@forelse($absensis as $a)

@php
    $namaPeserta = $a->isSekolah()
        ? $a->peserta?->nama
        : $a->homePrivate?->nama_peserta;

    $namaSekolah = $a->jadwal?->sekolah?->nama_sekolah ?? 'Home Private';
@endphp

<div class="bg-white border border-[#E3EEF0]
            rounded-xl p-4 shadow-sm text-sm">

    <div class="font-semibold text-gray-800">
        {{ $namaPeserta ?? '-' }}
    </div>

    <div class="text-xs text-gray-600">
        {{ $namaSekolah }}
    </div>

    <div class="mt-2 text-xs">
        <strong>Kegiatan:</strong>
        {{ $a->jadwal?->nama_kegiatan ?? '-' }}
    </div>

    <div class="text-xs mt-1">
        {{ optional($a->tanggal)->format('d/m/Y') }}
    </div>

    <div class="mt-3">
        <span class="px-2 py-1 rounded-full text-xs font-semibold
            @if($a->status === 'hadir') bg-green-100 text-green-700
            @elseif($a->status === 'sakit') bg-yellow-100 text-yellow-700
            @elseif($a->status === 'izin') bg-blue-100 text-blue-700
            @else bg-red-100 text-red-700 @endif">
            {{ ucfirst($a->status) }}
        </span>
    </div>

</div>

@empty
<div class="text-center text-gray-500 py-6">
    Tidak ada data absensi
</div>
@endforelse

</div>


{{-- ================= DESKTOP ================= --}}
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

@php
    $namaPeserta = $a->isSekolah()
        ? $a->peserta?->nama
        : $a->homePrivate?->nama_peserta;
@endphp

<tr>
    <td class="px-4 py-2">{{ $i + 1 }}</td>
    <td class="px-4 py-2 font-medium">
        {{ $namaPeserta ?? '-' }}
    </td>
    <td class="px-4 py-2">
        {{ $a->jadwal?->sekolah?->nama_sekolah ?? 'Home Private' }}
    </td>
    <td class="px-4 py-2">
        {{ $a->jadwal?->nama_kegiatan ?? '-' }}
    </td>
    <td class="px-4 py-2 text-center">
        {{ optional($a->tanggal)->format('d/m/Y') }}
    </td>
    <td class="px-4 py-2 text-center">
        <span class="px-2 py-1 rounded-full text-xs font-semibold
            @if($a->status === 'hadir') bg-green-100 text-green-700
            @elseif($a->status === 'sakit') bg-yellow-100 text-yellow-700
            @elseif($a->status === 'izin') bg-blue-100 text-blue-700
            @else bg-red-100 text-red-700 @endif">
            {{ ucfirst($a->status) }}
        </span>
    </td>
</tr>
@endforeach
</tbody>
</table>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const jenisSelect   = document.getElementById('jenis_peserta');
    const sekolahWrap   = document.getElementById('sekolah-wrapper');
    const sekolahSelect = document.getElementById('sekolah_id');

    function toggleSekolah() {
        if (jenisSelect.value === 'home_private') {
            sekolahWrap.classList.add('hidden');

            // kosongkan value agar tidak ikut terkirim
            if (sekolahSelect) {
                sekolahSelect.value = '';
            }
        } else {
            sekolahWrap.classList.remove('hidden');
        }
    }

    jenisSelect.addEventListener('change', toggleSekolah);

    // jalankan saat pertama load (penting untuk reload halaman)
    toggleSekolah();
});
</script>
@endpush


@endsection
