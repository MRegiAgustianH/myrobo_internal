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

    {{-- MODE TOGGLE --}}
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Mode</label>
        <div class="flex gap-2">
            <label class="flex items-center gap-2 px-4 py-2 border rounded-lg cursor-pointer {{ $mode === 'bulanan' ? 'bg-[#8FBFC2] font-semibold' : 'bg-white' }}">
                <input type="radio" name="mode" value="bulanan" {{ $mode === 'bulanan' ? 'checked' : '' }} onchange="toggleRekapMode()">
                Bulanan
            </label>
            <label class="flex items-center gap-2 px-4 py-2 border rounded-lg cursor-pointer {{ $mode === 'periode' ? 'bg-[#8FBFC2] font-semibold' : 'bg-white' }}">
                <input type="radio" name="mode" value="periode" {{ $mode === 'periode' ? 'checked' : '' }} onchange="toggleRekapMode()">
                Per Periode / Tahun Ajaran
            </label>
        </div>
    </div>

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

        {{-- SEKOLAH --}}
        <div id="filterSekolah" class="{{ $jenis === 'home_private' ? 'hidden' : '' }}">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Sekolah
            </label>

            @if(in_array(auth()->user()->role, ['superadmin', 'admin', 'admin_cabang', 'bendahara', 'sekretaris']))
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
                    {{ auth()->user()->sekolah?->nama_sekolah ?? '-' }}
                </div>
            @endif
        </div>

        {{-- BULAN (MODE BULANAN) --}}
        <div id="fieldBulan" class="{{ $mode === 'periode' ? 'hidden' : '' }}">
            <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
            <select name="bulan"
                    class="w-full bg-white border border-[#E3EEF0]
                           rounded-lg px-3 py-2 text-sm">
                @for($i=1;$i<=12;$i++)
                    <option value="{{ $i }}" {{ (int)$bulan === $i ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month((int) $i)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
        </div>

        {{-- TAHUN (MODE BULANAN) --}}
        <div id="fieldTahun" class="{{ $mode === 'periode' ? 'hidden' : '' }}">
            <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
            <input type="number" name="tahun"
                   value="{{ $tahun }}"
                   class="w-full bg-white border border-[#E3EEF0]
                          rounded-lg px-3 py-2 text-sm">
        </div>

        {{-- TAHUN AJARAN (MODE PERIODE) --}}
        <div id="fieldTahunAjaran" class="{{ $mode === 'bulanan' ? 'hidden' : '' }}">
            <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Ajaran</label>
            <input type="text" name="tahun_ajaran"
                   value="{{ $tahunAjaran }}"
                   placeholder="2025/2026"
                   class="w-full bg-white border border-[#E3EEF0]
                          rounded-lg px-3 py-2 text-sm">
        </div>

        {{-- SEMESTER (MODE PERIODE) --}}
        <div id="fieldSemester" class="{{ $mode === 'bulanan' ? 'hidden' : '' }}">
            <label class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
            <select name="semester"
                    class="w-full bg-white border border-[#E3EEF0]
                           rounded-lg px-3 py-2 text-sm">
                <option value="">Semua</option>
                <option value="ganjil" {{ $semester === 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                <option value="genap" {{ $semester === 'genap' ? 'selected' : '' }}>Genap</option>
            </select>
        </div>

        {{-- PERIODE (MODE PERIODE) --}}
        <div id="fieldPeriode" class="{{ $mode === 'bulanan' ? 'hidden' : '' }}">
            <label class="block text-sm font-medium text-gray-700 mb-1">Periode</label>
            <select name="periode"
                    class="w-full bg-white border border-[#E3EEF0]
                           rounded-lg px-3 py-2 text-sm">
                <option value="">Semua</option>
                @for($i=1;$i<=6;$i++)
                    <option value="{{ $i }}" {{ (int)$periode === $i ? 'selected' : '' }}>
                        Periode {{ $i }}
                    </option>
                @endfor
            </select>
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

<script>
function toggleRekapMode() {
    const mode = document.querySelector('input[name=mode]:checked')?.value || 'bulanan';
    ['fieldBulan', 'fieldTahun'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.classList.toggle('hidden', mode === 'periode');
    });
    ['fieldTahunAjaran', 'fieldSemester', 'fieldPeriode'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.classList.toggle('hidden', mode === 'bulanan');
    });
}
</script>

{{-- ================= EXPORT ================= --}}
@if($bulan && $tahun)
<div class="flex flex-wrap gap-2 mb-4">
    <form method="GET" action="{{ route('pembayaran.rekap.export-pdf') }}" target="_blank" class="inline">
        <input type="hidden" name="mode" value="{{ $mode }}">
        <input type="hidden" name="bulan" value="{{ $bulan }}">
        <input type="hidden" name="tahun" value="{{ $tahun }}">
        <input type="hidden" name="tahun_ajaran" value="{{ $tahunAjaran }}">
        <input type="hidden" name="semester" value="{{ $semester }}">
        <input type="hidden" name="periode" value="{{ $periode }}">

        @if(!empty(request('jenis_peserta')))
            <input type="hidden" name="jenis_peserta" value="{{ request('jenis_peserta') }}">
        @endif

        @if(!empty($sekolahId))
            <input type="hidden" name="sekolah_id" value="{{ $sekolahId }}">
        @endif

        <button class="inline-flex items-center gap-2 bg-white border border-[#E3EEF0] hover:bg-[#F6FAFB] px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
            <i data-feather="file-text" class="w-4 h-4 text-gray-500"></i>
            Export Rekap PDF
        </button>
    </form>

    <a href="{{ route('pembayaran.rekap.export-excel') }}?bulan={{ $bulan }}&tahun={{ $tahun }}{{ !empty(request('jenis_peserta')) ? '&jenis_peserta=' . request('jenis_peserta') : '' }}{{ !empty($sekolahId) ? '&sekolah_id=' . $sekolahId : '' }}{{ !empty(request('tahun_ajaran')) ? '&tahun_ajaran=' . request('tahun_ajaran') : '' }}{{ !empty(request('semester')) ? '&semester=' . request('semester') : '' }}{{ !empty(request('periode')) ? '&periode=' . request('periode') : '' }}"
       class="inline-flex items-center gap-2 bg-green-600 text-white hover:bg-green-700 px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
        <i data-feather="file" class="w-4 h-4"></i>
        Export Rekap Excel
    </a>
</div>
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

    <div class="flex items-center justify-between mb-2">
        <div class="font-semibold text-gray-800">{{ $namaPeserta ?? '-' }}</div>
        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
            {{ $p->status === 'lunas'
                ? 'bg-green-100 text-green-700'
                : 'bg-red-100 text-red-700' }}">
            {{ $p->status }}
        </span>
    </div>

    <div class="text-xs text-gray-600">{{ $namaSekolah }}</div>

    <div class="mt-3 flex justify-between items-center">
        <div>
            <div class="text-xs text-gray-500">
                @if($p->isPeriode())
                    TA {{ $p->tahun_ajaran }} - {{ ucfirst($p->semester) }} P{{ $p->periode }}
                @else
                    {{ \Carbon\Carbon::create()->month((int) $p->bulan)->translatedFormat('F') }} {{ $p->tahun }}
                @endif
            </div>
            <div class="text-xs text-gray-500 mt-0.5">
                Bayar: {{ $p->tanggal_bayar?->format('d/m/Y') ?? '-' }}
            </div>
        </div>
        <div class="font-bold text-gray-900 font-mono">
            Rp {{ number_format($p->jumlah ?? 0,0,',','.') }}
        </div>
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
<thead class="bg-[#F6FAFB] border-b border-[#E3EEF0] text-gray-600 font-semibold">
<tr>
    <th class="px-6 py-3.5 text-center w-16">No</th>
    <th class="px-6 py-3.5 text-left">Peserta</th>
    <th class="px-6 py-3.5 text-left">Jenis</th>
    <th class="px-6 py-3.5 text-left">Sekolah / Target</th>
    <th class="px-6 py-3.5 text-left">Periode</th>
    <th class="px-6 py-3.5 text-center">Tanggal Bayar</th>
    <th class="px-6 py-3.5 text-right">Jumlah</th>
    <th class="px-6 py-3.5 text-center w-28">Status</th>
</tr>
</thead>

<tbody class="divide-y divide-[#E3EEF0] align-middle">
@foreach($pembayarans as $i => $p)
@php
    $namaPeserta = $p->jenis_peserta === 'home_private'
        ? $p->homePrivate?->nama_peserta
        : $p->peserta?->nama;

    $namaSekolah = $p->jenis_peserta === 'home_private'
        ? 'Home Private'
        : ($p->sekolah?->nama_sekolah ?? '-');
@endphp

<tr class="{{ $p->status === 'belum' ? 'bg-red-50/40' : 'hover:bg-gray-50/50' }} transition-colors">
    <td class="px-6 py-4 text-center text-gray-500 font-mono">{{ $i+1 }}</td>
    <td class="px-6 py-4 font-semibold text-gray-900">{{ $namaPeserta ?? '-' }}</td>
    <td class="px-6 py-4 text-gray-600 capitalize">{{ str_replace('_',' ',$p->jenis_peserta) }}</td>
    <td class="px-6 py-4 text-gray-600">{{ $namaSekolah }}</td>
    <td class="px-6 py-4 text-xs text-gray-700">
        @if($p->isPeriode())
            <span class="font-medium">TA {{ $p->tahun_ajaran }}</span><br>
            <span class="text-gray-500">{{ ucfirst($p->semester) }} - Periode {{ $p->periode }}</span>
        @else
            {{ \Carbon\Carbon::create()->month((int) $p->bulan)->translatedFormat('F') }} {{ $p->tahun }}
        @endif
    </td>
    <td class="px-6 py-4 text-center text-gray-600 font-mono">
        {{ $p->tanggal_bayar?->format('d/m/Y') ?? '-' }}
    </td>
    <td class="px-6 py-4 text-right font-bold text-gray-900 font-mono">
        Rp {{ number_format($p->jumlah ?? 0,0,',','.') }}
    </td>
    <td class="px-6 py-4 text-center">
        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
            {{ $p->status === 'lunas'
                ? 'bg-green-100 text-green-700'
                : 'bg-red-100 text-red-700' }}">
            {{ $p->status }}
        </span>
    </td>
</tr>
@endforeach
</tbody>
</table>
</div>




<div class="mt-4">{{$pembayarans->links()}}</div>

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
