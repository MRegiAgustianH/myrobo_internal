@extends('layouts.app')

@section('header')
Indikator Kompetensi – {{ $kompetensi->nama_kompetensi }}
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

{{-- ACTION BAR --}}
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-5">
    <a href="{{ route('kompetensi.index') }}"
       class="text-sm text-gray-600 hover:underline">
        ← Kembali ke Kompetensi
    </a>

    <a href="{{ route('kompetensi.indikator.create', $kompetensi->id) }}"
       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
        + Tambah Indikator
    </a>
</div>

{{-- ================= MOBILE CARD VIEW ================= --}}
<div class="grid grid-cols-1 gap-4 md:hidden">

@forelse($indikators as $i)
<div class="bg-white rounded-xl shadow p-4 space-y-3">

    <div>
        <p class="font-semibold text-gray-800">
            {{ $i->nama_indikator }}
        </p>
    </div>

    <div class="flex gap-2 pt-2">
        <a href="{{ route('kompetensi.indikator.edit', [$kompetensi->id, $i->id]) }}"
           class="flex-1 bg-blue-100 text-blue-700 text-xs py-2 rounded text-center">
            Edit
        </a>

        <form action="{{ route('kompetensi.indikator.destroy', [$kompetensi->id, $i->id]) }}"
              method="POST"
              onsubmit="return confirm('Hapus indikator ini?')">
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
<div class="bg-white rounded-xl shadow p-6 text-center text-sm text-gray-500">
    Belum ada indikator
</div>
@endforelse

</div>

{{-- ================= DESKTOP TABLE VIEW ================= --}}
<div class="hidden md:block bg-white rounded-lg shadow overflow-x-auto">

<table class="min-w-full text-sm">
    <thead class="bg-gray-50 border-b">
        <tr class="text-gray-600 uppercase text-xs tracking-wider">
            <th class="px-4 py-3 text-left">
                Nama Indikator
            </th>
            <th class="px-4 py-3 text-center w-40">
                Aksi
            </th>
        </tr>
    </thead>

    <tbody class="divide-y">
    @forelse($indikators as $i)
        <tr class="hover:bg-gray-50 transition">
            <td class="px-4 py-3 font-medium">
                {{ $i->nama_indikator }}
            </td>

            <td class="px-4 py-3 text-center">
                <div class="inline-flex gap-2 justify-center">
                    <a href="{{ route('kompetensi.indikator.edit', [$kompetensi->id, $i->id]) }}"
                       class="bg-blue-100 text-blue-700 px-3 py-1 rounded text-xs">
                        Edit
                    </a>

                    <form action="{{ route('kompetensi.indikator.destroy', [$kompetensi->id, $i->id]) }}"
                          method="POST"
                          onsubmit="return confirm('Hapus indikator ini?')">
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
            <td colspan="2"
                class="px-4 py-6 text-center text-gray-500">
                Belum ada indikator
            </td>
        </tr>
    @endforelse
    </tbody>
</table>

</div>

@endsection
