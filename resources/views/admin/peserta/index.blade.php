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
        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded text-sm">
            Download Template
        </a>

        <button onclick="openImportModal()"
            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm">
            Import Excel
        </button>

        <button onclick="openCreateModal()"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
            + Tambah Peserta
        </button>

    </div>

</div>


@if(session('success'))
<div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
    {{ session('success') }}
</div>
@endif
@if(session('import_errors'))
<div class="mb-4 p-4 bg-red-50 border border-red-200 rounded">
    <h3 class="font-semibold text-red-700 mb-2">
        Import selesai dengan beberapa error
    </h3>

    <p class="text-sm text-gray-700 mb-2">
        Data berhasil disimpan: {{ session('success_count') }}
    </p>

    <ul class="text-sm text-red-700 list-disc pl-5 space-y-1">
        @foreach(session('import_errors') as $err)
            <li>
                Baris {{ $err['row'] }}:
                <ul class="list-disc pl-5">
                    @foreach($err['errors'] as $msg)
                        <li>{{ $msg }}</li>
                    @endforeach
                </ul>
            </li>
        @endforeach
    </ul>
</div>
@endif


{{-- TABLE WRAPPER (SCROLL ONLY HERE) --}}
<div class="bg-white rounded-lg shadow">

    <div class="relative overflow-x-auto">

        {{-- scroll hint --}}
        <div class="md:hidden absolute top-2 right-4 text-xs text-gray-400 pointer-events-none">
            ⇆ geser
        </div>

        <table class="min-w-[900px] w-full text-sm border-collapse">
            <thead class="bg-gray-50 border-b">
                <tr class="text-gray-600 uppercase text-xs tracking-wider">
                    <th class="px-4 py-3 text-left whitespace-nowrap">Nama</th>
                    <th class="px-4 py-3 text-center whitespace-nowrap">JK</th>
                    <th class="px-4 py-3 text-left whitespace-nowrap">Kelas</th>
                    <th class="px-4 py-3 text-left whitespace-nowrap">Kontak</th>
                    <th class="px-4 py-3 text-center whitespace-nowrap">Status</th>
                    <th class="px-4 py-3 text-center whitespace-nowrap w-40">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y">
            @forelse($pesertas as $p)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 font-medium whitespace-nowrap">
                        {{ $p->nama }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        {{ $p->jenis_kelamin }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        {{ $p->kelas ?? '-' }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        {{ $p->kontak ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-center whitespace-nowrap">
                        @if($p->status === 'aktif')
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs">
                                Aktif
                            </span>
                        @else
                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs">
                                Tidak
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center whitespace-nowrap">
                        <div class="inline-flex gap-1">
                            <button onclick='openEditModal(@json($p))'
                                class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded text-xs">
                                Edit
                            </button>
                            <form action="{{ route('peserta.destroy',$p->id) }}"
                                  method="POST"
                                  onsubmit="return confirmDelete(event)">
                                @csrf @method('DELETE')
                                <button class="bg-red-100 text-red-700 px-3 py-1 rounded text-xs">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                        Belum ada peserta.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

    </div>
</div>



{{-- ================= MODAL SWEETALERT ================= --}}
<script>
function openCreateModal() {
    Swal.fire({
        title: 'Tambah Peserta',
        html: pesertaForm(),
        showConfirmButton: false,
        width: 600,
        footer: actionButtons("{{ route('peserta.store') }}", 'POST')
    });
}

function openEditModal(data) {
    Swal.fire({
        title: 'Edit Peserta',
        html: pesertaForm(data),
        showConfirmButton: false,
        width: 600,
        footer: actionButtons(`/peserta/${data.id}`, 'PUT')
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

function pesertaForm(data = {}) {
    return `
    <input type="hidden" id="csrf" value="{{ csrf_token() }}">
    <input type="hidden" id="sekolah_id" value="{{ $sekolah->id }}">

    <div class="space-y-4 text-left text-sm">

        <div>
            <label class="block mb-1 font-medium">Nama Peserta</label>
            <input id="nama" class="w-full px-3 py-2 border rounded"
                value="${data.nama ?? ''}">
        </div>

        <div>
            <label class="block mb-1 font-medium">Jenis Kelamin</label>
            <select id="jk" class="w-full px-3 py-2 border rounded">
                <option value="">-- Pilih --</option>
                <option value="L" ${data.jenis_kelamin === 'L' ? 'selected' : ''}>Laki-laki</option>
                <option value="P" ${data.jenis_kelamin === 'P' ? 'selected' : ''}>Perempuan</option>
            </select>
        </div>

        <div>
            <label class="block mb-1 font-medium">Kelas</label>
            <input id="kelas" class="w-full px-3 py-2 border rounded"
                value="${data.kelas ?? ''}">
        </div>

        <div>
            <label class="block mb-1 font-medium">Kontak</label>
            <input id="kontak" class="w-full px-3 py-2 border rounded"
                value="${data.kontak ?? ''}">
        </div>

        <div>
            <label class="block mb-1 font-medium">Status</label>
            <select id="status" class="w-full px-3 py-2 border rounded">
                <option value="aktif" ${data.status !== 'tidak' ? 'selected' : ''}>Aktif</option>
                <option value="tidak" ${data.status === 'tidak' ? 'selected' : ''}>Tidak Aktif</option>
            </select>
        </div>

    </div>
    `;
}

function handleSubmit(action, method) {
    const nama = document.getElementById('nama').value;
    const jk = document.getElementById('jk').value;

    if (!nama || !jk) {
        Swal.showValidationMessage('Nama dan jenis kelamin wajib diisi');
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
        <input type="hidden" name="sekolah_id" value="${document.getElementById('sekolah_id').value}">
        <input type="hidden" name="nama" value="${document.getElementById('nama').value}">
        <input type="hidden" name="jenis_kelamin" value="${document.getElementById('jk').value}">
        <input type="hidden" name="kelas" value="${document.getElementById('kelas').value}">
        <input type="hidden" name="kontak" value="${document.getElementById('kontak').value}">
        <input type="hidden" name="status" value="${document.getElementById('status').value}">
    `;

    document.body.appendChild(form);
    form.submit();
}

function confirmDelete(e) {
    e.preventDefault();
    Swal.fire({
        title: 'Hapus Peserta?',
        text: 'Data tidak dapat dikembalikan',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus'
    }).then(res => {
        if (res.isConfirmed) {
            e.target.submit();
        }
    });
}

function openImportModal() {
    Swal.fire({
        title: 'Import Peserta',
        html: `
            <form id="importForm"
                method="POST"
                enctype="multipart/form-data"
                action="{{ route('peserta.import', $sekolah->id) }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <input type="file" name="file"
                    accept=".xls,.xlsx"
                    class="w-full border rounded px-3 py-2 text-sm"
                    required>

                <p class="text-xs text-gray-500 mt-2">
                    Format: nama | jenis_kelamin | kelas | kontak
                </p>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Import',
        preConfirm: () => {
            document.getElementById('importForm').submit();
        }
    });
}
</script>

@endsection
