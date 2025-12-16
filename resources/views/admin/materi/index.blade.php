@extends('layouts.app')

@section('header')
Manajemen Materi
@endsection

@section('content')

{{-- ACTION BAR --}}
<div class="flex justify-end mb-4">
    <button onclick="openCreateModal()"
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
        + Tambah Materi
    </button>
</div>

@if(session('success'))
<div class="mb-4 p-3 rounded bg-green-100 text-green-700">
    {{ session('success') }}
</div>
@endif

<div class="bg-white rounded-lg shadow overflow-x-auto">
<table class="min-w-full text-sm">
<thead class="bg-gray-100">
<tr>
    <th class="px-4 py-2 text-left">Nama Materi</th>
    <th class="px-4 py-2">Status</th>
    <th class="px-4 py-2 w-32 text-center">Aksi</th>
</tr>
</thead>
<tbody>
@foreach($materis as $m)
<tr class="border-t hover:bg-gray-50">
    <td class="px-4 py-2 font-medium">{{ $m->nama_materi }}</td>
    <td class="px-4 py-2 text-center">
        @if($m->status === 'aktif')
            <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">Aktif</span>
        @else
            <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">Nonaktif</span>
        @endif
    </td>
    <td class="px-4 py-2 text-center">
    <div class="inline-flex gap-2 flex-nowrap justify-center">

        <button onclick='openEditModal(@json($m))'
            class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded text-xs">
            Edit
        </button>

        <form action="{{ route('materi.destroy',$m->id) }}"
              method="POST" class="inline"
              onsubmit="return confirmDelete(event)">
            @csrf @method('DELETE')
            <button
                class="bg-red-100 text-red-700 px-3 py-1 rounded text-xs">
                Hapus
            </button>
        </form>
    </div>
    </td>
</tr>
@endforeach
</tbody>
</table>
</div>

{{-- ================= MODAL SCRIPT ================= --}}
<script>
function openCreateModal() {
    Swal.fire({
        title: 'Tambah Materi',
        html: materiForm(),
        showConfirmButton: false,
        width: 600,
        footer: actionButtons("{{ route('materi.store') }}", 'POST')
    });
}

function openEditModal(data) {
    Swal.fire({
        title: 'Edit Materi',
        html: materiForm(data),
        showConfirmButton: false,
        width: 600,
        footer: actionButtons(`/materi/${data.id}`, 'PUT')
    });
}

function actionButtons(action, method) {
    return `
    <div class="w-full flex justify-start gap-2">
        <button onclick="handleSubmit('${action}','${method}')"
            class="btn-primary">
            ${method === 'POST' ? 'Simpan' : 'Update'}
        </button>
        <button onclick="Swal.close()" class="btn-secondary">
            Batal
        </button>
    </div>
    `;
}

function materiForm(data = {}) {
    return `
    <input type="hidden" id="csrf" value="{{ csrf_token() }}">

    <div class="space-y-4 text-left text-sm">

        <div>
            <label class="block mb-1 font-medium">Nama Materi</label>
            <input id="nama_materi"
                class="w-full px-3 py-2 border rounded"
                value="${data.nama_materi ?? ''}">
        </div>

        <div>
            <label class="block mb-1 font-medium">Deskripsi</label>
            <textarea id="deskripsi"
                class="w-full px-3 py-2 border rounded"
                rows="3">${data.deskripsi ?? ''}</textarea>
        </div>

        <div>
            <label class="block mb-1 font-medium">Status</label>
            <select id="status" class="w-full px-3 py-2 border rounded">
                <option value="aktif" ${data.status !== 'nonaktif' ? 'selected' : ''}>Aktif</option>
                <option value="nonaktif" ${data.status === 'nonaktif' ? 'selected' : ''}>Nonaktif</option>
            </select>
        </div>

    </div>
    `;
}

function handleSubmit(action, method) {
    const nama = document.getElementById('nama_materi').value;
    if (!nama) {
        Swal.showValidationMessage('Nama materi wajib diisi');
        return;
    }
    submitForm(action, method);
}

function submitForm(action, method) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = action;

    form.innerHTML = `
        <input type="hidden" name="_token" value="${document.getElementById('csrf').value}">
        ${method !== 'POST' ? `<input type="hidden" name="_method" value="${method}">` : ''}
        <input type="hidden" name="nama_materi" value="${document.getElementById('nama_materi').value}">
        <input type="hidden" name="deskripsi" value="${document.getElementById('deskripsi').value}">
        <input type="hidden" name="status" value="${document.getElementById('status').value}">
    `;
    document.body.appendChild(form);
    form.submit();
}

function confirmDelete(e) {
    e.preventDefault();
    Swal.fire({
        title: 'Hapus materi?',
        text: 'Materi akan dihapus permanen',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus'
    }).then(res => {
        if (res.isConfirmed) {
            e.target.submit();
        }
    });
}
</script>

@endsection
