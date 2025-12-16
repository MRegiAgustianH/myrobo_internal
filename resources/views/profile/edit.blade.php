@extends('layouts.app')

@section('header')
Profil Pengguna
@endsection

@section('content')

<div class="space-y-6 max-w-3xl">

    {{-- UPDATE PROFILE --}}
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="font-semibold mb-4">Informasi Profil</h3>
        @include('profile.partials.update-profile-information-form')
    </div>

    {{-- UPDATE PASSWORD --}}
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="font-semibold mb-4">Ubah Password</h3>
        @include('profile.partials.update-password-form')
    </div>

    {{-- DELETE ACCOUNT --}}
    <div class="bg-white p-6 rounded-lg shadow border border-red-200">
        <h3 class="font-semibold mb-4 text-red-600">Hapus Akun</h3>
        @include('profile.partials.delete-user-form')
    </div>

</div>

@endsection
