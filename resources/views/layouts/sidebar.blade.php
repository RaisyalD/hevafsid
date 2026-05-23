{{-- Mobile overlay --}}
<div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
     class="fixed inset-0 z-20 bg-black/40 lg:hidden"></div>

{{-- Sidebar --}}
<aside class="fixed inset-y-0 left-0 z-30 w-64 transform bg-white shadow-xl transition-transform duration-300
              lg:relative lg:translate-x-0 lg:shadow-none lg:border-r lg:border-pink-100"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

    {{-- Brand --}}
    <div class="border-b border-pink-100 px-5 py-4">
        <div class="flex items-center gap-3">
            <div class="relative flex-shrink-0">
                <img src="/images/hevafsid.jpeg" alt="hevafsid"
                     class="h-11 w-11 rounded-2xl object-cover shadow-md ring-2 ring-pink-100">
                <span class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full bg-emerald-400 ring-2 ring-white"></span>
            </div>
            <div class="min-w-0">
                <div class="text-sm font-bold text-gray-900 leading-tight">hevafsid</div>
                <div class="text-xs text-rose-500 font-medium">ERP System</div>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex flex-col gap-0.5 overflow-y-auto p-4" style="height: calc(100vh - 4rem);">

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
           class="sidebar-item flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-600 transition-all
                  {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>

        {{-- Master Data --}}
        @if(auth()->user()->canManageStock() || auth()->user()->isSuperAdmin())
        <div class="mt-3 mb-1 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Master Data</div>

        <a href="{{ route('products.index') }}"
           class="sidebar-item flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-600 transition-all
                  {{ request()->routeIs('products.*') ? 'active' : '' }}">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            Master Barang
        </a>

        <a href="{{ route('categories.index') }}"
           class="sidebar-item flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-600 transition-all
                  {{ request()->routeIs('categories.*') ? 'active' : '' }}">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            Kategori
        </a>

        <a href="{{ route('suppliers.index') }}"
           class="sidebar-item flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-600 transition-all
                  {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Supplier
        </a>
        @endif

        {{-- Transaksi --}}
        @if(auth()->user()->canManageStock())
        <div class="mt-3 mb-1 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Transaksi</div>

        <a href="{{ route('incoming.index') }}"
           class="sidebar-item flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-600 transition-all
                  {{ request()->routeIs('incoming.*') ? 'active' : '' }}">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
            </svg>
            Barang Masuk
        </a>

        <a href="{{ route('outgoing.index') }}"
           class="sidebar-item flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-600 transition-all
                  {{ request()->routeIs('outgoing.*') ? 'active' : '' }}">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 8V4m0 0l4 4m-4-4l-4 4M7 16v4m0 0l-4-4m4 4l4-4"/>
            </svg>
            Barang Keluar
        </a>

        <a href="{{ route('reject.index') }}"
           class="sidebar-item flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-600 transition-all
                  {{ request()->routeIs('reject.*') ? 'active' : '' }}">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
            </svg>
            Barang Reject
        </a>
        @endif

        {{-- Stok --}}
        <div class="mt-3 mb-1 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Stok</div>

        <a href="{{ route('batches.index') }}"
           class="sidebar-item flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-600 transition-all
                  {{ request()->routeIs('batches.*') ? 'active' : '' }}">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
            </svg>
            Batch FIFO
        </a>

        <a href="{{ route('inventory-card.index') }}"
           class="sidebar-item flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-600 transition-all
                  {{ request()->routeIs('inventory-card.*') ? 'active' : '' }}">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
            Kartu Persediaan
        </a>

        {{-- Keuangan --}}
        @if(auth()->user()->canManageFinance())
        <div class="mt-3 mb-1 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Keuangan</div>

        <a href="{{ route('financial.index') }}"
           class="sidebar-item flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-600 transition-all
                  {{ request()->routeIs('financial.*') ? 'active' : '' }}">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Kas & Keuangan
        </a>
        @endif

        {{-- Laporan --}}
        @if(auth()->user()->canManageFinance() || auth()->user()->isOwner())
        <a href="{{ route('reports.index') }}"
           class="sidebar-item flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-600 transition-all
                  {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Laporan
        </a>
        @endif

        {{-- Admin --}}
        @if(auth()->user()->isSuperAdmin())
        <div class="mt-3 mb-1 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Administrasi</div>
        <a href="{{ route('users.index') }}"
           class="sidebar-item flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-600 transition-all
                  {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Manajemen User
        </a>
        @endif

        {{-- Spacer + User info --}}
        <div class="flex-1"></div>
        <div class="mt-4 rounded-xl bg-pink-50 p-3">
            <div class="flex items-center gap-3">
                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}"
                     class="h-8 w-8 rounded-full object-cover">
                <div class="min-w-0">
                    <div class="truncate text-sm font-medium text-gray-900">{{ auth()->user()->name }}</div>
                    <div class="truncate text-xs text-rose-500">{{ auth()->user()->role?->display_name }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit" class="w-full rounded-lg bg-white py-1.5 text-xs font-medium text-gray-600 hover:text-rose-600 border border-pink-200 hover:border-rose-300 transition">
                    Keluar
                </button>
            </form>
        </div>
    </nav>
</aside>
