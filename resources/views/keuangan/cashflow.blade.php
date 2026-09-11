@extends('layouts.app')

@section('header')
<div class="flex items-center justify-between flex-wrap gap-4">
    <div class="flex items-center gap-3">
        <div class="p-2 bg-[#8FBFC2]/20 rounded-xl">
            <i data-feather="activity" class="w-5 h-5 text-[#6FA9AD]"></i>
        </div>
        <div>
            <h1 class="text-lg font-semibold text-gray-800">Cashflow (Arus Kas)</h1>
            <p class="text-xs text-gray-500">Monitoring kas masuk, kas keluar, dan saldo bersih</p>
        </div>
    </div>
    
    @if(in_array(auth()->user()->role, ['superadmin', 'admin_cabang', 'bendahara']))
    <button onclick="openSaldoModal()"
        class="inline-flex items-center gap-2 bg-[#8FBFC2] hover:bg-[#6FA9AD] text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition shadow-sm">
        <i data-feather="plus" class="w-4 h-4"></i>
        Set Saldo / Kas Manual
    </button>
    @endif
</div>
@endsection

@section('content')

{{-- ================= FILTER ================= --}}
<form method="GET" class="bg-[#F6FAFB] border border-[#E3EEF0] rounded-2xl shadow-sm mb-6 p-5">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        @if(auth()->user()->role === 'superadmin' && isset($cabangs))
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Filter Cabang</label>
            <select name="cabang_id" class="w-full bg-white border border-[#E3EEF0] rounded-lg px-3 py-2 text-sm" onchange="this.form.submit()">
                <option value="">-- Semua Cabang --</option>
                @foreach($cabangs as $cb)
                    <option value="{{ $cb->id }}" {{ (string)$cabangId === (string)$cb->id ? 'selected' : '' }}>
                        {{ $cb->nama_cabang }} ({{ $cb->kode_cabang }})
                    </option>
                @endforeach
            </select>
        </div>
        @endif

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
            <select name="bulan" class="w-full bg-white border border-[#E3EEF0] rounded-lg px-3 py-2 text-sm">
                @for($i=1;$i<=12;$i++)
                    <option value="{{ $i }}" {{ (int)$bulan === $i ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month((int)$i)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
            <input type="number" name="tahun" value="{{ $tahun }}" class="w-full bg-white border border-[#E3EEF0] rounded-lg px-3 py-2 text-sm">
        </div>

        <div class="flex gap-2">
            <button class="flex-1 bg-[#8FBFC2] hover:bg-[#6FA9AD] text-gray-900 px-4 py-2 rounded-lg font-medium text-sm transition">
                Tampilkan
            </button>
            <a href="{{ route('keuangan.cashflow') }}" class="flex-1 bg-white border border-[#E3EEF0] px-4 py-2 rounded-lg text-center text-sm transition">
                Reset
            </a>
        </div>
    </div>
</form>

{{-- ================= REKAP CARD ================= --}}
<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 mb-3">Ikhtisar Kas</h3>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-2xl border p-5 shadow-sm">
        <div class="text-xs text-gray-500 uppercase tracking-wider">Total Kas Masuk</div>
        <div class="text-2xl font-bold text-green-600 mt-1">Rp {{ number_format($totalMasukAll, 0, ',', '.') }}</div>
        <p class="text-xs text-gray-400 mt-2">Seluruh waktu (all-time)</p>
    </div>
    <div class="bg-white rounded-2xl border p-5 shadow-sm">
        <div class="text-xs text-gray-500 uppercase tracking-wider">Total Kas Keluar</div>
        <div class="text-2xl font-bold text-red-600 mt-1">Rp {{ number_format($totalKeluarAll, 0, ',', '.') }}</div>
        <p class="text-xs text-gray-400 mt-2">Seluruh waktu (all-time)</p>
    </div>
    <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-2xl border border-indigo-200 p-5 shadow-sm">
        <div class="text-xs text-indigo-700 uppercase tracking-wider">Saldo Bersih (Net)</div>
        <div class="text-2xl font-bold text-indigo-900 mt-1">Rp {{ number_format($saldoAll, 0, ',', '.') }}</div>
        <p class="text-xs text-indigo-500 mt-2">Kas Saat Ini</p>
    </div>
</div>

{{-- ================= BULANAN REKAP ================= --}}
<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 mb-3">
    Arus Kas Bulanan: {{ \Carbon\Carbon::create()->month((int)$bulan)->translatedFormat('F') }} {{ $tahun }}
</h3>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 text-sm">
    <div class="bg-green-50/50 border border-green-200 rounded-xl p-4 flex justify-between items-center">
        <div>
            <span class="text-xs text-green-700 uppercase tracking-wide">Kas Masuk</span>
            <span class="block text-lg font-bold text-green-800">Rp {{ number_format($totalMasukBulan, 0, ',', '.') }}</span>
        </div>
        <i data-feather="trending-up" class="w-8 h-8 text-green-500/50"></i>
    </div>
    <div class="bg-red-50/50 border border-red-200 rounded-xl p-4 flex justify-between items-center">
        <div>
            <span class="text-xs text-red-700 uppercase tracking-wide">Kas Keluar</span>
            <span class="block text-lg font-bold text-red-800">Rp {{ number_format($totalKeluarBulan, 0, ',', '.') }}</span>
        </div>
        <i data-feather="trending-down" class="w-8 h-8 text-red-500/50"></i>
    </div>
    <div class="bg-blue-50/50 border border-blue-200 rounded-xl p-4 flex justify-between items-center">
        <div>
            <span class="text-xs text-blue-700 uppercase tracking-wide">Net Month</span>
            <span class="block text-lg font-bold text-blue-800">Rp {{ number_format($saldoBulan, 0, ',', '.') }}</span>
        </div>
        <i data-feather="activity" class="w-8 h-8 text-blue-500/50"></i>
    </div>
</div>

{{-- ================= BREAKDOWN KAS MASUK ================= --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="bg-white border rounded-2xl shadow-sm overflow-hidden">
        <div class="px-5 py-3 bg-green-50 border-b flex items-center gap-2">
            <i data-feather="trending-up" class="w-4 h-4 text-green-600"></i>
            <h3 class="font-semibold text-sm text-green-800">Rincian Kas Masuk Bulan Ini</h3>
        </div>
        <div class="p-5 space-y-2">
            @if(!empty($masukDetail))
                @foreach($masukDetail as $kategori => $total)
                <div class="flex justify-between items-center py-2 border-b last:border-0">
                    <span class="text-sm text-gray-700">{{ $kategori }}</span>
                    <span class="text-sm font-semibold text-green-700">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
                @endforeach
                <div class="flex justify-between items-center pt-3 mt-2 border-t-2">
                    <span class="font-semibold text-sm text-gray-800">Total Kas Masuk</span>
                    <span class="font-bold text-green-700">Rp {{ number_format($totalMasukBulan, 0, ',', '.') }}</span>
                </div>
            @else
                <p class="text-center text-gray-400 text-sm py-4">Belum ada pemasukan bulan ini</p>
            @endif
        </div>
    </div>

    <div class="bg-white border rounded-2xl shadow-sm overflow-hidden">
        <div class="px-5 py-3 bg-red-50 border-b flex items-center gap-2">
            <i data-feather="trending-down" class="w-4 h-4 text-red-600"></i>
            <h3 class="font-semibold text-sm text-red-800">Rincian Kas Keluar Bulan Ini</h3>
        </div>
        <div class="p-5 space-y-2">
            @if(!empty($keluarDetail))
                @foreach($keluarDetail as $kategori => $total)
                <div class="flex justify-between items-center py-2 border-b last:border-0">
                    <span class="text-sm text-gray-700">{{ $kategori }}</span>
                    <span class="text-sm font-semibold text-red-700">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
                @endforeach
                <div class="flex justify-between items-center pt-3 mt-2 border-t-2">
                    <span class="font-semibold text-sm text-gray-800">Total Kas Keluar</span>
                    <span class="font-bold text-red-700">Rp {{ number_format($totalKeluarBulan, 0, ',', '.') }}</span>
                </div>
            @else
                <p class="text-center text-gray-400 text-sm py-4">Belum ada pengeluaran bulan ini</p>
            @endif
        </div>
    </div>
</div>

{{-- ================= LIST TRANSAKSI ================= --}}
<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 mb-3">Riwayat Transaksi</h3>
<div class="bg-white border rounded-2xl shadow-sm overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-[#F6FAFB] border-b text-gray-600">
            <tr>
                <th class="px-6 py-3 text-left">Tanggal</th>
                <th class="px-6 py-3 text-center">Tipe</th>
                <th class="px-6 py-3 text-left">Kategori</th>
                <th class="px-6 py-3 text-left">Deskripsi</th>
                <th class="px-6 py-3 text-left">Sekolah / Target</th>
                <th class="px-6 py-3 text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($transaksis as $t)
            <tr class="hover:bg-gray-50/50">
                <td class="px-6 py-4 whitespace-nowrap">{{ $t->tanggal->format('d F Y') }}</td>
                <td class="px-6 py-4 text-center whitespace-nowrap">
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold uppercase tracking-wide
                        {{ $t->tipe === 'masuk' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $t->tipe }}
                    </span>
                </td>
                <td class="px-6 py-4 font-medium">{{ $t->kategori }}</td>
                <td class="px-6 py-4 text-gray-600 max-w-xs truncate">{{ $t->deskripsi }}</td>
                <td class="px-6 py-4 text-gray-500">{{ $t->sekolah?->nama_sekolah ?? 'Home Private / Lainnya' }}</td>
                <td class="px-6 py-4 text-right font-semibold whitespace-nowrap
                    {{ $t->tipe === 'masuk' ? 'text-green-600' : 'text-red-600' }}">
                    {{ $t->tipe === 'masuk' ? '+' : '-' }} Rp {{ number_format($t->jumlah, 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-gray-500 italic">
                    <i data-feather="inbox" class="w-8 h-8 mx-auto mb-2 text-gray-300"></i>
                    Tidak ada transaksi arus kas pada periode ini
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $transaksis->links() }}</div>

{{-- ================= MODAL SCRIPT ================= --}}
@if(in_array(auth()->user()->role, ['superadmin', 'admin_cabang', 'bendahara']))
<script>
function openSaldoModal() {
    Swal.fire({
        title: 'Input Transaksi Kas Manual',
        html: `
        <form id="cashForm" class="space-y-4 text-left text-sm">
            @csrf
            <div>
                <label class="font-medium">Tipe Transaksi</label>
                <select name="tipe" class="w-full px-3 py-2 border rounded-lg mt-1">
                    <option value="masuk">Kas Masuk (Pemasukan / Saldo Awal)</option>
                    <option value="keluar">Kas Keluar (Pengeluaran)</option>
                </select>
            </div>
            @if(auth()->user()->role === 'superadmin')
            <div>
                <label class="font-medium">Cabang</label>
                <select name="cabang_id" class="w-full px-3 py-2 border rounded-lg mt-1">
                    <option value="">-- Pilih Cabang --</option>
                    @foreach($cabangs as $cb)
                        <option value="{{ $cb->id }}">{{ $cb->nama_cabang }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div>
                <label class="font-medium">Sekolah (Opsional)</label>
                <select name="sekolah_id" class="w-full px-3 py-2 border rounded-lg mt-1">
                    <option value="">-- Pilih Sekolah --</option>
                    @foreach(\App\Models\Sekolah::orderBy('nama_sekolah')->get() as $sk)
                        <option value="{{ $sk->id }}">{{ $sk->nama_sekolah }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="font-medium">Tanggal</label>
                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 border rounded-lg mt-1" required>
            </div>
            <div>
                <label class="font-medium">Kategori</label>
                <input type="text" name="kategori" placeholder="Contoh: Saldo Awal, Pengeluaran ATK" class="w-full px-3 py-2 border rounded-lg mt-1" required>
            </div>
            <div>
                <label class="font-medium">Jumlah (Rp)</label>
                <input type="number" name="jumlah" placeholder="Masukkan nominal" class="w-full px-3 py-2 border rounded-lg mt-1" required>
            </div>
            <div>
                <label class="font-medium">Deskripsi / Catatan</label>
                <textarea name="deskripsi" placeholder="Keterangan tambahan..." class="w-full px-3 py-2 border rounded-lg mt-1" rows="2"></textarea>
            </div>
        </form>
        `,
        showConfirmButton: false,
        width: 600,
        footer: `
        <div class="flex gap-2">
            <button onclick="submitCashForm()" class="bg-[#8FBFC2] text-white px-5 py-2.5 rounded-xl font-semibold text-sm shadow-sm transition">Simpan</button>
            <button onclick="Swal.close()" class="border px-5 py-2.5 rounded-xl text-sm transition">Batal</button>
        </div>`
    });
}

function submitCashForm() {
    const form = document.getElementById('cashForm');
    const fd = new FormData(form);
    
    fetch('{{ route("keuangan.store") }}', {
        method: 'POST',
        body: fd,
        headers: { 'Accept': 'application/json' }
    })
    .then(async r => {
        if (r.ok) {
            window.location.reload();
        } else {
            const err = await r.json();
            Swal.showValidationMessage(Object.values(err.errors || {}).flat().join('\n'));
        }
    });
}
</script>
@endif

@endsection