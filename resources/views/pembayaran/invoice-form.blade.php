@extends('layouts.app')

@section('header')
Cetak Invoice Sekolah
@endsection

@section('content')

<form action="{{ route('pembayaran.invoice.pdf') }}" method="GET" target="_blank"
      class="bg-white p-6 rounded shadow max-w-xl">

    <div class="space-y-4">

        <div>
            <label class="block text-sm font-medium mb-1">Sekolah</label>
            <select name="sekolah_id" class="w-full border rounded px-3 py-2" required>
                <option value="">-- Pilih Sekolah --</option>
                @foreach($sekolahs as $s)
                    <option value="{{ $s->id }}">{{ $s->nama_sekolah }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Bulan</label>
                <select name="bulan" class="w-full border rounded px-3 py-2" required>
                    @for($i=1;$i<=12;$i++)
                        <option value="{{ $i }}">{{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}</option>
                    @endfor
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Tahun</label>
                <input type="number" name="tahun" value="{{ now()->year }}"
                       class="w-full border rounded px-3 py-2" required>
            </div>
        </div>

        <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
            📄 Cetak Invoice
        </button>

    </div>
</form>

@endsection
