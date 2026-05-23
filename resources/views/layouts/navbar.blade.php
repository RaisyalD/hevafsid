<header class="flex h-16 items-center justify-between border-b border-pink-100 bg-white px-6 shadow-sm">
    {{-- Mobile hamburger --}}
    <button @click="sidebarOpen = !sidebarOpen"
            class="rounded-lg p-2 text-gray-500 hover:bg-pink-50 hover:text-rose-600 lg:hidden">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

    {{-- Page title / breadcrumb --}}
    <div class="hidden lg:block">
        <h1 class="text-lg font-semibold text-gray-900">@yield('page-title', 'Dashboard')</h1>
        @hasSection('breadcrumb')
        <p class="text-xs text-gray-400">@yield('breadcrumb')</p>
        @endif
    </div>

    {{-- Right side --}}
    <div class="flex items-center gap-3">
        {{-- Current date --}}
        <div class="hidden md:flex items-center gap-2 rounded-xl bg-pink-50 px-3 py-2 text-xs text-rose-600">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            {{ now()->translatedFormat('d F Y') }}
        </div>

        {{-- User dropdown --}}
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open"
                    class="flex items-center gap-2 rounded-xl border border-pink-200 bg-white px-3 py-2 text-sm hover:bg-pink-50 transition">
                <img src="{{ auth()->user()->avatar_url }}" alt=""
                     class="h-6 w-6 rounded-full object-cover">
                <span class="hidden md:block font-medium text-gray-700">{{ auth()->user()->name }}</span>
                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="open" x-cloak @click.away="open = false"
                 class="absolute right-0 top-full mt-1 w-48 rounded-xl border border-pink-100 bg-white shadow-lg py-1 z-50">
                <div class="px-4 py-2 border-b border-pink-50">
                    <div class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-rose-500">{{ auth()->user()->role?->display_name }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="flex w-full items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-pink-50 hover:text-rose-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
