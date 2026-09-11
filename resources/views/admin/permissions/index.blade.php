@extends('layouts.app')

@section('header')
Pengaturan Hak Akses & Maintenance
@endsection

@section('content')

@if(session('success'))
<div class="mb-4 p-3 rounded-lg bg-green-100 text-green-700 border border-green-200">
    {{ session('success') }}
</div>
@endif

{{-- ================= MAINTENANCE MODE ================= --}}
<div class="bg-white rounded-2xl shadow-sm border mb-8 overflow-hidden">
    <div class="p-6 border-b flex items-center gap-3">
        <div class="p-2 bg-orange-50 rounded-lg">
            <i data-feather="power" class="w-5 h-5 text-orange-600"></i>
        </div>
        <div>
            <h2 class="font-semibold text-gray-800">Maintenance Mode</h2>
            <p class="text-xs text-gray-500">Nonaktifkan modul yang masih under maintenance. Modul yang dimatikan tidak bisa diakses oleh semua user kecuali superadmin.</p>
        </div>
    </div>

    <div class="divide-y">
        @foreach($modules as $moduleKey => $moduleLabel)
        @php $setting = $moduleSettings[$moduleKey] ?? null; $isActive = $setting ? $setting->is_active : true; @endphp
        <div class="flex items-center justify-between p-4">
            <div>
                <p class="font-medium text-sm text-gray-800">{{ $moduleLabel }}</p>
                <p class="text-xs text-gray-400">module: {{ $moduleKey }}</p>
                @if(!$isActive && $setting?->maintenance_message)
                <p class="text-xs text-orange-600 mt-1">{{ $setting->maintenance_message }}</p>
                @endif
            </div>
            <form method="POST" action="{{ route('permissions.toggle-module') }}" class="flex items-center gap-3">
                @csrf
                <input type="hidden" name="module" value="{{ $moduleKey }}">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ $isActive ? 'checked' : '' }}
                           onchange="this.form.submit()"
                           class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-green-500 transition-all"></div>
                    <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full transition-all peer-checked:translate-x-5"></div>
                </label>
                <span class="text-xs font-medium {{ $isActive ? 'text-green-600' : 'text-red-600' }}">
                    {{ $isActive ? 'Aktif' : 'Maintenance' }}
                </span>
            </form>
        </div>
        @endforeach
    </div>
</div>

{{-- ================= HAK AKSES CRUD ================= --}}
<div class="bg-white rounded-2xl shadow-sm border overflow-hidden mb-4">
    <div class="p-6 border-b flex items-center gap-3">
        <div class="p-2 bg-indigo-50 rounded-lg">
            <i data-feather="shield" class="w-5 h-5 text-indigo-600"></i>
        </div>
        <div>
            <h2 class="font-semibold text-gray-800">Hak Akses CRUD per Role</h2>
            <p class="text-xs text-gray-500">Atur hak akses Create, Read, Update, Delete untuk setiap role</p>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('permissions.update') }}">
@csrf
@method('PUT')

<div class="bg-white rounded-2xl shadow-sm border overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-[#F6FAFB] border-b">
            <tr>
                <th class="px-4 py-3 text-left sticky left-0 bg-[#F6FAFB]">Module</th>
                @foreach($roles as $roleKey => $roleLabel)
                <th class="px-4 py-3 text-center" colspan="4">
                    {{ $roleLabel }}
                    @if($roleKey === 'superadmin')
                    <span class="text-xs text-gray-400">(selalu full)</span>
                    @endif
                </th>
                @endforeach
            </tr>
            <tr class="border-b">
                <th class="px-4 py-2"></th>
                @foreach($roles as $roleKey => $roleLabel)
                <th class="px-1 py-2 text-center text-[10px] uppercase text-gray-400">C</th>
                <th class="px-1 py-2 text-center text-[10px] uppercase text-gray-400">R</th>
                <th class="px-1 py-2 text-center text-[10px] uppercase text-gray-400">U</th>
                <th class="px-1 py-2 text-center text-[10px] uppercase text-gray-400">D</th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y">
            @foreach($modules as $moduleKey => $moduleLabel)
            <tr class="hover:bg-gray-50 {{ !module_active($moduleKey) ? 'opacity-50' : '' }}">
                <td class="px-4 py-3 font-medium whitespace-nowrap sticky left-0 bg-white">
                    {{ $moduleLabel }}
                    @if(!module_active($moduleKey))
                    <span class="text-[10px] text-orange-600">(maintenance)</span>
                    @endif
                </td>
                @foreach($roles as $roleKey => $roleLabel)
                    @php $p = $perms["{$roleKey}.{$moduleKey}"] ?? null @endphp
                    @php $disabled = $roleKey === 'superadmin' @endphp
                    <td class="px-1 py-3 text-center">
                        <input type="checkbox" name="{{ $roleKey }}.{{ $moduleKey }}.can_create" {{ $disabled || ($p?->can_create ?? false) ? 'checked' : '' }} {{ $disabled ? 'disabled' : '' }} class="rounded">
                    </td>
                    <td class="px-1 py-3 text-center">
                        <input type="checkbox" name="{{ $roleKey }}.{{ $moduleKey }}.can_read" {{ $disabled || ($p?->can_read ?? true) ? 'checked' : '' }} {{ $disabled ? 'disabled' : '' }} class="rounded">
                    </td>
                    <td class="px-1 py-3 text-center">
                        <input type="checkbox" name="{{ $roleKey }}.{{ $moduleKey }}.can_update" {{ $disabled || ($p?->can_update ?? false) ? 'checked' : '' }} {{ $disabled ? 'disabled' : '' }} class="rounded">
                    </td>
                    <td class="px-1 py-3 text-center">
                        <input type="checkbox" name="{{ $roleKey }}.{{ $moduleKey }}.can_delete" {{ $disabled || ($p?->can_delete ?? false) ? 'checked' : '' }} {{ $disabled ? 'disabled' : '' }} class="rounded">
                    </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<p class="text-xs text-gray-400 mt-2">C = Create, R = Read, U = Update, D = Delete</p>

<div class="mt-6 text-right">
    <button class="bg-[#8FBFC2] hover:bg-[#6FA9AD] text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition shadow-sm">
        Simpan Hak Akses
    </button>
</div>

</form>

@endsection