@extends('layouts.app')

@section('header')
<div class="flex items-center gap-3">
    <div class="p-2 bg-[#8FBFC2]/20 rounded-xl">
        <i data-feather="clipboard" class="w-5 h-5 text-[#6FA9AD]"></i>
    </div>
    <div>
        <h1 class="text-lg font-semibold text-gray-800">
            Manajemen Tugas Rapor
        </h1>
        <p class="text-xs text-gray-500">
            Kelola distribusi dan progres pengisian rapor
        </p>
    </div>
</div>
@endsection

@section('content')

@if(auth()->user()->role === 'superadmin' && isset($cabangs))
<form method="GET" class="mb-4 flex gap-3 items-end">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Filter Cabang</label>
        <select name="cabang_id" class="bg-white border border-[#E3EEF0] rounded-lg px-3 py-2 text-sm" onchange="this.form.submit()">
            <option value="">-- Semua Cabang --</option>
            @foreach($cabangs as $cb)
                <option value="{{ $cb->id }}" {{ (string)request('cabang_id') === (string)$cb->id ? 'selected' : '' }}>{{ $cb->nama_cabang }}</option>
            @endforeach
        </select>
    </div>
</form>
@endif

{{-- ================= SUCCESS ================= --}}
@if(session('success'))
<div class="mb-5 flex items-start gap-3 p-4
            bg-green-50 border border-green-200
            rounded-xl text-sm text-green-700">
    <i data-feather="check-circle" class="w-5 h-5 mt-0.5"></i>
    {{ session('success') }}
</div>
@endif

{{-- ================= ERROR VALIDATION ================= --}}
@if ($errors->any())
<div class="mb-5 flex items-start gap-3 p-4
            bg-red-50 border border-red-200
            rounded-xl text-sm text-red-700">
    <i data-feather="alert-triangle" class="w-5 h-5 mt-0.5"></i>
    <div>
        <p class="font-semibold mb-1">
            Gagal membuat tugas rapor
        </p>
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

{{-- ================= CREATE ================= --}}
<div class="bg-white rounded-2xl shadow-sm border mb-8">
    <div class="p-6 border-b flex items-center gap-3">
        <div class="p-2 bg-indigo-50 rounded-lg">
            <i data-feather="plus" class="w-5 h-5 text-indigo-600"></i>
        </div>
        <h2 class="font-semibold text-gray-800">
            Buat Tugas Rapor
        </h2>
    </div>

    <form method="POST" action="{{ route('admin.rapor-tugas.store') }}"
          class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        @csrf

        @if(auth()->user()->role === 'superadmin')
        {{-- CABANG --}}
        <div>
            <label class="text-xs font-medium text-gray-600 mb-1 flex items-center gap-1">
                <i data-feather="map-pin" class="w-3 h-3"></i> Cabang
            </label>
            <select name="cabang_id" class="w-full border rounded-lg px-3 py-2 text-sm">
                <option value="">Pilih Cabang</option>
                @foreach($cabangs as $cb)
                    <option value="{{ $cb->id }}" @selected(old('cabang_id') == $cb->id)>{{ $cb->nama_cabang }}</option>
                @endforeach
            </select>
        </div>
        @else
        <input type="hidden" name="cabang_id" value="{{ auth()->user()->cabang_id }}">
        @endif

        {{-- SEKOLAH --}}
        <div>
            <label class="text-xs font-medium text-gray-600 mb-1 flex items-center gap-1">
                <i data-feather="home" class="w-3 h-3"></i> Sekolah
            </label>
            <select name="sekolah_id" required
                class="w-full border rounded-lg px-3 py-2 text-sm
                       {{ $errors->has('sekolah_id') ? 'border-red-400 bg-red-50' : '' }}">
                <option value="">Pilih Sekolah</option>
                @foreach(\App\Models\Sekolah::when(auth()->user()->cabang_id && auth()->user()->role !== 'superadmin', fn($q) => $q->where('cabang_id', auth()->user()->cabang_id))->orderBy('nama_sekolah')->get() as $s)
                    <option value="{{ $s->id }}"
                        @selected(old('sekolah_id') == $s->id)>
                        {{ $s->nama_sekolah }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- SEMESTER --}}
        <div>
            <label class="text-xs font-medium text-gray-600 mb-1 flex items-center gap-1">
                <i data-feather="calendar" class="w-3 h-3"></i> Semester
            </label>
            <select name="semester_id" required
                class="w-full border rounded-lg px-3 py-2 text-sm
                       {{ $errors->has('semester_id') ? 'border-red-400 bg-red-50' : '' }}">
                <option value="">Pilih Semester</option>
                @foreach(\App\Models\Semester::orderBy('nama_semester')->get() as $s)
                    <option value="{{ $s->id }}"
                        @selected(old('semester_id') == $s->id)>
                        {{ $s->nama_semester }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- INSTRUKTUR --}}
        <div>
            <label class="text-xs font-medium text-gray-600 mb-1 flex items-center gap-1">
                <i data-feather="user" class="w-3 h-3"></i> Instruktur
            </label>
            <select name="instruktur_id" required
                class="w-full border rounded-lg px-3 py-2 text-sm
                       {{ $errors->has('instruktur_id') ? 'border-red-400 bg-red-50' : '' }}">
                <option value="">Pilih Instruktur</option>
                @foreach(\App\Models\User::where('role','instruktur')->when(auth()->user()->cabang_id && auth()->user()->role !== 'superadmin', fn($q) => $q->where('cabang_id', auth()->user()->cabang_id))->get() as $u)
                    <option value="{{ $u->id }}"
                        @selected(old('instruktur_id') == $u->id)>
                        {{ $u->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- DEADLINE --}}
        <div>
            <label class="text-xs font-medium text-gray-600 mb-1 flex items-center gap-1">
                <i data-feather="clock" class="w-3 h-3"></i> Deadline
            </label>
            <input type="date" name="deadline"
                   value="{{ old('deadline') }}"
                   class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>

        {{-- BUTTON --}}
        <div class="sm:col-span-2 lg:col-span-4 pt-2">
            <button
                class="w-full flex items-center justify-center gap-2
                       bg-[#8FBFC2] hover:bg-[#6FA9AD]
                       text-white py-2.5 rounded-xl text-sm font-medium">
                <i data-feather="save" class="w-4 h-4"></i>
                Simpan Tugas Rapor
            </button>
        </div>
    </form>
</div>

{{-- ================================================= --}}
{{-- =============== MOBILE / TABLET ================= --}}
{{-- ================================================= --}}
<div class="space-y-4 lg:hidden">
@forelse($tugas as $t)
<div class="bg-white border rounded-2xl p-5 shadow-sm">

    <div class="flex justify-between items-start mb-3">
        <div>
            <p class="font-semibold text-gray-800">
                {{ $t->sekolah->nama_sekolah }}
            </p>
            <p class="text-xs text-gray-500">
                Semester {{ $t->semester->nama_semester }}
            </p>
        </div>

        <span class="px-3 py-1 rounded-full text-xs font-medium
            {{ $t->status === 'completed'
                ? 'bg-green-100 text-green-700'
                : 'bg-yellow-100 text-yellow-700' }}">
            {{ ucfirst(str_replace('_',' ',$t->status)) }}
        </span>
    </div>

    <div class="text-sm text-gray-600 space-y-1 mb-4">
        <div class="flex justify-between">
            <span>Instruktur</span>
            <span class="font-medium">{{ $t->instruktur->name }}</span>
        </div>
        <div class="flex justify-between">
            <span>Jumlah Rapor</span>
            <span class="font-medium">{{ $t->rapors_count }}</span>
        </div>
    </div>

    <div class="flex gap-2">
        <a href="{{ route('admin.rapor-tugas.show',$t->id) }}"
           class="flex-1 inline-flex justify-center items-center gap-2
                  bg-indigo-600 hover:bg-indigo-700
                  text-white py-2 rounded-xl text-xs font-semibold">
            <i data-feather="eye" class="w-4 h-4"></i>
            Detail
        </a>
        <a href="{{ route('admin.rapor-tugas.edit',$t->id) }}"
           class="flex-1 inline-flex justify-center items-center gap-2
                  bg-yellow-100 text-yellow-700 py-2 rounded-xl text-xs font-semibold">
            <i data-feather="edit" class="w-4 h-4"></i>
            Edit
        </a>
    </div>
    <button onclick="confirmDeleteTugas({{ $t->id }})"
        class="w-full mt-2 bg-red-100 text-red-700 py-2 rounded-xl text-xs font-semibold">
        Hapus Tugas
    </button>

</div>
@empty
<div class="text-center text-sm text-gray-500 py-10">
    Tidak ada tugas rapor
</div>
@endforelse
</div>

{{-- ================================================= --}}
{{-- =================== DESKTOP ===================== --}}
{{-- ================================================= --}}
<div class="hidden lg:block bg-white rounded-2xl shadow-sm border overflow-hidden">
    <div class="p-6 border-b flex items-center gap-3">
        <div class="p-2 bg-gray-100 rounded-lg">
            <i data-feather="list" class="w-5 h-5 text-gray-600"></i>
        </div>
        <h2 class="font-semibold text-gray-800">
            Daftar Tugas Rapor
        </h2>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="px-6 py-3 text-left">Sekolah</th>
                <th class="px-6 py-3 text-center">Semester</th>
                <th class="px-6 py-3 text-center">Instruktur</th>
                <th class="px-6 py-3 text-center">Rapor</th>
                <th class="px-6 py-3 text-center">Status</th>
                <th class="px-6 py-3 text-center w-64">Aksi</th>
            </tr>
            </thead>
            <tbody class="divide-y">
            @foreach($tugas as $t)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-medium">{{ $t->sekolah->nama_sekolah }}</td>
                <td class="px-6 py-4 text-center">{{ $t->semester->nama_semester }}</td>
                <td class="px-6 py-4 text-center">{{ $t->instruktur->name }}</td>
                <td class="px-6 py-4 text-center">{{ $t->rapors_count }}</td>
                <td class="px-6 py-4 text-center">
                    <span class="px-3 py-1 rounded-full text-xs
                        {{ $t->status === 'completed'
                            ? 'bg-green-100 text-green-700'
                            : 'bg-yellow-100 text-yellow-700' }}">
                        {{ ucfirst(str_replace('_',' ',$t->status)) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('admin.rapor-tugas.show',$t->id) }}"
                           class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800">
                            <i data-feather="eye" class="w-4 h-4"></i>
                            Detail
                        </a>
                        <a href="{{ route('admin.rapor-tugas.edit',$t->id) }}"
                           class="inline-flex items-center gap-1 text-yellow-600 hover:text-yellow-800">
                            <i data-feather="edit" class="w-4 h-4"></i>
                            Edit
                        </a>
                        <button onclick="confirmDeleteTugas({{ $t->id }})"
                            class="inline-flex items-center gap-1 text-red-600 hover:text-red-800">
                            <i data-feather="trash-2" class="w-4 h-4"></i>
                            Hapus
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $tugas->links() }}</div>

<form id="deleteForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

<script>
function confirmDeleteTugas(id) {
    Swal.fire({
        title: 'Hapus Tugas Rapor?',
        text: "Semua data rapor peserta dalam tugas ini akan dihapus secara permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteForm');
            form.action = `/admin/rapor-tugas/${id}`;
            form.submit();
        }
    });
}
</script>
@endsection