<div
    x-show="sidebarOpen"
    @click="sidebarOpen = false"
    class="fixed inset-0 z-30 bg-black/40 md:hidden"
    x-transition.opacity
></div>

<aside
    class="fixed md:static inset-y-0 left-0 z-40
           w-72 md:w-64
           min-h-screen
           bg-[#8FBFC2]
           transform transition-transform duration-300 ease-in-out
           -translate-x-full md:translate-x-0
           flex flex-col text-gray-800"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
>


    {{-- LOGO --}}
    <div class="flex justify-center items-center px-6 py-6 shrink-0">
        <img src="{{ asset('images/applogo.png') }}" class="h-24">
    </div>

    {{-- MENU --}}
        <nav class="flex-1 px-4 space-y-6 overflow-y-auto text-sm pb-6">

        {{-- ================= ADMIN SISTEM ================= --}}
        @if(Auth::user()->isAdmin())

        <div>
            <p class="text-xs uppercase tracking-wide text-gray-700 mb-2">Utama</p>

            <a href="{{ route('dashboard') }}"
            class="flex items-center gap-3 px-4 py-2.5 rounded-xl
            {{ request()->routeIs('dashboard')
                    ? 'bg-white text-gray-900 font-semibold shadow-sm'
                    : 'hover:bg-white/80' }}">
                <i data-feather="home" class="w-4 h-4"></i>
                Dashboard
            </a>

            <a href="{{ route('sekolah.index') }}"
            class="flex items-center gap-3 px-4 py-2.5 mt-1 rounded-xl
            {{ request()->routeIs('sekolah.*')
                    ? 'bg-white text-gray-900 font-semibold shadow-sm'
                    : 'hover:bg-white/80' }}">
                <i data-feather="grid" class="w-4 h-4"></i>
                Manajemen Sekolah
            </a>

            <a href="{{ route('users.index') }}"
            class="flex items-center gap-3 px-4 py-2.5 mt-1 rounded-xl
            {{ request()->routeIs('users.*')
                    ? 'bg-white text-gray-900 font-semibold shadow-sm'
                    : 'hover:bg-white/80' }}">
                <i data-feather="users" class="w-4 h-4"></i>
                Manajemen User
            </a>
        </div>

        <div>
            <p class="text-xs uppercase tracking-wide text-gray-700 mb-2">Penjadwalan</p>

            <a href="{{ route('materi.index') }}"
            class="flex items-center gap-3 px-4 py-2.5 rounded-xl
            {{ request()->routeIs('materi.*')
                    ? 'bg-white text-gray-900 font-semibold shadow-sm'
                    : 'hover:bg-white/80' }}">
                <i data-feather="book" class="w-4 h-4"></i>
                Materi
            </a>

            <a href="{{ route('jadwal.index') }}"
            class="flex items-center gap-3 px-4 py-2.5 mt-1 rounded-xl
            {{ request()->routeIs('jadwal.*')
                    ? 'bg-white text-gray-900 font-semibold shadow-sm'
                    : 'hover:bg-white/80' }}">
                <i data-feather="calendar" class="w-4 h-4"></i>
                Jadwal
            </a>
        </div>

        <div>
            <p class="text-xs uppercase tracking-wide text-gray-700 mb-2">Keuangan</p>

            <a href="{{ route('pembayaran.index') }}"
            class="flex items-center gap-3 px-4 py-2.5 rounded-xl
            {{ request()->routeIs('pembayaran.index')
                    ? 'bg-white text-gray-900 font-semibold shadow-sm'
                    : 'hover:bg-white/80' }}">
                <i data-feather="credit-card" class="w-4 h-4"></i>
                Pembayaran
            </a>

            <a href="{{ route('pembayaran.invoice.form') }}"
            class="flex items-center gap-3 px-4 py-2.5 mt-1 rounded-xl
            {{ request()->routeIs('pembayaran.invoice.*')
                    ? 'bg-white text-gray-900 font-semibold shadow-sm'
                    : 'hover:bg-white/80' }}">
                <i data-feather="file-text" class="w-4 h-4"></i>
                Cetak Invoice
            </a>
        </div>

        <div>
            <p class="text-xs uppercase tracking-wide text-gray-700 mb-2">Laporan</p>

            <a href="{{ route('absensi.rekap.filter') }}"
            class="flex items-center gap-3 px-4 py-2.5 rounded-xl
            {{ request()->routeIs('absensi.rekap.*')
                    ? 'bg-white text-gray-900 font-semibold shadow-sm'
                    : 'hover:bg-white/80' }}">
                <i data-feather="clipboard" class="w-4 h-4"></i>
                Rekap Absensi
            </a>

            <a href="{{ route('pembayaran.rekap') }}"
            class="flex items-center gap-3 px-4 py-2.5 mt-1 rounded-xl
            {{ request()->routeIs('pembayaran.rekap')
                    ? 'bg-white text-gray-900 font-semibold shadow-sm'
                    : 'hover:bg-white/80' }}">
                <i data-feather="bar-chart-2" class="w-4 h-4"></i>
                Rekap Pembayaran
            </a>

            <a href="{{ route('rapot.index') }}"
            class="flex items-center gap-3 px-4 py-2.5 mt-1 rounded-xl
            {{ request()->routeIs('rapot.index')
                    ? 'bg-white text-gray-900 font-semibold shadow-sm'
                    : 'hover:bg-white/80' }}">
                <i data-feather="bar-chart-2" class="w-4 h-4"></i>
                Rapot
            </a>
            
        </div>

        @endif


        {{-- ================= ADMIN SEKOLAH ================= --}}
        @if(Auth::user()->isAdminSekolah())

        <div>
            <p class="text-xs uppercase tracking-wide text-gray-700 mb-2">Utama</p>

            <a href="{{ route('dashboard') }}"
            class="flex items-center gap-3 px-4 py-2.5 rounded-xl
            {{ request()->routeIs('dashboard')
                    ? 'bg-white text-gray-900 font-semibold shadow-sm'
                    : 'hover:bg-white/80' }}">
                <i data-feather="home" class="w-4 h-4"></i>
                Dashboard
            </a>
        </div>

        <div>
            <p class="text-xs uppercase tracking-wide text-gray-700 mb-2">Laporan</p>

            <a href="{{ route('absensi.rekap.filter') }}"
            class="flex items-center gap-3 px-4 py-2.5 rounded-xl
            {{ request()->routeIs('absensi.rekap.*')
                    ? 'bg-white text-gray-900 font-semibold shadow-sm'
                    : 'hover:bg-white/80' }}">
                <i data-feather="clipboard" class="w-4 h-4"></i>
                Rekap Absensi
            </a>

            <a href="{{ route('pembayaran.rekap') }}"
            class="flex items-center gap-3 px-4 py-2.5 mt-1 rounded-xl
            {{ request()->routeIs('pembayaran.rekap')
                    ? 'bg-white text-gray-900 font-semibold shadow-sm'
                    : 'hover:bg-white/80' }}">
                <i data-feather="dollar-sign" class="w-4 h-4"></i>
                Rekap Pembayaran
            </a>
        </div>

        @endif


        {{-- ================= INSTRUKTUR ================= --}}
        @if(Auth::user()->role === 'instruktur')

        <div>
            <p class="text-xs uppercase tracking-wide text-gray-700 mb-2">Utama</p>

            <a href="{{ route('dashboard') }}"
            class="flex items-center gap-3 px-4 py-2.5 rounded-xl
            {{ request()->routeIs('dashboard')
                    ? 'bg-white text-gray-900 font-semibold shadow-sm'
                    : 'hover:bg-white/80' }}">
                <i data-feather="home" class="w-4 h-4"></i>
                Dashboard
            </a>

            <a href="{{ route('jadwal.index') }}"
            class="flex items-center gap-3 px-4 py-2.5 mt-1 rounded-xl
            {{ request()->routeIs('jadwal.*')
                    ? 'bg-white text-gray-900 font-semibold shadow-sm'
                    : 'hover:bg-white/80' }}">
                <i data-feather="calendar" class="w-4 h-4"></i>
                Jadwal Saya
            </a>
        </div>

        @endif

    </nav>


    {{-- PROFILE & LOGOUT --}}
        <div class="mt-auto border-t border-white/40 p-4 shrink-0 text-sm">

        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-white text-gray-900 flex items-center justify-center shadow-sm">
                <i data-feather="user" class="w-4 h-4"></i>
            </div>
            <div class="leading-tight overflow-hidden">
                <p class="font-semibold truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs capitalize text-gray-600">{{ Auth::user()->role }}</p>
            </div>
        </div>

        <a href="{{ route('profile.edit') }}"
           class="flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-white/80">
            <i data-feather="settings" class="w-4 h-4"></i>
            Pengaturan Profil
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                class="w-full flex items-center gap-3 px-4 py-2.5 mt-2 rounded-xl hover:bg-white/80">
                <i data-feather="log-out" class="w-4 h-4"></i>
                Logout
            </button>
        </form>

    </div>



</aside>
