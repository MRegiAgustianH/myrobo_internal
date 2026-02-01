<div
    x-show="sidebarOpen"
    @click="sidebarOpen = false"
    class="fixed inset-0 z-30 bg-black/40 md:hidden"
    x-transition.opacity
></div>

<aside
    class="fixed md:static inset-y-0 left-0 z-40
           w-64 md:w-56
           min-h-screen
           bg-[#8FBFC2]
           transform transition-transform duration-300 ease-in-out
           -translate-x-full md:translate-x-0
           flex flex-col text-gray-800"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
>

    {{-- LOGO --}}
    <div class="flex justify-center items-center px-4 py-5 shrink-0">
        <img src="{{ asset('images/applogo.png') }}"
             class="h-16 object-contain">
    </div>

    {{-- MENU --}}
        <nav
        class="flex-1 px-3 space-y-5 overflow-y-auto text-sm pb-6
            scrollbar-thin scrollbar-thumb-white/40 scrollbar-track-transparent">

    @php
        $menuClass = 'flex items-center gap-3 px-3 py-2 rounded-lg transition';
        $active = 'bg-white text-gray-900 font-semibold shadow-sm';
        $hover  = 'hover:bg-white/80';
    @endphp

    {{-- ================= ADMIN SISTEM ================= --}}
    @if(Auth::user()->isAdmin())

    <div>
        <p class="text-[11px] uppercase tracking-wide text-gray-700 mb-2">Utama</p>

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
    </div>

    <div>
        <p class="text-[11px] uppercase tracking-wide text-gray-700 mb-2">Penjadwalan</p>

        <a href="{{ route('materi.index') }}"
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
        <p class="text-[11px] uppercase tracking-wide text-gray-700 mb-2">Keuangan</p>

        <a href="{{ route('keuangan.index') }}"
        class="{{ $menuClass }} mt-1 {{ request()->routeIs('keuangan.index') ? $active : $hover }}">
            <i data-feather="activity" class="w-4 h-4"></i>
            Pengeluaran
        </a>

        <a href="{{ route('pembayaran.index') }}"
        class="{{ $menuClass }} {{ request()->routeIs('pembayaran.index') ? $active : $hover }}">
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
        <p class="text-[11px] uppercase tracking-wide text-gray-700 mb-2">Laporan</p>

        <a href="{{ route('rapor.manajemen') }}"
        class="{{ $menuClass }} {{ request()->routeIs('rapor.manajemen') ? $active : $hover }}">
            <i data-feather="bar-chart-2" class="w-4 h-4"></i>
            Rapor
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
        <p class="text-[11px] uppercase tracking-wide text-gray-700 mb-2">Instruktur</p>

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

        <a href="#"
        class="{{ $menuClass }} {{ request()->routeIs('#') ? $active : $hover }}">
            <i data-feather="book-open" class="w-4 h-4"></i>
            Modul Materi
        </a>
    </div>

    @endif

    {{-- ================= ADMIN SEKOLAH ================= --}}
    @if(Auth::user()->isAdminSekolah())

    <div>
        <p class="text-[11px] uppercase tracking-wide text-gray-700 mb-2">Admin Sekolah</p>

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
    <div class="border-t border-white/40 p-3 text-xs shrink-0">

        <div class="flex items-center gap-3 mb-3">
            <div
                class="w-9 h-9 rounded-full bg-white text-gray-900
                       flex items-center justify-center shadow-sm">
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
           class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/80">
            <i data-feather="settings" class="w-4 h-4"></i>
            Profil
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                class="w-full flex items-center gap-3 px-3 py-2 mt-1 rounded-lg hover:bg-white/80">
                <i data-feather="log-out" class="w-4 h-4"></i>
                Logout
            </button>
        </form>
    </div>

</aside>
