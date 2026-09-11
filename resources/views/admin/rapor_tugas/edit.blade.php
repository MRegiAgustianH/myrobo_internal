@extends('layouts.app')

@section('header')
<div class="flex items-center justify-between flex-wrap gap-4">
    <div class="flex items-center gap-3">
        <div class="p-2 bg-[#8FBFC2]/20 rounded-xl">
            <i data-feather="edit" class="w-5 h-5 text-[#6FA9AD]"></i>
        </div>
        <div>
            <h1 class="text-lg font-semibold text-gray-800">Edit Tugas Rapor</h1>
            <p class="text-xs text-gray-500">Ubah instruktur, deadline, atau cabang</p>
        </div>
    </div>
    <a href="{{ route('admin.rapor-tugas.show', $raporTugas->id) }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
        <i data-feather="arrow-left" class="w-4 h-4"></i>
        Kembali
    </a>
</div>
@endsection

@section('content')

@if($errors->any())
<div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
    <ul class="list-disc list-inside space-y-1">
        @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
    </ul>
</div>
@endif

<div class="bg-white rounded-2xl shadow-sm border max-w-2xl mx-auto">
    <div class="p-6 border-b">
        <div class="space-y-1 text-sm text-gray-600">
            <p><strong>Sekolah:</strong> {{ $raporTugas->sekolah->nama_sekolah }}</p>
            <p><strong>Semester:</strong> {{ $raporTugas->semester->nama_semester }}</p>
            <p class="text-xs text-gray-400">Sekolah & semester tidak dapat diubah karena rapor sudah digenerate.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.rapor-tugas.update', $raporTugas->id) }}" class="p-6 space-y-5">
        @csrf
        @method('PUT')

        @if(auth()->user()->role === 'superadmin')
        <div>
            <label class="text-xs font-medium text-gray-600 mb-1 flex items-center gap-1">
                <i data-feather="map-pin" class="w-3 h-3"></i> Cabang
            </label>
            <select name="cabang_id" class="w-full border rounded-lg px-3 py-2 text-sm">
                <option value="">Pilih Cabang</option>
                @foreach($cabangs as $cb)
                    <option value="{{ $cb->id }}" @selected(old('cabang_id', $raporTugas->cabang_id) == $cb->id)>{{ $cb->nama_cabang }}</option>
                @endforeach
            </select>
        </div>
        @else
        <input type="hidden" name="cabang_id" value="{{ auth()->user()->cabang_id }}">
        @endif

        <div>
            <label class="text-xs font-medium text-gray-600 mb-1 flex items-center gap-1">
                <i data-feather="user" class="w-3 h-3"></i> Instruktur
            </label>
            <select name="instruktur_id" required class="w-full border rounded-lg px-3 py-2 text-sm">
                <option value="">Pilih Instruktur</option>
                @foreach($instrukturs as $u)
                    <option value="{{ $u->id }}" @selected(old('instruktur_id', $raporTugas->instruktur_id) == $u->id)>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-xs font-medium text-gray-600 mb-1 flex items-center gap-1">
                <i data-feather="clock" class="w-3 h-3"></i> Deadline
            </label>
            <input type="date" name="deadline" value="{{ old('deadline', $raporTugas->deadline?->format('Y-m-d')) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>

        <div class="flex gap-3 pt-2">
            <button class="flex-1 bg-[#8FBFC2] hover:bg-[#6FA9AD] text-white py-2.5 rounded-xl text-sm font-medium">
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.rapor-tugas.show', $raporTugas->id) }}"
               class="flex-1 border rounded-xl py-2.5 text-center text-sm hover:bg-gray-50">
                Batal
            </a>
        </div>
    </form>
</div>

@endsection