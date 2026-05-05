@extends('layouts.app')

@section('header')
Indikator Kompetensi
@endsection

@section('content')

{{-- ================= CONTEXT HEADER ================= --}}
<div class="mb-4">
    <p class="text-sm text-gray-500">Materi</p>
    <h2 class="text-lg font-semibold text-gray-800">
        {{ $materi->nama_materi }}
    </h2>

    <p class="text-sm text-gray-500 mt-2">Kompetensi</p>
    <h3 class="font-medium text-gray-700">
        {{ $kompetensi->nama_kompetensi }}
    </h3>
</div>

{{-- FLASH MESSAGE --}}
@if(session('success'))
<div class="mb-4 p-3 bg-green-100 text-green-700 rounded text-sm">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-4 p-3 bg-red-100 text-red-700 rounded text-sm">
    {{ session('error') }}
</div>
@endif

{{-- ================= ACTION BAR ================= --}}
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">

    <a href="{{ route('materi.kompetensi.index', $materi->id) }}"
       class="text-sm text-gray-600 hover:underline">
        ← Kembali ke Kompetensi
    </a>

    <button onclick="openCreateModal()"
    class="inline-flex items-center gap-2
            bg-[#8FBFC2] hover:bg-[#6FA9AD]
            text-white px-4 py-2 rounded-lg text-sm transition">
        <i data-feather="plus" class="w-4 h-4"></i>
        Tambah Indikator
    </button>

</div>

{{-- ================= MOBILE CARD VIEW ================= --}}
<div class="grid grid-cols-1 gap-4 md:hidden">

@forelse($indikators as $i)
<div class="bg-white rounded-xl shadow p-4 space-y-3">

    <p class="font-semibold text-gray-800">
        {{ $i->nama_indikator }}
    </p>

    <div class="flex gap-2 pt-2 border-t">
        <button type="button"
        onclick='openEditModal(@json($i))'
        class="bg-blue-100 text-blue-700 px-3 py-1 rounded text-xs">
            Edit
        </button>


        <button type="button"
        onclick="confirmDelete({{ $i->id }})"
        class="bg-red-100 text-red-700 px-3 py-1 rounded text-xs">
            Hapus
        </button>

    </div>

</div>
@empty
<div class="bg-white rounded-xl shadow p-6 text-center text-sm text-gray-500">
    Belum ada indikator untuk kompetensi ini
</div>
@endforelse

</div>

{{-- ================= DESKTOP TABLE VIEW ================= --}}
<div class="hidden md:block bg-white rounded-xl shadow overflow-x-auto">

<table class="min-w-full text-sm">
    <thead class="bg-gray-50 border-b">
        <tr class="text-gray-600 uppercase text-xs tracking-wider">
            <th class="px-4 py-3 text-left">
                Nama Indikator
            </th>
            <th class="px-4 py-3 text-center w-40">
                Aksi
            </th>
        </tr>
    </thead>

    <tbody class="divide-y">
    @forelse($indikators as $i)
        <tr class="hover:bg-gray-50 transition">
            <td class="px-4 py-3 font-medium">
                {{ $i->nama_indikator }}
            </td>

            <td class="px-4 py-3 text-center">
                <div class="inline-flex gap-2 justify-center">

                    <button type="button"
                    onclick='openEditModal(@json($i))'
                    class="bg-blue-100 text-blue-700 px-3 py-1 rounded text-xs">
                        Edit
                    </button>


                    <button type="button"
                    onclick="confirmDelete({{ $i->id }})"
                    class="bg-red-100 text-red-700 px-3 py-1 rounded text-xs">
                        Hapus
                    </button>


                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="2"
                class="px-4 py-6 text-center text-gray-500">
                Belum ada indikator untuk kompetensi ini
            </td>
        </tr>
    @endforelse
    </tbody>
</table>

</div>

@push('scripts')
<script>
/* ================= FORM TEMPLATE ================= */
function indikatorForm(data = {}) {
    return `
        <input type="hidden" id="csrf" value="{{ csrf_token() }}">
        <div class="space-y-4 text-left text-sm">
            <div>
                <label class="font-medium">Nama Indikator</label>
                <input id="nama_indikator"
                    class="w-full border rounded-lg px-3 py-2"
                    value="${data.nama_indikator ?? ''}">
            </div>
        </div>
    `;
}

/* ================= CREATE ================= */
function openCreateModal() {
    Swal.fire({
        title: 'Tambah Indikator',
        html: indikatorForm(),
        showConfirmButton: false,
        width: 500,
        footer: modalActions(
            "{{ route('materi.kompetensi.indikator.store', [$materi->id, $kompetensi->id]) }}",
            'POST'
        )
    });
}

/* ================= EDIT ================= */
function openEditModal(data) {
    Swal.fire({
        title: 'Edit Indikator',
        html: indikatorForm(data),
        showConfirmButton: false,
        width: 500,
        footer: modalActions(
            `/admin/materi/{{ $materi->id }}/kompetensi/{{ $kompetensi->id }}/indikator/${data.id}`,
            'PUT'
        )
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
    const nama = document.getElementById('nama_indikator').value;

    if (!nama) {
        Swal.showValidationMessage('Nama indikator wajib diisi');
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = action;

    form.innerHTML = `
        <input type="hidden" name="_token" value="${document.getElementById('csrf').value}">
        ${method !== 'POST'
            ? `<input type="hidden" name="_method" value="${method}">`
            : ''
        }
        <input type="hidden" name="nama_indikator" value="${nama}">
    `;

    document.body.appendChild(form);
    form.submit();
}

/* ================= DELETE ================= */
function confirmDelete(id) {
    Swal.fire({
        title: 'Hapus Indikator?',
        text: 'Indikator akan dihapus permanen',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {

            const form = document.createElement('form');
            form.method = 'POST';
            form.action =
                `/admin/materi/{{ $materi->id }}/kompetensi/{{ $kompetensi->id }}/indikator/${id}`;

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


@endsection
