@extends('layouts.app')

@section('header')
Cetak Invoice
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
                Pilih jenis invoice dan periode pembayaran.
            </p>
        </div>

        {{-- FORM --}}
        <div class="space-y-5">

            {{-- JENIS INVOICE --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Jenis Invoice
                </label>
                <select id="jenis_invoice"
                        class="w-full bg-white border border-[#E3EEF0]
                               rounded-lg px-3 py-2">
                    <option value="sekolah">Sekolah</option>
                    <option value="home_private">Home Private</option>
                </select>
            </div>

            {{-- SEKOLAH --}}
            <div id="sekolah-wrapper">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Sekolah
                </label>
                <select id="sekolah_id"
                        class="w-full bg-white border border-[#E3EEF0]
                               rounded-lg px-3 py-2">
                    <option value="">-- Pilih Sekolah --</option>
                    @foreach($sekolahs as $s)
                        <option value="{{ $s->id }}">
                            {{ $s->nama_sekolah }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- HOME PRIVATE --}}
            <div id="home-wrapper" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Peserta Home Private
                </label>
                <select id="home_private_id"
                        class="w-full bg-white border border-[#E3EEF0]
                               rounded-lg px-3 py-2">
                    <option value="">-- Pilih Peserta --</option>
                    @foreach($homePrivates ?? [] as $hp)
                        <option value="{{ $hp->id }}">
                            {{ $hp->nama_peserta }}
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
                                   rounded-lg px-3 py-2">
                        @for($i=1;$i<=12;$i++)
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
                                  rounded-lg px-3 py-2">
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
const jenisSelect = document.getElementById('jenis_invoice');
const sekolahWrap = document.getElementById('sekolah-wrapper');
const homeWrap    = document.getElementById('home-wrapper');

jenisSelect.addEventListener('change', function () {
    if (this.value === 'home_private') {
        sekolahWrap.classList.add('hidden');
        homeWrap.classList.remove('hidden');
    } else {
        sekolahWrap.classList.remove('hidden');
        homeWrap.classList.add('hidden');
    }
});

function cetakInvoice() {

    const jenis   = jenisSelect.value; // sekolah | home_private
    const bulan   = document.getElementById('bulan').value;
    const tahun   = document.getElementById('tahun').value;
    const sekolah = document.getElementById('sekolah_id')?.value;
    const home    = document.getElementById('home_private_id')?.value;

    /* ================= VALIDASI ================= */
    if (!bulan || !tahun) {
        Swal.fire('Error', 'Bulan dan tahun wajib diisi', 'warning');
        return;
    }

    if (jenis === 'sekolah' && !sekolah) {
        Swal.fire('Error', 'Sekolah wajib dipilih', 'warning');
        return;
    }

    if (jenis === 'home_private' && !home) {
        Swal.fire('Error', 'Peserta Home Private wajib dipilih', 'warning');
        return;
    }

    /* ================= CHECK DATA ================= */
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('jenis_peserta', jenis); // ✅ FIXED
    formData.append('bulan', bulan);
    formData.append('tahun', tahun);

    if (jenis === 'sekolah') {
        formData.append('sekolah_id', sekolah);
    } else {
        formData.append('home_private_id', home);
    }

    fetch('{{ route("pembayaran.invoice.check") }}', {
        method: 'POST',
        body: formData,
        headers: { 'Accept': 'application/json' }
    })
    .then(async res => {
        if (!res.ok) throw await res.json();

        /* ================= OPEN PDF ================= */
        const params = new URLSearchParams({
            jenis_peserta: jenis,
            bulan: bulan,
            tahun: tahun
        });

        if (jenis === 'sekolah') {
            params.append('sekolah_id', sekolah);
        } else {
            params.append('home_private_id', home);
        }

        window.open(
            '{{ route("pembayaran.invoice.pdf") }}?' + params.toString(),
            '_blank'
        );
    })
    .catch(err => {
        Swal.fire(
            'Tidak Bisa Cetak Invoice',
            err?.errors
                ? Object.values(err.errors).flat().join('\n')
                : 'Data pembayaran tidak tersedia',
            'error'
        );
    });
}
</script>
@endpush

