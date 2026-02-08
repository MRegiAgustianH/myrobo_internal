@extends('layouts.app')

@section('header')
Manajemen Materi
@endsection

@section('content')

{{-- ACTION BAR --}}
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-5">
    <h2 class="text-lg font-semibold text-gray-700">
        Daftar Materi
    </h2>

    @if(! $readonly)
    <button onclick="openCreateModal()"
        class="inline-flex items-center gap-2 bg-[#8FBFC2] hover:bg-[#6FA9AD] text-white px-4 py-2 rounded-lg text-sm transition">
        <i data-feather="plus" class="w-4 h-4"></i>
        Tambah Materi
    </button>
    @endif

</div>

@if(session('success'))
<div class="mb-4 p-3 rounded bg-green-100 text-green-700">
    {{ session('success') }}
</div>
@endif

{{-- ================= MOBILE CARD VIEW ================= --}}
<div class="grid grid-cols-1 gap-4 md:hidden">

@foreach($materis as $m)
<div class="bg-white rounded-xl shadow p-4 space-y-3">

    <div>
        <p class="font-semibold text-gray-800">
            {{ $m->nama_materi }}
        </p>
        <p class="text-xs text-gray-500 line-clamp-2">
            {{ $m->deskripsi ?? '—' }}
        </p>
    </div>

    <div class="flex justify-between items-center text-sm">
        <span class="text-gray-500">Status</span>
        <span class="px-3 py-1 text-xs rounded-full
            {{ $m->status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
            {{ ucfirst($m->status) }}
        </span>
    </div>

    <div class="flex flex-wrap gap-2 pt-3">

        {{-- MODUL --}}
        <a href="{{ route('materi.modul.index', $m->id) }}"
        class="flex-1 bg-indigo-100 text-indigo-700 text-xs py-2 rounded text-center">
            Modul
        </a>

        {{-- KOMPETENSI --}}
        <a href="{{ route('materi.kompetensi.index', $m->id) }}"
        class="flex-1 bg-green-100 text-green-700 text-xs py-2 rounded text-center">
            Kompetensi
        </a>

        {{-- ADMIN ONLY --}}
        @if(! $readonly)
            <button onclick='openEditModal(@json($m))'
                class="flex-1 bg-yellow-100 text-yellow-700 text-xs py-2 rounded">
                Edit
            </button>

            <form action="{{ route('admin.materi.destroy',$m->id) }}"
                method="POST"
                onsubmit="return confirmDelete(event)">
                @csrf
                @method('DELETE')
                <button
                    class="bg-red-100 text-red-700 text-xs px-3 py-2 rounded">
                    Hapus
                </button>
            </form>
        @endif
    </div>



</div>
@endforeach

</div>

{{-- ================= DESKTOP TABLE VIEW ================= --}}
<div class="hidden md:block bg-white rounded-lg shadow overflow-x-auto">

<table class="min-w-full text-sm">
<thead class="bg-gray-100">
<tr>
    <th class="px-4 py-3 text-left">Nama Materi</th>
    <th class="px-4 py-3 text-left">Deskripsi</th>
    <th class="px-4 py-3 text-center">Status</th>
    <th class="px-4 py-3 w-48 text-center">Aksi</th>
</tr>
</thead>

<tbody class="divide-y">
@foreach($materis as $m)
<tr class="hover:bg-gray-50 transition">
    <td class="px-4 py-2 font-medium">
        {{ $m->nama_materi }}
    </td>

    <td class="px-4 py-2 text-gray-600 max-w-md truncate">
        {{ $m->deskripsi ?? '—' }}
    </td>

    <td class="px-4 py-2 text-center">
        <span class="px-3 py-1 text-xs rounded-full
            {{ $m->status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
            {{ ucfirst($m->status) }}
        </span>
    </td>

    <td class="px-4 py-2 text-center">
        <div class="inline-flex gap-2 justify-center flex-wrap">

            {{-- MODUL --}}
            <a href="{{ route('materi.modul.index', $m->id) }}"
            class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded text-xs">
                Modul
            </a>

            {{-- KOMPETENSI --}}
            <a href="{{ route('materi.kompetensi.index', $m->id) }}"
            class="bg-green-100 text-green-700 px-3 py-1 rounded text-xs">
                Kompetensi
            </a>

            {{-- ADMIN ONLY --}}
            @if(! $readonly)
                <button onclick='openEditModal(@json($m))'
                    class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded text-xs">
                    Edit
                </button>

                <form action="{{ route('admin.materi.destroy',$m->id) }}"
                    method="POST"
                    onsubmit="return confirmDelete(event)">
                    @csrf
                    @method('DELETE')
                    <button
                        class="bg-red-100 text-red-700 px-3 py-1 rounded text-xs">
                        Hapus
                    </button>
                </form>
            @endif

        </div>
    </td>


</tr>
@endforeach
</tbody>
</table>

</div>

@if(! $readonly)
{{-- ================= SCRIPT ================= --}}
<script>
function openCreateModal() {
    Swal.fire({
        title: 'Tambah Materi',
        html: materiForm(),
        showConfirmButton: false,
        width: 600,
        footer: actionButtons("{{ route('admin.materi.store') }}", 'POST')
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
    </div>`;
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
    </div>`;
}

function handleSubmit(action, method) {
    if (!document.getElementById('nama_materi').value) {
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
        if (res.isConfirmed) e.target.submit();
    });
}
</script>
@endif

@endsection
