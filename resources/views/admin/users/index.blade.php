@extends('layouts.app')

@section('header')
Manajemen User
@endsection

@section('content')

<div class="w-full flex justify-end mb-4">
    <button onclick="openCreateModal()"
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
        + Tambah User
    </button>
</div>

@if(session('success'))
<div class="mb-4 p-3 rounded bg-green-100 text-green-700">
    {{ session('success') }}
</div>
@endif

<div class="bg-white rounded shadow overflow-x-auto">
<table class="min-w-full text-sm">
<thead class="bg-gray-100">
<tr>
    <th class="px-4 py-2">Nama</th>
    <th class="px-4 py-2">Username</th>
    <th class="px-4 py-2">Email</th>
    <th class="px-4 py-2">Role</th>
    <th class="px-4 py-2 w-32">Aksi</th>
</tr>
</thead>
<tbody>
@foreach($users as $u)
<tr class="border-t">
    <td class="px-4 py-2">{{ $u->name }}</td>
    <td class="px-4 py-2">{{ $u->username }}</td>
    <td class="px-4 py-2">{{ $u->email }}</td>
    <td class="px-4 py-2 text-center">
        <span class="px-2 py-1 rounded text-xs bg-gray-100">
            {{ ucfirst(str_replace('_',' ',$u->role)) }}
        </span>
    </td>
    <td class="px-4 py-2 text-center space-x-1">
        <button onclick='openEditModal(@json($u))'
            class="text-xs bg-yellow-100 px-2 py-1 rounded">
            Edit
        </button>

        <form action="{{ route('users.destroy',$u->id) }}"
              method="POST" class="inline"
              onsubmit="return confirmDelete(event)">
            @csrf @method('DELETE')
            <button type="submit" class="text-xs bg-red-100 px-2 py-1 rounded">
                Hapus
            </button>
        </form>
    </td>
</tr>
@endforeach
</tbody>
</table>
</div>

<script>
function openCreateModal() {
    Swal.fire({
        title: 'Tambah User',
        html: userForm(),
        showConfirmButton: false,
        width: 600,
        footer: actionButtons("{{ route('users.store') }}", 'POST')
    });
}

function openEditModal(data) {
    Swal.fire({
        title: 'Edit User',
        html: userForm(data),
        showConfirmButton: false,
        width: 600,
        footer: actionButtons(`/users/${data.id}`, 'PUT')
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

function userForm(data = {}) {
    return `
    <input type="hidden" id="csrf" value="{{ csrf_token() }}">

    <div class="space-y-4 text-left text-sm">

        <div>
            <label class="block mb-1 font-medium">Nama</label>
            <input id="name" class="w-full px-3 py-2 border rounded"
                value="${data.name ?? ''}">
        </div>

        <div>
            <label class="block mb-1 font-medium">Username</label>
            <input id="username"
                class="w-full px-3 py-2 border rounded"
                value="${data.username ?? ''}">
        </div>

        <div>
            <label class="block mb-1 font-medium">Email</label>
            <input id="email" type="email"
                class="w-full px-3 py-2 border rounded"
                value="${data.email ?? ''}">
        </div>

        <div>
            <label class="block mb-1 font-medium">Role</label>
            <select id="role"
                onchange="toggleSekolah()"
                class="w-full px-3 py-2 border rounded">
                <option value="">-- Pilih Role --</option>
                <option value="admin" ${data.role === 'admin' ? 'selected' : ''}>Admin</option>
                <option value="instruktur" ${data.role === 'instruktur' ? 'selected' : ''}>Instruktur</option>
                <option value="admin_sekolah" ${data.role === 'admin_sekolah' ? 'selected' : ''}>Admin Sekolah</option>
            </select>
        </div>

        <div id="sekolah-wrapper"
             class="${data.role === 'admin_sekolah' ? '' : 'hidden'}">
            <label class="block mb-1 font-medium">Sekolah</label>
            <select id="sekolah_id"
                class="w-full px-3 py-2 border rounded">
                <option value="">-- Pilih Sekolah --</option>
                @foreach($sekolahs as $s)
                    <option value="{{ $s->id }}"
                        ${data.sekolah_id == {{ $s->id }} ? 'selected' : ''}>
                        {{ $s->nama_sekolah }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block mb-1 font-medium">
                Password <span class="text-xs text-gray-500">(kosongkan jika tidak diubah)</span>
            </label>
            <input id="password" type="password"
                class="w-full px-3 py-2 border rounded">
        </div>

    </div>
    `;
}

function toggleSekolah() {
    const role = document.getElementById('role').value;
    const wrapper = document.getElementById('sekolah-wrapper');

    if (role === 'admin_sekolah') {
        wrapper.classList.remove('hidden');
    } else {
        wrapper.classList.add('hidden');
        const sekolah = document.getElementById('sekolah_id');
        if (sekolah) sekolah.value = '';
    }
}

function handleSubmit(action, method) {
    const name = document.getElementById('name').value;
    const username = document.getElementById('username').value;
    const email = document.getElementById('email').value;
    const role = document.getElementById('role').value;

    if (!name || !username || !email || !role) {
        Swal.showValidationMessage('Nama, username, email, dan role wajib diisi');
        return;
    }

    submitForm(action, method);
}


function submitForm(action, method) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = action;

    const sekolahEl = document.getElementById('sekolah_id');

    form.innerHTML = `
        <input type="hidden" name="_token" value="${document.getElementById('csrf').value}">
        ${method !== 'POST'
            ? `<input type="hidden" name="_method" value="${method}">`
            : ''
        }

        <input type="hidden" name="name" value="${document.getElementById('name').value}">
        <input type="hidden" name="username" value="${document.getElementById('username').value}">
        <input type="hidden" name="email" value="${document.getElementById('email').value}">
        <input type="hidden" name="role" value="${document.getElementById('role').value}">
        <input type="hidden" name="password" value="${document.getElementById('password').value}">
        <input type="hidden" name="sekolah_id"
               value="${sekolahEl ? sekolahEl.value : ''}">
    `;

    document.body.appendChild(form);
    form.submit();
}


function confirmDelete(e) {
    e.preventDefault();
    Swal.fire({
        title: 'Hapus user?',
        text: 'User akan dihapus permanen',
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
