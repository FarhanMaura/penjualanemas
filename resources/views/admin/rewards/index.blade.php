<x-admin-app>
    <x-slot name="pageTitle">Program Reward & Loyalty</x-slot>
    <x-slot name="breadcrumb">Monitor program loyalitas dan tier pelanggan</x-slot>

    {{-- Statistik Utama --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6 mb-8">
        <div class="glass rounded-2xl p-5">
            <div class="flex justify-between items-start mb-3">
                <p class="text-xs text-gray-400 uppercase tracking-wider">Total Poin Beredar</p>
                <span class="text-2xl">⭐</span>
            </div>
            <p class="text-3xl font-bold text-yellow-400">{{ number_format($totalPoints) }}</p>
            <p class="text-xs text-gray-500 mt-1">poin aktif di semua member</p>
        </div>
        <div class="glass rounded-2xl p-5">
            <div class="flex justify-between items-start mb-3">
                <p class="text-xs text-gray-400 uppercase tracking-wider">Member Aktif Reward</p>
                <span class="text-2xl">🎁</span>
            </div>
            <p class="text-3xl font-bold text-green-400">{{ number_format($activeCount) }}</p>
            <p class="text-xs text-gray-500 mt-1">pelanggan dengan poin > 0</p>
        </div>
        <div class="glass rounded-2xl p-5">
            <div class="flex justify-between items-start mb-3">
                <p class="text-xs text-gray-400 uppercase tracking-wider">Total Reward Diklaim</p>
                <span class="text-2xl">✅</span>
            </div>
            <p class="text-3xl font-bold text-gray-300">{{ number_format($usedCount) }}</p>
            <p class="text-xs text-gray-500 mt-1">total klaim sepanjang waktu</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-6">
        {{-- Konfigurasi Tier --}}
        <div class="glass rounded-2xl p-6">
            <h3 class="font-semibold text-yellow-400 mb-5 flex items-center gap-2">
                🏆 Konfigurasi Tier Loyalty
            </h3>
            <div class="space-y-3">
                @foreach($tierConfig as $t)
                <div class="flex items-center justify-between p-4 rounded-xl" style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06);">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">{{ $t['icon'] }}</span>
                        <div>
                            <p class="font-semibold text-sm" style="color:{{ $t['color'] }}">{{ $t['name'] }}</p>
                            <p class="text-xs text-gray-500">{{ $t['range'] }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-300">{{ $t['benefit'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-4 p-4 rounded-xl text-xs text-gray-400" style="background:rgba(245,158,11,0.05); border:1px solid rgba(245,158,11,0.1);">
                💡 Tier naik otomatis saat pelanggan mencapai jumlah transaksi yang ditentukan. Hadiah milestone diberikan tiap kelipatan 10 transaksi.
            </div>
        </div>

        {{-- Reward Diklaim Terbaru --}}
        <div class="glass rounded-2xl p-6">
            <h3 class="font-semibold text-yellow-400 mb-5 flex items-center gap-2">
                🎁 Klaim Reward Terbaru
            </h3>

            @if($recentRedemptions->isEmpty())
            <div class="text-center py-10">
                <span class="text-5xl">📭</span>
                <p class="text-gray-500 text-sm mt-3">Belum ada reward yang diklaim</p>
                <p class="text-gray-600 text-xs mt-1">Data akan muncul setelah pelanggan klaim milestone reward</p>
            </div>
            @else
            <div class="space-y-3">
                @foreach($recentRedemptions as $r)
                <div class="flex items-center justify-between p-3 rounded-xl" style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06);">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold"
                             style="background:linear-gradient(135deg,#f59e0b,#d97706);">
                            {{ strtoupper(substr($r->user->name ?? 'U', 0, 2)) }}
                        </div>
                        <div>
                            <p class="font-medium text-sm text-white">{{ $r->user->name ?? 'Pelanggan' }}</p>
                            <p class="text-xs text-gray-500">{{ $r->reward_type ?? 'Milestone Reward' }}</p>
                        </div>
                    </div>
                    <p class="text-xs text-gray-600">{{ $r->created_at->isoFormat('D MMM Y') }}</p>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- Link ke Daftar Pelanggan --}}
    <div class="mt-8 glass rounded-2xl p-6 flex items-center justify-between">
        <div>
            <h3 class="font-semibold text-white">Lihat Status Tier Semua Pelanggan</h3>
            <p class="text-sm text-gray-400 mt-1">Kelola dan monitor progress tier tiap pelanggan secara individual</p>
        </div>
        <a href="{{ route('admin.customers.index') }}" class="px-6 py-3 rounded-xl font-bold text-white transition hover:scale-105 whitespace-nowrap"
           style="background:linear-gradient(135deg,#f59e0b,#d97706);">
            Lihat Semua Pelanggan →
        </a>
    </div>
</x-admin-app>
