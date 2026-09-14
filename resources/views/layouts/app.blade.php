

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('images/tablogo.png') }}" type="image/png">
    <title>{{ config('app.name', 'MyRobo Training') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
                    
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">

    <script src="https://unpkg.com/feather-icons"></script>

    <style>
/* Mobile FullCalendar Fix */
@media (max-width: 640px) {
    .fc .fc-toolbar-title {
        font-size: 1rem;
    }

    .fc-event {
        font-size: 0.8rem;
        padding: 4px 6px;
    }

    .fc-list-event-title {
        font-weight: 600;
    }

    .fc-list-event-time {
        font-size: 0.75rem;
        color: #6b7280;
    }
}
</style>


</head>


<body class="font-sans antialiased bg-gray-100">

<div x-data="{ sidebarOpen: false }" class="flex min-h-screen overflow-hidden">

    {{-- OVERLAY MOBILE --}}
    <div
        x-show="sidebarOpen"
        x-transition.opacity
        class="fixed inset-0 bg-black bg-opacity-50 z-30 md:hidden"
        @click="sidebarOpen = false">
    </div>

    {{-- SIDEBAR --}}
    @include('layouts.navigation')

    {{-- MAIN --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- TOP BAR (MOBILE) --}}
        <header class="bg-white shadow px-3 py-2.5 flex items-center gap-2 md:hidden sticky top-0 z-20">
            <button
                @click="sidebarOpen = true"
                class="p-2 -ml-1 rounded-lg text-2xl leading-none
                       hover:bg-gray-100 active:bg-gray-200 transition"
                aria-label="Buka menu">
                ☰
            </button>
            <span class="font-semibold truncate">MyRobo</span>
        </header>

        <main class="flex-1 p-3 sm:p-4 md:p-6 min-w-0">
            @hasSection('header')
                <h1 class="text-lg sm:text-xl font-semibold mb-4 sm:mb-6 leading-snug">
                    @yield('header')
                </h1>
            @endif

            @yield('content')
        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

@stack('scripts')

<script>
    feather.replace()
</script>
</body>



</html>

