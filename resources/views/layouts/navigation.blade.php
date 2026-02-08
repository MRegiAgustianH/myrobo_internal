{{-- OVERLAY MOBILE --}}
<div
    x-show="sidebarOpen"
    @click="sidebarOpen = false"
    x-transition.opacity
    class="fixed inset-0 z-30 bg-black/40 md:hidden">
</div>

{{-- SIDEBAR --}}
<aside
    class="fixed md:static inset-y-0 left-0 z-40
           w-64 md:w-56
           min-h-screen
           bg-gradient-to-b from-[#8FBFC2] to-[#7FB3B8]
           transform transition-transform duration-300 ease-out
           -translate-x-full md:translate-x-0
           flex flex-col text-gray-800 shadow-xl"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

    {{-- LOGO --}}
    <div class="flex justify-center items-center pt-4 pb-2 shrink-0">
        <img src="{{ asset('images/applogo.png') }}"
            class="h-24 object-contain drop-shadow-sm select-none">
    </div>


    {{-- NAV --}}
    <nav
        class="flex-1 px-3 space-y-6 overflow-y-auto text-sm pb-6
               scrollbar-thin scrollbar-thumb-white/40 scrollbar-track-transparent">

    @php
        $menuClass = 'group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200';
        $active = 'bg-white text-gray-900 font-semibold shadow';
        $hover  = 'hover:bg-white/80 hover:translate-x-1';
    @endphp

    {{-- ================= ADMIN SISTEM ================= --}}
    @if(Auth::user()->isAdmin())

    <div>
        <p class="text-[11px] uppercase tracking-wider text-gray-700 mb-2 px-3">
            Utama
        </p>

        <a href="{{ route('dashboard') }}"
           class="{{ $menuClass }} {{ request()->routeIs('dashboard') ? $active : $hover }}">
            <i data-feather="home" class="w-4 h-4"></i>
            Dashboard
        </a>

        <a href="{{ route('sekolah.index') }}"
           class="{{ $menuClass }} mt-1 {{ request()->routeIs('sekolah.*') ? $active : $hover }}">
            <i data-feather="grid" class="w-4 h-4"></i>
            Sekolah
        </a>

        <a href="{{ route('users.index') }}"
           class="{{ $menuClass }} mt-1 {{ request()->routeIs('users.*') ? $active : $hover }}">
            <i data-feather="users" class="w-4 h-4"></i>
            User
        </a>

        <a href="{{ route('home-private.index') }}"
           class="{{ $menuClass }} mt-1 {{ request()->routeIs('home-private.*') ? $active : $hover }}">
            <i data-feather="globe" class="w-4 h-4"></i>
            Home Private
        </a>
    </div>

    <div>
        <p class="text-[11px] uppercase tracking-wider text-gray-700 mb-2 px-3">
            Penjadwalan
        </p>

        <a href="{{ route('admin.materi.index') }}"
           class="{{ $menuClass }} {{ request()->routeIs('materi.*') ? $active : $hover }}">
            <i data-feather="book-open" class="w-4 h-4"></i>
            Materi
        </a>

        <a href="{{ route('jadwal.index') }}"
           class="{{ $menuClass }} mt-1 {{ request()->routeIs('jadwal.*') ? $active : $hover }}">
            <i data-feather="calendar" class="w-4 h-4"></i>
            Jadwal
        </a>
    </div>

    <div>
        <p class="text-[11px] uppercase tracking-wider text-gray-700 mb-2 px-3">
            Keuangan
        </p>

        <a href="{{ route('keuangan.index') }}"
           class="{{ $menuClass }} {{ request()->routeIs('keuangan.index') ? $active : $hover }}">
            <i data-feather="activity" class="w-4 h-4"></i>
            Pengeluaran
        </a>

        <a href="{{ route('pembayaran.index') }}"
           class="{{ $menuClass }} mt-1 {{ request()->routeIs('pembayaran.index') ? $active : $hover }}">
            <i data-feather="credit-card" class="w-4 h-4"></i>
            Pembayaran
        </a>

        <a href="{{ route('pembayaran.invoice.form') }}"
           class="{{ $menuClass }} mt-1 {{ request()->routeIs('pembayaran.invoice.*') ? $active : $hover }}">
            <i data-feather="file-text" class="w-4 h-4"></i>
            Cetak Invoice
        </a>
    </div>

    <div>
        <p class="text-[11px] uppercase tracking-wider text-gray-700 mb-2 px-3">
            Laporan
        </p>

        <a href="{{ route('admin.rapor-tugas.index') }}"
           class="{{ $menuClass }} {{ request()->routeIs('admin.rapor-tugas.*') ? $active : $hover }}">
            <i data-feather="bar-chart-2" class="w-4 h-4"></i>
            Penugasan Rapor
        </a>

        <a href="{{ route('absensi.rekap.filter') }}"
           class="{{ $menuClass }} mt-1 {{ request()->routeIs('absensi.rekap.*') ? $active : $hover }}">
            <i data-feather="clipboard" class="w-4 h-4"></i>
            Rekap Absensi
        </a>

        <a href="{{ route('pembayaran.rekap') }}"
           class="{{ $menuClass }} mt-1 {{ request()->routeIs('pembayaran.rekap') ? $active : $hover }}">
            <i data-feather="dollar-sign" class="w-4 h-4"></i>
            Rekap Pembayaran
        </a>
    </div>

    @endif

    {{-- ================= INSTRUKTUR ================= --}}
    @if(Auth::user()->role === 'instruktur')

    <div>
        <p class="text-[11px] uppercase tracking-wider text-gray-600 mb-2 px-3">
            Instruktur
        </p>

        <a href="{{ route('dashboard') }}"
           class="{{ $menuClass }} {{ request()->routeIs('dashboard.instruktur') ? $active : $hover }}">
            <i data-feather="home" class="w-4 h-4"></i>
            Dashboard
        </a>

        <a href="{{ route('jadwal.index') }}"
           class="{{ $menuClass }} mt-1 {{ request()->routeIs('jadwal.*') ? $active : $hover }}">
            <i data-feather="calendar" class="w-4 h-4"></i>
            Jadwal Saya
        </a>

        <a href="{{ route('admin.materi.index') }}"
           class="{{ $menuClass }} mt-1 {{ request()->routeIs('materi.*') ? $active : $hover }}">
            <i data-feather="book-open" class="w-4 h-4"></i>
            Materi & Modul
        </a>

        <a href="{{ route('instruktur.rapor-tugas.index') }}"
           class="{{ $menuClass }} mt-1 {{ request()->routeIs('instruktur.rapor-tugas.*') ? $active : $hover }}">
            <i data-feather="file-text" class="w-4 h-4"></i>
            Tugas Rapor
        </a>
    </div>

    @endif

    {{-- ================= ADMIN SEKOLAH ================= --}}
    @if(Auth::user()->isAdminSekolah())

    <div>
        <p class="text-[11px] uppercase tracking-wider text-gray-700 mb-2 px-3">
            Admin Sekolah
        </p>

        <a href="{{ route('dashboard') }}"
           class="{{ $menuClass }} {{ request()->routeIs('dashboard.admin_sekolah') ? $active : $hover }}">
            <i data-feather="home" class="w-4 h-4"></i>
            Dashboard
        </a>

        <a href="{{ route('absensi.rekap.filter') }}"
           class="{{ $menuClass }} mt-1 {{ request()->routeIs('absensi.rekap.*') ? $active : $hover }}">
            <i data-feather="clipboard" class="w-4 h-4"></i>
            Rekap Absensi
        </a>

        <a href="{{ route('pembayaran.rekap') }}"
           class="{{ $menuClass }} mt-1 {{ request()->routeIs('pembayaran.rekap') ? $active : $hover }}">
            <i data-feather="dollar-sign" class="w-4 h-4"></i>
            Rekap Pembayaran
        </a>
    </div>

    @endif

    </nav>

    {{-- PROFILE --}}
    <div class="border-t border-white/40 p-4 text-xs shrink-0">

        <div class="flex items-center gap-3 mb-4">
            <div
                class="w-10 h-10 rounded-full bg-white text-gray-900
                       flex items-center justify-center shadow">
                <i data-feather="user" class="w-4 h-4"></i>
            </div>

            <div class="leading-tight overflow-hidden">
                <p class="font-semibold truncate">
                    {{ Auth::user()->name }}
                </p>
                <p class="text-[11px] capitalize text-gray-600">
                    {{ Auth::user()->role }}
                </p>
            </div>
        </div>

        <a href="{{ route('profile.edit') }}"
           class="group flex items-center gap-3 px-3 py-2 rounded-xl
                  hover:bg-white/80 transition">
            <i data-feather="settings" class="w-4 h-4"></i>
            Profil
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                class="w-full group flex items-center gap-3 px-3 py-2 mt-1 rounded-xl
                       hover:bg-white/80 transition">
                <i data-feather="log-out" class="w-4 h-4"></i>
                Logout
            </button>
        </form>
    </div>

</aside>
