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

        <form action="{{ route('pembayaran.invoice.pdf') }}"
              method="GET" target="_blank">

            <div class="space-y-5">

                {{-- SEKOLAH --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Sekolah
                    </label>
                    <select name="sekolah_id"
                        class="w-full bg-white border border-[#E3EEF0]
                               rounded-lg px-3 py-2
                               text-gray-800
                               focus:outline-none
                               focus:ring-2 focus:ring-[#8FBFC2]/60
                               focus:border-[#8FBFC2]"
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
                        <select name="bulan"
                            class="w-full bg-white border border-[#E3EEF0]
                                   rounded-lg px-3 py-2
                                   text-gray-800
                                   focus:outline-none
                                   focus:ring-2 focus:ring-[#8FBFC2]/60
                                   focus:border-[#8FBFC2]"
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
                               name="tahun"
                               value="{{ now()->year }}"
                               class="w-full bg-white border border-[#E3EEF0]
                                      rounded-lg px-3 py-2
                                      text-gray-800
                                      focus:outline-none
                                      focus:ring-2 focus:ring-[#8FBFC2]/60
                                      focus:border-[#8FBFC2]"
                               required>
                    </div>
                </div>

                {{-- ACTION --}}
                <div class="pt-6 flex justify-end">
                    <button
                        class="inline-flex items-center gap-2
                               bg-gradient-to-r
                               from-[#8FBFC2] to-[#7AAEB1]
                               hover:from-[#7AAEB1] hover:to-[#6FA9AD]
                               text-gray-900 font-semibold
                               px-6 py-2.5 rounded-xl
                               shadow-sm transition-all duration-200">
                        <i data-feather="file-text" class="w-4 h-4"></i>
                        Cetak Invoice
                    </button>
                </div>

            </div>
        </form>
    </div>

</div>

@endsection
