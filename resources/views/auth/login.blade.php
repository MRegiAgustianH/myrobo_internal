<x-guest-layout>
    <div class="w-full max-w-sm">

        {{-- LOGO (LEBIH BESAR) --}}
        <div class="flex justify-center mb-8">
            <img
                src="{{ asset('images/applogo.png') }}"
                alt="MyRobo"
                class="h-32 md:h-48"
            >
        </div>

        {{-- CARD --}}
        <div class="bg-white rounded-xl shadow-lg px-6 py-6">

            <h2 class="text-center text-lg font-semibold mb-6">
                LOGIN 
            </h2>

            {{-- Error --}}
            @if ($errors->any())
                <div class="mb-4 text-sm text-red-600 text-center">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Username --}}
                <div class="mb-4">
                    <label class="block text-sm mb-1">Email</label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required autofocus
                        class="w-full bg-gray-200 rounded-md px-3 py-2 border-0 focus:ring-0"
                    >
                </div>

                {{-- Password --}}
                <div class="mb-6">
                    <label class="block text-sm mb-1">Password</label>
                    <input
                        type="password"
                        name="password"
                        required
                        class="w-full bg-gray-200 rounded-md px-3 py-2 border-0 focus:ring-0"
                    >
                </div>

                {{-- Button --}}
                <button
                    type="submit"
                    class="w-full bg-[#8FBFC2] hover:bg-[#7fb1b4]
                           text-white font-semibold py-2 rounded-md transition">
                    LOGIN
                </button>
            </form>

        </div>
    </div>
</x-guest-layout>
