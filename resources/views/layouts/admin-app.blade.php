<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' — ' : '' }}{{ config('app.name', 'Toko Emas') }} Admin</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/admin.css', 'resources/js/app.js'])

    {{ $styles ?? '' }}
</head>
<body class="text-white flex min-h-screen">

    <!-- ============================================================ -->
    <!-- SIDEBAR -->
    <!-- ============================================================ -->
    <aside id="sidebar" class="w-64 flex flex-col fixed z-30 transition-transform duration-300"
           style="background:#1a1830; border-right:1px solid rgba(245,158,11,0.1); top:0; bottom:0; overflow:hidden;">

        <!-- Logo / Brand -->
        <div class="p-5 flex items-center gap-3" style="border-bottom:1px solid rgba(245,158,11,0.1);">
            <div class="w-10 h-10 gold-gradient rounded-xl flex items-center justify-center font-bold text-sm shadow-lg">SB</div>
            <div>
                <p class="font-bold text-yellow-400 text-sm leading-tight" style="font-family:'Playfair Display',serif;">Sinar Baru II</p>
                <p class="text-xs text-gray-500 mt-0.5">Panel Admin</p>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 py-4 overflow-y-auto text-sm">

            <p class="sidebar-section">Utama</p>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link flex items-center gap-3 px-5 py-2.5 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="text-base w-5 text-center">📊</span> Dashboard
            </a>

            <p class="sidebar-section">Katalog & Harga</p>
            <a href="{{ route('admin.products.index') }}" class="sidebar-link flex items-center gap-3 px-5 py-2.5 {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <span class="text-base w-5 text-center">📦</span> Produk
            </a>
            <a href="{{ route('admin.gold-prices.index') }}" class="sidebar-link flex items-center gap-3 px-5 py-2.5 {{ request()->routeIs('admin.gold-prices.*') ? 'active' : '' }}">
                <span class="text-base w-5 text-center">💰</span> Harga Emas
            </a>

            <p class="sidebar-section">Operasional</p>
            <a href="{{ route('admin.reservations.index') }}" class="sidebar-link flex items-center justify-between px-5 py-2.5 {{ request()->routeIs('admin.reservations.*') ? 'active' : '' }}">
                <span class="flex items-center gap-3"><span class="text-base w-5 text-center">📋</span> Reservasi</span>
                @php $pendingCount = \App\Models\Reservation::where('status', 'pending')->count(); @endphp
                @if($pendingCount > 0)
                <span class="bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full leading-none">{{ $pendingCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.transactions.index') }}" class="sidebar-link flex items-center gap-3 px-5 py-2.5 {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">
                <span class="text-base w-5 text-center">✅</span> Transaksi
            </a>
            <a href="{{ route('admin.installments.index') }}" class="sidebar-link flex items-center gap-3 px-5 py-2.5 {{ request()->routeIs('admin.installments.*') ? 'active' : '' }}">
                <span class="text-base w-5 text-center">📅</span> Cicilan
            </a>
            <a href="{{ route('admin.pawns.index') }}" class="sidebar-link flex items-center gap-3 px-5 py-2.5 {{ request()->routeIs('admin.pawns.*') ? 'active' : '' }}">
                <span class="text-base w-5 text-center">🏦</span> Gadai
            </a>

            <p class="sidebar-section">CRM</p>
            <a href="{{ route('admin.customers.index') }}" class="sidebar-link flex items-center gap-3 px-5 py-2.5 {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                <span class="text-base w-5 text-center">👥</span> Pelanggan
            </a>
            <a href="{{ route('admin.rewards.index') }}" class="sidebar-link flex items-center gap-3 px-5 py-2.5 {{ request()->routeIs('admin.rewards.*') ? 'active' : '' }}">
                <span class="text-base w-5 text-center">🎁</span> Program Reward
            </a>
            <a href="{{ route('admin.reports.index') }}" class="sidebar-link flex items-center gap-3 px-5 py-2.5 {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <span class="text-base w-5 text-center">📈</span> Laporan
            </a>

            <!-- Divider -->
            <div style="border-top:1px solid rgba(245,158,11,0.1); margin:1rem 0;"></div>

            <a href="{{ url('/') }}" target="_blank" class="sidebar-link flex items-center gap-3 px-5 py-2.5">
                <span class="text-base w-5 text-center">🌐</span> Lihat Website
            </a>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar-link w-full flex items-center gap-3 px-5 py-2.5 text-red-400 hover:text-red-300 hover:bg-red-900/20">
                    <span class="text-base w-5 text-center">🚪</span> Keluar
                </button>
            </form>
        </nav>

        <!-- User Info at Bottom -->
        <div class="p-4" style="border-top:1px solid rgba(245,158,11,0.1);">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 gold-gradient rounded-full flex items-center justify-center text-sm font-bold shadow">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500">Administrator</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- ============================================================ -->
    <!-- MAIN CONTENT -->
    <!-- ============================================================ -->
    <div class="ml-64 flex-1 flex flex-col min-h-screen">

        <!-- Top Bar -->
        <header class="sticky top-0 z-20 flex items-center justify-between px-8 py-4"
                style="background:rgba(15,14,23,0.8); backdrop-filter:blur(12px); border-bottom:1px solid rgba(245,158,11,0.08);">
            <div>
                @isset($pageTitle)
                    <h1 class="text-lg font-bold text-white" style="font-family:'Playfair Display',serif;">{{ $pageTitle }}</h1>
                @endisset
                @isset($breadcrumb)
                    <p class="text-xs text-gray-500 mt-0.5">{{ $breadcrumb }}</p>
                @else
                    <p class="text-xs text-gray-500 mt-0.5">{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
                @endisset
            </div>
            <div class="flex items-center gap-3">
                <!-- Notification Bell -->
                <button class="glass w-9 h-9 flex items-center justify-center rounded-xl hover:bg-white/10 transition relative">
                    <span>🔔</span>
                </button>
                <!-- Profile -->
                <div class="glass flex items-center gap-2 px-3 py-1.5 rounded-xl">
                    <div class="w-7 h-7 gold-gradient rounded-full flex items-center justify-center text-xs font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="text-sm text-gray-200">{{ auth()->user()->name }}</span>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-8">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="px-8 py-4 text-xs text-gray-600 text-center" style="border-top:1px solid rgba(245,158,11,0.08);">
            © {{ date('Y') }} Toko Emas Sinar Baru II — Panel Admin
        </footer>
    </div>

</body>
</html>
