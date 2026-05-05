@extends('layouts.app')

@section('header')
Manajemen Sekolah
@endsection

@section('content')

{{-- ACTION BAR --}}
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-5">
    <h2 class="text-lg font-semibold text-gray-700">
        Daftar Sekolah Mitra
    </h2>

    <button
        onclick="openCreateModal()"
        class="inline-flex items-center gap-2 bg-[#8FBFC2] hover:bg-[#6FA9AD] text-white px-4 py-2 rounded-lg text-sm transition">
        <i data-feather="plus" class="w-4 h-4"></i>
        Tambah Sekolah
    </button>
</div>

@if(session('success'))
<div class="mb-4 p-3 rounded bg-green-100 text-green-700 text-sm">
    {{ session('success') }}
</div>
@endif

{{-- ================= MOBILE VIEW ================= --}}
<div class="grid grid-cols-1 gap-4 md:hidden">

@foreach($sekolahs as $s)
<div class="bg-white rounded-xl shadow p-4 space-y-3">

    <div>
        <p class="font-semibold text-gray-800">
            {{ $s->nama_sekolah }}
        </p>
        <p class="text-xs text-gray-500">
            {{ $s->alamat }}
        </p>
    </div>

    <div class="flex justify-between text-sm">
        <span class="text-gray-500">Kontak</span>
        <span class="font-medium">{{ $s->kontak }}</span>
    </div>

    <div class="flex justify-between text-sm">
        <span class="text-gray-500">Mulai</span>
        <span class="font-medium">
            {{ $s->tgl_mulai_kerjasama->format('d/m/Y') }}
        </span>
    </div>

    <div class="flex gap-2 pt-3">
        <a href="{{ route('sekolah.peserta.index',$s->id) }}"
           class="flex-1 bg-emerald-100 text-emerald-700 text-xs py-2 rounded text-center">
            Peserta
        </a>

        <button
            onclick='openEditModal(@json($s))'
            class="flex-1 bg-yellow-100 text-yellow-700 text-xs py-2 rounded">
            Edit
        </button>

        <form method="POST"
              action="{{ route('sekolah.destroy',$s->id) }}"
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
@endforeach

</div>

{{-- ================= DESKTOP VIEW ================= --}}
<div class="hidden md:block bg-white rounded-xl shadow">

    <div class="relative overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr class="text-gray-600 uppercase text-xs tracking-wider">
                    <th class="px-4 py-3 text-left">Sekolah</th>
                    <th class="px-4 py-3 text-center">Kontak</th>
                    <th class="px-4 py-3 text-center">Nominal</th>
                    <th class="px-4 py-3 text-center">Mulai</th>
                    <th class="px-4 py-3 text-center w-48">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y">
            @foreach($sekolahs as $s)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3">
                        <p class="font-medium">{{ $s->nama_sekolah }}</p>
                        <p class="text-xs text-gray-500 truncate max-w-xs">
                            {{ $s->alamat }}
                        </p>
                    </td>

                    <td class="px-4 py-3 text-center">
                        {{ $s->kontak }}
                    </td>

                    <td class="px-4 py-3 text-center">
                        Rp {{ number_format($s->nominal_pembayaran, 0, ',', '.') }}
                    </td>

                    <td class="px-4 py-3 text-center">
                        {{ $s->tgl_mulai_kerjasama->format('d/m/Y') }}
                    </td>

                    <td class="px-4 py-3 text-center">
                        <div class="inline-flex gap-1">
                            <a href="{{ route('sekolah.peserta.index',$s->id) }}"
                               class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded text-xs">
                                Peserta
                            </a>

                            <button
                                onclick='openEditModal(@json($s))'
                                class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded text-xs">
                                Edit
                            </button>

                            <form method="POST"
                                  action="{{ route('sekolah.destroy',$s->id) }}"
                                  onsubmit="return confirmDelete(event)">
                                @csrf
                                @method('DELETE')
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

{{-- ================= SWEETALERT SCRIPT ================= --}}
<script>
function openCreateModal() {
    Swal.fire({
        title: 'Tambah Sekolah',
        html: schoolForm(),
        showConfirmButton: false,
        width: 650,
        footer: actionButtons("{{ route('sekolah.store') }}", 'POST')
    });
}

function openEditModal(data) {
    Swal.fire({
        title: 'Edit Sekolah',
        html: schoolForm(data),
        showConfirmButton: false,
        width: 650,
        footer: actionButtons(`/sekolah/${data.id}`, 'PUT')
    });
}

function actionButtons(action, method) {
    return `
    <div class="flex gap-2">
        <button onclick="handleSubmit('${action}','${method}')" class="btn-primary">
            ${method === 'POST' ? 'Simpan' : 'Update'}
        </button>
        <button onclick="Swal.close()" class="btn-secondary">
            Batal
        </button>
    </div>`;
}

function schoolForm(data = {}) {
    return `
    <input type="hidden" id="csrf" value="{{ csrf_token() }}">

    <div class="space-y-4 text-left text-sm">
        <div>
            <label class="font-medium">Nama Sekolah</label>
            <input id="nama" class="w-full px-3 py-2 border rounded"
                   value="${data.nama_sekolah ?? ''}">
        </div>

        <div>
            <label class="font-medium">Alamat</label>
            <textarea id="alamat" class="w-full px-3 py-2 border rounded"
                      rows="2">${data.alamat ?? ''}</textarea>
        </div>

        <div>
            <label class="font-medium">Kontak</label>
            <input id="kontak" class="w-full px-3 py-2 border rounded"
                   value="${data.kontak ?? ''}">
        </div>

        <div>
            <label class="font-medium">Nominal Pembayaran (Rp)</label>
            <input id="nominal" type="number" class="w-full px-3 py-2 border rounded"
                   value="${data.nominal_pembayaran ?? 150000}">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="font-medium">Mulai Kerja Sama</label>
                <input id="mulai" type="date" class="w-full px-3 py-2 border rounded"
                       value="${data.tgl_mulai_kerjasama ? data.tgl_mulai_kerjasama.substring(0,10) : ''}">
            </div>

            <div>
                <label class="font-medium">Akhir Kerja Sama</label>
                <input id="akhir" type="date" class="w-full px-3 py-2 border rounded"
                       value="${data.tgl_akhir_kerjasama ? data.tgl_akhir_kerjasama.substring(0,10) : ''}">
            </div>
        </div>
    </div>`;
}

function handleSubmit(action, method) {
    if (!document.getElementById('nama').value || !document.getElementById('mulai').value) {
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
        <input type="hidden" name="nama_sekolah" value="${nama.value}">
        <input type="hidden" name="alamat" value="${alamat.value}">
        <input type="hidden" name="kontak" value="${kontak.value}">
        <input type="hidden" name="nominal_pembayaran" value="${nominal.value}">
        <input type="hidden" name="tgl_mulai_kerjasama" value="${mulai.value}">
        <input type="hidden" name="tgl_akhir_kerjasama" value="${akhir.value}">
    `;
    document.body.appendChild(form);
    form.submit();
}

function confirmDelete(e) {
    e.preventDefault();
    Swal.fire({
        title: 'Hapus Sekolah?',
        text: 'Data akan dihapus permanen',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus'
    }).then(res => {
        if (res.isConfirmed) e.target.submit();
    });
}
</script>

@endsection
