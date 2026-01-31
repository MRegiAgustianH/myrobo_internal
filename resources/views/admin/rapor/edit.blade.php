@extends('layouts.app')

@section('header')
Edit Rapor
@endsection

@section('content')

<form action="{{ route('rapor.update', $rapor->id) }}"
      method="POST" class="space-y-6">
@csrf
@method('PATCH')

@include('admin.rapor._form', ['rapor' => $rapor])

<div class="flex justify-end">
    <button class="bg-indigo-600 text-white px-6 py-2 rounded">
        Update Rapor
    </button>
</div>
</form>

@endsection
