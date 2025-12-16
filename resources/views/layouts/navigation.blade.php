<aside
    class="fixed md:static z-40 w-64 min-h-screen bg-[#6EC6C4]
           transform transition-transform duration-300
           -translate-x-full md:translate-x-0
           flex flex-col"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
>

    {{-- LOGO --}}
    <div class="flex justify-center items-center px-6 shrink-0">
        <img src="{{ asset('images/applogo.png') }}" class="h-32">
    </div>

    {{-- MENU --}}
    <nav class="flex-1 px-4 space-y-6 overflow-y-auto">

        {{-- ========================= --}}
        {{-- ADMIN SISTEM --}}
        {{-- ========================= --}}
        @if(Auth::user()->isAdmin())

            {{-- UTAMA --}}
            <div>
                <p class="text-sm font-semibold mb-2">Utama</p>

                <a href="{{ route('dashboard') }}"
                class="flex items-center gap-3 px-4 py-2 rounded-lg
                {{ request()->routeIs('dashboard') ? 'bg-white font-semibold' : 'hover:bg-white' }}">
                    🏠 Dashboard
                </a>

                <a href="{{ route('sekolah.index') }}"
                class="flex items-center gap-3 px-4 py-2 mt-2 rounded-lg hover:bg-white">
                    🏫 Manajemen Sekolah
                </a>

                <a href="{{ route('users.index') }}"
                class="flex items-center gap-3 px-4 py-2 mt-2 rounded-lg hover:bg-white">
                    👤 Manajemen User
                </a>
            </div>

            {{-- PENJADWALAN --}}
            <div>
                <p class="text-sm font-semibold mb-2">Penjadwalan</p>

                <a href="{{ route('materi.index') }}"
                class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white">
                    📚 Materi
                </a>

                <a href="{{ route('jadwal.index') }}"
                class="flex items-center gap-3 px-4 py-2 mt-2 rounded-lg hover:bg-white">
                    🗓️ Jadwal
                </a>
            </div>

            {{-- KEUANGAN --}}
            <div>
                <p class="text-sm font-semibold mb-2">Keuangan</p>

                <a href="{{ route('pembayaran.index') }}"
                class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white">
                    💳 Pembayaran
                </a>

                <a href="{{ route('pembayaran.invoice.form') }}"
                class="flex items-center gap-3 px-4 py-2 mt-2 rounded-lg hover:bg-white">
                    🧾 Cetak Invoice
                </a>
            </div>

            {{-- LAPORAN --}}
            <div>
                <p class="text-sm font-semibold mb-2">Laporan</p>

                <a href="{{ route('absensi.rekap.filter') }}"
                class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white">
                    📋 Rekap Absensi
                </a>

                <a href="{{ route('pembayaran.rekap') }}"
                class="flex items-center gap-3 px-4 py-2 mt-2 rounded-lg hover:bg-white">
                    📊 Rekap Pembayaran
                </a>
            </div>

        @endif


        {{-- ========================= --}}
        {{-- ADMIN SEKOLAH --}}
        {{-- ========================= --}}
        @if(Auth::user()->isAdminSekolah())

            <div>
                <p class="text-sm font-semibold mb-2">Utama</p>

                <a href="{{ route('dashboard') }}"
                class="flex items-center gap-3 px-4 py-2 rounded-lg
                {{ request()->routeIs('dashboard') ? 'bg-white font-semibold' : 'hover:bg-white' }}">
                    🏠 Dashboard
                </a>
            </div>

            <div>
                <p class="text-sm font-semibold mb-2">Laporan</p>

                <a href="{{ route('absensi.rekap.filter') }}"
                class="flex items-center gap-3 px-4 py-2 rounded-lg
                {{ request()->routeIs('absensi.rekap.*') ? 'bg-white font-semibold' : 'hover:bg-white' }}">
                    📋 Rekap Absensi
                </a>

                <a href="{{ route('pembayaran.rekap') }}"
                class="flex items-center gap-3 px-4 py-2 mt-2 rounded-lg hover:bg-white">
                    💰 Rekap Pembayaran
                </a>
            </div>

        @endif


        {{-- ========================= --}}
        {{-- INSTRUKTUR --}}
        {{-- ========================= --}}
        @if(Auth::user()->role === 'instruktur')

            <div>
                <p class="text-sm font-semibold mb-2">Utama</p>

                <a href="{{ route('dashboard') }}"
                class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-white">
                    🏠 Dashboard
                </a>

                <a href="{{ route('jadwal.index') }}"
                class="flex items-center gap-3 px-4 py-2 mt-2 rounded-lg hover:bg-white">
                    🗓️ Jadwal Saya
                </a>
            </div>

        @endif

    </nav>


    {{-- PROFILE & LOGOUT --}}
    <div class="mt-auto border-t border-white/40 p-4 shrink-0">

        <div class="flex items-center gap-3 mb-3">
            <div class="w-9 h-9 rounded-full bg-white flex items-center justify-center">
                👤
            </div>
            <div class="text-sm">
                <p class="font-semibold">{{ Auth::user()->name }}</p>
                <p class="text-xs capitalize">{{ Auth::user()->role }}</p>
            </div>
        </div>

        <a href="{{ route('profile.edit') }}"
           class="block px-4 py-2 rounded-lg text-sm hover:bg-white">
            ⚙️ Pengaturan Profil
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                class="w-full text-left px-4 py-2 mt-2 rounded-lg text-sm hover:bg-white">
                🚪 Logout
            </button>
        </form>

    </div>

</aside>
