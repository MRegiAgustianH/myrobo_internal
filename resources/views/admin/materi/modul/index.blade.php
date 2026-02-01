@extends('layouts.app')

@section('header')
Modul Materi – {{ $materi->nama_materi }}
@endsection

@section('content')

{{-- ACTION BAR --}}
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-5">
    <div>
        <a href="{{ route('materi.index') }}"
           class="text-sm text-gray-600 hover:underline">
            ← Kembali ke Materi
        </a>
        <h2 class="text-lg font-semibold text-gray-700 mt-1">
            Modul: {{ $materi->nama_materi }}
        </h2>
    </div>

    @if(in_array(auth()->user()->role, ['admin','instruktur']))
    <button onclick="openCreateModal()"
        class="inline-flex items-center gap-2 bg-[#8FBFC2] hover:bg-[#6FA9AD] text-white px-4 py-2 rounded-lg text-sm transition">
        <i data-feather="plus" class="w-4 h-4"></i>
        Tambah Modul
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

@forelse($materi->moduls as $modul)
<div class="bg-white rounded-xl shadow p-4 space-y-3">

    <div>
        <p class="font-semibold text-gray-800">
            {{ $modul->judul_modul }}
        </p>
        <p class="text-xs text-gray-500">
            Urutan: {{ $modul->urutan }}
        </p>
    </div>

    <div class="flex justify-between items-center text-sm">
        <span class="text-gray-500">Status</span>
        <span class="px-3 py-1 text-xs rounded-full
            {{ $modul->status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
            {{ ucfirst($modul->status) }}
        </span>
    </div>

    <div class="flex flex-wrap gap-2 pt-3">

        {{-- DOWNLOAD --}}
        @if(in_array(auth()->user()->role, ['admin','instruktur']))
        <a href="{{ route('materi.modul.download', $modul->id) }}"
           class="flex-1 bg-emerald-100 text-emerald-700 text-xs py-2 rounded text-center">
            Download PDF
        </a>
        @endif

        {{-- EDIT --}}
        <button onclick='openEditModal(@json($modul))'
            class="flex-1 bg-yellow-100 text-yellow-700 text-xs py-2 rounded">
            Edit
        </button>

        {{-- HAPUS --}}
        <form action="{{ route('materi.modul.destroy',$modul->id) }}"
              method="POST"
              onsubmit="return confirmDelete(event)">
            @csrf
            @method('DELETE')
            <button
                class="bg-red-100 text-red-700 text-xs px-3 py-2 rounded">
                Hapus
            </button>
        </form>
    </div>

</div>
@empty
<div class="text-center text-gray-500 py-10">
    Belum ada modul untuk materi ini.
</div>
@endforelse

</div>

{{-- ================= DESKTOP TABLE VIEW ================= --}}
<div class="hidden md:block bg-white rounded-lg shadow overflow-x-auto">

<table class="min-w-full text-sm">
<thead class="bg-gray-100">
<tr>
    <th class="px-4 py-3 text-left">Judul Modul</th>
    <th class="px-4 py-3 text-center">Urutan</th>
    <th class="px-4 py-3 text-center">Status</th>
    <th class="px-4 py-3 w-48 text-center">Aksi</th>
</tr>
</thead>

<tbody class="divide-y">
@foreach($materi->moduls as $modul)
<tr class="hover:bg-gray-50 transition">
    <td class="px-4 py-2 font-medium">
        {{ $modul->judul_modul }}
    </td>

    <td class="px-4 py-2 text-center">
        {{ $modul->urutan }}
    </td>

    <td class="px-4 py-2 text-center">
        <span class="px-3 py-1 text-xs rounded-full
            {{ $modul->status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
            {{ ucfirst($modul->status) }}
        </span>
    </td>

        <td class="px-4 py-2 text-center">
        <div class="flex justify-center gap-2 flex-wrap">

            {{-- PREVIEW --}}
            @if(in_array(auth()->user()->role, ['admin','instruktur']))
            <a href="{{ route('materi.modul.preview', $modul->id) }}"
            target="_blank"
            class="px-3 py-1 text-xs rounded
                    bg-sky-100 text-sky-700
                    hover:bg-sky-200 transition">
                Preview
            </a>
            @endif

            {{-- DOWNLOAD --}}
            @if(in_array(auth()->user()->role, ['admin','instruktur']))
            <a href="{{ route('materi.modul.download', $modul->id) }}"
            class="px-3 py-1 text-xs rounded
                    bg-emerald-100 text-emerald-700
                    hover:bg-emerald-200 transition">
                Download
            </a>
            @endif

            {{-- EDIT --}}
            <button onclick='openEditModal(@json($modul))'
                class="px-3 py-1 text-xs rounded
                    bg-yellow-100 text-yellow-700
                    hover:bg-yellow-200 transition">
                Edit
            </button>

            {{-- HAPUS --}}
            <form action="{{ route('materi.modul.destroy',$modul->id) }}"
                method="POST"
                onsubmit="return confirmDelete(event)">
                @csrf
                @method('DELETE')
                <button
                    class="px-3 py-1 text-xs rounded
                        bg-red-100 text-red-700
                        hover:bg-red-200 transition">
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

{{-- ================= SCRIPT ================= --}}
<script>
function openCreateModal() {
    Swal.fire({
        title: 'Tambah Modul',
        html: modulForm(),
        showConfirmButton: false,
        width: 600,
        footer: actionButtons("{{ route('materi.modul.store', $materi->id) }}", 'POST')
    });
}

function openEditModal(data) {
    Swal.fire({
        title: 'Edit Modul',
        html: modulForm(data),
        showConfirmButton: false,
        width: 600,
        footer: actionButtons(`/materi/modul/${data.id}`, 'PUT')
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

function modulForm(data = {}) {
    return `
    <input type="hidden" id="csrf" value="{{ csrf_token() }}">
    <div class="space-y-4 text-left text-sm">

        <div>
            <label class="block mb-1 font-medium">Judul Modul</label>
            <input id="judul_modul"
                class="w-full px-3 py-2 border rounded"
                value="${data.judul_modul ?? ''}">
        </div>

        <div>
            <label class="block mb-1 font-medium">Urutan</label>
            <input id="urutan" type="number"
                class="w-full px-3 py-2 border rounded"
                value="${data.urutan ?? 1}">
        </div>

        <div>
            <label class="block mb-1 font-medium">File PDF</label>
            <input id="file_pdf" type="file" accept="application/pdf"
                class="w-full px-3 py-2 border rounded">
            ${data.file_pdf ? '<p class="text-xs text-gray-500 mt-1">PDF sudah ada</p>' : ''}
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
    if (!document.getElementById('judul_modul').value) {
        Swal.showValidationMessage('Judul modul wajib diisi');
        return;
    }
    submitForm(action, method);
}

function submitForm(action, method) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = action;
    form.enctype = 'multipart/form-data';

    form.innerHTML = `
        <input type="hidden" name="_token" value="${document.getElementById('csrf').value}">
        ${method !== 'POST' ? `<input type="hidden" name="_method" value="${method}">` : ''}
        <input type="hidden" name="judul_modul" value="${document.getElementById('judul_modul').value}">
        <input type="hidden" name="urutan" value="${document.getElementById('urutan').value}">
        <input type="hidden" name="status" value="${document.getElementById('status').value}">
    `;

    const fileInput = document.getElementById('file_pdf');
    if (fileInput && fileInput.files.length > 0) {
        const fileField = document.createElement('input');
        fileField.type = 'file';
        fileField.name = 'file_pdf';
        fileField.files = fileInput.files;
        form.appendChild(fileField);
    }

    document.body.appendChild(form);
    form.submit();
}

function confirmDelete(e) {
    e.preventDefault();
    Swal.fire({
        title: 'Hapus modul?',
        text: 'Modul akan dihapus permanen',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus'
    }).then(res => {
        if (res.isConfirmed) e.target.submit();
    });
}
</script>

@endsection
