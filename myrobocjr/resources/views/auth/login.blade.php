<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center px-4 bg-tranparent">

        <div class="w-full max-w-md">

            {{-- LOGO --}}
            <div class="flex justify-center mb-8">
                <img
                    src="{{ asset('images/applogo.png') }}"
                    alt="MyRobo"
                    class="h-28 sm:h-32 md:h-40 transition"
                >
            </div>

            {{-- CARD --}}
            <div class="bg-white rounded-2xl shadow-md px-6 sm:px-8 py-8">

                <h2 class="text-center text-xl font-semibold text-gray-800 mb-6">
                    LOGIN
                </h2>

                {{-- Error --}}
                @if ($errors->any())
                    <div class="mb-4 text-sm text-red-600 text-center">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    {{-- Username --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">
                            Username
                        </label>
                        <input
                            type="text"
                            name="username"
                            value="{{ old('username') }}"
                            required
                            autofocus
                            class="w-full rounded-lg px-4 py-2.5
                                bg-gray-100 border border-gray-200
                                focus:outline-none focus:ring-2
                                focus:ring-[#8FBFC2]/70
                                focus:border-[#8FBFC2]"
                        >
                    </div>

                    {{-- Password --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">
                            Password
                        </label>
                        <input
                            type="password"
                            name="password"
                            required
                            class="w-full rounded-lg px-4 py-2.5
                                   bg-gray-100 border border-gray-200
                                   focus:outline-none focus:ring-2
                                   focus:ring-[#8FBFC2]/70
                                   focus:border-[#8FBFC2]"
                        >
                    </div>

                    {{-- Button --}}
                    <button
                        type="submit"
                        class="w-full mt-2
                               bg-gradient-to-r from-[#8FBFC2] to-[#7AAEB1]
                               hover:from-[#7AAEB1] hover:to-[#6FA9AD]
                               text-white font-semibold
                               py-3 rounded-xl
                               shadow-sm transition-all duration-200">
                        Login
                    </button>
                </form>

            </div>

            {{-- FOOTER --}}
            <p class="mt-6 text-center text-xs text-gray-500">
                © {{ date('Y') }} MyRobo. All rights reserved.
            </p>

        </div>
    </div>
</x-guest-layout>
