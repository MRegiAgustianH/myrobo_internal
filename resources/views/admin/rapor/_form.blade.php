@php
    $rapor = $rapor ?? null;
@endphp

<div class="space-y-10">

{{-- ================= DATA RAPOR ================= --}}
<div class="bg-white border rounded-2xl p-6 shadow-sm">
    <h2 class="text-base font-semibold mb-6 text-gray-800">
        Data Rapor
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Sekolah --}}
        <div>
            <label class="block text-sm font-medium mb-1">Sekolah</label>
            <select name="sekolah_id"
                    class="w-full border rounded-lg px-4 py-2 text-sm"
                    required>
                <option value="">-- Pilih Sekolah --</option>
                @foreach($sekolahs as $sekolah)
                    <option value="{{ $sekolah->id }}"
                        @selected(old('sekolah_id', $rapor->sekolah_id ?? '') == $sekolah->id)>
                        {{ $sekolah->nama_sekolah }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Peserta --}}
        <div>
            <label class="block text-sm font-medium mb-1">Peserta</label>
            <select name="peserta_id"
                    class="w-full border rounded-lg px-4 py-2 text-sm"
                    required>
                <option value="">-- Pilih Peserta --</option>
                @foreach($pesertas as $peserta)
                    <option value="{{ $peserta->id }}"
                        @selected(old('peserta_id', $rapor->peserta_id ?? '') == $peserta->id)>
                        {{ $peserta->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Semester --}}
        <div>
            <label class="block text-sm font-medium mb-1">Semester</label>
            <select name="semester_id"
                    class="w-full border rounded-lg px-4 py-2 text-sm"
                    required>
                <option value="">-- Pilih Semester --</option>
                @foreach($semesters as $semester)
                    <option value="{{ $semester->id }}"
                        @selected(old('semester_id', $rapor->semester_id ?? '') == $semester->id)>
                        {{ $semester->nama_semester }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Materi --}}
        <div>
            <label class="block text-sm font-medium mb-1">Materi</label>
            <input type="text"
                   name="materi"
                   value="{{ old('materi', $rapor->materi ?? '') }}"
                   class="w-full border rounded-lg px-4 py-2 text-sm"
                   required>
        </div>

        {{-- Nilai Akhir --}}
        <div>
            <label class="block text-sm font-medium mb-1">Nilai Akhir</label>
            <select name="nilai_akhir"
                    class="w-full border rounded-lg px-4 py-2 text-sm"
                    required>
                <option value="A" @selected(old('nilai_akhir', $rapor->nilai_akhir ?? '')=='A')>A</option>
                <option value="B" @selected(old('nilai_akhir', $rapor->nilai_akhir ?? '')=='B')>B</option>
                <option value="C" @selected(old('nilai_akhir', $rapor->nilai_akhir ?? '')=='C')>C</option>
            </select>
        </div>

        {{-- Kesimpulan --}}
        <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-1">Kesimpulan</label>
            <textarea name="kesimpulan"
                      rows="3"
                      class="w-full border rounded-lg px-4 py-2 text-sm">{{ old('kesimpulan', $rapor->kesimpulan ?? '') }}</textarea>
        </div>
    </div>
</div>

{{-- ================= PILIH SEMUA ================= --}}
<div class="bg-indigo-50 border border-indigo-200 rounded-2xl p-5">
    <h3 class="font-semibold text-indigo-700 mb-3 text-sm">
        Penilaian Cepat (Semua Indikator)
    </h3>

    <div class="flex flex-wrap gap-6 text-sm">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="pilih_semua_global"
                   onclick="pilihSemuaGlobal('C')">
            <span>Cukup Semua</span>
        </label>

        <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="pilih_semua_global"
                   onclick="pilihSemuaGlobal('B')">
            <span>Baik Semua</span>
        </label>

        <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="pilih_semua_global"
                   onclick="pilihSemuaGlobal('SB')">
            <span>Sangat Baik Semua</span>
        </label>
    </div>
</div>

{{-- ================= PENILAIAN KOMPETENSI ================= --}}
<div class="space-y-8">

@foreach($kompetensis as $kompetensi)
<div class="bg-white border rounded-2xl p-6 shadow-sm">

    <h4 class="font-semibold text-gray-800 mb-5">
        {{ $kompetensi->nama_kompetensi }}
    </h4>

    <div class="space-y-5">
    @foreach($kompetensi->indikatorKompetensis as $indikator)

        <div class="flex flex-col md:flex-row md:items-center gap-4">

            <div class="md:flex-1 text-sm text-gray-700">
                {{ $indikator->nama_indikator }}
            </div>

            <div class="flex gap-6 text-sm">
                @php
                    $nilaiLama = old(
                        "nilai.$indikator->id",
                        data_get(
                            optional($rapor?->nilai)
                                ->firstWhere('indikator_kompetensi_id', $indikator->id),
                            'nilai'
                        )
                    );
                @endphp

                <label class="flex items-center gap-1">
                    <input type="radio"
                           name="nilai[{{ $indikator->id }}]"
                           value="C"
                           class="nilai-indikator"
                           @checked($nilaiLama === 'C')
                           required>
                    Cukup
                </label>

                <label class="flex items-center gap-1">
                    <input type="radio"
                           name="nilai[{{ $indikator->id }}]"
                           value="B"
                           class="nilai-indikator"
                           @checked($nilaiLama === 'B')>
                    Baik
                </label>

                <label class="flex items-center gap-1">
                    <input type="radio"
                           name="nilai[{{ $indikator->id }}]"
                           value="SB"
                           class="nilai-indikator"
                           @checked($nilaiLama === 'SB')>
                    Sangat Baik
                </label>
            </div>
        </div>

    @endforeach
    </div>
</div>
@endforeach

</div>

{{-- ================= SCRIPT ================= --}}
<script>
function pilihSemuaGlobal(nilai) {
    document.querySelectorAll('.nilai-indikator').forEach(radio => {
        if (radio.value === nilai) {
            radio.checked = true;
        }
    });
}
</script>
