@extends('layouts.app')

@section('header')
<div class="flex items-center gap-4">
    <div class="p-3 rounded-2xl bg-[#8FBFC2]/20">
        <i data-feather="edit-3" class="w-6 h-6 text-[#6FA9AD]"></i>
    </div>
    <div>
        <h1 class="text-xl font-semibold text-gray-800">
            Isi Rapor Peserta
        </h1>
        <p class="text-xs text-gray-500">
            Lengkapi penilaian rapor peserta secara detail
        </p>
    </div>
</div>
@endsection

@section('content')

{{-- ================= INFO PESERTA ================= --}}
<div class="bg-white rounded-2xl shadow-sm border mb-8">
    <div class="p-6 grid grid-cols-1 sm:grid-cols-3 gap-6 text-sm">

        {{-- PESERTA --}}
        <div>
            <p class="text-xs text-gray-400 flex items-center gap-1">
                <i data-feather="user" class="w-3 h-3"></i>
                Nama Peserta
            </p>
            <p class="font-semibold text-gray-800">
                {{ $peserta->nama }}
            </p>
        </div>

        {{-- SEKOLAH --}}
        <div>
            <p class="text-xs text-gray-400 flex items-center gap-1">
                <i data-feather="home" class="w-3 h-3"></i>
                Sekolah
            </p>
            <p class="font-semibold text-gray-800">
                {{ $raporTugas->sekolah->nama_sekolah }}
            </p>
        </div>

        {{-- SEMESTER --}}
        <div>
            <p class="text-xs text-gray-400 flex items-center gap-1">
                <i data-feather="calendar" class="w-3 h-3"></i>
                Semester
            </p>
            <p class="font-semibold text-gray-800">
                {{ $raporTugas->semester->nama_semester }}
            </p>
        </div>

    </div>
</div>

{{-- ================= FORM RAPOR ================= --}}
<form method="POST"
      action="{{ route('instruktur.rapor.store', [$raporTugas->id, $peserta->id]) }}">
    @csrf

    @include('admin.rapor._form', [
        'rapor'       => $rapor,
        'materis'     => $materis,
        'kompetensis' => $kompetensis,
        'readonly'    => false
    ])

    {{-- ================= ACTION ================= --}}
    <div class="flex flex-col sm:flex-row justify-end gap-3 mt-8">

        {{-- BACK --}}
        <a href="{{ route('instruktur.rapor-tugas.show', $raporTugas->id) }}"
           class="inline-flex items-center justify-center gap-2
                  px-5 py-2.5 rounded-xl
                  border border-gray-300
                  text-sm font-medium text-gray-700
                  hover:bg-gray-50 transition">
            <i data-feather="arrow-left" class="w-4 h-4"></i>
            Kembali
        </a>

        {{-- SUBMIT --}}
        <button type="submit"
            class="inline-flex items-center justify-center gap-2
                   px-6 py-2.5 rounded-xl
                   bg-[#8FBFC2] hover:bg-[#6FA9AD]
                   text-white text-sm font-semibold transition">
            <i data-feather="save" class="w-4 h-4"></i>
            Simpan Rapor
        </button>

    </div>
</form>

@endsection
