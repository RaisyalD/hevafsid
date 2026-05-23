<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password — hevafsid</title>
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
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
            </div>
            <h2 class="text-xl font-bold text-center text-gray-900 mb-1">Lupa Password?</h2>
            <p class="text-sm text-center text-gray-500 mb-6">Masukkan email dan kami akan kirimkan link reset password.</p>

            @if(session('status'))
            <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
            @endif

            @if($errors->any())
            <div class="mb-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <input type="email" name="email" required autofocus value="{{ old('email') }}"
                           class="w-full rounded-xl border border-pink-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-rose-300">
                </div>
                <button type="submit"
                        class="w-full bg-gradient-to-r from-rose-500 to-pink-600 text-white font-semibold py-3 rounded-xl transition hover:shadow-md">
                    Kirim Link Reset
                </button>
            </form>

            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="text-sm text-rose-500 hover:text-rose-700">← Kembali ke Login</a>
            </div>
        </div>
    </div>
</body>
</html>
