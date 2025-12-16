@extends('layouts.app')

@section('header')
Data Sekolah 
@endsection

@section('content')

{{-- ACTION BAR --}}
<div class="w-full flex justify-end mb-4">
    <button
        onclick="openCreateModal()"
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
        + Tambah Sekolah
    </button>
</div>


@if(session('success'))
<div class="mb-4 p-3 rounded bg-green-100 text-green-700">
    {{ session('success') }}
</div>
@endif

<div class="bg-white rounded-lg shadow w-full">

    {{-- SCROLL CONTAINER --}}
    <div
        class="relative w-full overflow-x-auto overscroll-x-contain
               touch-pan-x">

        {{-- HINT MOBILE --}}
        <div class="md:hidden absolute top-2 right-4 text-xs text-gray-400 pointer-events-none">
            ⇆ geser
        </div>

        <table class="min-w-[1100px] w-full text-sm border-collapse">
            <thead class="bg-gray-50 border-b">
                <tr class="text-gray-600 uppercase text-xs tracking-wider">
                    <th class="px-4 py-3 text-left whitespace-nowrap">Sekolah</th>
                    <th class="px-4 py-3 text-center whitespace-nowrap">Kontak</th>
                    <th class="px-4 py-3 text-center whitespace-nowrap">Mulai</th>
                    <th class="px-4 py-3 text-center whitespace-nowrap w-40">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y">
            @foreach($sekolahs as $s)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 font-medium whitespace-nowrap">
                        {{ $s->nama_sekolah }}
                    </td>

                    <td class="px-4 py-3 text-center whitespace-nowrap">
                        {{ $s->kontak }}
                    </td>

                    <td class="px-4 py-3 text-center whitespace-nowrap">
                        {{ $s->tgl_mulai_kerjasama->format('d/m/Y') }}
                    </td>

                    <td class="px-4 py-3 text-center whitespace-nowrap">
                        <div class="inline-flex gap-1">
                            <a href="{{ route('peserta.bySekolah',$s->id) }}"
                               class="bg-green-100 text-green-700 px-3 py-1 rounded text-xs">
                               Peserta
                            </a>

                            <button
                                onclick='openEditModal(@json($s))'
                                class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded text-xs">
                                Edit
                            </button>
                            <form action="{{ route('sekolah.destroy',$s->id) }}"
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
</div>



{{-- SWEETALERT --}}
<script>
function openCreateModal() {
    Swal.fire({
        title: 'Tambah Sekolah',
        html: schoolForm(),
        showConfirmButton: false,
        showCancelButton: false,
        width: 650,
        footer: actionButtons("{{ route('sekolah.store') }}", 'POST')
    });
}

function openEditModal(data) {
    Swal.fire({
        title: 'Edit Sekolah',
        html: schoolForm(data),
        showConfirmButton: false,
        showCancelButton: false,
        width: 650,
        footer: actionButtons(`/sekolah/${data.id}`, 'PUT')
    });
}

function actionButtons(action, method) {
    return `
    <div class="w-full flex justify-start gap-2">
        <button
            onclick="handleSubmit('${action}', '${method}')"
            class="btn-primary">
            ${method === 'POST' ? 'Simpan' : 'Update'}
        </button>

        <button
            onclick="Swal.close()"
            class="btn-secondary">
            Batal
        </button>
    </div>
    `;
}

function schoolForm(data = {}) {
    return `
    <input type="hidden" id="csrf" value="{{ csrf_token() }}">

    <div class="space-y-4 text-left text-sm">
        <div>
            <label class="block mb-1 font-medium">Nama Sekolah</label>
            <input id="nama" class="w-full px-3 py-2 border rounded"
                value="${data.nama_sekolah ?? ''}">
        </div>

        <div>
            <label class="block mb-1 font-medium">Alamat</label>
            <textarea id="alamat" class="w-full px-3 py-2 border rounded"
                rows="2">${data.alamat ?? ''}</textarea>
        </div>

        <div>
            <label class="block mb-1 font-medium">Kontak</label>
            <input id="kontak" class="w-full px-3 py-2 border rounded"
                value="${data.kontak ?? ''}">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block mb-1 font-medium">Mulai Kerja Sama</label>
                <input id="mulai" type="date"
                    class="w-full px-3 py-2 border rounded"
                    value="${data.tgl_mulai_kerjasama ? data.tgl_mulai_kerjasama.substring(0,10) : ''}">
            </div>

            <div>
                <label class="block mb-1 font-medium">Akhir Kerja Sama</label>
                <input id="akhir" type="date"
                    class="w-full px-3 py-2 border rounded"
                    value="${data.tgl_akhir_kerjasama ? data.tgl_akhir_kerjasama.substring(0,10) : ''}">
            </div>
        </div>
    </div>
    `;
}

function handleSubmit(action, method) {
    const nama = document.getElementById('nama').value;
    const mulai = document.getElementById('mulai').value;

    if (!nama || !mulai) {
        Swal.showValidationMessage('Nama sekolah dan tanggal mulai wajib diisi');
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
        <input type="hidden" name="nama_sekolah" value="${document.getElementById('nama').value}">
        <input type="hidden" name="alamat" value="${document.getElementById('alamat').value}">
        <input type="hidden" name="kontak" value="${document.getElementById('kontak').value}">
        <input type="hidden" name="tgl_mulai_kerjasama" value="${document.getElementById('mulai').value}">
        <input type="hidden" name="tgl_akhir_kerjasama" value="${document.getElementById('akhir').value}">
    `;

    document.body.appendChild(form);
    form.submit();
}

function confirmDelete(e) {
    e.preventDefault();
    Swal.fire({
        title: 'Hapus Sekolah?',
        text: 'Sekolah akan dihapus permanen',
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
