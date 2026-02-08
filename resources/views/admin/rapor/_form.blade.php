@php
    $rapor = $rapor ?? null;
    $readonly = $readonly ?? false;
@endphp

{{-- ================= CATATAN REVISI ================= --}}
@if($rapor && ($rapor->status === 'revision' || $rapor->catatan_revisi))
<div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-5 text-sm">
    <div class="flex items-start gap-3">
        <i data-feather="alert-triangle" class="w-5 h-5 text-red-600 mt-0.5"></i>
        <div>
            <p class="font-semibold text-red-700 mb-1">
                Catatan Revisi dari Admin
            </p>
            <p class="text-gray-700 leading-relaxed">
                {{ $rapor->catatan_revisi }}
            </p>
        </div>
    </div>
</div>
@endif

<div class="space-y-10">

{{-- ================= DATA RAPOR ================= --}}
<div class="bg-white border rounded-3xl p-6 shadow-sm">
    <div class="flex items-center gap-2 mb-6">
        <i data-feather="file-text" class="w-5 h-5 text-gray-600"></i>
        <h2 class="text-base font-semibold text-gray-800">
            Data Rapor
        </h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Materi Acuan --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Materi Acuan
                <span class="text-xs text-gray-400">(master kompetensi)</span>
            </label>
            <select id="materi_id" name="materi_id"
                class="w-full border rounded-xl px-4 py-2 text-sm
                       focus:ring focus:ring-[#8FBFC2]/40"
                onchange="loadKompetensi(this.value)"
                {{ $readonly ? 'disabled' : '' }}
                required>
                <option value="">Pilih Materi</option>
                @foreach($materis as $m)
                    <option value="{{ $m->id }}"
                        @selected(old('materi_id', $rapor->materi_id ?? '') == $m->id)>
                        {{ $m->nama_materi }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Nilai Akhir --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Nilai Akhir
            </label>
            <select name="nilai_akhir"
                class="w-full border rounded-xl px-4 py-2 text-sm
                       focus:ring focus:ring-[#8FBFC2]/40"
                {{ $readonly ? 'disabled' : '' }}
                required>
                <option value="">Pilih Nilai</option>
                @foreach(['A','B','C'] as $n)
                    <option value="{{ $n }}"
                        @selected(old('nilai_akhir', $rapor->nilai_akhir ?? '') == $n)>
                        {{ $n }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Materi di Rapor --}}
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Materi pada Rapor
                <span class="text-xs text-gray-400">(narasi yang muncul di rapor)</span>
            </label>
            <textarea name="materi"
                rows="2"
                class="w-full border rounded-xl px-4 py-2 text-sm
                       focus:ring focus:ring-[#8FBFC2]/40"
                {{ $readonly ? 'readonly' : '' }}
                required>{{ old('materi', $rapor->materi ?? '') }}</textarea>
        </div>

        {{-- Kesimpulan --}}
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Kesimpulan
            </label>
            <textarea name="kesimpulan"
                rows="3"
                class="w-full border rounded-xl px-4 py-2 text-sm
                       focus:ring focus:ring-[#8FBFC2]/40"
                {{ $readonly ? 'readonly' : '' }}>{{ old('kesimpulan', $rapor->kesimpulan ?? '') }}</textarea>
        </div>

    </div>
</div>

{{-- ================= PENILAIAN CEPAT ================= --}}
@if(! $readonly)
<div class="bg-indigo-50 border border-indigo-200 rounded-3xl p-6">
    <div class="flex items-center gap-2 mb-4">
        <i data-feather="zap" class="w-5 h-5 text-indigo-600"></i>
        <h3 class="font-semibold text-indigo-700 text-sm">
            Penilaian Cepat
        </h3>
    </div>

    <div class="flex flex-wrap gap-3">
        <button type="button"
            onclick="pilihSemua('C')"
            class="px-4 py-2 rounded-xl text-sm font-medium
                   bg-gray-200 hover:bg-gray-300 transition">
            Cukup Semua
        </button>

        <button type="button"
            onclick="pilihSemua('B')"
            class="px-4 py-2 rounded-xl text-sm font-medium
                   bg-yellow-100 text-yellow-800 hover:bg-yellow-200 transition">
            Baik Semua
        </button>

        <button type="button"
            onclick="pilihSemua('SB')"
            class="px-4 py-2 rounded-xl text-sm font-medium
                   bg-green-100 text-green-800 hover:bg-green-200 transition">
            Sangat Baik Semua
        </button>
    </div>
</div>
@endif

{{-- ================= PENILAIAN DETAIL ================= --}}
<div id="penilaian-container" class="space-y-6">
    @includeWhen(
        !empty($kompetensis),
        'admin.rapor._kompetensi_indikator',
        ['kompetensis' => $kompetensis, 'rapor' => $rapor, 'readonly' => $readonly]
    )
</div>

</div>

{{-- ================= SCRIPT ================= --}}
<script>
function loadKompetensi(materiId) {
    const box = document.getElementById('penilaian-container');

    if (!materiId) {
        box.innerHTML =
            '<div class="text-sm italic text-gray-500">Silakan pilih materi acuan untuk menampilkan kompetensi.</div>';
        return;
    }

    box.innerHTML =
        '<div class="text-sm text-gray-500">Memuat kompetensi...</div>';

    fetch(`/rapor/materi/${materiId}/kompetensi`)
        .then(res => {
            if (!res.ok) throw new Error();
            return res.text();
        })
        .then(html => {
            box.innerHTML = html;
        })
        .catch(() => {
            box.innerHTML =
                '<div class="text-sm text-red-500">Gagal memuat kompetensi.</div>';
        });
}

function pilihSemua(nilai) {
    document.querySelectorAll('.nilai-indikator').forEach(radio => {
        if (radio.disabled) return;
        if (radio.value === nilai) {
            radio.checked = true;
        }
    });
}
</script>
