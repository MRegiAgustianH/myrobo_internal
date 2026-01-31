@extends('layouts.app')

@section('header')
Manajemen Rapor
@endsection

@section('content')

{{-- ACTION BAR --}}
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">

    <h2 class="text-lg font-semibold text-gray-700">
        Daftar Rapor Peserta
    </h2>

    <div class="flex gap-2">
        <a href="{{ route('rapor.create') }}"
           class="bg-[#8FBFC2] hover:bg-[#7aaeb2] text-white px-4 py-2 rounded-lg text-sm shadow">
            + Tambah Rapor
        </a>

        <a href="{{ route('kompetensi.index') }}"
           class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm shadow">
            Kelola Kompetensi
        </a>
    </div>
</div>

{{-- GRID RAPOR --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

@forelse($rapors as $rapor)
    <div
        class="bg-white rounded-2xl shadow-md hover:shadow-xl transition overflow-hidden border">

        {{-- HEADER CARD --}}
        <div class="bg-[#8FBFC2] text-white px-5 py-4">
            <h3 class="font-semibold text-base truncate">
                {{ $rapor->peserta->nama }}
            </h3>
            <p class="text-xs opacity-90">
                {{ $rapor->sekolah->nama_sekolah }}
            </p>
        </div>

        {{-- BODY CARD --}}
        <div class="p-5 space-y-3 text-sm text-gray-700">

            <div class="flex justify-between">
                <span class="text-gray-500">Semester</span>
                <span class="font-medium">
                    {{ $rapor->semester->nama_semester }}
                </span>
            </div>

            <div class="flex justify-between items-center">
                <span class="text-gray-500">Nilai Akhir</span>

                <span
                    class="px-3 py-1 rounded-full text-xs font-semibold
                    {{ $rapor->nilai_akhir >= 85 ? 'bg-emerald-100 text-emerald-700' :
                       ($rapor->nilai_akhir >= 70 ? 'bg-yellow-100 text-yellow-700' :
                       'bg-red-100 text-red-700') }}">
                    {{ $rapor->nilai_akhir }}
                </span>
            </div>
        </div>

        {{-- FOOTER ACTION --}}
        <div class="border-t px-4 py-3 bg-gray-50 flex justify-between gap-2 text-xs">

            <a href="{{ route('rapor.cetak', $rapor->id) }}"
            target="_blank"
            class="flex-1 text-center bg-indigo-600 hover:bg-indigo-700 text-white py-1.5 rounded flex items-center justify-center gap-1">
                Cetak
            </a>


            <a href="{{ route('rapor.edit', $rapor->id) }}"
               class="flex-1 text-center bg-yellow-400 hover:bg-yellow-500 py-1.5 rounded text-gray-800">
                Edit
            </a>

            <form method="POST"
                  action="{{ route('rapor.destroy', $rapor->id) }}"
                  onsubmit="return confirm('Hapus rapor ini?')"
                  class="flex-1">
                @csrf
                @method('DELETE')
                <button
                    class="w-full bg-red-500 hover:bg-red-600 text-white py-1.5 rounded">
                    Hapus
                </button>
            </form>
        </div>

    </div>
@empty
    <div class="col-span-full text-center py-12 text-gray-500">
        Belum ada data rapor.
    </div>
@endforelse

</div>

@endsection
