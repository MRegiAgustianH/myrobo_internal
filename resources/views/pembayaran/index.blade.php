@extends('layouts.app')

@section('header')
Pembayaran Bulanan
@endsection

@section('content')

@if(session('success'))
<div class="mb-4 p-3 rounded-lg bg-green-100 text-green-700 border border-green-200">
    {{ session('success') }}
</div>
@endif

{{-- FILTER --}}
<form method="GET"
      class="bg-[#F6FAFB] border border-[#E3EEF0]
             rounded-2xl shadow-sm mb-6 p-5">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sekolah</label>
            <select name="sekolah_id"
                class="w-full bg-white border border-[#E3EEF0]
                       rounded-lg px-3 py-2 text-sm
                       focus:ring-2 focus:ring-[#8FBFC2]/60 focus:border-[#8FBFC2]">
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
            <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
            <select name="bulan"
                class="w-full bg-white border border-[#E3EEF0]
                       rounded-lg px-3 py-2 text-sm
                       focus:ring-2 focus:ring-[#8FBFC2]/60 focus:border-[#8FBFC2]">
                @for($i=1;$i<=12;$i++)
                    <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
            <input type="number" name="tahun" value="{{ $tahun }}"
                class="w-full bg-white border border-[#E3EEF0]
                       rounded-lg px-3 py-2 text-sm
                       focus:ring-2 focus:ring-[#8FBFC2]/60 focus:border-[#8FBFC2]">
        </div>

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

<form action="{{ route('pembayaran.store') }}" method="POST">
@csrf

<div class="bg-white border border-[#E3EEF0]
            rounded-2xl shadow-sm overflow-x-auto">

<table class="min-w-full text-sm">
<thead class="bg-[#F6FAFB] border-b border-[#E3EEF0]">
<tr>
    <th class="px-3 py-2 text-left">No</th>
    <th class="px-3 py-2 text-left">Peserta</th>
    <th class="px-3 py-2 text-center">Lunas</th>
    <th class="px-3 py-2 text-center">Tanggal Bayar</th>
</tr>
</thead>

<tbody class="divide-y divide-[#E3EEF0]">
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
    <td class="px-3 py-2 text-center">
        <input type="checkbox"
            class="rounded border-gray-300 text-[#8FBFC2] focus:ring-[#8FBFC2]"
            name="pembayaran[{{ $p->id }}][status]"
            value="lunas"
            data-peserta="{{ $p->id }}"
            {{ $pembayaran?->status === 'lunas' ? 'checked' : '' }}>
    </td>

    {{-- JUMLAH (HIDDEN, OTOMATIS) --}}
    <input type="hidden"
        id="jumlah-{{ $p->id }}"
        name="pembayaran[{{ $p->id }}][jumlah]"
        value="{{ $pembayaran?->status === 'lunas' ? 150000 : '' }}">


    {{-- TANGGAL --}}
    <td class="px-3 py-2">
        <input type="date"
            name="pembayaran[{{ $p->id }}][tanggal_bayar]"
            value="{{ $pembayaran?->tanggal_bayar?->format('Y-m-d') }}"
            class="w-full bg-white border border-[#E3EEF0]
                   rounded-lg px-2 py-1 text-sm
                   focus:ring-2 focus:ring-[#8FBFC2]/60 focus:border-[#8FBFC2]"
            {{ $pembayaran?->status !== 'lunas' ? 'disabled' : '' }}>
    </td>
</tr>

@endforeach
</tbody>
</table>

</div>

<input type="hidden" name="bulan" value="{{ $bulan }}">
<input type="hidden" name="tahun" value="{{ $tahun }}">

<div class="mt-5 flex justify-end">
    <button 
        class="inline-flex items-center gap-2
               bg-gradient-to-r from-[#8FBFC2] to-[#7AAEB1]
               hover:from-[#7AAEB1] hover:to-[#6FA9AD]
               text-gray-900 font-semibold
               px-6 py-2.5 rounded-xl
               shadow-sm transition">
        <i data-feather="save" class="w-4 h-4"></i>
        Simpan Pembayaran
    </button>
</div>

</form>

@push('scripts')
<script>
document.querySelectorAll('input[type="checkbox"][data-peserta]').forEach(cb => {
    cb.addEventListener('change', function () {
        const id = this.dataset.peserta;
        const jumlahInput = document.getElementById('jumlah-' + id);
        const tanggalInput = document.querySelector(
            `input[name="pembayaran[${id}][tanggal_bayar]"]`
        );

        if (this.checked) {
            jumlahInput.value = 150000;
            tanggalInput.removeAttribute('disabled');

            // otomatis isi tanggal hari ini jika kosong
            if (!tanggalInput.value) {
                const today = new Date().toISOString().slice(0, 10);
                tanggalInput.value = today;
            }
        } else {
            jumlahInput.value = '';
            tanggalInput.value = '';
            tanggalInput.setAttribute('disabled', true);
        }
    });
});
</script>
@endpush


@endsection
