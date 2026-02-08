@extends('layouts.app')

@section('header')
<div class="flex items-center justify-between flex-wrap gap-4">

    <div class="flex items-center gap-3">
        <div class="p-2 rounded-xl bg-indigo-50">
            <i data-feather="clipboard-check" class="w-5 h-5 text-indigo-600"></i>
        </div>
        <div>
            <h1 class="text-lg font-semibold text-gray-800">
                Verifikasi Rapor Peserta
            </h1>
            <p class="text-xs text-gray-500">
                Tinjau nilai, kesimpulan, dan lakukan validasi rapor
            </p>
        </div>
    </div>

    {{-- BACK --}}
    <a href="{{ url()->previous() }}"
       class="inline-flex items-center gap-2
              px-4 py-2 rounded-xl
              border border-gray-300
              text-sm font-medium text-gray-700
              hover:bg-gray-50 transition">
        <i data-feather="arrow-left" class="w-4 h-4"></i>
        Kembali
    </a>

</div>
@endsection

@section('content')

{{-- ================= FLASH ================= --}}
@if(session('success'))
<div class="mb-6 flex items-center gap-2 px-4 py-3
            bg-green-50 border border-green-200
            rounded-xl text-sm text-green-700">
    <i data-feather="check-circle" class="w-4 h-4"></i>
    {{ session('success') }}
</div>
@endif

{{-- ================= IDENTITAS + NILAI AKHIR ================= --}}
<div class="bg-white rounded-3xl shadow-sm border mb-8">
    <div class="p-6 grid grid-cols-1 md:grid-cols-4 gap-6 text-sm">

        {{-- PESERTA --}}
        <div class="flex items-start gap-3">
            <div class="p-2 bg-gray-100 rounded-lg">
                <i data-feather="user" class="w-4 h-4 text-gray-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Peserta</p>
                <p class="font-semibold text-gray-800">
                    {{ $rapor->peserta->nama }}
                </p>
            </div>
        </div>

        {{-- SEKOLAH --}}
        <div class="flex items-start gap-3">
            <div class="p-2 bg-gray-100 rounded-lg">
                <i data-feather="home" class="w-4 h-4 text-gray-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Sekolah</p>
                <p class="font-semibold text-gray-800">
                    {{ $rapor->sekolah->nama_sekolah }}
                </p>
            </div>
        </div>

        {{-- SEMESTER --}}
        <div class="flex items-start gap-3">
            <div class="p-2 bg-gray-100 rounded-lg">
                <i data-feather="calendar" class="w-4 h-4 text-gray-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Semester</p>
                <p class="font-semibold text-gray-800">
                    {{ $rapor->semester->nama_semester }}
                </p>
            </div>
        </div>

        {{-- NILAI AKHIR --}}
        <div class="flex items-start gap-3">
            <div class="p-2 bg-indigo-100 rounded-lg">
                <i data-feather="award" class="w-4 h-4 text-indigo-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Nilai Akhir</p>
                <p class="text-3xl font-bold leading-none
                    @switch($rapor->nilai_akhir)
                        @case('A') text-green-600 @break
                        @case('B') text-yellow-600 @break
                        @case('C') text-red-600 @break
                        @default text-gray-500
                    @endswitch">
                    {{ $rapor->nilai_akhir ?? '-' }}
                </p>
            </div>
        </div>

    </div>
</div>

{{-- ================= DETAIL RAPOR ================= --}}
<div class="bg-white rounded-3xl shadow-sm border p-6 mb-8 space-y-10">

    {{-- MATERI --}}
    <div>
        <p class="text-xs text-gray-500 flex items-center gap-1 mb-1">
            <i data-feather="book-open" class="w-3 h-3"></i>
            Materi
        </p>
        <p class="font-medium text-gray-800 leading-relaxed">
            {{ $rapor->materi }}
        </p>
    </div>

    {{-- PENILAIAN --}}
    <div>
        <p class="text-xs text-gray-500 flex items-center gap-1 mb-4">
            <i data-feather="bar-chart-2" class="w-3 h-3"></i>
            Penilaian Kompetensi
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($rapor->nilaiRapors as $nilai)
            <div class="border rounded-2xl p-4 flex justify-between items-center
                        hover:bg-gray-50 transition">
                <div>
                    <p class="font-medium text-gray-800">
                        {{ $nilai->indikatorKompetensi->nama_indikator }}
                    </p>
                    <p class="text-xs text-gray-500">
                        Indikator Kompetensi
                    </p>
                </div>

                <div class="text-right">
                    <p class="text-xs text-gray-500">Nilai</p>
                    <p class="text-xl font-semibold text-indigo-600">
                        {{ $nilai->nilai }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- KESIMPULAN --}}
    <div>
        <p class="text-xs text-gray-500 flex items-center gap-1 mb-1">
            <i data-feather="file-text" class="w-3 h-3"></i>
            Kesimpulan
        </p>
        <p class="text-gray-800 leading-relaxed">
            {{ $rapor->kesimpulan ?? '-' }}
        </p>
    </div>

    {{-- CATATAN REVISI SEBELUMNYA --}}
    @if($rapor->catatan_revisi)
    <div class="bg-red-50 border border-red-200 rounded-2xl p-4">
        <p class="font-semibold text-red-700 flex items-center gap-2 mb-1">
            <i data-feather="alert-triangle" class="w-4 h-4"></i>
            Catatan Revisi Sebelumnya
        </p>
        <p class="text-sm text-red-700 leading-relaxed">
            {{ $rapor->catatan_revisi }}
        </p>
    </div>
    @endif

</div>

{{-- ================= AKSI ADMIN ================= --}}
@if(in_array($rapor->status, ['submitted','revision']))
<div class="bg-white rounded-3xl shadow-sm border p-6 space-y-6">

    {{-- APPROVE --}}
    <form method="POST"
          action="{{ route('admin.rapor.verifikasi.approve', $rapor->id) }}"
          onsubmit="return confirm('Setujui rapor ini?')">
        @csrf
        @method('PATCH')

        <button
            class="w-full sm:w-auto inline-flex items-center gap-2
                   bg-green-600 hover:bg-green-700
                   text-white px-6 py-3 rounded-xl
                   text-sm font-semibold transition">
            <i data-feather="check-circle" class="w-4 h-4"></i>
            Setujui Rapor
        </button>
    </form>

    <hr class="border-dashed">

    {{-- REVISION --}}
    <form method="POST"
          action="{{ route('admin.rapor.verifikasi.revision', $rapor->id) }}"
          class="space-y-3">
        @csrf
        @method('PATCH')

        <label class="text-xs font-medium text-gray-600 flex items-center gap-1">
            <i data-feather="edit-3" class="w-3 h-3"></i>
            Catatan Revisi
        </label>

        <textarea name="catatan_revisi"
            rows="3"
            required
            class="w-full border rounded-xl px-4 py-2 text-sm
                   focus:ring focus:ring-red-200"
            placeholder="Tuliskan catatan perbaikan yang harus dilakukan instruktur..."></textarea>

        <button
            class="inline-flex items-center gap-2
                   bg-red-500 hover:bg-red-600
                   text-white px-6 py-3 rounded-xl
                   text-sm font-semibold transition">
            <i data-feather="rotate-ccw" class="w-4 h-4"></i>
            Minta Revisi
        </button>
    </form>

</div>
@endif

@endsection
