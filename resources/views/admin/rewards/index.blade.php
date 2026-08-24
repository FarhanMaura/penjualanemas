<x-admin-app>
    <x-slot name="pageTitle">Program Reward & Loyalty</x-slot>
    <x-slot name="breadcrumb">Monitor program loyalitas dan tier pelanggan Toko Emas Sinar Baru II</x-slot>

    {{-- Statistik Utama --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6 mb-8">
        <div class="glass rounded-2xl p-5 bg-white border border-[#e8e3d5] shadow-sm">
            <div class="flex justify-between items-start mb-3">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Poin Beredar</p>
                <span class="text-2xl">⭐</span>
            </div>
            <p class="text-3xl font-black text-[#C6A443]">{{ number_format($totalPoints) }}</p>
            <p class="text-xs text-slate-500 font-semibold mt-1">poin aktif di semua member</p>
        </div>
        <div class="glass rounded-2xl p-5 bg-emerald-50/60 border border-emerald-200 shadow-sm">
            <div class="flex justify-between items-start mb-3">
                <p class="text-xs font-bold text-emerald-900 uppercase tracking-wider">Member Aktif Reward</p>
                <span class="text-2xl">🎁</span>
            </div>
            <p class="text-3xl font-black text-[#085C54]">{{ number_format($activeCount) }}</p>
            <p class="text-xs text-emerald-800 font-semibold mt-1">pelanggan dengan poin > 0</p>
        </div>
        <div class="glass rounded-2xl p-5 bg-blue-50/60 border border-blue-200 shadow-sm">
            <div class="flex justify-between items-start mb-3">
                <p class="text-xs font-bold text-blue-900 uppercase tracking-wider">Total Reward Diklaim</p>
                <span class="text-2xl">✅</span>
            </div>
            <p class="text-3xl font-black text-blue-900">{{ number_format($usedCount) }}</p>
            <p class="text-xs text-blue-800 font-semibold mt-1">total klaim sepanjang waktu</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Konfigurasi Tier --}}
        <div class="glass rounded-2xl p-6 bg-white border border-[#e8e3d5] shadow-md">
            <h3 class="font-bold text-slate-900 font-playfair mb-5 flex items-center gap-2 text-base">
                🏆 Konfigurasi Tier Loyalty
            </h3>
            <div class="space-y-3">
                @foreach($tierConfig as $t)
                <div class="flex items-center justify-between p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">{{ $t['icon'] }}</span>
                        <div>
                            <p class="font-bold text-sm" style="color:{{ $t['color'] }}">{{ $t['name'] }}</p>
                            <p class="text-xs text-slate-500 font-semibold">{{ $t['range'] }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold text-slate-800">{{ $t['benefit'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-4 p-4 rounded-xl text-xs text-slate-700 font-medium bg-[#F4EDD9]/50 border border-[#e8e3d5]">
                💡 Tier naik otomatis saat pelanggan mencapai jumlah transaksi yang ditentukan. Hadiah milestone diberikan tiap kelipatan 10 transaksi.
            </div>
        </div>

        {{-- Reward Diklaim Terbaru --}}
        <div class="glass rounded-2xl p-6 bg-white border border-[#e8e3d5] shadow-md">
            <h3 class="font-bold text-slate-900 font-playfair mb-5 flex items-center gap-2 text-base">
                🎁 Klaim Reward Terbaru
            </h3>

            @if($recentRedemptions->isEmpty())
            <div class="text-center py-10">
                <span class="text-5xl">📭</span>
                <p class="text-slate-600 font-bold text-sm mt-3">Belum ada reward yang diklaim</p>
                <p class="text-slate-500 text-xs mt-1">Data akan muncul setelah pelanggan klaim milestone reward</p>
            </div>
            @else
            <div class="space-y-3">
                @foreach($recentRedemptions as $r)
                <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-200 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-extrabold text-[#042623] gold-gradient border border-[#C6A443] shadow-sm">
                            {{ strtoupper(substr($r->user->name ?? 'U', 0, 2)) }}
                        </div>
                        <div>
                            <p class="font-bold text-sm text-slate-900">{{ $r->user->name ?? 'Pelanggan' }}</p>
                            <p class="text-xs text-slate-500 font-medium">{{ $r->reward_type ?? 'Milestone Reward' }}</p>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 font-semibold">{{ $r->created_at->isoFormat('D MMM Y') }}</p>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- Link ke Daftar Pelanggan --}}
    <div class="mt-8 glass rounded-2xl p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white border border-[#e8e3d5] shadow-md">
        <div>
            <h3 class="font-bold text-slate-900 text-base">Lihat Status Tier Semua Pelanggan</h3>
            <p class="text-sm text-slate-600 mt-1">Kelola dan monitor progress tier tiap pelanggan secara individual</p>
        </div>
        <a href="{{ route('admin.customers.index') }}" class="px-6 py-3 rounded-xl font-extrabold text-[#042623] gold-gradient border border-[#C6A443] shadow-md transition hover:scale-105 whitespace-nowrap">
            Lihat Semua Pelanggan →
        </a>
    </div>
</x-admin-app>

