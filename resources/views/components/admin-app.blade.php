<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' — ' : '' }}{{ config('app.name', 'Toko Emas') }} Admin</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/css/admin.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; background: #0f0e17; overflow-x: hidden; }
        .gold-gradient { background: linear-gradient(135deg, #f59e0b, #d97706, #92400e); }
        .glass { background: rgba(255,255,255,0.04); backdrop-filter: blur(12px); border: 1px solid rgba(245,158,11,0.15); }
        .sidebar-link { transition: color 0.15s ease, background-color 0.15s ease, border-color 0.15s ease; color: #9ca3af; }
        .sidebar-link:hover { background: rgba(245,158,11,0.1); color: #fbbf24; }
        .sidebar-link.active { background: rgba(245,158,11,0.15); color: #fbbf24; border-right: 3px solid #f59e0b; }
        .sidebar-section { font-size: 0.65rem; letter-spacing: 0.1em; text-transform: uppercase; color: #4b5563; padding: 0 1rem; margin-top: 1rem; margin-bottom: 0.25rem; font-weight: 600; }
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
    <!-- SIDEBAR -->
    <!-- ============================================================ -->
    <aside id="sidebar" class="sidebar w-64 flex flex-col fixed z-30 transition-transform duration-300 lg:translate-x-0"
           style="background:#1a1830; border-right:1px solid rgba(245,158,11,0.1); top:0; bottom:0; overflow:hidden;">

        <!-- Logo / Brand -->
        <div class="p-5 flex items-center gap-3" style="border-bottom:1px solid rgba(245,158,11,0.1);">
            <div class="w-10 h-10 gold-gradient rounded-xl flex items-center justify-center font-bold text-sm shadow-lg">SB</div>
            <div>
                <p class="font-bold text-yellow-400 text-sm leading-tight" style="font-family:'Playfair Display',serif;">Sinar Baru II</p>
                <p class="text-[11px] text-amber-400/90 font-medium mt-0.5">Teluk Lubuk Muara Enim</p>
                <p class="text-xs text-gray-500 mt-0.5">Panel Admin</p>
            </div>
            <!-- Close button (mobile) -->
            <button onclick="toggleSidebar()" class="ml-auto lg:hidden text-gray-400 hover:text-white text-xl">&times;</button>
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
            <a href="{{ route('admin.negotiations.index') }}" class="sidebar-link flex items-center gap-3 px-5 py-2.5 {{ request()->routeIs('admin.negotiations.*') ? 'active' : '' }}">
                <span class="text-base w-5 text-center">🤝</span> Tawar Harga
            </a>
            <a href="{{ route('admin.reservations.index') }}" class="sidebar-link flex items-center gap-3 px-5 py-2.5 {{ request()->routeIs('admin.reservations.*') ? 'active' : '' }}">
                <span class="text-base w-5 text-center">📋</span> Reservasi
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
    <main class="flex-1 min-w-0 w-full p-4 lg:p-8 min-h-screen flex flex-col ml-0 lg:ml-64">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 lg:mb-8">
            <div class="flex items-center gap-3">
                <!-- Hamburger (mobile) -->
                <button onclick="toggleSidebar()" class="lg:hidden text-gray-300 hover:text-white text-xl p-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div>
                    @isset($pageTitle)
                        <h1 class="text-xl lg:text-2xl font-bold" style="font-family:'Playfair Display',serif;">{{ $pageTitle }}</h1>
                    @endisset
                    @isset($breadcrumb)
                        <p class="text-xs lg:text-sm text-gray-400 mt-1">{{ $breadcrumb }}</p>
                    @else
                        <p class="text-xs lg:text-sm text-gray-400 mt-1">{{ now()->isoFormat('dddd, D MMMM Y') }} • {{ now()->format('H:i') }} WIB</p>
                    @endisset
                </div>
            </div>
            <div class="flex items-center gap-3 lg:gap-4">
                <div class="relative">
                    <button class="glass px-3 lg:px-4 py-2 rounded-xl text-sm flex items-center gap-2 hover:bg-white/10 transition">
                        🔔 <span class="text-yellow-400 font-bold text-xs">5</span>
                    </button>
                </div>
                <div class="flex items-center gap-2 lg:gap-3 glass px-3 lg:px-4 py-2 rounded-xl">
                    <div class="w-8 h-8 gold-gradient rounded-full flex items-center justify-center text-sm font-bold shadow">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="hidden sm:block">
                        <p class="text-sm font-medium">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400">Administrator</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="flex-1">
            {{ $slot }}
        </div>
    </main>

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
