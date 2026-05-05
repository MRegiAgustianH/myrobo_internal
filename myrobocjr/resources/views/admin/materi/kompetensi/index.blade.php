@extends('layouts.app')

@section('header')
Manajemen Kompetensi
@endsection

@section('content')

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

{{-- ================= HEADER INFO ================= --}}
<div class="mb-4">
    <p class="text-sm text-gray-500">Materi</p>
    <h2 class="text-lg font-semibold text-gray-800">
        {{ $materi->nama_materi }}
    </h2>
</div>

{{-- ================= ACTION BAR ================= --}}
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">

    <a href="{{ route('admin.materi.index') }}"
       class="text-sm text-gray-600 hover:underline">
        ← Kembali ke Materi
    </a>

    <button onclick="openCreateModal()"
    class="inline-flex items-center gap-2 bg-[#8FBFC2] hover:bg-[#6FA9AD] text-white px-4 py-2 rounded-lg text-sm transition">
        <i data-feather="plus" class="w-4 h-4"></i>
        Tambah Kompetensi
    </button>

</div>

{{-- ================= MOBILE CARD VIEW ================= --}}
<div class="grid grid-cols-1 gap-4 md:hidden">

@forelse($kompetensis as $k)
<div class="bg-white rounded-xl shadow p-4 space-y-3">

    <div>
        <p class="font-semibold text-gray-800">
            {{ $k->nama_kompetensi }}
        </p>
        <p class="text-xs text-gray-500">
            {{ $k->indikator_kompetensis_count }} indikator
        </p>
    </div>

    <div class="flex gap-2 pt-2 border-t">
        <a href="{{ route('materi.kompetensi.indikator.index',[$materi->id, $k->id]) }}"
           class="flex-1 bg-green-100 text-green-700 text-xs py-2 rounded text-center">
            Indikator
        </a>

        <button onclick='openEditModal(@json($k))'
        class="bg-blue-100 text-blue-700 px-3 py-1 rounded text-xs">
            Edit
        </button>


        <button type="button"
            onclick="confirmDelete({{ $k->id }})"
            class="bg-red-100 text-red-700 px-3 py-1 rounded text-xs">
            Hapus
        </button>

    </div>

</div>
@empty
<div class="text-center text-sm text-gray-500 py-6">
    Belum ada kompetensi untuk materi ini
</div>
@endforelse

</div>

{{-- ================= DESKTOP TABLE VIEW ================= --}}
<div class="hidden md:block bg-white rounded-xl shadow overflow-x-auto">

<table class="min-w-full text-sm">
    <thead class="bg-gray-50 border-b">
        <tr class="text-gray-600 uppercase text-xs tracking-wider">
            <th class="px-4 py-3 text-left">
                Nama Kompetensi
            </th>
            <th class="px-4 py-3 text-center">
                Indikator
            </th>
            <th class="px-4 py-3 text-center w-56">
                Aksi
            </th>
        </tr>
    </thead>

    <tbody class="divide-y">
    @forelse($kompetensis as $k)
        <tr class="hover:bg-gray-50 transition">
            <td class="px-4 py-3 font-medium">
                {{ $k->nama_kompetensi }}
            </td>

            <td class="px-4 py-3 text-center text-gray-600 text-xs">
                {{ $k->indikator_kompetensis_count }}
            </td>

            <td class="px-4 py-3 text-center">
                <div class="inline-flex gap-2 justify-center">

                    <a href="{{ route('materi.kompetensi.indikator.index', [$materi->id, $k->id]) }}"
                       class="bg-green-100 text-green-700 px-3 py-1 rounded text-xs">
                        Indikator
                    </a>

                    <button onclick='openEditModal(@json($k))'
                    class="bg-blue-100 text-blue-700 px-3 py-1 rounded text-xs">
                        Edit
                    </button>


                    <button type="button"
                        onclick="confirmDelete({{ $k->id }})"
                        class="bg-red-100 text-red-700 px-3 py-1 rounded text-xs">
                        Hapus
                    </button>

                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="3"
                class="px-4 py-6 text-center text-gray-500 text-sm">
                Belum ada kompetensi untuk materi ini
            </td>
        </tr>
    @endforelse
    </tbody>
</table>

</div>

@push('scripts')
<script>
/* ================= FORM TEMPLATE ================= */
function kompetensiForm(data = {}) {
    return `
        <input type="hidden" id="csrf" value="{{ csrf_token() }}">
        <div class="space-y-4 text-left text-sm">
            <div>
                <label class="font-medium">Nama Kompetensi</label>
                <input id="nama_kompetensi"
                    class="w-full border rounded-lg px-3 py-2"
                    value="${data.nama_kompetensi ?? ''}">
            </div>
        </div>
    `;
}

/* ================= CREATE ================= */
function openCreateModal() {
    Swal.fire({
        title: 'Tambah Kompetensi',
        html: kompetensiForm(),
        showConfirmButton: false,
        width: 500,
        footer: modalActions(
            "{{ route('materi.kompetensi.store', $materi->id) }}",
            'POST'
        )
    });
}

/* ================= EDIT ================= */
function openEditModal(data) {
    Swal.fire({
        title: 'Edit Kompetensi',
        html: kompetensiForm(data),
        showConfirmButton: false,
        width: 500,
        footer: modalActions(
            `/admin/materi/{{ $materi->id }}/kompetensi/${data.id}`,
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
    const nama = document.getElementById('nama_kompetensi').value;

    if (!nama) {
        Swal.showValidationMessage('Nama kompetensi wajib diisi');
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
        <input type="hidden" name="nama_kompetensi" value="${nama}">
    `;

    document.body.appendChild(form);
    form.submit();
}

/* ================= DELETE ================= */
function confirmDelete(id) {
    Swal.fire({
        title: 'Hapus Kompetensi?',
        text: 'Kompetensi akan dihapus permanen',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal'
    }).then(res => {
        if (res.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/materi/{{ $materi->id }}/kompetensi/${id}`;

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
