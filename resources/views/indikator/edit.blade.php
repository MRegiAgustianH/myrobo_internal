@extends('layouts.app')

@section('header')
Edit Indikator – {{ $kompetensi->nama_kompetensi }}
@endsection

@section('content')

<div class="max-w-2xl mx-auto">

    <div class="bg-white p-6 sm:p-8 rounded-xl shadow">

        <form method="POST"
              action="{{ route('kompetensi.indikator.update', [$kompetensi->id, $indikator->id]) }}">
            @csrf
            @method('PUT')

            {{-- INPUT --}}
            <div class="mb-5">
                <label
                    for="nama_indikator"
                    class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Indikator
                </label>

                <input
                    type="text"
                    id="nama_indikator"
                    name="nama_indikator"
                    value="{{ $indikator->nama_indikator }}"
                    class="w-full border rounded-lg px-4 py-2 text-sm
                           focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    required>
            </div>

            {{-- ACTION --}}
            <div class="flex justify-end gap-3 pt-4 border-t">

                <a
                    href="{{ route('kompetensi.indikator.index', $kompetensi->id) }}"
                    class="px-4 py-2 text-sm text-gray-600 hover:underline">
                    Batal
                </a>

                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700
                           text-white px-5 py-2 rounded-lg text-sm">
                    Update
                </button>

            </div>
        </form>

    </div>

</div>

@endsection
