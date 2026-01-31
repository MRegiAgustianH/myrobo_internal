@extends('layouts.app')

@section('header')
Tambah Indikator – {{ $kompetensi->nama_kompetensi }}
@endsection

@section('content')

<div class="bg-white p-6 rounded shadow md:w-1/2">

<form method="POST"
      action="{{ route('kompetensi.indikator.store', $kompetensi->id) }}">
    @csrf

    <label class="block text-sm font-medium mb-1">Nama Indikator</label>
    <input type="text"
           name="nama_indikator"
           class="w-full border rounded px-3 py-2 text-sm"
           required>

    <div class="mt-4 flex justify-end">
        <a href="{{ route('kompetensi.indikator.index', $kompetensi->id) }}"
           class="px-4 py-2 mr-2 text-sm">
            Batal
        </a>

        <button class="bg-blue-600 text-white px-4 py-2 rounded text-sm">
            Simpan
        </button>
    </div>
</form>

</div>

@endsection
