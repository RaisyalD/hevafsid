<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — hevafsid</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-rose-50 via-pink-50 to-fuchsia-50 p-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl border border-pink-100 p-8">
            <div class="flex justify-center mb-6">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-rose-500 to-pink-600">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
            </div>
            <h2 class="text-xl font-bold text-center text-gray-900 mb-1">Buat Password Baru</h2>
            <p class="text-sm text-center text-gray-500 mb-6">Masukkan password baru untuk akun Anda.</p>

            @if($errors->any())
            <div class="mb-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <input type="email" name="email" required value="{{ old('email', $request->email) }}"
                           class="w-full rounded-xl border border-pink-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-rose-300">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Password Baru</label>
                    <input type="password" name="password" required autofocus
                           class="w-full rounded-xl border border-pink-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-rose-300">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full rounded-xl border border-pink-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-rose-300">
                </div>

                <button type="submit"
                        class="w-full bg-gradient-to-r from-rose-500 to-pink-600 text-white font-semibold py-3 rounded-xl transition hover:shadow-md">
                    Simpan Password Baru
                </button>
            </form>

            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="text-sm text-rose-500 hover:text-rose-700">← Kembali ke Login</a>
            </div>
        </div>
    </div>
</body>
</html>