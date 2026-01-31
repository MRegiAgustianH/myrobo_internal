@extends('layouts.app')

@section('header')
Tambah Rapor
@endsection

@section('content')

<form action="{{ route('rapor.store') }}" method="POST" class="space-y-6">
@csrf

@include('admin.rapor._form')

<div class="flex justify-end">
    <button class="bg-indigo-600 text-white px-6 py-2 rounded">
        Simpan Rapor
    </button>
</div>
</form>

@endsection
