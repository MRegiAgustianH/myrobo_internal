@php
    $showCabangFilter = auth()->user()->role === 'superadmin' && isset($cabangs);
@endphp

@if($showCabangFilter)
<div>
    <label class="block text-sm font-medium mb-1">Cabang</label>
    <select name="cabang_id"
            class="w-full bg-white border border-[#E3EEF0] rounded-lg px-3 py-2 text-sm"
            onchange="this.form.submit()">
        <option value="">-- Semua Cabang --</option>
        @foreach($cabangs as $cb)
            <option value="{{ $cb->id }}"
                {{ (string)request('cabang_id') === (string)$cb->id ? 'selected' : '' }}>
                {{ $cb->nama_cabang }} ({{ $cb->kode_cabang }})
            </option>
        @endforeach
    </select>
</div>
@endif