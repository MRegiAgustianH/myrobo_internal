@extends('layouts.app')

@section('header')
Pengeluaran
@endsection

@section('content')

@if(session('success'))
<div class="mb-4 p-4 rounded-xl bg-green-50 text-green-700 border border-green-200">
    {{ session('success') }}
</div>
@endif

@php
    $totalPengeluaran = $pengeluarans->sum('jumlah');
@endphp

{{-- ================= HEADER ================= --}}
<div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
    <div>
        <h2 class="text-xl font-semibold text-gray-800">Daftar Pengeluaran</h2>
        <p class="text-sm text-gray-500 mt-1">
            Total halaman ini:
            <span class="font-semibold text-gray-700">
                Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
            </span>
        </p>
    </div>

    <div class="flex flex-col sm:flex-row gap-2">
        <a href="{{ route('keuangan.gaji.instruktur') }}"
           class="inline-flex items-center justify-center gap-2
                  bg-emerald-500 hover:bg-emerald-600
                  text-white px-4 py-2 rounded-xl
                  text-sm font-semibold transition">
            <i data-feather="dollar-sign" class="w-4 h-4"></i>
            Gaji Instruktur
        </a>

        <button onclick="openCreateModal()"
           class="inline-flex items-center justify-center gap-2
                  bg-[#8FBFC2] hover:bg-[#6FA9AD]
                  text-white px-4 py-2 rounded-xl
                  text-sm font-semibold transition">
            <i data-feather="plus" class="w-4 h-4"></i>
            Tambah Pengeluaran
        </button>
    </div>
</div>

{{-- ================= DESKTOP TABLE ================= --}}
<div class="hidden md:block bg-white rounded-2xl border border-[#E3EEF0] shadow-sm overflow-x-auto">
<table class="min-w-full text-sm">
    <thead class="bg-[#F6FAFB] border-b border-[#E3EEF0]">
        <tr>
            <th class="px-5 py-3 text-left">Tanggal</th>
            <th class="px-5 py-3">Kategori</th>
            <th class="px-5 py-3">Deskripsi</th>
            <th class="px-5 py-3 text-right">Jumlah</th>
            <th class="px-5 py-3 text-center">Aksi</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-[#E3EEF0]">
        @foreach($pengeluarans as $p)
        <tr class="hover:bg-gray-50">
            <td class="px-5 py-3">{{ $p->tanggal }}</td>
            <td class="px-5 py-3 font-medium">{{ $p->kategori }}</td>
            <td class="px-5 py-3 text-gray-600">{{ $p->deskripsi ?? '—' }}</td>
            <td class="px-5 py-3 text-right font-semibold">
                Rp {{ number_format($p->jumlah, 0, ',', '.') }}
            </td>
            <td class="px-5 py-3 text-center whitespace-nowrap">
                <button
                    onclick="openEditModal(this)"
                    data-id="{{ $p->id }}"
                    data-tanggal="{{ $p->tanggal }}"
                    data-kategori="{{ $p->kategori }}"
                    data-deskripsi="{{ $p->deskripsi }}"
                    data-jumlah="{{ $p->jumlah }}"
                    class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded text-xs">
                    Edit
                </button>

                <button
                    onclick="openDeleteModal('{{ $p->id }}')"
                    class="bg-red-100 text-red-700 px-3 py-1 rounded text-xs">
                    Hapus
                </button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>

{{-- ================= MOBILE CARD ================= --}}
<div class="md:hidden space-y-3">
@foreach($pengeluarans as $p)
<div class="bg-white rounded-xl border p-4 shadow-sm">
    <div class="flex justify-between">
        <div>
            <p class="font-semibold">{{ $p->kategori }}</p>
            <p class="text-xs text-gray-500">{{ $p->tanggal }}</p>
        </div>
        <p class="font-bold">
            Rp {{ number_format($p->jumlah, 0, ',', '.') }}
        </p>
    </div>

    <p class="mt-2 text-sm text-gray-600">{{ $p->deskripsi ?? '—' }}</p>

    <div class="mt-3 flex justify-end gap-4 text-sm">
        <button onclick="openEditModal(this)"
                data-id="{{ $p->id }}"
                data-tanggal="{{ $p->tanggal }}"
                data-kategori="{{ $p->kategori }}"
                data-deskripsi="{{ $p->deskripsi }}"
                data-jumlah="{{ $p->jumlah }}"
                class="flex-1 bg-yellow-100 text-yellow-700 text-xs py-2 rounded">
            Edit
        </button>
        <button onclick="openDeleteModal('{{ $p->id }}')"
                class="bg-red-100 text-red-700 text-xs px-3 py-2 rounded">
            Hapus
        </button>
    </div>
</div>
@endforeach
</div>

<div class="mt-6">
    {{ $pengeluarans->links() }}
</div>

{{-- ================= MODAL TAMBAH ================= --}}
<div id="createModal" class="modal-overlay fixed inset-0 z-50 hidden bg-black/50">
<div class="modal-content bg-white rounded-xl shadow-lg w-full max-w-md p-6">
    <h3 class="text-lg font-semibold mb-5">Tambah Pengeluaran</h3>

    <form method="POST" action="{{ route('keuangan.store') }}">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Tanggal
            </label>
            <input type="date" name="tanggal"
                   class="w-full border rounded-lg px-3 py-2 text-sm"
                   required>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Kategori
            </label>
            <input type="text" name="kategori"
                   class="w-full border rounded-lg px-3 py-2 text-sm"
                   placeholder="Contoh: Operasional"
                   required>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Jumlah
            </label>
            <input type="number" name="jumlah"
                   class="w-full border rounded-lg px-3 py-2 text-sm"
                   placeholder="Contoh: 150000"
                   required>
        </div>

        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Deskripsi
            </label>
            <textarea name="deskripsi"
                      class="w-full border rounded-lg px-3 py-2 text-sm"
                      rows="3"
                      placeholder="Opsional"></textarea>
        </div>

        <div class="flex justify-end gap-2">
            <button type="button"
                    onclick="closeCreateModal()"
                    class="px-4 py-2 rounded-lg border text-sm">
                Batal
            </button>
            <button
                class="px-4 py-2 rounded-lg
                       bg-[#8FBFC2] hover:bg-[#6FA9AD]
                       text-white text-sm font-semibold">
                Simpan
            </button>
        </div>
    </form>
</div>
</div>


{{-- ================= MODAL EDIT ================= --}}
<div id="editModal" class="modal-overlay fixed inset-0 z-50 hidden bg-black/50">
<div class="modal-content bg-white rounded-xl shadow-lg w-full max-w-md p-6">
    <h3 class="text-lg font-semibold mb-5">Edit Pengeluaran</h3>

    <form id="editForm" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Tanggal
            </label>
            <input type="date" name="tanggal" id="editTanggal"
                   class="w-full border rounded-lg px-3 py-2 text-sm"
                   required>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Kategori
            </label>
            <input type="text" name="kategori" id="editKategori"
                   class="w-full border rounded-lg px-3 py-2 text-sm"
                   required>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Jumlah
            </label>
            <input type="number" name="jumlah" id="editJumlah"
                   class="w-full border rounded-lg px-3 py-2 text-sm"
                   required>
        </div>

        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Deskripsi
            </label>
            <textarea name="deskripsi" id="editDeskripsi"
                      class="w-full border rounded-lg px-3 py-2 text-sm"
                      rows="3"></textarea>
        </div>

        <div class="flex justify-end gap-2">
            <button type="button"
                    onclick="closeEditModal()"
                    class="px-4 py-2 rounded-lg border text-sm">
                Batal
            </button>
            <button
                class="px-4 py-2 rounded-lg
                       bg-blue-600 hover:bg-blue-700
                       text-white text-sm font-semibold">
                Update
            </button>
        </div>
    </form>
</div>
</div>

{{-- ================= MODAL HAPUS ================= --}}
<div id="deleteModal" class="modal-overlay fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
<div class="modal-content bg-white rounded-xl shadow-lg w-full max-w-md p-6">
    <h3 class="text-lg font-semibold mb-4">Hapus Pengeluaran?</h3>
    <form id="deleteForm" method="POST">
        @csrf
        @method('DELETE')
        <div class="flex justify-end gap-2">
            <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 border rounded">Batal</button>
            <button class="px-4 py-2 bg-red-600 text-white rounded font-semibold">Hapus</button>
        </div>
    </form>
</div>
</div>

{{-- ================= SCRIPT ================= --}}
<script>
const createModal = document.getElementById('createModal');
const editModal   = document.getElementById('editModal');
const deleteModal = document.getElementById('deleteModal');
const editForm    = document.getElementById('editForm');
const deleteForm  = document.getElementById('deleteForm');

function showModal(modal){
    modal.classList.remove('hidden');
    setTimeout(() => modal.classList.add('show'), 10);
}
function hideModal(modal){
    modal.classList.remove('show');
    setTimeout(() => modal.classList.add('hidden'), 200);
}

function openCreateModal(){ showModal(createModal); }
function closeCreateModal(){ hideModal(createModal); }

function openEditModal(el){
    editForm.action = "{{ url('keuangan') }}/" + el.dataset.id;
    editTanggal.value   = el.dataset.tanggal;
    editKategori.value  = el.dataset.kategori;
    editJumlah.value    = el.dataset.jumlah;
    editDeskripsi.value = el.dataset.deskripsi ?? '';
    showModal(editModal);
}
function closeEditModal(){ hideModal(editModal); }

function openDeleteModal(id){
    deleteForm.action = "{{ url('keuangan') }}/" + id;
    showModal(deleteModal);
}
function closeDeleteModal(){ hideModal(deleteModal); }
</script>

{{-- ================= MODAL ANIMATION ================= --}}
<style>
.modal-overlay {
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    pointer-events: none;
    transition: opacity .2s ease;
}
.modal-overlay.show {
    opacity: 1;
    pointer-events: auto;
}
.modal-content {
    transform: translateY(16px) scale(.95);
    opacity: 0;
    transition: all .2s ease;
}
.modal-overlay.show .modal-content {
    transform: translateY(0) scale(1);
    opacity: 1;
}
</style>

@endsection
