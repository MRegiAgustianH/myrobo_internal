@extends('layouts.app')

@section('header')
Gaji Instruktur
@endsection

@section('content')

@php
    $periode = sprintf('%04d-%02d', $tahun, $bulan);
@endphp

{{-- ALERT --}}
@if(session('success'))
<div class="mb-4 p-3 rounded-lg bg-green-100 text-green-700 border border-green-200">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-4 p-3 rounded-lg bg-red-100 text-red-700 border border-red-200">
    {{ session('error') }}
</div>
@endif

{{-- ================= HEADER ================= --}}
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-semibold text-gray-800">
            Penggajian Instruktur
        </h2>
        <p class="text-sm text-gray-500">
            Periode {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}
            · Rp 60.000 / pertemuan
        </p>
    </div>

    <a href="{{ route('keuangan.index') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-600 hover:underline">
        ← Kembali ke Pengeluaran
    </a>
</div>

{{-- ================= TABLE / CARD LIST ================= --}}
<div class="bg-white rounded-2xl border border-[#E3EEF0] shadow-sm overflow-hidden">

    {{-- DESKTOP TABLE --}}
    <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-[#F6FAFB] border-b border-[#E3EEF0]">
                <tr>
                    <th class="px-4 py-3 text-left">Instruktur</th>
                    <th class="px-4 py-3 text-center">Hadir</th>
                    <th class="px-4 py-3 text-right">Total Gaji</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E3EEF0]">
                @foreach($instrukturs as $i)

                @php
                    $totalGaji = $i->total_hadir * 60000;
                    $sudahDibayar = in_array($i->id, $sudahDibayarIds);
                @endphp

                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium">{{ $i->name }}</td>

                    <td class="px-4 py-3 text-center">
                        {{ $i->total_hadir }}x
                    </td>

                    <td class="px-4 py-3 text-right font-semibold">
                        Rp {{ number_format($totalGaji, 0, ',', '.') }}
                    </td>

                    <td class="px-4 py-3 text-center">
                        @if($sudahDibayar)
                            <span class="badge-success">Sudah Dibayar</span>
                        @else
                            <span class="badge-warning">Belum Dibayar</span>
                        @endif
                    </td>

                    <td class="px-4 py-3 text-center">
                        @if(!$sudahDibayar)
                            <button
                                onclick="openModal(
                                    '{{ $i->id }}',
                                    '{{ $i->name }}',
                                    '{{ number_format($totalGaji,0,',','.') }}'
                                )"
                                class="inline-flex items-center gap-1
                                    text-emerald-600 font-semibold hover:underline">
                                Bayar
                            </button>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                </tr>

                @endforeach
            </tbody>
        </table>
    </div>

    {{-- MOBILE CARD --}}
    <div class="md:hidden divide-y">
        @foreach($instrukturs as $i)

        @php
            $totalGaji = $i->total_hadir * 60000;
            $sudahDibayar = \App\Models\Keuangan::where([
                'tipe'        => 'keluar',
                'kategori'    => 'Gaji Instruktur',
                'sumber_id'   => $i->id,
                'sumber_type' => \App\Models\User::class,
                'periode'     => $periode,
            ])->exists();
        @endphp

        <div class="p-4">
            <div class="flex justify-between items-start">
                <div>
                    <p class="font-semibold">{{ $i->name }}</p>
                    <p class="text-xs text-gray-500">{{ $i->total_hadir }}x pertemuan</p>
                </div>
                <p class="font-bold">
                    Rp {{ number_format($totalGaji, 0, ',', '.') }}
                </p>
            </div>

            <div class="mt-3 flex justify-between items-center">
                @if($sudahDibayar)
                    <span class="badge-success">Sudah Dibayar</span>
                @else
                    <span class="badge-warning">Belum Dibayar</span>
                @endif

                @if(!$sudahDibayar)
                    <button
                        onclick="openModal('{{ $i->id }}','{{ $i->name }}','{{ number_format($totalGaji,0,',','.') }}')"
                        class="text-emerald-600 font-semibold text-sm">
                        Bayar
                    </button>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- ================= MODAL VERIFIKASI ================= --}}
<div id="modalBayar"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">

    <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
        <h3 class="text-lg font-semibold mb-2">Konfirmasi Pembayaran</h3>
        <p class="text-sm text-gray-600 mb-4">
            Anda akan membayar gaji instruktur berikut:
        </p>

        <div class="bg-gray-50 rounded-lg p-4 mb-4">
            <p class="font-semibold" id="modalNama"></p>
            <p class="text-sm text-gray-600">
                Total: <span class="font-semibold">Rp <span id="modalJumlah"></span></span>
            </p>
            <p class="text-xs text-gray-500 mt-1">
                Periode {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}
            </p>
        </div>

        <form method="POST" action="{{ route('keuangan.gaji.bayar') }}">
            @csrf
            <input type="hidden" name="instruktur_id" id="modalInstruktur">
            <input type="hidden" name="bulan" value="{{ $bulan }}">
            <input type="hidden" name="tahun" value="{{ $tahun }}">

            <div class="flex justify-end gap-2">
                <button type="button"
                        onclick="closeModal()"
                        class="px-4 py-2 text-sm rounded-lg border">
                    Batal
                </button>
                <button
                    class="px-4 py-2 text-sm rounded-lg
                           bg-emerald-600 hover:bg-emerald-700
                           text-white font-semibold">
                    Konfirmasi Bayar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ================= STYLE & SCRIPT ================= --}}
<style>
.badge-success {
    @apply inline-flex px-3 py-1 rounded-full text-xs font-semibold
           bg-emerald-100 text-emerald-700;
}
.badge-warning {
    @apply inline-flex px-3 py-1 rounded-full text-xs font-semibold
           bg-yellow-100 text-yellow-700;
}
</style>

<script>
function openModal(id, nama, jumlah) {
    document.getElementById('modalInstruktur').value = id;
    document.getElementById('modalNama').innerText = nama;
    document.getElementById('modalJumlah').innerText = jumlah;
    document.getElementById('modalBayar').classList.remove('hidden');
    document.getElementById('modalBayar').classList.add('flex');
}

function closeModal() {
    document.getElementById('modalBayar').classList.add('hidden');
    document.getElementById('modalBayar').classList.remove('flex');
}
</script>

@endsection
