@php
    $rapor = $rapor ?? null;
    $readonly = $readonly ?? false;
@endphp

<div class="space-y-8">

@forelse($kompetensis as $kompetensi)
<div class="bg-white border rounded-2xl p-6 shadow-sm">

    <h4 class="font-semibold text-gray-800 mb-5">
        {{ $kompetensi->nama_kompetensi }}
    </h4>

    <div class="space-y-5">
    @forelse($kompetensi->indikatorKompetensis as $indikator)

        @php
            $nilaiLama = old(
                "nilai.$indikator->id",
                optional($rapor?->nilaiRapors
                    ->firstWhere('indikator_kompetensi_id', $indikator->id)
                )->nilai
            );
        @endphp

        <div class="flex flex-col md:flex-row md:items-center gap-4">
            <div class="md:flex-1 text-sm text-gray-700">
                {{ $indikator->nama_indikator }}
            </div>

            <div class="flex gap-6 text-sm">
                @foreach(['C'=>'Cukup','B'=>'Baik','SB'=>'Sangat Baik'] as $k => $label)
                    <label class="flex items-center gap-1">
                        <input type="radio"
                               name="nilai[{{ $indikator->id }}]"
                               value="{{ $k }}"
                               class="nilai-indikator"
                               {{ $readonly ? 'disabled' : '' }}
                               @checked($nilaiLama === $k)
                               required>
                        {{ $label }}
                    </label>
                @endforeach
            </div>
        </div>

    @empty
        <div class="text-sm text-gray-500 italic">
            Belum ada indikator untuk kompetensi ini
        </div>
    @endforelse
    </div>

</div>
@empty
<div class="text-sm text-gray-500 italic">
    Tidak ada kompetensi pada materi ini
</div>
@endforelse

</div>
