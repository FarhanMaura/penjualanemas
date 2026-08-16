<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Member — Toko Emas Sinar Baru II</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #F4EDD9;
            color: #1A2E2B;
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }
        .gold-gradient {
            background: linear-gradient(138.58deg, #E3D193 3.56%, #C6A443 91.71%);
            color: #042623 !important;
            font-weight: 700;
        }
        .card-auth {
            background: #ffffff;
            border: 1px solid #e8e3d5;
            box-shadow: 0 15px 35px -5px rgba(8, 92, 84, 0.08), 0 2px 10px rgba(0, 0, 0, 0.04);
        }
        .input-field {
            width: 100%;
            background: #ffffff;
            border: 1px solid #d5cebe;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            color: #1A2E2B;
            font-size: 0.875rem;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .input-field:focus {
            border-color: #085C54;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(8, 92, 84, 0.15);
        }
        .input-field::placeholder { color: #94a3b8; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 py-12">

    <div class="w-full max-w-md relative z-10">
        {{-- Brand --}}
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex flex-col items-center gap-2 group">
                <div class="w-14 h-14 gold-gradient rounded-2xl flex items-center justify-center font-bold text-xl shadow-lg border border-[#C6A443] transition-transform group-hover:scale-105">SB</div>
                <p class="font-bold text-[#042623] text-2xl drop-shadow-sm font-playfair">Sinar Baru II</p>
                <p class="text-xs text-[#085C54] font-semibold">Buat Akun Member Eksklusif</p>
            </a>
        </div>

        {{-- Card --}}
        <div class="card-auth rounded-3xl p-8">
            <h1 class="text-2xl font-bold text-[#042623] mb-1.5 font-playfair">Daftar Jadi Member</h1>
            <p class="text-xs text-slate-500 mb-6 font-normal">Dapatkan akses ke semua fitur, simulasi cicilan, & reward</p>

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                {{-- Name --}}
                <div>
                    <label class="block text-xs text-[#042623] mb-1.5 font-bold">Nama Lengkap</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}"
                           class="input-field" placeholder="Nama lengkap Anda" required autofocus autocomplete="name">
                    @error('name')
                    <p class="text-xs text-red-600 mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-xs text-[#042623] mb-1.5 font-bold">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                           class="input-field" placeholder="email@contoh.com" required autocomplete="username">
                    @error('email')
                    <p class="text-xs text-red-600 mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-xs text-[#042623] mb-1.5 font-bold">Password</label>
                    <input id="password" type="password" name="password"
                           class="input-field" placeholder="Minimal 8 karakter" required autocomplete="new-password">
                    @error('password')
                    <p class="text-xs text-red-600 mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label class="block text-xs text-[#042623] mb-1.5 font-bold">Konfirmasi Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation"
                           class="input-field" placeholder="Ulangi password" required autocomplete="new-password">
                    @error('password_confirmation')
                    <p class="text-xs text-red-600 mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Benefit Info (EmasKITA Theme #F4EDD9) --}}
                <div class="p-3.5 rounded-2xl text-xs space-y-1.5 bg-[#F4EDD9] border border-[#E3D193]">
                    <p class="text-[#085C54] font-bold mb-1">✨ Keuntungan Member Sinar Baru II:</p>
                    <p class="text-[#042623] font-medium">📋 Buat reservasi produk emas online</p>
                    <p class="text-[#042623] font-medium">💰 Pantau harga emas real-time & tawar harga</p>
                    <p class="text-[#042623] font-medium">🎁 Reward eksklusif di setiap 10 transaksi</p>
                </div>

                {{-- Submit --}}
                <button type="submit" class="w-full py-3.5 bg-[#085C54] text-white hover:text-[#E3D193] rounded-xl font-bold text-sm shadow-lg hover:bg-[#063e39] transition hover:scale-[1.01] mt-2 border border-[#063e39]">
                    Buat Akun Sekarang
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-slate-200 text-center">
                <p class="text-xs text-slate-500 font-normal">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="text-[#085C54] font-bold hover:text-[#C6A443] ml-1">
                        Masuk di sini
                    </a>
                </p>
            </div>
        </div>

        {{-- Back to Home --}}
        <div class="text-center mt-6">
            <a href="{{ route('home') }}" class="text-xs text-slate-600 font-semibold hover:text-[#085C54] transition inline-flex items-center gap-1.5">
                ← Kembali ke Beranda
            </a>
        </div>
    </div>

</body>
</html>
