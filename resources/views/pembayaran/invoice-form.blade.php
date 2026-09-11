@extends('layouts.app')

@section('header')
Cetak Invoice
@endsection

@section('content')

<div class="max-w-xl mx-auto">
    <div class="bg-[#F6FAFB] border border-[#E3EEF0] rounded-2xl shadow-sm p-6">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-800">Cetak Invoice Pembayaran</h2>
            <p class="text-sm text-gray-500">Pilih jenis invoice dan periode pembayaran.</p>
        </div>

        <div class="space-y-5">

            {{-- MODE --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mode</label>
                <div class="flex gap-2">
                    <label class="flex items-center gap-2 px-4 py-2 border rounded-lg cursor-pointer bg-white">
                        <input type="radio" name="mode_radio" value="bulanan" checked onchange="toggleInvoiceMode()">
                        Bulanan
                    </label>
                    <label class="flex items-center gap-2 px-4 py-2 border rounded-lg cursor-pointer bg-white">
                        <input type="radio" name="mode_radio" value="periode" onchange="toggleInvoiceMode()">
                        Per Periode
                    </label>
                </div>
            </div>

            {{-- JENIS INVOICE --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Invoice</label>
                <select id="jenis_invoice" class="w-full bg-white border border-[#E3EEF0] rounded-lg px-3 py-2">
                    <option value="sekolah">Sekolah</option>
                    <option value="home_private">Home Private</option>
                </select>
            </div>

            {{-- SEKOLAH --}}
            <div id="sekolah-wrapper">
                <label class="block text-sm font-medium text-gray-700 mb-1">Sekolah</label>
                <select id="sekolah_id" class="w-full bg-white border border-[#E3EEF0] rounded-lg px-3 py-2">
                    <option value="">-- Pilih Sekolah --</option>
                    @foreach($sekolahs as $s)
                        <option value="{{ $s->id }}">{{ $s->nama_sekolah }}</option>
                    @endforeach
                </select>
            </div>

            {{-- HOME PRIVATE --}}
            <div id="home-wrapper" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Peserta Home Private</label>
                <select id="home_private_id" class="w-full bg-white border border-[#E3EEF0] rounded-lg px-3 py-2">
                    <option value="">-- Pilih Peserta --</option>
                    @foreach($homePrivates ?? [] as $hp)
                        <option value="{{ $hp->id }}">{{ $hp->nama_peserta }}</option>
                    @endforeach
                </select>
            </div>

            {{-- BULAN & TAHUN (MODE BULANAN) --}}
            <div id="bulanan-fields" class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                    <select id="bulan" class="w-full bg-white border border-[#E3EEF0] rounded-lg px-3 py-2">
                        @for($i=1;$i<=12;$i++)
                            <option value="{{ $i }}">{{ \Carbon\Carbon::create()->month((int) $i)->translatedFormat('F') }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                    <input type="number" id="tahun" value="{{ now()->year }}" class="w-full bg-white border border-[#E3EEF0] rounded-lg px-3 py-2">
                </div>
            </div>

            {{-- PERIODE (MODE PERIODE) --}}
            <div id="periode-fields" class="hidden space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Ajaran</label>
                    <input type="text" id="tahun_ajaran" value="{{ date('Y') . '/' . (date('Y') + 1) }}" placeholder="2025/2026" class="w-full bg-white border border-[#E3EEF0] rounded-lg px-3 py-2">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                        <select id="semester" class="w-full bg-white border border-[#E3EEF0] rounded-lg px-3 py-2">
                            <option value="ganjil">Ganjil</option>
                            <option value="genap">Genap</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Periode</label>
                        <select id="periode" class="w-full bg-white border border-[#E3EEF0] rounded-lg px-3 py-2">
                            @for($i=1;$i<=6;$i++)
                                <option value="{{ $i }}">Periode {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>

            {{-- ACTION --}}
            <div class="pt-6 flex justify-end">
                <button type="button" onclick="cetakInvoice()"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-[#8FBFC2] to-[#7AAEB1] hover:from-[#7AAEB1] hover:to-[#6FA9AD] text-gray-900 font-semibold px-6 py-2.5 rounded-xl shadow-sm transition">
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

function toggleInvoiceMode() {
    const mode = document.querySelector('input[name=mode_radio]:checked').value;
    document.getElementById('bulanan-fields').classList.toggle('hidden', mode === 'periode');
    document.getElementById('periode-fields').classList.toggle('hidden', mode === 'bulanan');
}

function cetakInvoice() {
    const mode  = document.querySelector('input[name=mode_radio]:checked').value;
    const jenis = jenisSelect.value;
    const sekolah = document.getElementById('sekolah_id')?.value;
    const home    = document.getElementById('home_private_id')?.value;

    // VALIDASI
    if (jenis === 'sekolah' && !sekolah) {
        Swal.fire('Error', 'Sekolah wajib dipilih', 'warning');
        return;
    }
    if (jenis === 'home_private' && !home) {
        Swal.fire('Error', 'Peserta Home Private wajib dipilih', 'warning');
        return;
    }

    // BUILD PARAMS
    const params = { jenis_peserta: jenis, mode: mode };

    if (mode === 'bulanan') {
        params.bulan = document.getElementById('bulan').value;
        params.tahun = document.getElementById('tahun').value;
        if (!params.bulan || !params.tahun) {
            Swal.fire('Error', 'Bulan dan tahun wajib diisi', 'warning');
            return;
        }
    } else {
        params.tahun_ajaran = document.getElementById('tahun_ajaran').value;
        params.semester = document.getElementById('semester').value;
        params.periode = document.getElementById('periode').value;
        if (!params.tahun_ajaran) {
            Swal.fire('Error', 'Tahun ajaran wajib diisi', 'warning');
            return;
        }
    }

    if (jenis === 'sekolah') {
        params.sekolah_id = sekolah;
    } else {
        params.home_private_id = home;
    }

    // CHECK DATA
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    Object.entries(params).forEach(([k, v]) => formData.append(k, v));

    fetch('{{ route("pembayaran.invoice.check") }}', {
        method: 'POST',
        body: formData,
        headers: { 'Accept': 'application/json' }
    })
    .then(async res => {
        if (!res.ok) throw await res.json();
        const qs = new URLSearchParams(params).toString();
        window.open('{{ route("pembayaran.invoice.pdf") }}?' + qs, '_blank');
    })
    .catch(err => {
        Swal.fire(
            'Tidak Bisa Cetak Invoice',
            err?.errors ? Object.values(err.errors).flat().join('\n') : 'Data pembayaran tidak tersedia',
            'error'
        );
    });
}
</script>
@endpush