<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' — ' : '' }}{{ config('app.name', 'Toko Emas') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/css/customer.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; background: #0f0e17; overflow-x: hidden; }
        .gold-gradient { background: linear-gradient(135deg, #f59e0b, #d97706, #92400e); }
        .progress-bar { background: linear-gradient(90deg, #f59e0b, #d97706); }
        .glass { background: rgba(255,255,255,0.04); backdrop-filter: blur(12px); border: 1px solid rgba(245,158,11,0.15); }
        .sidebar-link { transition: color 0.15s ease, background-color 0.15s ease, border-color 0.15s ease; color: #9ca3af; display: flex; align-items: center; gap: 0.75rem; padding: 0.625rem 1.25rem; }
        .sidebar-link:hover { background: rgba(245,158,11,0.1); color: #fbbf24; }
        .sidebar-link.active { background: rgba(245,158,11,0.15); color: #fbbf24; border-right: 3px solid #f59e0b; }
        .sidebar-section { font-size: 0.65rem; letter-spacing: 0.1em; text-transform: uppercase; color: #374151; padding: 0 1.25rem; margin-top: 1rem; margin-bottom: 0.25rem; font-weight: 600; }
        .sidebar-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:25; pointer-events:none; }
        .sidebar-overlay.active { display:block; pointer-events:auto; }
        @media (max-width: 1023px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
        }
    </style>

    {{ $styles ?? '' }}
    {{ $scripts ?? '' }}
</head>
<body class="text-white flex min-h-screen">

    <!-- ============================================================ -->
    <!-- SIDEBAR OVERLAY (Mobile) -->
    <!-- ============================================================ -->
    <div id="sidebar-overlay" class="sidebar-overlay" onclick="toggleSidebar()"></div>

    <!-- ============================================================ -->
    <!-- SIDEBAR (Customer) -->
    <!-- ============================================================ -->
    <aside id="sidebar" class="sidebar w-64 flex flex-col fixed z-30 transition-transform duration-300 lg:translate-x-0"
           style="background:#1a1830; border-right:1px solid rgba(245,158,11,0.1); top:0; bottom:0; overflow:hidden;">

        <!-- Logo / Brand -->
        <div class="p-5 flex items-center gap-3" style="border-bottom:1px solid rgba(245,158,11,0.1);">
            <div class="w-10 h-10 gold-gradient rounded-xl flex items-center justify-center font-bold text-sm shadow-lg">SB</div>
            <div>
                <p class="font-bold text-yellow-400 text-sm leading-tight" style="font-family:'Playfair Display',serif;">Sinar Baru II</p>
                <p class="text-[11px] text-amber-400/90 font-medium mt-0.5">Teluk Lubuk Muara Enim</p>
                <p class="text-xs text-gray-500 mt-0.5">Member Area</p>
            </div>
            <!-- Close button (mobile) -->
            <button onclick="toggleSidebar()" class="ml-auto lg:hidden text-gray-400 hover:text-white text-xl">&times;</button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 py-4 overflow-y-auto text-sm">

            <p class="sidebar-section">Menu Utama</p>
            <a href="{{ route('customer.dashboard') }}" class="sidebar-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
                <span class="text-base w-5 text-center">🏠</span> Beranda
            </a>
            <a href="{{ route('customer.catalog.index') }}" class="sidebar-link {{ request()->routeIs('customer.catalog*') ? 'active' : '' }}">
                <span class="text-base w-5 text-center">💍</span> Katalog Produk
            </a>
            <p class="sidebar-section">Aktivitas Saya</p>
            <a href="{{ route('customer.negotiations.index') }}" class="sidebar-link {{ request()->routeIs('customer.negotiations*') ? 'active' : '' }}">
                <span class="text-base w-5 text-center">🤝</span> Tawar Harga
            </a>
            <a href="{{ route('customer.reservations.index') }}" class="sidebar-link {{ request()->routeIs('customer.reservations*') ? 'active' : '' }}">
                <span class="text-base w-5 text-center">📋</span> Reservasi Saya
            </a>
            <a href="{{ route('customer.transactions.index') }}" class="sidebar-link {{ request()->routeIs('customer.transactions*') ? 'active' : '' }}">
                <span class="text-base w-5 text-center">🧾</span> Riwayat Transaksi
            </a>
            <a href="{{ route('customer.installments.index') }}" class="sidebar-link {{ request()->routeIs('customer.installments*') ? 'active' : '' }}">
                <span class="text-base w-5 text-center">📅</span> Cicilan Saya
            </a>
            <a href="{{ route('customer.pawns.index') }}" class="sidebar-link {{ request()->routeIs('customer.pawns*') ? 'active' : '' }}">
                <span class="text-base w-5 text-center">🏦</span> Gadai Saya
            </a>

            <p class="sidebar-section">Loyalty</p>
            <a href="{{ route('customer.rewards.index') }}" class="sidebar-link {{ request()->routeIs('customer.rewards*') ? 'active' : '' }}">
                <span class="text-base w-5 text-center">🎁</span> Reward & Poin
            </a>

            <!-- Divider -->
            <div style="border-top:1px solid rgba(245,158,11,0.1); margin:1rem 0;"></div>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar-link w-full text-red-400 hover:text-red-300 hover:bg-red-900/20">
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
                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- ============================================================ -->
    <!-- MAIN CONTENT -->
    <!-- ============================================================ -->
    <div class="flex-1 min-w-0 w-full flex flex-col min-h-screen ml-0 lg:ml-64">

        <!-- Top Bar -->
        <header class="sticky top-0 z-20 flex items-center justify-between px-4 lg:px-8 py-3 lg:py-4"
                style="background:rgba(15,14,23,0.8); backdrop-filter:blur(12px); border-bottom:1px solid rgba(245,158,11,0.08);">
            <div class="flex items-center gap-3">
                <!-- Hamburger (mobile) -->
                <button onclick="toggleSidebar()" class="lg:hidden text-gray-300 hover:text-white text-xl p-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div>
                    @isset($pageTitle)
                        <h1 class="text-base lg:text-lg font-bold text-white" style="font-family:'Playfair Display',serif;">{{ $pageTitle }}</h1>
                    @endisset
                    @isset($breadcrumb)
                        <p class="text-xs text-gray-500 mt-0.5 hidden sm:block">{{ $breadcrumb }}</p>
                    @else
                        <p class="text-xs text-gray-500 mt-0.5 hidden sm:block">{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
                    @endisset
                </div>
            </div>
            <div class="flex items-center gap-2 lg:gap-3">
                <!-- Notification Bell -->
                <button class="glass w-9 h-9 flex items-center justify-center rounded-xl hover:bg-white/10 transition">
                    <span>🔔</span>
                </button>
                <!-- Profile -->
                <div class="glass flex items-center gap-2 px-2 lg:px-3 py-1.5 rounded-xl">
                    <div class="w-7 h-7 gold-gradient rounded-full flex items-center justify-center text-xs font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="text-sm text-gray-200 hidden sm:inline">{{ auth()->user()->name }}</span>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 min-w-0 w-full p-4 lg:p-8">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="px-4 lg:px-8 py-4 text-xs text-gray-600 text-center" style="border-top:1px solid rgba(245,158,11,0.08);">
            © {{ date('Y') }} Toko Emas Sinar Baru II
        </footer>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
        }
    </script>

</body>
</html>
