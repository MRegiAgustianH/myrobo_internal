@extends('layouts.app')

@section('header')
Cetak Invoice Sekolah
@endsection

@section('content')

<div class="max-w-xl mx-auto">

    {{-- CARD --}}
    <div class="bg-[#F6FAFB] border border-[#E3EEF0]
                rounded-2xl shadow-sm p-6">

        {{-- TITLE --}}
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-800">
                Cetak Invoice Pembayaran
            </h2>
            <p class="text-sm text-gray-500">
                Pilih sekolah dan periode untuk menghasilkan invoice pembayaran.
            </p>
        </div>

        {{-- FORM --}}
        <div class="space-y-5">

            {{-- SEKOLAH --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Sekolah
                </label>
                <select id="sekolah_id"
                    class="w-full bg-white border border-[#E3EEF0]
                           rounded-lg px-3 py-2 text-gray-800"
                    required>
                    <option value="">-- Pilih Sekolah --</option>
                    @foreach($sekolahs as $s)
                        <option value="{{ $s->id }}">
                            {{ $s->nama_sekolah }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- BULAN & TAHUN --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Bulan
                    </label>
                    <select id="bulan"
                        class="w-full bg-white border border-[#E3EEF0]
                               rounded-lg px-3 py-2"
                        required>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}">
                                {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tahun
                    </label>
                    <input type="number"
                           id="tahun"
                           value="{{ now()->year }}"
                           class="w-full bg-white border border-[#E3EEF0]
                                  rounded-lg px-3 py-2"
                           required>
                </div>
            </div>

            {{-- ACTION --}}
            <div class="pt-6 flex justify-end">
                <button type="button"
                        onclick="cetakInvoice()"
                        class="inline-flex items-center gap-2
                               bg-gradient-to-r from-[#8FBFC2] to-[#7AAEB1]
                               hover:from-[#7AAEB1] hover:to-[#6FA9AD]
                               text-gray-900 font-semibold
                               px-6 py-2.5 rounded-xl
                               shadow-sm transition">
                    <i data-feather="file-text" class="w-4 h-4"></i>
                    Cetak Invoice
                </button>
            </div>

        </div>

    </div>

</div>

@endsection

@push('scripts')
<script>
function cetakInvoice() {

    const sekolah = document.getElementById('sekolah_id').value;
    const bulan   = document.getElementById('bulan').value;
    const tahun   = document.getElementById('tahun').value;

    if (!sekolah || !bulan || !tahun) {
        Swal.fire({
            icon: 'warning',
            title: 'Data belum lengkap',
            text: 'Sekolah, bulan, dan tahun wajib dipilih'
        });
        return;
    }

    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('sekolah_id', sekolah);
    formData.append('bulan', bulan);
    formData.append('tahun', tahun);

    // 🔎 CEK DATA PEMBAYARAN DULU
    fetch('{{ route("pembayaran.invoice.check") }}', {
    method: 'POST',
    body: formData,
    headers: {
        'Accept': 'application/json'
    }
})
.then(async response => {
    if (!response.ok) {
        const data = await response.json();
        throw data;
    }

    // ✅ DATA ADA → BUKA PDF
    window.open(
        '{{ route("pembayaran.invoice.pdf") }}' +
        `?sekolah_id=${sekolah}&bulan=${bulan}&tahun=${tahun}`,
        '_blank'
    );
})
.catch(error => {
    Swal.fire({
        icon: 'error',
        title: 'Tidak Bisa Cetak Invoice',
        text: error?.errors
            ? Object.values(error.errors).flat().join('\n')
            : 'Data pembayaran tidak tersedia'
    });
});

}
</script>
@endpush
