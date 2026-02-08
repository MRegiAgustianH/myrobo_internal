@extends('layouts.app')

@section('header')
Gaji Instruktur
@endsection

@section('content')

@php
    $periode = sprintf('%04d-%02d', $tahun, $bulan);
@endphp

{{-- ================= FLASH MESSAGE ================= --}}
@foreach (['success' => 'emerald', 'error' => 'red'] as $key => $color)
@if(session($key))
<div class="mb-4 rounded-xl border border-{{ $color }}-200 bg-{{ $color }}-50 px-4 py-3 text-sm text-{{ $color }}-700">
    {{ session($key) }}
</div>
@endif
@endforeach

{{-- ================= HEADER ================= --}}
<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">
            Penggajian Instruktur
        </h2>
        <p class="text-sm text-gray-500">
            Periode {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}
        </p>
    </div>

    <div class="flex items-center gap-3">
        @if(auth()->user()->isAdmin())
        <div class="relative" id="gajiDropdownWrapper">
            <button
                type="button"
                onclick="toggleGajiDropdown()"
                class="inline-flex items-center gap-2 rounded-xl
                    bg-indigo-600 px-4 py-2 text-sm font-semibold text-white
                    shadow hover:bg-indigo-700 transition focus:outline-none">

                <i data-feather="settings" class="w-4 h-4"></i>
                Set Gaji
                <i data-feather="chevron-down" class="w-4 h-4"></i>
            </button>

            <div id="dropdownGaji"
                class="absolute right-0 mt-3 hidden w-64
                        rounded-2xl border border-gray-200 bg-white
                        shadow-xl z-50 overflow-hidden">

                {{-- HEADER --}}
                <div class="px-4 py-3 border-b bg-gray-50">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        Pengaturan Gaji
                    </p>
                </div>

                {{-- ITEM --}}
                <div class="py-2">

                    <button
                        type="button"
                        onclick="event.stopPropagation(); openModalGaji('sekolah')"
                        class="group w-full flex items-center gap-3 px-4 py-3
                            text-sm text-gray-700 hover:bg-indigo-50
                            transition">

                        <span class="flex items-center justify-center w-9 h-9
                                    rounded-xl bg-indigo-100 text-indigo-600
                                    group-hover:bg-indigo-600 group-hover:text-white
                                    transition">
                            <i data-feather="award" class="w-4 h-4"></i>
                        </span>

                        <div class="text-left">
                            <p class="font-semibold">Sekolah</p>
                            <p class="text-xs text-gray-500">
                                Atur tarif instruktur sekolah
                            </p>
                        </div>
                    </button>

                    <button
                        type="button"
                        onclick="event.stopPropagation(); openModalGaji('home_private')"
                        class="group w-full flex items-center gap-3 px-4 py-3
                            text-sm text-gray-700 hover:bg-emerald-50
                            transition">

                        <span class="flex items-center justify-center w-9 h-9
                                    rounded-xl bg-emerald-100 text-emerald-600
                                    group-hover:bg-emerald-600 group-hover:text-white
                                    transition">
                            <i data-feather="home" class="w-4 h-4"></i>
                        </span>

                        <div class="text-left">
                            <p class="font-semibold">Home Private</p>
                            <p class="text-xs text-gray-500">
                                Tarif khusus privat
                            </p>
                        </div>
                    </button>

                </div>
            </div>

        </div>
        @endif


        <a href="{{ route('keuangan.index') }}"
           class="inline-flex items-center gap-1 text-sm text-gray-600 hover:text-gray-900">
            <i data-feather="arrow-left" class="w-4 h-4"></i>
            Kembali
        </a>
    </div>
</div>

{{-- ================= DESKTOP TABLE ================= --}}
<div class="hidden md:block rounded-2xl border bg-white shadow-sm overflow-x-auto">
<table class="min-w-full text-sm">
<thead class="border-b bg-gray-50">
<tr>
    <th class="px-5 py-3 text-left">Instruktur</th>
    <th class="px-5 py-3 text-center">Hadir</th>
    <th class="px-5 py-3 text-right">Total Gaji</th>
    <th class="px-5 py-3 text-center">Status</th>
    <th class="px-5 py-3 text-center">Aksi</th>
</tr>
</thead>
<tbody class="divide-y">
@foreach($instrukturs as $i)
@php
    $dibayar = in_array($i->id, $sudahDibayarIds);
@endphp
<tr class="hover:bg-gray-50">
    <td class="px-5 py-4 font-medium text-gray-800">
        {{ $i->name }}
    </td>

    <td class="px-5 py-4 text-center text-gray-600">
        {{ $i->total_hadir }}x
    </td>

    <td class="px-5 py-4 text-right font-semibold">
        @if(!$i->tarif_valid && $i->total_hadir > 0)
            <span class="inline-flex items-center gap-1 text-red-600">
                <i data-feather="alert-circle" class="w-4 h-4"></i>
                Tarif belum diset
            </span>
        @else
            Rp {{ number_format($i->total_gaji, 0, ',', '.') }}
        @endif
    </td>

    <td class="px-5 py-4 text-center">
        <span class="{{ $dibayar ? 'badge-success' : 'badge-warning' }}">
            {{ $dibayar ? 'Sudah Dibayar' : 'Belum Dibayar' }}
        </span>
    </td>

    <td class="px-5 py-4 text-center">
        @if(!$dibayar && $i->total_gaji > 0 && $i->tarif_valid)
        <button
            onclick="openModal(
                '{{ $i->id }}',
                '{{ $i->name }}',
                '{{ number_format($i->total_gaji,0,',','.') }}'
            )"
            class="inline-flex items-center gap-1 font-semibold text-emerald-600 hover:text-emerald-700">
            <i data-feather="credit-card" class="w-4 h-4"></i>
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

{{-- ================= MOBILE CARD ================= --}}
<div class="md:hidden space-y-4">
@foreach($instrukturs as $i)
@php $dibayar = in_array($i->id, $sudahDibayarIds); @endphp
<div class="rounded-2xl border bg-white p-4 shadow-sm">
    <div class="flex justify-between">
        <div>
            <p class="font-semibold text-gray-800">{{ $i->name }}</p>
            <p class="text-xs text-gray-500">{{ $i->total_hadir }}x pertemuan</p>
        </div>
        <div class="text-right font-bold">
            @if(!$i->tarif_valid && $i->total_hadir > 0)
                <span class="text-red-600 text-sm">Tarif belum diset</span>
            @else
                Rp {{ number_format($i->total_gaji,0,',','.') }}
            @endif
        </div>
    </div>

    <div class="mt-3 flex justify-between items-center">
        <span class="{{ $dibayar ? 'badge-success' : 'badge-warning' }}">
            {{ $dibayar ? 'Sudah Dibayar' : 'Belum Dibayar' }}
        </span>

        @if(!$dibayar && $i->total_gaji > 0 && $i->tarif_valid)
        <button
            onclick="openModal('{{ $i->id }}','{{ $i->name }}','{{ number_format($i->total_gaji,0,',','.') }}')"
            class="text-sm font-semibold text-emerald-600">
            Bayar
        </button>
        @endif
    </div>
</div>
@endforeach
</div>

{{-- ================= MODAL BAYAR ================= --}}
<div id="modalBayar"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40">

    <div class="w-full max-w-md rounded-2xl bg-white shadow-xl p-6
                transition-all duration-200 scale-95 opacity-0"
         id="modalBayarBox">

        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
            <i data-feather="credit-card" class="w-5 h-5"></i>
            Konfirmasi Pembayaran
        </h3>

        <div class="bg-gray-50 rounded-xl p-4 mb-4">
            <p class="font-semibold text-gray-800" id="modalNama"></p>
            <p class="text-sm text-gray-600">
                Total:
                <span class="font-semibold">
                    Rp <span id="modalJumlah"></span>
                </span>
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
                        onclick="closeModalBayar()"
                        class="btn-secondary">
                    Batal
                </button>
                <button class="btn-primary">
                    Konfirmasi Bayar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id, nama, jumlah) {
    modalInstruktur.value = id;
    modalNama.innerText = nama;
    modalJumlah.innerText = jumlah;

    modalBayar.classList.remove('hidden');
    modalBayar.classList.add('flex');

    setTimeout(() => {
        modalBayarBox.classList.remove('scale-95','opacity-0');
    }, 10);
}

function closeModalBayar() {
    modalBayarBox.classList.add('scale-95','opacity-0');

    setTimeout(() => {
        modalBayar.classList.add('hidden');
        modalBayar.classList.remove('flex');
    }, 200);
}
</script>

{{-- ================= MODAL SET GAJI (UPDATED) ================= --}}
<div id="modalSetGaji"
     class="fixed inset-0 z-[999] hidden" 
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true">
    
    {{-- BACKDROP --}}
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" 
         id="modalBackdrop"></div>

    {{-- MODAL BOX --}}
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            
            <div id="modalSetGajiBox"
                 class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl 
                        transition-all sm:my-8 sm:w-full sm:max-w-lg scale-95 opacity-0 duration-300 ease-out">

                {{-- HEADER --}}
                <div class="bg-white px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-50 text-indigo-600 ring-4 ring-indigo-50/50">
                            <i data-feather="dollar-sign" class="h-6 w-6"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold leading-6 text-gray-900" id="modal-title">
                                Atur Gaji Instruktur
                            </h3>
                            <p id="modalInfo" class="mt-1 text-sm text-gray-500">
                                Sesuaikan nominal tarif per pertemuan.
                            </p>
                        </div>
                    </div>
                    
                    <button type="button" onclick="closeModalGaji()"
                            class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <i data-feather="x" class="h-5 w-5"></i>
                    </button>
                </div>

                {{-- FORM BODY --}}
                <form method="POST" action="{{ route('tarif-gaji.quick-store') }}">
                    @csrf
                    <input type="hidden" name="jenis_jadwal" id="modalJenisJadwal">
                    
                    {{-- Input Hidden untuk menyimpan ID jika sedang mode edit (opsional/case specific) --}}
                    {{-- <input type="hidden" name="id" id="modalId"> --}}

                    <div class="px-6 py-6 space-y-5">
                        
                        {{-- SELECTION: SEKOLAH --}}
                        <div id="formSekolah" class="hidden space-y-2">
                            <label for="modalSekolah" class="block text-sm font-medium text-gray-700">
                                Pilih Sekolah
                            </label>
                            <div class="relative">
                                {{-- PERBAIKAN: Ditambahkan class 'appearance-none' agar panah browser hilang --}}
                                <select id="modalSekolah" name="sekolah_id"
                                        class="appearance-none block w-full rounded-xl border-gray-300 bg-gray-50 py-3 pl-4 pr-10 text-gray-900 
                                            focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500 sm:text-sm transition-all shadow-sm">
                                    <option value="">-- Pilih Sekolah --</option>
                                    @foreach($sekolahs as $s)
                                        <option value="{{ $s->id }}">{{ $s->nama_sekolah }}</option>
                                    @endforeach
                                </select>
                                {{-- Icon Custom --}}
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                    <i data-feather="" class="h-4 w-4"></i>
                                </div>
                            </div>
                        </div>

                        {{-- SELECTION: HOME PRIVATE --}}
                        <div id="formHomePrivate" class="hidden space-y-2">
                            <label for="modalHomePrivate" class="block text-sm font-medium text-gray-700">
                                Pilih Peserta Private
                            </label>
                            <div class="relative">
                                {{-- PERBAIKAN: Ditambahkan class 'appearance-none' --}}
                                <select id="modalHomePrivate" name="home_private_id"
                                        class="appearance-none block w-full rounded-xl border-gray-300 bg-gray-50 py-3 pl-4 pr-10 text-gray-900 
                                            focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500 sm:text-sm transition-all shadow-sm">
                                    <option value="">-- Pilih Peserta --</option>
                                    @foreach($homePrivates as $hp)
                                        <option value="{{ $hp->id }}">{{ $hp->nama_peserta }}</option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                    <i data-feather="" class="h-4 w-4"></i>
                                </div>
                            </div>
                        </div>

                        {{-- INPUT NOMINAL --}}
                        <div class="space-y-2">
                            <label for="modalTarif" class="block text-sm font-medium text-gray-700">
                                Tarif per Pertemuan
                            </label>
                            <div class="relative mt-1 rounded-xl shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                    <span class="text-gray-500 sm:text-sm font-bold">Rp</span>
                                </div>
                                {{-- Value akan diisi lewat Javascript --}}
                                <input type="number" 
                                    name="tarif" 
                                    id="modalTarif" 
                                    class="block w-full rounded-xl border-gray-300 py-3 pl-12 pr-16 text-gray-900 placeholder-gray-300 
                                            focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 sm:text-sm font-semibold text-lg transition-all" 
                                    placeholder="0"
                                    min="0"
                                    required>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                                    <span class="text-gray-400 text-xs bg-gray-50 px-2 py-1 rounded">/ sesi</span>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 flex items-center gap-1 mt-2">
                                <i data-feather="info" class="w-3 h-3"></i> 
                                Pastikan nominal sudah sesuai kesepakatan.
                            </p>
                        </div>
                    </div>

                    {{-- FOOTER --}}
                    <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse sm:gap-3 border-t border-gray-100">
                        <button type="submit"
                                class="inline-flex w-full justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 hover:shadow-indigo-200 transition-all sm:w-auto items-center gap-2">
                            <i data-feather="save" class="w-4 h-4"></i>
                            Simpan Perubahan
                        </button>
                        <button type="button" 
                                onclick="closeModalGaji()"
                                class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-5 py-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-all">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Init Icons jika belum terload otomatis
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });

    /* ================= ELEMENT SELECTORS ================= */
    const modalSetGaji     = document.getElementById('modalSetGaji');
    const modalBackdrop    = document.getElementById('modalBackdrop');
    const modalBox         = document.getElementById('modalSetGajiBox');
    
    // Form Inputs
    const modalJenisJadwal = document.getElementById('modalJenisJadwal');
    const modalInfo        = document.getElementById('modalInfo');
    const formSekolah      = document.getElementById('formSekolah');
    const formHomePrivate  = document.getElementById('formHomePrivate');
    const modalTarif       = document.getElementById('modalTarif');
    
    // Dropdown Logic (Existing)
    const dropdownGaji     = document.getElementById('dropdownGaji');
    const gajiWrapper      = document.getElementById('gajiDropdownWrapper');

    /* ================= LOGIC DROPDOWN ================= */
    function toggleGajiDropdown() {
        if(dropdownGaji) {
            dropdownGaji.classList.toggle('hidden');
        }
    }

    // Close dropdown on outside click
    document.addEventListener('click', e => {
        if (gajiWrapper && dropdownGaji && !gajiWrapper.contains(e.target)) {
            dropdownGaji.classList.add('hidden');
        }
    });

    /* ================= LOGIC MODAL ================= */
    // Parameter:
    // 1. jenis: 'sekolah' atau 'private'
    // 2. nominal: (integer) nilai gaji saat ini, default 0
    // 3. selectedId: (integer) id sekolah/private yang mau diedit, default null
    function openModalGaji(jenis, nominal = 0, selectedId = null) {
        // Sembunyikan dropdown menu pemicu (jika ada)
        if(dropdownGaji) dropdownGaji.classList.add('hidden');

        // Reset Form & Set Nilai Awal
        modalTarif.value = nominal; // Isi input dengan data tarif lama
        formSekolah.classList.add('hidden');
        formHomePrivate.classList.add('hidden');
        
        // Set Jenis Jadwal
        modalJenisJadwal.value = jenis;

        if (jenis === 'sekolah') {
            modalInfo.innerText = 'Atur tarif standar untuk Sekolah.';
            formSekolah.classList.remove('hidden');
            
            // Auto select Sekolah jika ID dikirim
            if(selectedId) {
                document.getElementById('modalSekolah').value = selectedId;
            } else {
                document.getElementById('modalSekolah').value = ""; // Reset
            }

        } else {
            modalInfo.innerText = 'Atur tarif khusus untuk Home Private.';
            formHomePrivate.classList.remove('hidden');
            
            // Auto select Private jika ID dikirim
            if(selectedId) {
                document.getElementById('modalHomePrivate').value = selectedId;
            } else {
                document.getElementById('modalHomePrivate').value = ""; // Reset
            }
        }

        // Tampilkan Modal
        modalSetGaji.classList.remove('hidden');
        
        // Animasi Masuk
        setTimeout(() => {
            modalBackdrop.classList.remove('opacity-0');
            modalBox.classList.remove('scale-95', 'opacity-0');
            modalBox.classList.add('scale-100', 'opacity-100');
        }, 50);

        // Refresh icons
        if (typeof feather !== 'undefined') feather.replace();
    }

    function closeModalGaji() {
        // Animation Out
        modalBackdrop.classList.add('opacity-0');
        modalBox.classList.remove('scale-100', 'opacity-100');
        modalBox.classList.add('scale-95', 'opacity-0');

        // Hide Container after animation finishes
        setTimeout(() => {
            modalSetGaji.classList.add('hidden');
        }, 300); // Sesuaikan dengan durasi transition CSS (300ms)
    }

    /* ================= KEYBOARD EVENTS ================= */
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && !modalSetGaji.classList.contains('hidden')) {
            closeModalGaji();
        }
    });
</script>




@endsection
