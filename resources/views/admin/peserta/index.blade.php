@extends('layouts.app')

@section('header')
Peserta – {{ $sekolah->nama_sekolah }}
@endsection

@section('content')

<div class="flex justify-between items-center mb-4">
    <a href="{{ route('sekolah.index') }}"
       class="text-sm text-gray-600 hover:underline">
        ← Kembali ke Sekolah
    </a>

    <div class="flex gap-2">
        <a href="{{ route('peserta.template.download') }}"
           class="bg-gray-600 text-white px-4 py-2 rounded text-sm">
            Download Template
        </a>

        <button type="button"
            onclick="openImportModal()"
            class="bg-green-600 text-white px-4 py-2 rounded text-sm">
            Import Excel
        </button>

        <button type="button"
            onclick="openCreateModal()"
            class="bg-blue-600 text-white px-4 py-2 rounded text-sm">
            + Tambah Peserta
        </button>
    </div>
</div>

@if(session('success'))
<div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
    {{ session('success') }}
</div>
@endif

<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="min-w-[900px] w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr class="uppercase text-xs text-gray-600">
                <th class="px-4 py-3 text-left">Nama</th>
                <th class="px-4 py-3 text-center">JK</th>
                <th class="px-4 py-3 text-left">Kelas</th>
                <th class="px-4 py-3 text-left">Kontak</th>
                <th class="px-4 py-3 text-center">Status</th>
                <th class="px-4 py-3 text-center w-40">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
        @forelse($pesertas as $p)
            <tr>
                <td class="px-4 py-2">{{ $p->nama }}</td>
                <td class="px-4 py-2 text-center">{{ $p->jenis_kelamin }}</td>
                <td class="px-4 py-2">{{ $p->kelas ?? '-' }}</td>
                <td class="px-4 py-2">{{ $p->kontak ?? '-' }}</td>
                <td class="px-4 py-2 text-center">
                    @if($p->status === 'aktif')
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs">Aktif</span>
                    @else
                        <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs">Tidak</span>
                    @endif
                </td>
                <td class="px-4 py-2 text-center">
                    <div class="inline-flex gap-1">
                        <button type="button"
                            onclick='openEditModal(@json($p))'
                            class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded text-xs">
                            Edit
                        </button>
                        <form action="{{ route('peserta.destroy', $p->id) }}"
                              method="POST"
                              onsubmit="return confirmDelete(event)">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="bg-red-100 text-red-700 px-3 py-1 rounded text-xs">
                                Hapus
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                    Belum ada peserta
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

{{-- MODAL TAMBAH & EDIT PESERTA --}}
<div id="pesertaModal"
     class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center transition-opacity duration-300 opacity-0">

    <div id="modalBox"
     class="bg-white w-full max-w-lg rounded-lg shadow-lg
            transform transition-all duration-300
            scale-95 opacity-0">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h2 id="modalTitle" class="text-lg font-semibold">
                Tambah Peserta
            </h2>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                ✕
            </button>
        </div>

        <form id="pesertaForm" method="POST" class="px-6 py-4 space-y-4">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <input type="hidden" name="sekolah_id" value="{{ $sekolah->id }}">

            <div>
                <label class="block text-sm font-medium">Nama</label>
                <input type="text" name="nama" id="nama"
                       class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium">Jenis Kelamin</label>
                <select name="jenis_kelamin" id="jenis_kelamin"
                        class="w-full border rounded px-3 py-2" required>
                    <option value="">-- Pilih --</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium">Kelas</label>
                <input type="text" name="kelas" id="kelas"
                       class="w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium">Kontak</label>
                <input type="text" name="kontak" id="kontak"
                       class="w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium">Status</label>
                <select name="status" id="status"
                        class="w-full border rounded px-3 py-2">
                    <option value="aktif">Aktif</option>
                    <option value="tidak">Tidak</option>
                </select>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t">
                <button type="button"
                        onclick="closeModal()"
                        class="px-4 py-2 rounded bg-gray-200">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 rounded bg-blue-600 text-white">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openCreateModal() {
    document.getElementById('modalTitle').innerText = 'Tambah Peserta';
    document.getElementById('pesertaForm').action =
        "{{ route('sekolah.peserta.store', $sekolah->id) }}";

    document.getElementById('formMethod').value = 'POST';
    document.getElementById('pesertaForm').reset();

    showModal();
}

function openEditModal(peserta) {
    document.getElementById('modalTitle').innerText = 'Edit Peserta';
    document.getElementById('pesertaForm').action =
        "{{ route('peserta.update', ':id') }}".replace(':id', peserta.id);

    document.getElementById('formMethod').value = 'PATCH';

    document.getElementById('nama').value = peserta.nama;
    document.getElementById('jenis_kelamin').value = peserta.jenis_kelamin;
    document.getElementById('kelas').value = peserta.kelas ?? '';
    document.getElementById('kontak').value = peserta.kontak ?? '';
    document.getElementById('status').value = peserta.status;

    showModal();
}

function showModal() {
    const modal = document.getElementById('pesertaModal');
    const box   = document.getElementById('modalBox');

    modal.classList.remove('hidden');

    // trigger animation
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        box.classList.remove('scale-95', 'opacity-0');
        box.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function closeModal() {
    const modal = document.getElementById('pesertaModal');
    const box   = document.getElementById('modalBox');

    // reverse animation
    modal.classList.add('opacity-0');
    box.classList.remove('scale-100', 'opacity-100');
    box.classList.add('scale-95', 'opacity-0');

    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}
</script>




@endsection
