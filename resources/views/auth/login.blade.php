<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — hevafsid</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="min-h-screen flex bg-gradient-to-br from-rose-50 via-pink-50 to-fuchsia-50">

    {{-- Left illustration panel --}}
    <div class="hidden lg:flex lg:w-1/2 flex-col items-center justify-center bg-gradient-to-br from-rose-500 to-pink-600 p-12">
        <div class="text-center text-white">
            <div class="mb-6 flex justify-center">
                <img src="/images/hevafsid.jpeg" alt="hevafsid" class="h-20 w-20 rounded-2xl object-cover shadow-lg">
            </div>
            <h1 class="text-4xl font-bold mb-2">hevafsid</h1>
            <p class="text-pink-100 text-lg">Sistem Manajemen Inventori & Keuangan</p>
            <p class="text-pink-200 text-sm mt-2">Hijab & Fashion Store Management</p>

            <div class="mt-12 grid grid-cols-2 gap-4 text-left">
                @foreach(['FIFO Inventory', 'Barcode Scanning', 'Financial Reports', 'Role-based Access'] as $feature)
                <div class="flex items-center gap-2 rounded-xl bg-white/15 px-4 py-3">
                    <svg class="h-5 w-5 text-pink-200 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span class="text-sm text-pink-100">{{ $feature }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Login form --}}
    <div class="flex w-full lg:w-1/2 items-center justify-center p-8">
        <div class="w-full max-w-md">
            <div class="lg:hidden flex justify-center mb-8">
                <div class="flex items-center gap-3">
                    <img src="/images/hevafsid.jpeg" alt="hevafsid" class="h-10 w-10 rounded-xl object-cover">
                    <span class="text-xl font-bold text-gray-900">hevafsid</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border border-pink-100 p-8">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900">Selamat Datang!</h2>
                    <p class="text-gray-500 text-sm mt-1">Masuk ke akun hevafsid Anda</p>
                </div>

                @if($errors->any())
                <div class="mb-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                               class="w-full rounded-xl border border-pink-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-rose-300 focus:border-rose-400 transition"
                               placeholder="email@example.com">
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <label class="block text-sm font-medium text-gray-700">Password</label>
                            @if(Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs text-rose-500 hover:text-rose-700">
                                Lupa password?
                            </a>
                            @endif
                        </div>
                        <input type="password" name="password" required
                               class="w-full rounded-xl border border-pink-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-rose-300 focus:border-rose-400 transition"
                               placeholder="••••••••">
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="remember" id="remember"
                               class="h-4 w-4 rounded border-pink-300 text-rose-500 focus:ring-rose-300">
                        <label for="remember" class="text-sm text-gray-600">Ingat saya</label>
                    </div>

                    <button type="submit"
                            class="w-full bg-gradient-to-r from-rose-500 to-pink-600 hover:from-rose-600 hover:to-pink-700 text-white font-semibold py-3 rounded-xl transition-all duration-200 shadow hover:shadow-md">
                        Masuk
                    </button>
                </form>
            </div>

            <p class="text-center text-xs text-gray-400 mt-6">
                © {{ date('Y') }} hevafsid. Sistem Manajemen Inventori Fashion.
            </p>
        </div>
    </div>
</body>
</html>
