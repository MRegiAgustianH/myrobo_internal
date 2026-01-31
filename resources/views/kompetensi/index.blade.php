@extends('layouts.app')

@section('header')
Manajemen Kompetensi
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
    <a href="{{ route('rapor.manajemen') }}"
       class="text-sm text-gray-600">
        ← Kembali ke Manajemen Rapor
    </a>
    <a href="{{ route('kompetensi.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded text-sm">
        + Tambah Kompetensi
    </a>
</div>

<div class="bg-white rounded shadow">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-2 text-left">Nama Kompetensi</th>
                <th class="px-4 py-2 text-center w-40">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
        @foreach($kompetensis as $k)
            <tr>
                <td class="px-4 py-2">{{ $k->nama_kompetensi }}</td>
                <td class="px-4 py-2 text-center">
                    <a href="{{ route('kompetensi.indikator.index', $k->id) }}"
                    class="text-green-600 text-sm mr-2">
                        Indikator
                    </a>

                    <a href="{{ route('kompetensi.edit', $k->id) }}"
                    class="text-blue-600 text-sm mr-2">
                        Edit
                    </a>

                    <form action="{{ route('kompetensi.destroy', $k->id) }}"
                        method="POST"
                        class="inline"
                        onsubmit="return confirm('Hapus kompetensi ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-600 text-sm">
                            Hapus
                        </button>
                    </form>
                </td>

            </tr>
        @endforeach
        </tbody>
    </table>
</div>

@endsection

