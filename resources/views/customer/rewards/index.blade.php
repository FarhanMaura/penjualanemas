<x-customer-app>
    <x-slot name="pageTitle">Sinar Baru Rewards</x-slot>
    <x-slot name="breadcrumb">Kumpulkan transaksi dan nikmati benefit eksklusif</x-slot>

    <div class="grid md:grid-cols-3 gap-6 mb-8">
        {{-- Status Tier --}}
        @php
            $tier = $summary['reward']->tier ?? 'bronze';
            $tierInfo = [
                'bronze'   => ['icon'=>'🥉', 'name'=>'Bronze', 'color'=>'#b45309'],
                'silver'   => ['icon'=>'🥈', 'name'=>'Silver', 'color'=>'#9ca3af'],
                'gold'     => ['icon'=>'🥇', 'name'=>'Gold', 'color'=>'#f59e0b'],
                'platinum' => ['icon'=>'💎', 'name'=>'Platinum', 'color'=>'#60a5fa'],
            ][$tier] ?? ['icon'=>'🥉', 'name'=>'Bronze', 'color'=>'#b45309'];
        @endphp
        
        <div class="glass rounded-3xl p-8 text-center relative overflow-hidden group">
            <div class="absolute inset-0 opacity-10 group-hover:opacity-20 transition" style="background:{{ $tierInfo['color'] }};"></div>
            <div class="text-7xl mb-4 transform group-hover:scale-110 transition">{{ $tierInfo['icon'] }}</div>
            <p class="text-gray-400 text-sm">Status Tier Saat Ini</p>
            <h2 class="text-2xl font-bold font-playfair mt-1 text-white">{{ $tierInfo['name'] }} Member</h2>
            <p class="text-xs text-gray-500 mt-2">Member sejak {{ $summary['reward']->created_at?->isoFormat('MMM Y') ?? '-' }}</p>
        </div>

        {{-- Progress ke Tier Berikutnya --}}
        <div class="md:col-span-2 glass rounded-3xl p-8 flex flex-col justify-center">
            <h3 class="font-bold text-white mb-2">Progress Tier & Reward</h3>
            <p class="text-sm text-gray-400 mb-6">Kumpulkan transaksi untuk naik tier dan dapatkan hadiah di kelipatan 10 transaksi!</p>
            
            <div class="flex justify-between items-end mb-2">
                <div>
                    <span class="text-3xl font-bold text-yellow-400">{{ $summary['completed_count'] }}</span>
                    <span class="text-sm text-gray-500">transaksi selesai</span>
                </div>
                @if($summary['next_tier'])
                <div class="text-right">
                    <span class="text-sm text-gray-400">Menuju {{ ucfirst($summary['next_tier']) }}</span>
                    <p class="text-xs text-yellow-400">{{ $summary['transactions_left'] }} transaksi lagi</p>
                </div>
                @else
                <div class="text-right text-yellow-400 text-sm font-semibold">Max Tier Reached!</div>
                @endif
            </div>

            <div class="h-3 rounded-full overflow-hidden" style="background:rgba(255,255,255,0.05);">
                <div class="h-full rounded-full transition-all duration-1000" 
                     style="width:{{ $summary['progress_pct'] }}%; background:linear-gradient(90deg,#f59e0b,#d97706);"></div>
            </div>
            
            <div class="flex justify-between mt-2 text-xs text-gray-500">
                <span>0</span>
                <span>5</span>
                <span>10</span>
                <span>20+</span>
            </div>
            
            @if($summary['milestone_eligible'])
            <div class="mt-6 p-4 rounded-xl flex items-center gap-4" style="background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.3);">
                <span class="text-3xl">🎁</span>
                <div>
                    <p class="text-sm font-bold text-green-400">Selamat! Anda berhak mendapat Hadiah Milestone!</p>
                    <p class="text-xs text-green-500/70 mt-1">Tunjukkan halaman ini ke kasir toko untuk klaim cuci emas gratis + diskon.</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Benefit Tier --}}
    <div class="glass rounded-3xl p-8 mb-8">
        <h3 class="font-bold text-white text-lg mb-6">Keuntungan Tiap Tier</h3>
        <div class="grid md:grid-cols-4 gap-6">
            <div class="p-5 rounded-2xl {{ $tier == 'bronze' ? 'ring-2 ring-yellow-500/50 bg-white/5' : '' }}" style="border:1px solid rgba(255,255,255,0.05);">
                <div class="text-3xl mb-3">🥉</div>
                <h4 class="font-bold text-white mb-2">Bronze</h4>
                <ul class="text-xs text-gray-400 space-y-2">
                    <li>✓ Reservasi Online</li>
                    <li>✓ Update Harga Real-time</li>
                </ul>
            </div>
            <div class="p-5 rounded-2xl {{ $tier == 'silver' ? 'ring-2 ring-yellow-500/50 bg-white/5' : '' }}" style="border:1px solid rgba(255,255,255,0.05);">
                <div class="text-3xl mb-3">🥈</div>
                <h4 class="font-bold text-white mb-2">Silver <span class="text-gray-500 text-[10px] ml-1">(5 Transaksi)</span></h4>
                <ul class="text-xs text-gray-400 space-y-2">
                    <li>✓ Benefit Bronze</li>
                    <li>✓ Prioritas Reservasi</li>
                </ul>
            </div>
            <div class="p-5 rounded-2xl relative overflow-hidden {{ $tier == 'gold' ? 'ring-2 ring-yellow-400 bg-yellow-900/10' : '' }}" style="border:1px solid rgba(245,158,11,0.2);">
                <div class="text-3xl mb-3">🥇</div>
                <h4 class="font-bold text-yellow-400 mb-2">Gold <span class="text-gray-500 text-[10px] ml-1">(10 Transaksi)</span></h4>
                <ul class="text-xs text-gray-300 space-y-2">
                    <li>✓ Benefit Silver</li>
                    <li>🎁 <span class="text-yellow-400 font-semibold">Gratis Cuci Emas</span></li>
                    <li>🎁 <span class="text-yellow-400 font-semibold">Diskon Khusus</span></li>
                </ul>
            </div>
            <div class="p-5 rounded-2xl {{ $tier == 'platinum' ? 'ring-2 ring-blue-400 bg-blue-900/10' : '' }}" style="border:1px solid rgba(96,165,250,0.2);">
                <div class="text-3xl mb-3">💎</div>
                <h4 class="font-bold text-blue-400 mb-2">Platinum <span class="text-gray-500 text-[10px] ml-1">(20+ Transaksi)</span></h4>
                <ul class="text-xs text-gray-300 space-y-2">
                    <li>✓ Benefit Gold</li>
                    <li>✓ Layanan VIP Toko</li>
                    <li>✓ Akses Produk Eksklusif</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- History Transaksi Poin --}}
    <div class="glass rounded-3xl p-8">
        <h3 class="font-bold text-white text-lg mb-6">Riwayat Transaksi Terselesaikan</h3>
        <p class="text-sm text-gray-400 mb-6">Hanya transaksi dengan status <strong>Selesai (Completed)</strong> yang dihitung ke dalam progress tier Anda.</p>
        
        <div class="text-center py-6">
            <a href="{{ route('customer.transactions.index') }}" class="px-6 py-3 rounded-xl font-semibold text-white transition hover:scale-105 inline-block"
               style="background:linear-gradient(135deg,#f59e0b,#d97706);">
                Lihat Semua Riwayat Transaksi →
            </a>
        </div>
    </div>
</x-customer-app>
