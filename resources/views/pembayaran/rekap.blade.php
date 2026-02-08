@extends('layouts.app')

@section('header')
Rekap Pembayaran
@endsection

@section('content')

{{-- ================= FILTER ================= --}}
@php $jenis = request('jenis_peserta'); @endphp

<form method="GET"
      class="bg-[#F6FAFB] border border-[#E3EEF0]
             rounded-2xl shadow-sm mb-6 p-5">

    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">

        {{-- JENIS PESERTA --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Jenis Peserta
            </label>
            <select name="jenis_peserta"
                    id="jenisPeserta"
                    class="w-full bg-white border border-[#E3EEF0]
                           rounded-lg px-3 py-2 text-sm">
                <option value="">Semua</option>
                <option value="sekolah" {{ $jenis === 'sekolah' ? 'selected' : '' }}>
                    Sekolah
                </option>
                <option value="home_private" {{ $jenis === 'home_private' ? 'selected' : '' }}>
                    Home Private
                </option>
            </select>
        </div>

        {{-- SEKOLAH --}}
        <div id="filterSekolah" class="{{ $jenis === 'home_private' ? 'hidden' : '' }}">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Sekolah
            </label>

            @if(auth()->user()->isAdmin())
                <select name="sekolah_id"
                        class="w-full bg-white border border-[#E3EEF0]
                               rounded-lg px-3 py-2 text-sm">
                    <option value="">-- Semua Sekolah --</option>
                    @foreach($sekolahs as $s)
                        <option value="{{ $s->id }}"
                            {{ (string)$sekolahId === (string)$s->id ? 'selected' : '' }}>
                            {{ $s->nama_sekolah }}
                        </option>
                    @endforeach
                </select>
            @else
                <input type="hidden" name="sekolah_id" value="{{ auth()->user()->sekolah_id }}">
                <div class="px-3 py-2 bg-white border border-[#E3EEF0]
                            rounded-lg text-sm text-gray-700">
                    {{ auth()->user()->sekolah->nama_sekolah }}
                </div>
            @endif
        </div>

        {{-- BULAN --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
            <select name="bulan"
                    class="w-full bg-white border border-[#E3EEF0]
                           rounded-lg px-3 py-2 text-sm">
                @for($i=1;$i<=12;$i++)
                    <option value="{{ $i }}" {{ (int)$bulan === $i ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
        </div>

        {{-- TAHUN --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
            <input type="number" name="tahun"
                   value="{{ $tahun }}"
                   class="w-full bg-white border border-[#E3EEF0]
                          rounded-lg px-3 py-2 text-sm">
        </div>

        {{-- ACTION --}}
        <div class="flex gap-2">
            <button class="flex-1 bg-[#8FBFC2] hover:bg-[#6FA9AD]
                           text-gray-900 px-4 py-2 rounded-lg">
                Tampilkan
            </button>
            <a href="{{ url()->current() }}"
               class="flex-1 bg-white border border-[#E3EEF0]
                      px-4 py-2 rounded-lg text-center text-sm">
                Reset
            </a>
        </div>

    </div>
</form>

{{-- ================= EXPORT ================= --}}
@if($bulan && $tahun)
<form method="GET"
      action="{{ route('pembayaran.rekap.export-pdf') }}"
      target="_blank"
      class="mb-4">

    <input type="hidden" name="bulan" value="{{ $bulan }}">
    <input type="hidden" name="tahun" value="{{ $tahun }}">

    @if(!empty(request('jenis_peserta')))
        <input type="hidden" name="jenis_peserta" value="{{ request('jenis_peserta') }}">
    @endif

    @if(!empty($sekolahId))
        <input type="hidden" name="sekolah_id" value="{{ $sekolahId }}">
    @endif

    <button
        class="inline-flex items-center gap-2
               bg-white border border-[#E3EEF0]
               hover:bg-[#F6FAFB]
               px-4 py-2 rounded-lg
               text-sm font-medium">
        <i data-feather="file-text" class="w-4 h-4"></i>
        Export Rekap PDF
    </button>
</form>
@endif

{{-- ================= TOTAL ================= --}}
<div class="mt-4 mb-4 text-center font-semibold text-gray-800">
    Total Lunas:
    <span class="block sm:inline text-gray-900">
        Rp {{ number_format($totalLunas,0,',','.') }}
    </span>
</div>

{{-- ================= MOBILE ================= --}}
<div class="space-y-4 md:hidden">

@forelse($pembayarans as $p)
@php
    $namaPeserta = $p->jenis_peserta === 'home_private'
        ? $p->homePrivate?->nama_peserta
        : $p->peserta?->nama;

    $namaSekolah = $p->jenis_peserta === 'home_private'
        ? 'Home Private'
        : ($p->sekolah?->nama_sekolah ?? '-');
@endphp

<div class="border rounded-xl p-4 shadow-sm text-sm
            {{ $p->status === 'belum' ? 'bg-red-50/70' : 'bg-white' }}">

    <div class="font-semibold text-gray-800">
        {{ $namaPeserta ?? '-' }}
    </div>

    <div class="text-xs text-gray-600 mt-1">
        {{ $namaSekolah }}
    </div>

    <div class="mt-2 text-xs">
        Tanggal:
        <span class="font-medium">
            {{ $p->tanggal_bayar?->format('d/m/Y') ?? '-' }}
        </span>
    </div>

    <div class="mt-2 font-semibold">
        Rp {{ number_format($p->jumlah ?? 0,0,',','.') }}
    </div>

    <div class="mt-3">
        <span class="px-2 py-1 rounded-full text-xs font-semibold
            {{ $p->status === 'lunas'
                ? 'bg-green-100 text-green-700'
                : 'bg-red-100 text-red-700' }}">
            {{ strtoupper($p->status) }}
        </span>
    </div>
</div>
@empty
<div class="text-center text-gray-500 py-6">
    Tidak ada data pembayaran
</div>
@endforelse

</div>


{{-- ================= DESKTOP ================= --}}
<div class="hidden md:block bg-white border border-[#E3EEF0]
            rounded-2xl shadow-sm overflow-x-auto">

<table class="min-w-full text-sm">
<thead class="bg-[#F6FAFB] border-b border-[#E3EEF0]">
<tr>
    <th class="px-3 py-2">No</th>
    <th class="px-3 py-2 text-left">Peserta</th>
    <th class="px-3 py-2">Jenis</th>
    <th class="px-3 py-2">Sekolah</th>
    <th class="px-3 py-2 text-center">Tanggal</th>
    <th class="px-3 py-2 text-right">Jumlah</th>
    <th class="px-3 py-2 text-center">Status</th>
</tr>
</thead>

<tbody class="divide-y divide-[#E3EEF0]">
@foreach($pembayarans as $i => $p)
@php
    $namaPeserta = $p->jenis_peserta === 'home_private'
        ? $p->homePrivate?->nama_peserta
        : $p->peserta?->nama;

    $namaSekolah = $p->jenis_peserta === 'home_private'
        ? 'Home Private'
        : ($p->sekolah?->nama_sekolah ?? '-');
@endphp

<tr class="{{ $p->status === 'belum' ? 'bg-red-50/60' : '' }}">
    <td class="px-3 py-2">{{ $i+1 }}</td>
    <td class="px-3 py-2 font-medium">{{ $namaPeserta ?? '-' }}</td>
    <td class="px-3 py-2 capitalize">{{ str_replace('_',' ',$p->jenis_peserta) }}</td>
    <td class="px-3 py-2">{{ $namaSekolah }}</td>
    <td class="px-3 py-2 text-center">
        {{ $p->tanggal_bayar?->format('d/m/Y') ?? '-' }}
    </td>
    <td class="px-3 py-2 text-right font-medium">
        Rp {{ number_format($p->jumlah ?? 0,0,',','.') }}
    </td>
    <td class="px-3 py-2 text-center">
        <span class="px-2 py-1 rounded-full text-xs font-semibold
            {{ $p->status === 'lunas'
                ? 'bg-green-100 text-green-700'
                : 'bg-red-100 text-red-700' }}">
            {{ strtoupper($p->status) }}
        </span>
    </td>
</tr>
@endforeach
</tbody>
</table>
</div>




@endsection


@push('scripts')
<script>
document.getElementById('jenisPeserta')?.addEventListener('change', function () {
    const sekolah = document.getElementById('filterSekolah');
    if (this.value === 'home_private') {
        sekolah.classList.add('hidden');
    } else {
        sekolah.classList.remove('hidden');
    }
});
</script>
@endpush
