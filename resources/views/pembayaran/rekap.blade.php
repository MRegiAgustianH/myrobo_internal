@extends('layouts.app')

@section('header')
Rekap Pembayaran
@endsection

@section('content')

{{-- FILTER --}}
<form method="GET"
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
                            {{ $sekolahId == $s->id ? 'selected' : '' }}>
                            {{ $s->nama_sekolah }}
                        </option>
                    @endforeach
                </select>
            @endif

            @if(auth()->user()->isAdminSekolah())
                <input type="hidden" name="sekolah_id"
                       value="{{ auth()->user()->sekolah_id }}">
                <div
                    class="px-3 py-2 bg-white border border-[#E3EEF0]
                           rounded-lg text-sm text-gray-700">
                    {{ auth()->user()->sekolah->nama_sekolah }}
                </div>
            @endif
        </div>

        {{-- BULAN --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Bulan
            </label>
            <select name="bulan"
                    class="w-full bg-white border border-[#E3EEF0]
                           rounded-lg px-3 py-2 text-sm
                           focus:ring-2 focus:ring-[#8FBFC2]/60 focus:border-[#8FBFC2]">
                @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month((int)$i)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
        </div>

        {{-- TAHUN --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Tahun
            </label>
            <input type="number"
                   name="tahun"
                   value="{{ $tahun }}"
                   class="w-full bg-white border border-[#E3EEF0]
                          rounded-lg px-3 py-2 text-sm
                          focus:ring-2 focus:ring-[#8FBFC2]/60 focus:border-[#8FBFC2]">
        </div>

        {{-- BUTTON --}}
        <div>
            <button
                class="w-full inline-flex items-center justify-center gap-2
                       bg-[#8FBFC2] hover:bg-[#6FA9AD]
                       text-gray-900 font-medium
                       px-4 py-2 rounded-lg transition">
                <i data-feather="search" class="w-4 h-4"></i>
                Tampilkan
            </button>
        </div>

    </div>
</form>

{{-- EXPORT --}}
@if($pembayarans->count())
<form method="GET"
      action="{{ route('pembayaran.rekap.export-pdf') }}"
      target="_blank"
      class="mb-4">

    <input type="hidden" name="sekolah_id" value="{{ $sekolahId }}">
    <input type="hidden" name="bulan" value="{{ $bulan }}">
    <input type="hidden" name="tahun" value="{{ $tahun }}">

    <button
        class="inline-flex items-center gap-2
               bg-white border border-[#E3EEF0]
               hover:bg-[#F6FAFB]
               text-gray-800
               px-4 py-2 rounded-lg shadow-sm transition">
        <i data-feather="file-text" class="w-4 h-4"></i>
        Export PDF
    </button>
</form>
@endif

{{-- TABLE --}}
<div class="bg-white border border-[#E3EEF0]
            rounded-2xl shadow-sm overflow-x-auto">

<table class="min-w-full text-sm">
<thead class="bg-[#F6FAFB] border-b border-[#E3EEF0]">
<tr>
    <th class="px-3 py-2 text-left">No</th>
    <th class="px-3 py-2 text-left">Peserta</th>
    <th class="px-3 py-2 text-left">Sekolah</th>
    <th class="px-3 py-2 text-center">Tanggal</th>
    <th class="px-3 py-2 text-right">Jumlah</th>
    <th class="px-3 py-2 text-center">Status</th>
</tr>
</thead>

<tbody class="divide-y divide-[#E3EEF0]">
@foreach($pembayarans as $i => $p)
<tr class="{{ $p->status === 'belum' ? 'bg-red-50/60' : '' }}">
    <td class="px-3 py-2">{{ $i+1 }}</td>

    <td class="px-3 py-2 font-medium">
        {{ $p->peserta->nama }}
    </td>

    <td class="px-3 py-2">
        {{ $p->sekolah->nama_sekolah }}
    </td>

    <td class="px-3 py-2 text-center">
        {{ $p->tanggal_bayar
            ? \Carbon\Carbon::parse($p->tanggal_bayar)->format('d/m/Y')
            : '-' }}
    </td>

    <td class="px-3 py-2 text-right font-medium">
        Rp {{ number_format($p->jumlah,0,',','.') }}
    </td>

    <td class="px-3 py-2 text-center font-semibold">
        <span
            class="px-2 py-1 rounded-full text-xs
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

{{-- TOTAL --}}
<div class="mt-4 text-right font-semibold text-gray-800">
    Total Lunas:
    <span class="text-gray-900">
        Rp {{ number_format($totalLunas,0,',','.') }}
    </span>
</div>

@endsection
