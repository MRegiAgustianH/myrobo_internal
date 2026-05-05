@extends('layouts.app')

@section('header')
Manajemen Kompetensi
@endsection

@section('content')

{{-- FLASH MESSAGE --}}
@if(session('success'))
<div class="mb-4 p-3 bg-green-100 text-green-700 rounded text-sm">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-4 p-3 bg-red-100 text-red-700 rounded text-sm">
    {{ session('error') }}
</div>
@endif

{{-- ================= HEADER INFO ================= --}}
<div class="mb-4">
    <p class="text-sm text-gray-500">Materi</p>
    <h2 class="text-lg font-semibold text-gray-800">
        {{ $materi->nama_materi }}
    </h2>
</div>

{{-- ================= ACTION BAR ================= --}}
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">

    <a href="{{ route('materi.index') }}"
       class="text-sm text-gray-600 hover:underline">
        ← Kembali ke Materi
    </a>

    <a href="{{ route('kompetensi.create', ['materi_id' => $materi->id]) }}"
       class="inline-flex items-center gap-2
              bg-[#8FBFC2] hover:bg-[#6FA9AD]
              text-white px-4 py-2 rounded-lg text-sm transition">
        <i data-feather="plus" class="w-4 h-4"></i>
        Tambah Kompetensi
    </a>
</div>

{{-- ================= MOBILE CARD VIEW ================= --}}
<div class="grid grid-cols-1 gap-4 md:hidden">

@forelse($kompetensis as $k)
<div class="bg-white rounded-xl shadow p-4 space-y-3">

    <div>
        <p class="font-semibold text-gray-800">
            {{ $k->nama_kompetensi }}
        </p>
        <p class="text-xs text-gray-500">
            {{ $k->indikator_kompetensis_count }} indikator
        </p>
    </div>

    <div class="flex gap-2 pt-2 border-t">
        <a href="{{ route('kompetensi.indikator.index', $k->id) }}"
           class="flex-1 bg-green-100 text-green-700 text-xs py-2 rounded text-center">
            Indikator
        </a>

        <a href="{{ route('kompetensi.edit', $k->id) }}"
           class="flex-1 bg-blue-100 text-blue-700 text-xs py-2 rounded text-center">
            Edit
        </a>

        <form action="{{ route('kompetensi.destroy', $k->id) }}"
              method="POST"
              onsubmit="return confirm('Hapus kompetensi ini?')">
            @csrf
            @method('DELETE')
            <button
                class="bg-red-100 text-red-700 text-xs px-3 py-2 rounded">
                Hapus
            </button>
        </form>
    </div>

</div>
@empty
<div class="text-center text-sm text-gray-500 py-6">
    Belum ada kompetensi untuk materi ini
</div>
@endforelse

</div>

{{-- ================= DESKTOP TABLE VIEW ================= --}}
<div class="hidden md:block bg-white rounded-xl shadow overflow-x-auto">

<table class="min-w-full text-sm">
    <thead class="bg-gray-50 border-b">
        <tr class="text-gray-600 uppercase text-xs tracking-wider">
            <th class="px-4 py-3 text-left">
                Nama Kompetensi
            </th>
            <th class="px-4 py-3 text-center">
                Indikator
            </th>
            <th class="px-4 py-3 text-center w-56">
                Aksi
            </th>
        </tr>
    </thead>

    <tbody class="divide-y">
    @forelse($kompetensis as $k)
        <tr class="hover:bg-gray-50 transition">
            <td class="px-4 py-3 font-medium">
                {{ $k->nama_kompetensi }}
            </td>

            <td class="px-4 py-3 text-center text-gray-600 text-xs">
                {{ $k->indikator_kompetensis_count }}
            </td>

            <td class="px-4 py-3 text-center">
                <div class="inline-flex gap-2 justify-center">

                    <a href="{{ route('kompetensi.indikator.index', $k->id) }}"
                       class="bg-green-100 text-green-700 px-3 py-1 rounded text-xs">
                        Indikator
                    </a>

                    <a href="{{ route('kompetensi.edit', $k->id) }}"
                       class="bg-blue-100 text-blue-700 px-3 py-1 rounded text-xs">
                        Edit
                    </a>

                    <form action="{{ route('kompetensi.destroy', $k->id) }}"
                          method="POST"
                          onsubmit="return confirm('Hapus kompetensi ini?')">
                        @csrf
                        @method('DELETE')
                        <button
                            class="bg-red-100 text-red-700 px-3 py-1 rounded text-xs">
                            Hapus
                        </button>
                    </form>

                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="3"
                class="px-4 py-6 text-center text-gray-500 text-sm">
                Belum ada kompetensi untuk materi ini
            </td>
        </tr>
    @endforelse
    </tbody>
</table>

</div>

@endsection
