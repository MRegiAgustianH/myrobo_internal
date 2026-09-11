@extends('layouts.app')

@section('header')
Manajemen Cabang
@endsection

@section('content')

<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-5">
    <h2 class="text-lg font-semibold text-gray-700">Daftar Cabang</h2>
    <button onclick="openCreateModal()"
        class="inline-flex items-center gap-2 bg-[#8FBFC2] hover:bg-[#6FA9AD] text-white px-4 py-2 rounded-lg text-sm transition">
        <i data-feather="plus" class="w-4 h-4"></i>
        Tambah Cabang
    </button>
</div>

@if(session('success'))
<div class="mb-4 p-3 rounded bg-green-100 text-green-700 text-sm">{{ session('success') }}</div>
@endif

{{-- MOBILE --}}
<div class="grid grid-cols-1 gap-4 md:hidden">
@foreach($cabangs as $c)
<div class="bg-white rounded-xl shadow p-4 space-y-2">
    <div class="flex items-center gap-3">
        @if($c->logo)
        <img src="{{ asset('storage/' . $c->logo) }}" class="w-12 h-12 rounded-lg object-contain">
        @else
        <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400">
            <i data-feather="image" class="w-5 h-5"></i>
        </div>
        @endif
        <div>
            <p class="font-semibold text-gray-800">{{ $c->nama_cabang }}</p>
            <p class="text-xs text-gray-500">Kode: {{ $c->kode_cabang }}</p>
        </div>
    </div>
    <div class="text-sm text-gray-600">{{ $c->alamat ?? '-' }}</div>
    <div class="flex items-center gap-2">
        @if($c->is_aktif)
        <span class="px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-700">Aktif</span>
        @else
        <span class="px-2 py-0.5 rounded-full text-xs bg-red-100 text-red-700">Nonaktif</span>
        @endif
    </div>
    <div class="flex gap-2 pt-2">
        <button onclick='openEditModal(@json($c))' class="flex-1 bg-yellow-100 text-yellow-700 text-xs py-2 rounded">Edit</button>
        <form method="POST" action="{{ route('cabang.destroy', $c->id) }}" onsubmit="return confirmDelete(event)">
            @csrf @method('DELETE')
            <button class="bg-red-100 text-red-700 text-xs px-3 py-2 rounded">Hapus</button>
        </form>
    </div>
</div>
@endforeach
</div>

{{-- DESKTOP --}}
<div class="hidden md:block bg-white rounded-xl shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr class="text-gray-600 uppercase text-xs tracking-wider">
                <th class="px-4 py-3 text-left">Logo</th>
                <th class="px-4 py-3 text-left">Cabang</th>
                <th class="px-4 py-3 text-center">Kode</th>
                <th class="px-4 py-3 text-left">Alamat</th>
                <th class="px-4 py-3 text-center">Status</th>
                <th class="px-4 py-3 text-center w-32">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
        @foreach($cabangs as $c)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">
                    @if($c->logo)
                    <img src="{{ asset('storage/' . $c->logo) }}" class="w-10 h-10 rounded-lg object-contain">
                    @else
                    <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400">
                        <i data-feather="image" class="w-4 h-4"></i>
                    </div>
                    @endif
                </td>
                <td class="px-4 py-3 font-medium">{{ $c->nama_cabang }}</td>
                <td class="px-4 py-3 text-center font-mono">{{ $c->kode_cabang }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $c->alamat ?? '-' }}</td>
                <td class="px-4 py-3 text-center">
                    @if($c->is_aktif)
                    <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">Aktif</span>
                    @else
                    <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700">Nonaktif</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="inline-flex gap-1">
                        <button onclick='openEditModal(@json($c))' class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded text-xs">Edit</button>
                        <form method="POST" action="{{ route('cabang.destroy', $c->id) }}" onsubmit="return confirmDelete(event)">
                            @csrf @method('DELETE')
                            <button class="bg-red-100 text-red-700 px-3 py-1 rounded text-xs">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $cabangs->links() }}</div>

<script>
function openCreateModal() {
    Swal.fire({
        title: 'Tambah Cabang',
        html: cabangForm(),
        showConfirmButton: false,
        width: 600,
        footer: actionButtons("{{ route('cabang.store') }}", 'POST')
    });
}

function openEditModal(data) {
    Swal.fire({
        title: 'Edit Cabang',
        html: cabangForm(data),
        showConfirmButton: false,
        width: 600,
        footer: actionButtons(`/cabang/${data.id}`, 'POST')
    });
}

function actionButtons(action, method) {
    return `
    <div class="flex gap-2">
        <button onclick="submitCabangForm('${action}')" class="bg-[#8FBFC2] text-white px-4 py-2 rounded-lg text-sm">Simpan</button>
        <button onclick="Swal.close()" class="border px-4 py-2 rounded-lg text-sm">Batal</button>
    </div>`;
}

function cabangForm(data = {}) {
    const checked = data.is_aktif !== false ? 'checked' : '';
    return `
    <form id="cabangForm" enctype="multipart/form-data" class="space-y-4 text-left text-sm">
        <div>
            <label class="font-medium">Nama Cabang</label>
            <input name="nama_cabang" class="w-full px-3 py-2 border rounded" value="${data.nama_cabang ?? ''}">
        </div>
        <div>
            <label class="font-medium">Kode Cabang</label>
            <input name="kode_cabang" class="w-full px-3 py-2 border rounded" value="${data.kode_cabang ?? ''}">
        </div>
        <div>
            <label class="font-medium">Alamat</label>
            <textarea name="alamat" class="w-full px-3 py-2 border rounded" rows="2">${data.alamat ?? ''}</textarea>
        </div>
        <div>
            <label class="font-medium">Kontak</label>
            <input name="kontak" class="w-full px-3 py-2 border rounded" value="${data.kontak ?? ''}">
        </div>
        <div>
            <label class="font-medium">Logo</label>
            <input type="file" name="logo" accept="image/*" class="w-full px-3 py-2 border rounded">
            ${data.logo ? `<img src="/storage/${data.logo}" class="w-16 h-16 mt-2 rounded object-contain">` : ''}
        </div>
        <label class="flex items-center gap-2">
            <input type="checkbox" name="is_aktif" ${checked} class="rounded">
            <span>Aktif</span>
        </label>
    </form>`;
}

function submitCabangForm(action) {
    const form = document.getElementById('cabangForm');
    const fd = new FormData(form);
    fd.append('_token', '{{ csrf_token() }}');
    if (action.includes('/cabang/')) {
        fd.append('_method', 'PUT');
    }
    fetch(action, { method: 'POST', body: fd, headers: {'Accept':'application/json'} })
    .then(r => r.json().then(d => ({ok:r.ok, data:d})))
    .then(({ok, data}) => {
        if (ok) { window.location.reload(); }
        else { Swal.showValidationMessage(Object.values(data.errors || {}).flat().join('\n')); }
    });
}

function confirmDelete(e) {
    e.preventDefault();
    Swal.fire({
        title: 'Hapus Cabang?',
        text: 'Data cabang akan dihapus permanen',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus'
    }).then(res => { if (res.isConfirmed) e.target.submit(); });
}
</script>

@endsection