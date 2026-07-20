<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk — Toko Emas Sinar Baru II</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family:'Inter',sans-serif; background:#0f0e17; }
        .gold-gradient { background:linear-gradient(135deg,#f59e0b,#d97706,#92400e); }
        .glass { background:rgba(255,255,255,0.04); backdrop-filter:blur(12px); border:1px solid rgba(245,158,11,0.15); }
        .input-field {
            width:100%; background:rgba(255,255,255,0.05); border:1px solid rgba(245,158,11,0.2);
            border-radius:0.75rem; padding:0.75rem 1rem; color:#fff; font-size:0.875rem;
            outline:none; transition:border-color 0.2s;
        }
        .input-field:focus { border-color:rgba(245,158,11,0.6); background:rgba(255,255,255,0.07); }
        .input-field::placeholder { color:#6b7280; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4" style="background:radial-gradient(ellipse at top,#2d1a0a 0%,#0f0e17 60%);">

    <div class="w-full max-w-md">
        {{-- Brand --}}
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex flex-col items-center gap-2">
                <div class="w-14 h-14 gold-gradient rounded-2xl flex items-center justify-center font-bold text-xl shadow-lg" style="box-shadow:0 0 30px rgba(245,158,11,0.4);">SB</div>
                <p class="font-bold text-yellow-400 text-lg" style="font-family:'Playfair Display',serif;">Sinar Baru II</p>
                <p class="text-xs text-gray-500">Toko Emas Terpercaya</p>
            </a>
        </div>

        {{-- Card --}}
        <div class="glass rounded-3xl p-8">
            <h1 class="text-xl font-bold text-white mb-1" style="font-family:'Playfair Display',serif;">Selamat Datang Kembali</h1>
            <p class="text-sm text-gray-400 mb-6">Masuk ke akun member Anda</p>

            {{-- Session Status --}}
            @if(session('status'))
            <div class="mb-4 p-3 rounded-xl text-sm text-green-400" style="background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.2);">
                {{ session('status') }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                {{-- Email --}}
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                           class="input-field" placeholder="email@contoh.com" required autofocus autocomplete="username">
                    @error('email')
                    <p class="text-xs text-red-400 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">Password</label>
                    <input id="password" type="password" name="password"
                           class="input-field" placeholder="••••••••" required autocomplete="current-password">
                    @error('password')
                    <p class="text-xs text-red-400 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember + Forgot --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded" style="accent-color:#f59e0b;">
                        <span class="text-xs text-gray-400">Ingat saya</span>
                    </label>
                    @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs text-yellow-400 hover:underline">Lupa password?</a>
                    @endif
                </div>

                {{-- Submit --}}
                <button type="submit" class="w-full py-3 rounded-xl font-semibold text-sm text-white transition hover:opacity-90 mt-2"
                        style="background:linear-gradient(135deg,#f59e0b,#d97706);box-shadow:0 0 20px rgba(245,158,11,0.3);">
                    Masuk ke Akun
                </button>
            </form>

            {{-- Register Link --}}
            @if(Route::has('register'))
            <p class="text-center text-xs text-gray-500 mt-6">
                Belum punya akun?
                <a href="{{ route('register') }}" class="text-yellow-400 hover:underline font-medium">Daftar sekarang</a>
            </p>
            @endif
        </div>

        <p class="text-center text-xs text-gray-700 mt-6">
            <a href="{{ route('home') }}" class="hover:text-gray-500 transition">← Kembali ke Beranda</a>
        </p>
    </div>

</body>
</html>
