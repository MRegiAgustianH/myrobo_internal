@extends('layouts.app')

@section('header')
Indikator Kompetensi – {{ $kompetensi->nama_kompetensi }}
@endsection

@section('content')

@if(session('success'))
<div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
    {{ session('error') }}
</div>
@endif

<div class="mb-4 flex justify-between">
    <a href="{{ route('kompetensi.index') }}"
       class="text-sm text-gray-600">
        ← Kembali ke Kompetensi
    </a>

    <a href="{{ route('kompetensi.indikator.create', $kompetensi->id) }}"
       class="bg-blue-600 text-white px-4 py-2 rounded text-sm">
        + Tambah Indikator
    </a>
</div>

<div class="bg-white rounded shadow">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-2 text-left">Nama Indikator</th>
                <th class="px-4 py-2 text-center w-40">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
        @forelse($indikators as $i)
            <tr>
                <td class="px-4 py-2">{{ $i->nama_indikator }}</td>
                <td class="px-4 py-2 text-center">
                    <a href="{{ route('kompetensi.indikator.edit', [$kompetensi->id, $i->id]) }}"
                       class="text-blue-600 text-sm mr-2">
                        Edit
                    </a>

                    <form action="{{ route('kompetensi.indikator.destroy', [$kompetensi->id, $i->id]) }}"
                          method="POST"
                          class="inline"
                          onsubmit="return confirm('Hapus indikator ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-600 text-sm">
                            Hapus
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="2" class="px-4 py-4 text-center text-gray-500">
                    Belum ada indikator
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

@endsection
