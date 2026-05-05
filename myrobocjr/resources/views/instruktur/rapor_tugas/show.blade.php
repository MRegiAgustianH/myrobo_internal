@extends('layouts.app')

@section('header')
<div class="flex items-center gap-4">
    <div class="p-3 rounded-2xl bg-[#8FBFC2]/20">
        <i data-feather="layers" class="w-6 h-6 text-[#6FA9AD]"></i>
    </div>
    <div>
        <h1 class="text-xl font-semibold text-gray-800">
            Detail Tugas Rapor
        </h1>
        <p class="text-xs text-gray-500">
            Kelola dan lengkapi rapor peserta dengan rapi
        </p>
    </div>
</div>
@endsection

@section('content')

{{-- ================= INFO TUGAS ================= --}}
<div class="bg-white rounded-3xl shadow-sm border mb-8">
    <div class="p-6 grid grid-cols-1 sm:grid-cols-3 gap-6 text-sm">

        <div>
            <p class="text-xs text-gray-400 flex items-center gap-1">
                <i data-feather="home" class="w-3 h-3"></i>
                Sekolah
            </p>
            <p class="font-semibold text-gray-800">
                {{ $raporTugas->sekolah->nama_sekolah }}
            </p>
        </div>

        <div>
            <p class="text-xs text-gray-400 flex items-center gap-1">
                <i data-feather="calendar" class="w-3 h-3"></i>
                Semester
            </p>
            <p class="font-semibold text-gray-800">
                {{ $raporTugas->semester->nama_semester }}
            </p>
        </div>

        <div>
            <p class="text-xs text-gray-400 flex items-center gap-1">
                <i data-feather="clock" class="w-3 h-3"></i>
                Deadline
            </p>
            <p class="font-semibold
                {{ $raporTugas->deadline &&
                   \Carbon\Carbon::parse($raporTugas->deadline)->isPast()
                    ? 'text-red-600'
                    : 'text-gray-800' }}">
                {{ $raporTugas->deadline
                    ? \Carbon\Carbon::parse($raporTugas->deadline)->format('d M Y')
                    : '—' }}
            </p>
        </div>

    </div>
</div>

{{-- ================================================= --}}
{{-- ================= MOBILE / TABLET =============== --}}
{{-- ================================================= --}}
<div class="space-y-5 lg:hidden">

@forelse($rapors as $rapor)
<div class="bg-white border rounded-2xl p-5 shadow-sm">

    {{-- HEADER --}}
    <div class="flex items-start justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-[#8FBFC2]/20
                        flex items-center justify-center
                        text-sm font-semibold text-[#6FA9AD]">
                {{ strtoupper(substr($rapor->peserta->nama,0,1)) }}
            </div>
            <div>
                <p class="font-medium text-gray-800 leading-tight">
                    {{ $rapor->peserta->nama }}
                </p>
                <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[11px] font-medium
                    {{
                        $rapor->status === 'approved' ? 'bg-green-100 text-green-700' :
                        ($rapor->status === 'submitted' ? 'bg-blue-100 text-blue-700' :
                        ($rapor->status === 'revision' ? 'bg-yellow-100 text-yellow-700' :
                        'bg-gray-200 text-gray-700'))
                    }}">
                    {{ ucfirst($rapor->status) }}
                </span>
            </div>
        </div>
    </div>

    {{-- ACTIONS --}}
    <div class="space-y-2">

        {{-- PRIMARY --}}
        <a href="{{ route('instruktur.rapor.edit', [$raporTugas->id, $rapor->peserta->id]) }}"
           class="w-full h-11 inline-flex items-center justify-center gap-2
                  rounded-xl text-sm font-semibold
                  bg-[#8FBFC2] hover:bg-[#6FA9AD]
                  text-gray-900 transition">

            @if(in_array($rapor->status, ['draft','revision']))
                <i data-feather="edit-3" class="w-4 h-4"></i>
                Isi Rapor
            @else
                <i data-feather="eye" class="w-4 h-4"></i>
                Lihat Rapor
            @endif
        </a>

        {{-- SECONDARY --}}
        @if($rapor->status === 'approved')
        <a href="{{ route('instruktur.rapor.cetak', $rapor->id) }}"
           target="_blank"
           class="w-full h-11 inline-flex items-center justify-center gap-2
                  rounded-xl text-sm font-medium
                  border border-indigo-200
                  text-indigo-600 hover:bg-indigo-50 transition">
            <i data-feather="printer" class="w-4 h-4"></i>
            Cetak Rapor
        </a>
        @endif

    </div>

</div>
@empty
<div class="text-center text-sm text-gray-400 py-12">
    <i data-feather="inbox" class="w-7 h-7 mx-auto mb-2"></i>
    Tidak ada rapor pada tugas ini
</div>
@endforelse

</div>

{{-- ================================================= --}}
{{-- =================== DESKTOP ===================== --}}
{{-- ================================================= --}}
<div class="hidden lg:block bg-white rounded-3xl shadow-sm border overflow-hidden">

    <div class="px-6 py-5 border-b flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i data-feather="users" class="w-4 h-4 text-gray-500"></i>
            <h2 class="font-semibold text-gray-800 text-sm">
                Daftar Rapor Peserta
            </h2>
        </div>
        <span class="text-xs text-gray-400">
            {{ $rapors->count() }} peserta
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
            <tr>
                <th class="px-6 py-3 text-left">Peserta</th>
                <th class="px-6 py-3 text-center">Status</th>
                <th class="px-6 py-3 text-center w-64">Aksi</th>
            </tr>
            </thead>

            <tbody class="divide-y">
            @forelse($rapors as $rapor)
            <tr class="hover:bg-gray-50 transition">

                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-[#8FBFC2]/20
                                    flex items-center justify-center
                                    text-xs font-semibold text-[#6FA9AD]">
                            {{ strtoupper(substr($rapor->peserta->nama,0,1)) }}
                        </div>
                        <span class="font-medium text-gray-800">
                            {{ $rapor->peserta->nama }}
                        </span>
                    </div>
                </td>

                <td class="px-6 py-4 text-center">
                    <span class="px-3 py-1 rounded-full text-xs font-medium
                        {{
                            $rapor->status === 'approved' ? 'bg-green-100 text-green-700' :
                            ($rapor->status === 'submitted' ? 'bg-blue-100 text-blue-700' :
                            ($rapor->status === 'revision' ? 'bg-yellow-100 text-yellow-700' :
                            'bg-gray-200 text-gray-700'))
                        }}">
                        {{ ucfirst($rapor->status) }}
                    </span>
                </td>

                <td class="px-6 py-4">
                    <div class="flex items-center justify-center gap-2">

                        <a href="{{ route('instruktur.rapor.edit', [$raporTugas->id, $rapor->peserta->id]) }}"
                           class="inline-flex items-center justify-center gap-2
                                  min-w-[120px]
                                  px-4 py-2 rounded-xl text-xs font-semibold
                                  bg-[#8FBFC2] hover:bg-[#6FA9AD]
                                  text-gray-900 transition">

                            @if(in_array($rapor->status, ['draft','revision']))
                                <i data-feather="edit-3" class="w-3 h-3"></i>
                                Isi Rapor
                            @else
                                <i data-feather="eye" class="w-3 h-3"></i>
                                Lihat Rapor
                            @endif
                        </a>

                        @if($rapor->status === 'approved')
                        <a href="{{ route('instruktur.rapor.cetak', $rapor->id) }}"
                           target="_blank"
                           class="inline-flex items-center justify-center
                                  w-9 h-9 rounded-xl
                                  bg-indigo-600 hover:bg-indigo-700
                                  text-white transition">
                            <i data-feather="printer" class="w-4 h-4"></i>
                        </a>
                        @else
                        <div class="w-9 h-9"></div>
                        @endif

                    </div>
                </td>

            </tr>
            @empty
            <tr>
                <td colspan="3" class="px-6 py-12 text-center text-gray-400">
                    <i data-feather="inbox" class="w-7 h-7 mx-auto mb-2"></i>
                    Tidak ada rapor pada tugas ini
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
