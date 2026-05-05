@extends('layouts.app')

@section('header')
Kelola Home Private
@endsection

@section('content')

@if(session('success'))
<div class="mb-4 p-3 bg-green-100 text-green-700 rounded text-sm">
    {{ session('success') }}
</div>
@endif

{{-- ACTION BAR --}}
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
    <h2 class="text-lg font-semibold text-gray-700">
        Daftar Home Private
    </h2>

    <button onclick="openCreateModal()"
        class="w-full sm:w-auto inline-flex justify-center items-center gap-2
               bg-[#8FBFC2] hover:bg-[#6FA9AD]
               text-white px-4 py-2 rounded-lg text-sm transition">
        <i data-feather="plus" class="w-4 h-4"></i>
        Tambah Home Private
    </button>
</div>


{{-- TABLE DESKTOP --}}
<div class="hidden md:block bg-white rounded-xl shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-100 text-gray-600">
            <tr>
                <th class="p-3 text-left">Kegiatan</th>
                <th class="p-3 text-left">Peserta</th>
                <th class="p-3 text-left">Wali</th>
                <th class="p-3 text-left">No HP</th>
                <th class="p-3 text-left">Status</th>
                <th class="p-3 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
        @foreach($homePrivates as $hp)
            <tr class="border-t hover:bg-gray-50 transition">
                <td class="p-3">{{ $hp->nama_kegiatan }}</td>
                <td class="p-3">{{ $hp->nama_peserta }}</td>
                <td class="p-3">{{ $hp->nama_wali ?? '-' }}</td>
                <td class="p-3">{{ $hp->no_hp ?? '-' }}</td>
                <td class="p-3">
                    <span class="px-2 py-1 rounded-full text-xs font-medium
                        {{ $hp->status === 'aktif'
                            ? 'bg-green-100 text-green-700'
                            : 'bg-gray-200 text-gray-700' }}">
                        {{ ucfirst($hp->status) }}
                    </span>
                </td>
                <td class="p-3 text-center space-x-3">
                    <button onclick='openEditModal(@json($hp))'
                        class="text-yellow-600 hover:underline text-sm">
                        Edit
                    </button>
                    <button onclick="confirmDelete({{ $hp->id }})"
                        class="text-red-600 hover:underline text-sm">
                        Hapus
                    </button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

{{-- CARD MOBILE --}}
<div class="md:hidden space-y-4">
@foreach($homePrivates as $hp)
    <div class="bg-white rounded-xl shadow p-4 space-y-3">
        <div class="flex justify-between items-start">
            <h3 class="font-semibold text-gray-800">
                {{ $hp->nama_kegiatan }}
            </h3>
            <span class="px-2 py-1 rounded-full text-xs
                {{ $hp->status === 'aktif'
                    ? 'bg-green-100 text-green-700'
                    : 'bg-gray-200 text-gray-700' }}">
                {{ ucfirst($hp->status) }}
            </span>
        </div>

        <div class="text-sm text-gray-600 space-y-1">
            <p><strong>Peserta:</strong> {{ $hp->nama_peserta }}</p>
            <p><strong>Wali:</strong> {{ $hp->nama_wali ?? '-' }}</p>
            <p><strong>No HP:</strong> {{ $hp->no_hp ?? '-' }}</p>
        </div>

        <div class="flex justify-end gap-3 pt-2 border-t">
            <button onclick='openEditModal(@json($hp))'
                class="text-yellow-600 text-sm">
                Edit
            </button>
            <button onclick="confirmDelete({{ $hp->id }})"
                class="text-red-600 text-sm">
                Hapus
            </button>
        </div>
    </div>
@endforeach
</div>


@endsection


{{-- ================= SCRIPT ================= --}}
@push('scripts')
<script>
/* ================= FORM TEMPLATE ================= */
function homePrivateForm(data = {}) {
    return `
    <input type="hidden" id="csrf" value="{{ csrf_token() }}">

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-left">

        <div class="sm:col-span-2">
            <label class="font-medium">Nama Kegiatan</label>
            <input id="nama_kegiatan"
                class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-[#8FBFC2]"
                value="${data.nama_kegiatan ?? ''}">
        </div>

        <div>
            <label class="font-medium">Nama Peserta</label>
            <input id="nama_peserta"
                class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-[#8FBFC2]"
                value="${data.nama_peserta ?? ''}">
        </div>

        <div>
            <label class="font-medium">Nama Wali</label>
            <input id="nama_wali"
                class="w-full border rounded-lg px-3 py-2"
                value="${data.nama_wali ?? ''}">
        </div>

        <div>
            <label class="font-medium">No HP</label>
            <input id="no_hp"
                class="w-full border rounded-lg px-3 py-2"
                value="${data.no_hp ?? ''}">
        </div>

        <div>
            <label class="font-medium">Status</label>
            <select id="status"
                class="w-full border rounded-lg px-3 py-2">
                <option value="aktif" ${data.status !== 'nonaktif' ? 'selected' : ''}>Aktif</option>
                <option value="nonaktif" ${data.status === 'nonaktif' ? 'selected' : ''}>Nonaktif</option>
            </select>
        </div>

    </div>
    `;
}


/* ================= CREATE ================= */
function openCreateModal() {
    Swal.fire({
        title: 'Tambah Home Private',
        html: homePrivateForm(),
        showConfirmButton: false,
        width: 600,
        footer: modalActions('{{ route("home-private.store") }}', 'POST')
    });
}

/* ================= EDIT ================= */
function openEditModal(data) {
    Swal.fire({
        title: 'Edit Home Private',
        html: homePrivateForm(data),
        showConfirmButton: false,
        width: 600,
        footer: modalActions(`/home-private/${data.id}`, 'PUT')
    });
}

/* ================= ACTION BUTTON ================= */
function modalActions(action, method) {
    return `
        <div class="flex gap-2">
            <button onclick="submitForm('${action}','${method}')"
                class="bg-[#8FBFC2] text-white px-4 py-2 rounded">
                Simpan
            </button>
            <button onclick="Swal.close()"
                class="border px-4 py-2 rounded">
                Batal
            </button>
        </div>
    `;
}

/* ================= SUBMIT ================= */
function submitForm(action, method) {
    if (!document.getElementById('nama_kegiatan').value ||
        !document.getElementById('nama_peserta').value) {
        Swal.showValidationMessage('Nama kegiatan & peserta wajib diisi');
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = action;

    form.innerHTML = `
        <input type="hidden" name="_token" value="${document.getElementById('csrf').value}">
        ${method !== 'POST' ? `<input type="hidden" name="_method" value="${method}">` : ''}
        <input type="hidden" name="nama_kegiatan" value="${document.getElementById('nama_kegiatan').value}">
        <input type="hidden" name="nama_peserta" value="${document.getElementById('nama_peserta').value}">
        <input type="hidden" name="nama_wali" value="${document.getElementById('nama_wali').value}">
        <input type="hidden" name="no_hp" value="${document.getElementById('no_hp').value}">
        <input type="hidden" name="status" value="${document.getElementById('status').value}">
    `;

    document.body.appendChild(form);
    form.submit();
}

/* ================= DELETE ================= */
function confirmDelete(id) {
    Swal.fire({
        title: 'Hapus Home Private?',
        text: 'Data akan dihapus permanen',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal'
    }).then(res => {
        if (res.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/home-private/${id}`;
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
