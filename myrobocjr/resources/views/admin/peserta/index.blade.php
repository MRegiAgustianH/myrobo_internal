@extends('layouts.app')

@section('header')
Peserta – {{ $sekolah->nama_sekolah }}
@endsection

@section('content')

{{-- ACTION BAR --}}
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-5">
    <a href="{{ route('sekolah.index') }}"
       class="text-sm text-gray-600 hover:underline">
        ← Kembali ke Sekolah
    </a>

    <div class="flex flex-wrap gap-2">
        <a href="{{ route('peserta.template.download') }}"
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">
            Download Template
        </a>

        <button
            onclick="openImportModal()"
            class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm">
            Import Excel
        </button>

        <button
            onclick="openCreateModal()"
            class="inline-flex items-center gap-2 bg-[#8FBFC2] hover:bg-[#6FA9AD] text-white px-4 py-2 rounded-lg text-sm transition">
            <i data-feather="plus" class="w-4 h-4"></i>
            Tambah Peserta
        </button>
    </div>
</div>

@if(session('success'))
<div class="mb-4 p-3 bg-green-100 text-green-700 rounded text-sm">
    {{ session('success') }}
</div>
@endif

{{-- ================= MOBILE VIEW ================= --}}
<div class="grid grid-cols-1 gap-4 md:hidden">

@forelse($pesertas as $p)
<div class="bg-white rounded-xl shadow p-4 space-y-3">

    <div>
        <p class="font-semibold text-gray-800">
            {{ $p->nama }}
        </p>
        <p class="text-xs text-gray-500">
            {{ $p->kelas ?? '–' }}
        </p>
    </div>

    <div class="flex justify-between text-sm">
        <span class="text-gray-500">Jenis Kelamin</span>
        <span>{{ $p->jenis_kelamin }}</span>
    </div>

    <div class="flex justify-between text-sm">
        <span class="text-gray-500">Kontak</span>
        <span>{{ $p->kontak ?? '-' }}</span>
    </div>

    <div class="flex justify-between items-center text-sm">
        <span class="text-gray-500">Status</span>
        @if($p->status === 'aktif')
            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs">
                Aktif
            </span>
        @else
            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs">
                Tidak
            </span>
        @endif
    </div>

    <div class="flex gap-2 pt-3">
        <button
            onclick='openEditModal(@json($p))'
            class="flex-1 bg-yellow-100 text-yellow-700 text-xs py-2 rounded">
            Edit
        </button>

        <form action="{{ route('peserta.destroy', $p->id) }}"
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
<div class="text-center text-gray-500 py-12">
    Belum ada peserta
</div>
@endforelse

</div>

{{-- ================= DESKTOP VIEW ================= --}}
<div class="hidden md:block bg-white rounded-xl shadow overflow-x-auto">

    <table class="min-w-[900px] w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr class="uppercase text-xs text-gray-600 tracking-wider">
                <th class="px-4 py-3 text-left">Nama</th>
                <th class="px-4 py-3 text-center">JK</th>
                <th class="px-4 py-3 text-left">Kelas</th>
                <th class="px-4 py-3 text-left">Kontak</th>
                <th class="px-4 py-3 text-center">Status</th>
                <th class="px-4 py-3 text-center w-40">Aksi</th>
            </tr>
        </thead>

        <tbody class="divide-y">
        @forelse($pesertas as $p)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-2 font-medium">
                    {{ $p->nama }}
                </td>

                <td class="px-4 py-2 text-center">
                    {{ $p->jenis_kelamin }}
                </td>

                <td class="px-4 py-2">
                    {{ $p->kelas ?? '-' }}
                </td>

                <td class="px-4 py-2">
                    {{ $p->kontak ?? '-' }}
                </td>

                <td class="px-4 py-2 text-center">
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

                <td class="px-4 py-2 text-center">
                    <div class="inline-flex gap-1">
                        <button
                            onclick='openEditModal(@json($p))'
                            class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded text-xs">
                            Edit
                        </button>

                        <form action="{{ route('peserta.destroy', $p->id) }}"
                              method="POST"
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
        @empty
            <tr>
                <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                    Belum ada peserta
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

{{-- ================= MODAL TAMBAH & EDIT ================= --}}
<div id="pesertaModal"
     class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center">

    <div id="modalBox"
         class="bg-white w-full max-w-lg rounded-xl shadow-lg
                transform transition-all duration-300
                scale-95 opacity-0">

        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h2 id="modalTitle" class="text-lg font-semibold">Tambah Peserta</h2>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">✕</button>
        </div>

        <form id="pesertaForm" method="POST" class="px-6 py-4 space-y-4">
            @csrf
            <input type="hidden" id="formMethod" name="_method" value="POST">
            <input type="hidden" name="sekolah_id" value="{{ $sekolah->id }}">

            <div>
                <label class="text-sm font-medium">Nama</label>
                <input id="nama" name="nama"
                       class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="text-sm font-medium">Jenis Kelamin</label>
                <select id="jenis_kelamin" name="jenis_kelamin"
                        class="w-full border rounded px-3 py-2" required>
                    <option value="">-- Pilih --</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </div>

            <div>
                <label class="text-sm font-medium">Kelas</label>
                <input id="kelas" name="kelas"
                       class="w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="text-sm font-medium">Kontak</label>
                <input id="kontak" name="kontak"
                       class="w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="text-sm font-medium">Status</label>
                <select id="status" name="status"
                        class="w-full border rounded px-3 py-2">
                    <option value="aktif">Aktif</option>
                    <option value="tidak">Tidak</option>
                </select>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t">
                <button type="button"
                        onclick="closeModal()"
                        class="px-4 py-2 bg-gray-200 rounded">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-[#8FBFC2] text-white rounded">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ================= SCRIPT ================= --}}
<script>
function openCreateModal() {
    pesertaForm.reset();
    formMethod.value = 'POST';
    modalTitle.innerText = 'Tambah Peserta';
    pesertaForm.action = "{{ route('sekolah.peserta.store', $sekolah->id) }}";
    showModal();
}

function openEditModal(p) {
    modalTitle.innerText = 'Edit Peserta';
    pesertaForm.action = "{{ route('peserta.update', ':id') }}".replace(':id', p.id);
    formMethod.value = 'PATCH';

    nama.value = p.nama;
    jenis_kelamin.value = p.jenis_kelamin;
    kelas.value = p.kelas ?? '';
    kontak.value = p.kontak ?? '';
    status.value = p.status;

    showModal();
}

function showModal() {
    pesertaModal.classList.remove('hidden');
    setTimeout(() => {
        modalBox.classList.remove('scale-95','opacity-0');
        modalBox.classList.add('scale-100','opacity-100');
    }, 10);
}

function closeModal() {
    modalBox.classList.add('scale-95','opacity-0');
    modalBox.classList.remove('scale-100','opacity-100');
    setTimeout(() => pesertaModal.classList.add('hidden'), 300);
}

function confirmDelete(e) {
    e.preventDefault();
    Swal.fire({
        title: 'Hapus Peserta?',
        text: 'Data akan dihapus permanen',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus'
    }).then(r => {
        if (r.isConfirmed) e.target.submit();
    });
}
</script>

<script>
function openImportModal() {
    Swal.fire({
        title: 'Import Peserta (Excel)',
        html: `
            <form id="importForm"
                  action="{{ route('peserta.import', $sekolah->id) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="space-y-4 text-left text-sm">

                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div>
                    <label class="block font-medium mb-1">
                        File Excel
                    </label>
                    <input
                        type="file"
                        name="file"
                        accept=".xlsx,.xls"
                        class="w-full border rounded px-3 py-2"
                        required>
                    <p class="text-xs text-gray-500 mt-1">
                        Format: .xlsx / .xls
                    </p>
                </div>

                <div class="bg-gray-50 p-3 rounded text-xs text-gray-600">
                    <p class="font-medium mb-1">Kolom wajib:</p>
                    <ul class="list-disc ml-4 space-y-0.5">
                        <li>nama</li>
                        <li>jenis_kelamin (L / P)</li>
                        <li>kelas (opsional)</li>
                        <li>kontak (opsional)</li>
                        <li>status (aktif / tidak)</li>
                    </ul>
                </div>

            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Import',
        cancelButtonText: 'Batal',
        focusConfirm: false,
        preConfirm: () => {
            const fileInput =
                Swal.getPopup().querySelector('input[type="file"]');

            if (!fileInput.files.length) {
                Swal.showValidationMessage('File Excel wajib dipilih');
                return false;
            }

            const fileName = fileInput.files[0].name;
            if (!fileName.match(/\.(xlsx|xls)$/)) {
                Swal.showValidationMessage('File harus .xlsx atau .xls');
                return false;
            }

            return true;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('importForm').submit();
        }
    });
}
</script>


@endsection
