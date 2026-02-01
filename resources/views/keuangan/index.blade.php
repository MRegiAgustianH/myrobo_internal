@extends('layouts.app')

@section('header')
Pengeluaran
@endsection

@section('content')

@if(session('success'))
<div class="mb-4 p-3 rounded-lg bg-green-100 text-green-700 border border-green-200">
    {{ session('success') }}
</div>
@endif

@php
    $totalPengeluaran = $pengeluarans->sum('jumlah');
@endphp

{{-- ================= HEADER ================= --}}
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-semibold text-gray-800">Daftar Pengeluaran</h2>
        <p class="text-sm text-gray-500">
            Total halaman ini:
            <span class="font-semibold">
                Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
            </span>
        </p>
    </div>

    <div class="flex gap-2">
        <a href="{{ route('keuangan.gaji.instruktur') }}"
           class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600
                  text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
            <i data-feather="dollar-sign" class="w-4 h-4"></i>
            Gaji Instruktur
        </a>

        <button onclick="openCreateModal()"
           class="inline-flex items-center gap-2 bg-[#8FBFC2] hover:bg-[#6FA9AD] text-white px-4 py-2 rounded-lg text-sm transition">
            <i data-feather="plus" class="w-4 h-4"></i>
            Tambah Pengeluaran
        </button>
    </div>
</div>

{{-- ================= TABLE ================= --}}
<div class="bg-white rounded-2xl border border-[#E3EEF0] shadow-sm overflow-x-auto">
<table class="min-w-full text-sm">
    <thead class="bg-[#F6FAFB] border-b border-[#E3EEF0]">
        <tr>
            <th class="px-4 py-3 text-left">Tanggal</th>
            <th class="px-4 py-3">Kategori</th>
            <th class="px-4 py-3">Deskripsi</th>
            <th class="px-4 py-3 text-right">Jumlah</th>
            <th class="px-4 py-3 text-center">Aksi</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-[#E3EEF0]">
        @foreach($pengeluarans as $p)
        <tr class="hover:bg-gray-50">
            <td class="px-4 py-3">{{ $p->tanggal }}</td>
            <td class="px-4 py-3 font-medium">{{ $p->kategori }}</td>
            <td class="px-4 py-3 text-gray-600">{{ $p->deskripsi ?? '-' }}</td>
            <td class="px-4 py-3 text-right font-semibold">
                Rp {{ number_format($p->jumlah, 0, ',', '.') }}
            </td>
            <td class="px-4 py-3 text-center">
                <button
                    onclick="openEditModal(this)"
                    data-id="{{ $p->id }}"
                    data-tanggal="{{ $p->tanggal }}"
                    data-kategori="{{ $p->kategori }}"
                    data-deskripsi="{{ $p->deskripsi }}"
                    data-jumlah="{{ $p->jumlah }}"
                    class="inline-flex items-center gap-1 text-blue-600 hover:underline text-sm">
                    <i data-feather="edit-2" class="w-4 h-4"></i>
                    Edit
                </button>

                <button onclick="openDeleteModal('{{ $p->id }}')"
                        class="inline-flex items-center gap-1 text-red-600 hover:underline text-sm ml-2">
                    <i data-feather="trash-2" class="w-4 h-4"></i>
                    Hapus
                </button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>

<div class="mt-4">
    {{ $pengeluarans->links() }}
</div>

{{-- ================= MODAL TAMBAH ================= --}}
<div id="createModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
<div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
    <h3 class="text-lg font-semibold mb-4">Tambah Pengeluaran</h3>

    <form method="POST" action="{{ route('keuangan.store') }}">
        @csrf

        <div class="mb-3">
            <label class="text-sm">Tanggal</label>
            <input type="date" name="tanggal" class="w-full border rounded px-3 py-2" required>
        </div>

        <div class="mb-3">
            <label class="text-sm">Kategori</label>
            <input type="text" name="kategori" class="w-full border rounded px-3 py-2" required>
        </div>

        <div class="mb-3">
            <label class="text-sm">Jumlah</label>
            <input type="number" name="jumlah" class="w-full border rounded px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label class="text-sm">Deskripsi</label>
            <textarea name="deskripsi" class="w-full border rounded px-3 py-2"></textarea>
        </div>

        <div class="flex justify-end gap-2">
            <button type="button" onclick="closeCreateModal()" class="px-4 py-2 border rounded">Batal</button>
            <button class="px-4 py-2 bg-[#8FBFC2] rounded font-semibold">Simpan</button>
        </div>
    </form>
</div>
</div>

{{-- ================= MODAL EDIT ================= --}}
<div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
<div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
    <h3 class="text-lg font-semibold mb-4">Edit Pengeluaran</h3>

    <form id="editForm" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="text-sm">Tanggal</label>
            <input type="date" name="tanggal" id="editTanggal"
                   class="w-full border rounded px-3 py-2" required>
        </div>

        <div class="mb-3">
            <label class="text-sm">Kategori</label>
            <input type="text" name="kategori" id="editKategori"
                   class="w-full border rounded px-3 py-2" required>
        </div>

        <div class="mb-3">
            <label class="text-sm">Jumlah</label>
            <input type="number" name="jumlah" id="editJumlah"
                   class="w-full border rounded px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label class="text-sm">Deskripsi</label>
            <textarea name="deskripsi" id="editDeskripsi"
                      class="w-full border rounded px-3 py-2"></textarea>
        </div>

        <div class="flex justify-end gap-2">
            <button type="button" onclick="closeEditModal()" class="px-4 py-2 border rounded">Batal</button>
            <button class="px-4 py-2 bg-blue-600 text-white rounded font-semibold">Update</button>
        </div>
    </form>
</div>
</div>

{{-- ================= MODAL HAPUS ================= --}}
<div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
<div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
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
function openCreateModal(){
    document.getElementById('createModal').classList.remove('hidden');
    document.getElementById('createModal').classList.add('flex');
    feather.replace();
}
function closeCreateModal(){
    document.getElementById('createModal').classList.add('hidden');
}

function openEditModal(el){
    document.getElementById('editForm').action = "{{ url('keuangan') }}/" + el.dataset.id;
    editTanggal.value   = el.dataset.tanggal;
    editKategori.value  = el.dataset.kategori;
    editJumlah.value    = el.dataset.jumlah;
    editDeskripsi.value = el.dataset.deskripsi ?? '';
    document.getElementById('editModal').classList.remove('hidden');
    document.getElementById('editModal').classList.add('flex');
    feather.replace();
}
function closeEditModal(){
    document.getElementById('editModal').classList.add('hidden');
}

function openDeleteModal(id){
    deleteForm.action = "{{ url('keuangan') }}/" + id;
    deleteModal.classList.remove('hidden');
    deleteModal.classList.add('flex');
}
function closeDeleteModal(){
    deleteModal.classList.add('hidden');
}
</script>

@endsection
