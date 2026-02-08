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
          class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @csrf

        {{-- SEKOLAH --}}
        <div>
            <label class="text-xs font-medium text-gray-600 mb-1 flex items-center gap-1">
                <i data-feather="home" class="w-3 h-3"></i> Sekolah
            </label>
            <select name="sekolah_id" required
                class="w-full border rounded-lg px-3 py-2 text-sm
                       {{ $errors->has('sekolah_id') ? 'border-red-400 bg-red-50' : '' }}">
                <option value="">Pilih Sekolah</option>
                @foreach(\App\Models\Sekolah::orderBy('nama_sekolah')->get() as $s)
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
                @foreach(\App\Models\User::where('role','instruktur')->get() as $u)
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

    <a href="{{ route('admin.rapor-tugas.show',$t->id) }}"
       class="w-full inline-flex justify-center items-center gap-2
              bg-indigo-600 hover:bg-indigo-700
              text-white py-2 rounded-xl text-xs font-semibold">
        <i data-feather="eye" class="w-4 h-4"></i>
        Detail
    </a>

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
                <th class="px-6 py-3 text-center">Aksi</th>
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
                    <a href="{{ route('admin.rapor-tugas.show',$t->id) }}"
                       class="inline-flex items-center gap-1
                              text-indigo-600 hover:text-indigo-800">
                        <i data-feather="eye" class="w-4 h-4"></i>
                        Detail
                    </a>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
