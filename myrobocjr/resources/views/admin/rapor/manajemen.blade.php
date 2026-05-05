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
           class="inline-flex items-center gap-2 bg-[#8FBFC2] hover:bg-[#6FA9AD] text-white px-4 py-2 rounded-lg text-sm transition">
            <i data-feather="plus" class="w-4 h-4"></i>
            Tambah Rapor
        </a>
    </div>
</div>

{{-- GRID RAPOR --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

@forelse($rapors as $rapor)
    <div
        class="group bg-white rounded-2xl border shadow-sm
               hover:shadow-xl hover:-translate-y-1
               transition-all duration-300 overflow-hidden">

        {{-- HEADER --}}
        <div class="bg-gradient-to-r from-[#8FBFC2] to-[#6FA9AD] px-5 py-4 text-white">
            <h3 class="font-semibold text-base truncate">
                {{ $rapor->peserta->nama }}
            </h3>
            <p class="text-xs opacity-90 truncate">
                {{ $rapor->sekolah->nama_sekolah }}
            </p>
        </div>

        {{-- BODY --}}
        <div class="p-5 space-y-4 text-sm text-gray-700">

            <div class="flex justify-between items-center">
                <span class="text-gray-500">Semester</span>
                <span class="font-medium text-gray-800">
                    {{ $rapor->semester->nama_semester }}
                </span>
            </div>

            <div class="flex justify-between items-center">
                <span class="text-gray-500">Nilai Akhir</span>

                <span
                    class="px-3 py-1 rounded-full text-xs font-semibold tracking-wide
                    {{ $rapor->nilai_akhir === 'A'
                        ? 'bg-emerald-100 text-emerald-700'
                        : ($rapor->nilai_akhir === 'B'
                            ? 'bg-yellow-100 text-yellow-700'
                            : 'bg-red-100 text-red-700') }}">
                    {{ $rapor->nilai_akhir }}
                </span>
            </div>

            {{-- Materi --}}
            <div class="pt-2 border-t text-xs text-gray-500 line-clamp-2">
                {{ $rapor->materi }}
            </div>
        </div>

        {{-- FOOTER ACTION --}}
        <div class="px-4 py-3 bg-gray-50 border-t">
            <div class="flex gap-2 text-xs">

                <a href="{{ route('rapor.cetak', $rapor->id) }}"
                   target="_blank"
                   class="flex-1 inline-flex items-center justify-center gap-1
                          bg-indigo-600 hover:bg-indigo-700
                          text-white py-2 rounded-lg transition">
                    Cetak
                </a>

                <a href="{{ route('rapor.edit', $rapor->id) }}"
                   class="flex-1 inline-flex items-center justify-center gap-1
                          bg-yellow-400 hover:bg-yellow-500
                          text-gray-800 py-2 rounded-lg transition">
                    Edit
                </a>

                <button type="button"
                    onclick="confirmDeleteRapor({{ $rapor->id }})"
                    class="flex-1 inline-flex items-center justify-center gap-1
                           bg-red-500 hover:bg-red-600
                           text-white py-2 rounded-lg transition">
                    Hapus
                </button>

            </div>
        </div>

    </div>
@empty
    <div class="col-span-full text-center py-16 text-gray-500">
        Belum ada data rapor.
    </div>
@endforelse

</div>


@push('scripts')
<script>
function confirmDeleteRapor(id) {
    Swal.fire({
        title: 'Hapus Rapor?',
        text: 'Rapor yang dihapus tidak dapat dikembalikan',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#dc2626'
    }).then((result) => {
        if (result.isConfirmed) {

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/rapor/${id}`;

            form.innerHTML = `
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="DELETE">
            `;

            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush


@endsection
