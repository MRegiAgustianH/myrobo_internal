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

{{-- MODE TOGGLE --}}
<div class="mb-4">
    <label class="block text-sm font-medium mb-1">Mode Pembayaran</label>
    <div class="flex gap-2">
        <label class="flex items-center gap-2 px-4 py-2 border rounded-lg cursor-pointer {{ $mode === 'bulanan' ? 'bg-[#8FBFC2] font-semibold' : 'bg-white' }}">
            <input type="radio" name="mode" value="bulanan" {{ $mode === 'bulanan' ? 'checked' : '' }} onchange="toggleMode()">
            Bulanan
        </label>
        <label class="flex items-center gap-2 px-4 py-2 border rounded-lg cursor-pointer {{ $mode === 'periode' ? 'bg-[#8FBFC2] font-semibold' : 'bg-white' }}">
            <input type="radio" name="mode" value="periode" {{ $mode === 'periode' ? 'checked' : '' }} onchange="toggleMode()">
            Per Periode
        </label>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-7 gap-4 items-end">

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

    {{-- STATUS PEMBAYARAN --}}
    <div>
        <label class="block text-sm font-medium mb-1">
            Status
        </label>
        <select name="status" class="w-full border rounded-lg px-3 py-2 text-sm">
            <option value="">Semua</option>
            <option value="lunas" {{ $statusFilter === 'lunas' ? 'selected' : '' }}>Lunas</option>
            <option value="belum" {{ $statusFilter === 'belum' ? 'selected' : '' }}>Belum Lunas</option>
        </select>
    </div>

    {{-- CABANG (SUPERADMIN) --}}
    @if(auth()->user()->role === 'superadmin' && isset($cabangs))
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cabang</label>
                    <select name="cabang_id" class="w-full bg-white border border-[#E3EEF0] rounded-lg px-3 py-2 text-sm">
                        <option value="">-- Semua Cabang --</option>
                        @foreach($cabangs as $cb)
                            <option value="{{ $cb->id }}" {{ (string)request('cabang_id') === (string)$cb->id ? 'selected' : '' }}>
                                {{ $cb->nama_cabang }} ({{ $cb->kode_cabang }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

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

    {{-- BULAN (MODE BULANAN) --}}
    <div id="fieldBulan" class="{{ $mode === 'periode' ? 'hidden' : '' }}">
        <label class="block text-sm font-medium mb-1">Bulan</label>
        <select name="bulan"
                class="w-full border rounded-lg px-3 py-2 text-sm">
            @for($i=1;$i<=12;$i++)
                <option value="{{ $i }}" {{ (int)$bulan === $i ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month((int) $i)->translatedFormat('F') }}
                </option>
            @endfor
        </select>
    </div>

    {{-- TAHUN (MODE BULANAN) --}}
    <div id="fieldTahun" class="{{ $mode === 'periode' ? 'hidden' : '' }}">
        <label class="block text-sm font-medium mb-1">Tahun</label>
        <input type="number"
               name="tahun"
               value="{{ $tahun }}"
               class="w-full border rounded-lg px-3 py-2 text-sm">
    </div>

    {{-- TAHUN AJARAN (MODE PERIODE) --}}
    <div id="fieldTahunAjaran" class="{{ $mode === 'bulanan' ? 'hidden' : '' }}">
        <label class="block text-sm font-medium mb-1">Tahun Ajaran</label>
        <input type="text"
               name="tahun_ajaran"
               value="{{ $tahunAjaran }}"
               placeholder="2025/2026"
               class="w-full border rounded-lg px-3 py-2 text-sm">
    </div>

    {{-- SEMESTER (MODE PERIODE) --}}
    <div id="fieldSemester" class="{{ $mode === 'bulanan' ? 'hidden' : '' }}">
        <label class="block text-sm font-medium mb-1">Semester</label>
        <select name="semester"
                class="w-full border rounded-lg px-3 py-2 text-sm">
            <option value="ganjil" {{ $semester === 'ganjil' ? 'selected' : '' }}>Ganjil</option>
            <option value="genap" {{ $semester === 'genap' ? 'selected' : '' }}>Genap</option>
        </select>
    </div>

    {{-- PERIODE (MODE PERIODE) --}}
    <div id="fieldPeriode" class="{{ $mode === 'bulanan' ? 'hidden' : '' }}">
        <label class="block text-sm font-medium mb-1">Periode</label>
        <select name="periode"
                class="w-full border rounded-lg px-3 py-2 text-sm">
            @for($i=1;$i<=$jumlahPeriode;$i++)
                <option value="{{ $i }}" {{ (int)$periode === $i ? 'selected' : '' }}>
                    Periode {{ $i }}
                </option>
            @endfor
        </select>
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

{{-- ================= TABEL PEMBAYARAN ================= --}}
<div class="bg-white border rounded-2xl shadow-sm overflow-x-auto">
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
    <input type="hidden" name="pembayaran[{{ $p->id }}][jenis]" value="sekolah">
    <input type="hidden" name="pembayaran[{{ $p->id }}][sekolah_id]" value="{{ $p->sekolah_id }}">
    <td class="text-center">
        <input type="checkbox"
            name="pembayaran[{{ $p->id }}][status]"
            class="pay-check"
            {{ $pay?->status === 'lunas' ? 'checked' : '' }}>
    </td>
    <td class="text-center">
        <input type="number"
               name="pembayaran[{{ $p->id }}][jumlah]"
               value="{{ $pay ? $pay->jumlah : ($p->sekolah->nominal_pembayaran ?? 150000) }}"
               class="border rounded px-2 py-1 text-sm w-24 text-center pay-amount"
               {{ $pay?->status !== 'lunas' ? 'disabled' : '' }}>
    </td>
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
    <td class="px-4 py-2">â€“</td>
    <td class="px-4 py-2">{{ $hp->nama_peserta }}</td>
    <td class="px-4 py-2">Home Private</td>
    <input type="hidden" name="pembayaran[{{ $hp->id }}][jenis]" value="home_private">
    <td class="text-center">
        <input type="checkbox"
            name="pembayaran[{{ $hp->id }}][status]"
            class="pay-check"
            {{ $pay?->status === 'lunas' ? 'checked' : '' }}>
    </td>
    <td class="text-center">
        <input type="number"
               name="pembayaran[{{ $hp->id }}][jumlah]"
               value="{{ $pay ? $pay->jumlah : 450000 }}"
               class="border rounded px-2 py-1 text-sm w-24 text-center pay-amount"
               {{ $pay?->status !== 'lunas' ? 'disabled' : '' }}>
    </td>
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

<input type="hidden" name="mode" value="{{ $mode }}">
<input type="hidden" name="bulan" value="{{ $bulan }}">
<input type="hidden" name="tahun" value="{{ $tahun }}">
<input type="hidden" name="tahun_ajaran" value="{{ $tahunAjaran }}">
<input type="hidden" name="semester" value="{{ $semester }}">
<input type="hidden" name="periode" value="{{ $periode }}">

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

        const amount = box.querySelector('.pay-amount');

        if (this.checked) {
            date.disabled = false;
            if (amount) amount.disabled = false;

            if (!date.value) {
                date.value = new Date().toISOString().slice(0,10);
            }
        } else {
            date.value = '';
            date.disabled = true;
            if (amount) amount.disabled = true;
        }
    });
});


function toggleMode() {
    const mode = document.querySelector('input[name=mode]:checked')?.value || 'bulanan';
    const bulananFields = ['fieldBulan', 'fieldTahun'];
    const periodeFields = ['fieldTahunAjaran', 'fieldSemester', 'fieldPeriode'];
    bulananFields.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.classList.toggle('hidden', mode === 'periode');
    });
    periodeFields.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.classList.toggle('hidden', mode === 'bulanan');
    });
}

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
