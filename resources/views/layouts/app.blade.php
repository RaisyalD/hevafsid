<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — hevafsid ERP</title>

    {{-- Favicon & App Icons --}}
    <link rel="icon" type="image/jpeg" href="/images/hevafsid.jpeg">
    <link rel="apple-touch-icon" href="/images/hevafsid.jpeg">

    {{-- App Metadata --}}
    <meta name="description" content="hevafsid ERP — Sistem Manajemen Inventory & Keuangan">
    <meta name="application-name" content="hevafsid ERP">
    <meta name="theme-color" content="#f43f5e">

    {{-- Open Graph --}}
    <meta property="og:type"        content="website">
    <meta property="og:title"       content="hevafsid ERP System">
    <meta property="og:description" content="Sistem Manajemen Inventory & Keuangan">
    <meta property="og:image"       content="/images/hevafsid.jpeg">

    {{-- Tailwind CSS & Alpine.js via CDN (replace with Vite in production) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        pink: {
                            50:  '#fdf2f8', 100: '#fce7f3', 200: '#fbcfe8',
                            300: '#f9a8d4', 400: '#f472b6', 500: '#ec4899',
                            600: '#db2777', 700: '#be185d', 800: '#9d174d',
                            900: '#831843',
                        },
                        rose: {
                            50:  '#fff1f2', 100: '#ffe4e6', 200: '#fecdd3',
                            300: '#fda4af', 400: '#fb7185', 500: '#f43f5e',
                            600: '#e11d48', 700: '#be123c', 800: '#9f1239',
                            900: '#881337',
                        },
                    },
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
                    },
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- Inter font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        [x-cloak] { display: none !important; }
        .sidebar-item.active { background: linear-gradient(135deg, #f43f5e, #ec4899); color: white; }
        .sidebar-item.active svg { color: white; }
        .sidebar-item:not(.active):hover { background-color: #fdf2f8; color: #be185d; }
        .card { @apply bg-white rounded-2xl shadow-sm border border-pink-100 p-6; }
        .btn-primary { @apply bg-rose-500 hover:bg-rose-600 text-white font-medium px-4 py-2 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md; }
        .btn-secondary { @apply bg-white hover:bg-pink-50 text-rose-600 border border-rose-200 font-medium px-4 py-2 rounded-xl transition-all duration-200; }
        .input { @apply w-full rounded-xl border border-pink-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-300 focus:border-rose-400 transition; }
        .badge-green  { @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800; }
        .badge-red    { @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800; }
        .badge-yellow { @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800; }
        .badge-blue   { @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800; }
        .badge-pink   { @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800; }
        .table th { @apply px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider; }
        .table td { @apply px-4 py-3 text-sm text-gray-700; }
        .table tr:hover td { background-color: #fdf2f8; }
    </style>

    @stack('styles')
</head>
<body class="h-full bg-rose-50/30 font-sans" x-data="{ sidebarOpen: false }">

<div class="flex h-screen overflow-hidden">
    {{-- ── Sidebar ──────────────────────────────────────────────────── --}}
    @include('layouts.sidebar')

    {{-- ── Main content ─────────────────────────────────────────────── --}}
    <div class="flex flex-1 flex-col overflow-hidden">

        {{-- Top navbar --}}
        @include('layouts.navbar')

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto p-6">

            {{-- Flash messages --}}
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                     class="mb-4 flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-emerald-800 text-sm">
                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
                     class="mb-4 flex items-center gap-3 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-red-800 text-sm">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-red-800 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')

{{-- ── Global Confirm Modal ──────────────────────────────────────────── --}}
<div id="confirmModal"
     x-data="confirmModalData()"
     @open-confirm.window="show($event.detail)"
     @keydown.escape.window="if(open) cancel()"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="cancel()"></div>

    {{-- Card --}}
    <div class="relative bg-white rounded-2xl shadow-2xl border border-pink-100 max-w-sm w-full p-7"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-1"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-1">

        {{-- Icon --}}
        <div class="flex justify-center mb-5">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl"
                 :class="danger ? 'bg-rose-100' : 'bg-amber-100'">
                <svg class="h-7 w-7" :class="danger ? 'text-rose-500' : 'text-amber-500'"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
        </div>

        {{-- Title --}}
        <h3 class="text-center text-base font-bold text-gray-900 mb-2" x-text="title"></h3>

        {{-- Message --}}
        <p class="text-center text-sm text-gray-500 leading-relaxed mb-7" x-text="message"></p>

        {{-- Buttons --}}
        <div class="flex gap-3">
            <button type="button" @click="cancel()"
                    class="flex-1 rounded-xl border border-pink-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-pink-50 hover:text-rose-600 transition-all duration-200">
                Batalkan
            </button>
            <button type="button" @click="confirm()"
                    class="flex-1 rounded-xl px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:shadow-md transition-all duration-200"
                    :class="danger ? 'bg-rose-500 hover:bg-rose-600' : 'bg-amber-500 hover:bg-amber-600'"
                    x-text="action">
            </button>
        </div>
    </div>
</div>

<script>
function confirmModalData() {
    return {
        open: false,
        title: '',
        message: '',
        action: 'Ya, Lanjutkan',
        danger: true,
        formId: null,
        show: function(detail) {
            this.title   = detail.title   || 'Konfirmasi';
            this.message = detail.message || 'Apakah Anda yakin?';
            this.action  = detail.action  || 'Ya, Lanjutkan';
            this.danger  = detail.danger  !== false;
            this.formId  = detail.formId  || null;
            this.open    = true;
        },
        confirm: function() {
            var id = this.formId;
            this.open = false;
            if (id) {
                setTimeout(function() {
                    var form = document.getElementById(id);
                    if (form) form.submit();
                }, 150);
            }
        },
        cancel: function() {
            this.open = false;
        }
    };
}

function openConfirm(title, message, action, formId, isDanger) {
    window.dispatchEvent(new CustomEvent('open-confirm', {
        detail: {
            title:   title,
            message: message,
            action:  action,
            formId:  formId,
            danger:  isDanger !== false
        }
    }));
}
</script>
</body>
</html>
