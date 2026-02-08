@extends('layouts.app')

@section('header')
Pembayaran Bulanan
@endsection

@section('content')

{{-- ================= ALERT ================= --}}
@if(session('success'))
<div class="mb-4 p-3 rounded-lg bg-green-100 text-green-700 border border-green-200">
    {{ session('success') }}
</div>
@endif

@php
    $showSekolah     = request('jenis_peserta') !== 'home_private';
    $showHomePrivate = request('jenis_peserta') !== 'sekolah';
@endphp

{{-- ================= FILTER ================= --}}
<form method="GET"
      class="bg-[#F6FAFB] border border-[#E3EEF0]
             rounded-2xl shadow-sm mb-6 p-5">

@php
    $jenis = request('jenis_peserta');
@endphp

<div class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">

    {{-- JENIS PESERTA --}}
    <div>
        <label class="block text-sm font-medium mb-1">
            Jenis Peserta
        </label>
        <select name="jenis_peserta"
                id="jenisPeserta"
                class="w-full border rounded-lg px-3 py-2 text-sm">
            <option value="">Semua</option>
            <option value="sekolah" {{ $jenis === 'sekolah' ? 'selected' : '' }}>
                Sekolah
            </option>
            <option value="home_private" {{ $jenis === 'home_private' ? 'selected' : '' }}>
                Home Private
            </option>
        </select>
    </div>

    {{-- SEKOLAH (HIDDEN JIKA HOME PRIVATE) --}}
    <div id="filterSekolah"
         class="{{ $jenis === 'home_private' ? 'hidden' : '' }}">
        <label class="block text-sm font-medium mb-1">
            Sekolah
        </label>

        <select name="sekolah_id"
                class="w-full border rounded-lg px-3 py-2 text-sm">
            <option value="">Semua Sekolah</option>
            @foreach($sekolahs as $s)
                <option value="{{ $s->id }}"
                    {{ (string)$sekolahId === (string)$s->id ? 'selected' : '' }}>
                    {{ $s->nama_sekolah }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- BULAN --}}
    <div>
        <label class="block text-sm font-medium mb-1">Bulan</label>
        <select name="bulan"
                class="w-full border rounded-lg px-3 py-2 text-sm">
            @for($i=1;$i<=12;$i++)
                <option value="{{ $i }}" {{ (int)$bulan === $i ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
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
               class="w-full border rounded-lg px-3 py-2 text-sm">
    </div>

    {{-- ACTION --}}
    <div class="md:col-span-2 flex gap-2">
        <button
            class="flex-1 bg-[#8FBFC2] rounded-lg py-2 font-medium">
            Tampilkan
        </button>

        <a href="{{ url()->current() }}"
           class="flex-1 border rounded-lg py-2 text-center text-sm">
            Reset
        </a>
    </div>

</div>
</form>


<form action="{{ route('pembayaran.store') }}" method="POST">
@csrf

{{-- ================= MOBILE ================= --}}
<div class="space-y-4 md:hidden">

{{-- ===== SEKOLAH ===== --}}
@if($showSekolah)
@foreach($pesertaSekolah as $p)
@php $pay = $pembayaranMap['sekolah_'.$p->id] ?? null; @endphp
<div class="bg-white border rounded-xl p-4 shadow-sm">
    <div class="font-semibold">{{ $p->nama }}</div>
    <div class="text-xs text-blue-700 mb-3">Sekolah</div>

    <div class="flex justify-between items-center">
        <span class="font-semibold">Rp 150.000</span>
        <input type="checkbox"
            name="pembayaran[{{ $p->id }}][status]"
            class="pay-check"
            {{ $pay?->status === 'lunas' ? 'checked' : '' }}>
    </div>

    <input type="hidden" name="pembayaran[{{ $p->id }}][jenis]" value="sekolah">
    <input type="hidden" name="pembayaran[{{ $p->id }}][sekolah_id]" value="{{ $p->sekolah_id }}">

    <input type="date"
        name="pembayaran[{{ $p->id }}][tanggal_bayar]"
        value="{{ $pay?->tanggal_bayar?->format('Y-m-d') }}"
        class="w-full mt-3 border rounded px-3 py-2 text-sm"
        {{ $pay?->status !== 'lunas' ? 'disabled' : '' }}>
</div>
@endforeach
@endif

{{-- ===== HOME PRIVATE ===== --}}
@if($showHomePrivate)
@foreach($homePrivates as $hp)
@php $pay = $pembayaranMap['home_'.$hp->id] ?? null; @endphp
<div class="bg-[#FAFAFA] border rounded-xl p-4 shadow-sm">
    <div class="font-semibold">{{ $hp->nama_peserta }}</div>
    <div class="text-xs text-purple-700 mb-3">Home Private</div>

    <div class="flex justify-between items-center">
        <span class="font-semibold">Rp 450.000</span>
        <input type="checkbox"
            name="pembayaran[{{ $hp->id }}][status]"
            class="pay-check"
            {{ $pay?->status === 'lunas' ? 'checked' : '' }}>
    </div>

    <input type="hidden" name="pembayaran[{{ $hp->id }}][jenis]" value="home_private">

    <input type="date"
        name="pembayaran[{{ $hp->id }}][tanggal_bayar]"
        value="{{ $pay?->tanggal_bayar?->format('Y-m-d') }}"
        class="w-full mt-3 border rounded px-3 py-2 text-sm"
        {{ $pay?->status !== 'lunas' ? 'disabled' : '' }}>
</div>
@endforeach
@endif
</div>

{{-- ================= DESKTOP ================= --}}
<div class="hidden md:block bg-white border rounded-2xl shadow-sm overflow-x-auto">
<table class="min-w-full text-sm">
<thead class="bg-[#F6FAFB] border-b">
<tr>
    <th class="px-4 py-2">No</th>
    <th class="px-4 py-2 text-left">Peserta</th>
    <th class="px-4 py-2">Jenis</th>
    <th class="px-4 py-2 text-center">Lunas</th>
    <th class="px-4 py-2 text-center">Nominal</th>
    <th class="px-4 py-2 text-center">Tanggal</th>
</tr>
</thead>

<tbody class="divide-y">

@if($showSekolah)
@foreach($pesertaSekolah as $i => $p)
@php $pay = $pembayaranMap['sekolah_'.$p->id] ?? null; @endphp
<tr>
    <td class="px-4 py-2">{{ $i+1 }}</td>
    <td class="px-4 py-2">{{ $p->nama }}</td>
    <td class="px-4 py-2">Sekolah</td>
    <td class="text-center">
        <input type="checkbox"
            name="pembayaran[{{ $p->id }}][status]"
            class="pay-check"
            {{ $pay?->status === 'lunas' ? 'checked' : '' }}>
    </td>
    <td class="text-center">Rp 150.000</td>
    <td class="text-center">
        <input type="date"
            name="pembayaran[{{ $p->id }}][tanggal_bayar]"
            value="{{ $pay?->tanggal_bayar?->format('Y-m-d') }}"
            class="border rounded px-2 py-1 text-sm"
            {{ $pay?->status !== 'lunas' ? 'disabled' : '' }}>
    </td>
</tr>
@endforeach
@endif

@if($showHomePrivate)
@foreach($homePrivates as $hp)
@php $pay = $pembayaranMap['home_'.$hp->id] ?? null; @endphp
<tr class="bg-gray-50">
    <td class="px-4 py-2">–</td>
    <td class="px-4 py-2">{{ $hp->nama_peserta }}</td>
    <td class="px-4 py-2">Home Private</td>
    <td class="text-center">
        <input type="checkbox"
            name="pembayaran[{{ $hp->id }}][status]"
            class="pay-check"
            {{ $pay?->status === 'lunas' ? 'checked' : '' }}>
    </td>
    <td class="text-center">Rp 450.000</td>
    <td class="text-center">
        <input type="date"
            name="pembayaran[{{ $hp->id }}][tanggal_bayar]"
            value="{{ $pay?->tanggal_bayar?->format('Y-m-d') }}"
            class="border rounded px-2 py-1 text-sm"
            {{ $pay?->status !== 'lunas' ? 'disabled' : '' }}>
    </td>
</tr>
@endforeach
@endif

</tbody>
</table>
</div>

<input type="hidden" name="bulan" value="{{ $bulan }}">
<input type="hidden" name="tahun" value="{{ $tahun }}">

<div class="mt-6 text-right">
    <button class="bg-[#8FBFC2] px-6 py-2 rounded-xl font-semibold">
        Simpan Pembayaran
    </button>
</div>

</form>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.pay-check').forEach(cb => {
    cb.addEventListener('change', function () {
        const box = this.closest('tr') || this.closest('.shadow-sm');
        const date = box.querySelector('input[type=date]');
        if (!date) return;

        if (this.checked) {
            date.disabled = false;
            if (!date.value) {
                date.value = new Date().toISOString().slice(0,10);
            }
        } else {
            date.value = '';
            date.disabled = true;
        }
    });
});


document.getElementById('jenisPeserta')?.addEventListener('change', function () {
    const sekolahBox = document.getElementById('filterSekolah');

    if (this.value === 'home_private') {
        sekolahBox.classList.add('hidden');
    } else {
        sekolahBox.classList.remove('hidden');
    }
});
</script>
@endpush
