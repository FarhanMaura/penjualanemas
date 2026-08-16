<x-customer-app>
    <x-slot name="pageTitle">Sinar Baru Rewards</x-slot>
    <x-slot name="breadcrumb">Kumpulkan transaksi dan nikmati benefit eksklusif</x-slot>

    <div class="grid md:grid-cols-3 gap-6 mb-8">
        {{-- Status Tier --}}
        @php
            $tier = $summary['reward']->tier ?? 'bronze';
            $tierInfo = [
                'bronze'   => ['icon'=>'🥉', 'name'=>'Bronze', 'color'=>'#b45309', 'bg'=>'#fef3c7', 'border'=>'#fde68a'],
                'silver'   => ['icon'=>'🥈', 'name'=>'Silver', 'color'=>'#475569', 'bg'=>'#f1f5f9', 'border'=>'#cbd5e1'],
                'gold'     => ['icon'=>'🥇', 'name'=>'Gold', 'color'=>'#b45309', 'bg'=>'#fef3c7', 'border'=>'#fde68a'],
                'platinum' => ['icon'=>'💎', 'name'=>'Platinum', 'color'=>'#0284c7', 'bg'=>'#e0f2fe', 'border'=>'#bae6fd'],
            ][$tier] ?? ['icon'=>'🥉', 'name'=>'Bronze', 'color'=>'#b45309', 'bg'=>'#fef3c7', 'border'=>'#fde68a'];
        @endphp
        
        <div class="glass rounded-3xl p-8 text-center relative overflow-hidden bg-white border border-[#e8e3d5] shadow-md">
            <div class="text-7xl mb-4 transform group-hover:scale-110 transition">{{ $tierInfo['icon'] }}</div>
            <p class="text-slate-500 font-semibold text-xs uppercase tracking-wider">Status Tier Saat Ini</p>
            <h2 class="text-2xl font-extrabold font-playfair mt-1 text-[#042623]">{{ $tierInfo['name'] }} Member</h2>
            <p class="text-xs text-slate-500 font-medium mt-2">Member sejak {{ $summary['reward']->created_at?->isoFormat('MMM Y') ?? '-' }}</p>
        </div>

        {{-- Progress ke Tier Berikutnya --}}
        <div class="md:col-span-2 glass rounded-3xl p-8 flex flex-col justify-center bg-white border border-[#e8e3d5] shadow-md">
            <h3 class="font-bold text-slate-900 text-lg mb-1">Progress Tier & Reward</h3>
            <p class="text-sm text-slate-600 mb-6 font-medium">Kumpulkan transaksi untuk naik tier dan dapatkan hadiah di kelipatan 10 transaksi!</p>
            
            <div class="flex justify-between items-end mb-2">
                <div>
                    <span class="text-3xl font-extrabold text-[#C6A443]">{{ $summary['completed_count'] }}</span>
                    <span class="text-sm font-semibold text-slate-600 ml-1">transaksi selesai</span>
                </div>
                @if($summary['next_tier'])
                <div class="text-right">
                    <span class="text-sm text-slate-600 font-medium">Menuju {{ ucfirst($summary['next_tier']) }}</span>
                    <p class="text-xs font-bold text-[#085C54]">{{ $summary['transactions_left'] }} transaksi lagi</p>
                </div>
                @else
                <div class="text-right text-[#085C54] text-sm font-bold">Max Tier Reached!</div>
                @endif
            </div>

            <div class="h-3.5 rounded-full overflow-hidden bg-slate-100 border border-slate-200">
                <div class="h-full rounded-full transition-all duration-1000 bg-gradient-to-r from-[#E3D193] via-[#C6A443] to-[#085C54]" 
                     style="width:{{ $summary['progress_pct'] }}%;"></div>
            </div>
            
            <div class="flex justify-between mt-2 text-xs font-bold text-slate-500">
                <span>0</span>
                <span>5</span>
                <span>10</span>
                <span>20+</span>
            </div>
            
            @if($summary['milestone_eligible'])
            <div class="mt-6 p-4 rounded-xl flex items-center gap-4 bg-emerald-50 border border-emerald-300 text-emerald-900">
                <span class="text-3xl">🎁</span>
                <div>
                    <p class="text-sm font-bold text-emerald-950">Selamat! Anda berhak mendapat Hadiah Milestone!</p>
                    <p class="text-xs text-emerald-800 font-medium mt-1">Tunjukkan halaman ini ke kasir toko untuk klaim cuci emas gratis + diskon.</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Benefit Tier --}}
    <div class="glass rounded-3xl p-8 mb-8 bg-white border border-[#e8e3d5] shadow-md">
        <h3 class="font-bold text-slate-900 text-lg mb-6">Keuntungan Tiap Tier</h3>
        <div class="grid md:grid-cols-4 gap-6">
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 {{ $tier == 'bronze' ? 'ring-2 ring-[#085C54] bg-[#F4EDD9]/40' : '' }}">
                <div class="text-3xl mb-3">🥉</div>
                <h4 class="font-bold text-slate-900 mb-2">Bronze</h4>
                <ul class="text-xs text-slate-700 font-medium space-y-2">
                    <li>✓ Reservasi Online</li>
                    <li>✓ Update Harga Real-time</li>
                </ul>
            </div>
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 {{ $tier == 'silver' ? 'ring-2 ring-[#085C54] bg-[#F4EDD9]/40' : '' }}">
                <div class="text-3xl mb-3">🥈</div>
                <h4 class="font-bold text-slate-900 mb-2">Silver <span class="text-slate-500 text-[10px] ml-1 font-normal">(5 Transaksi)</span></h4>
                <ul class="text-xs text-slate-700 font-medium space-y-2">
                    <li>✓ Benefit Bronze</li>
                    <li>✓ Prioritas Reservasi</li>
                </ul>
            </div>
            <div class="p-5 rounded-2xl relative overflow-hidden bg-amber-50/60 border border-amber-200 {{ $tier == 'gold' ? 'ring-2 ring-amber-500' : '' }}">
                <div class="text-3xl mb-3">🥇</div>
                <h4 class="font-bold text-[#866a20] mb-2">Gold <span class="text-slate-500 text-[10px] ml-1 font-normal">(10 Transaksi)</span></h4>
                <ul class="text-xs text-slate-800 font-semibold space-y-2">
                    <li>✓ Benefit Silver</li>
                    <li>🎁 <span class="text-[#085C54] font-bold">Gratis Cuci Emas</span></li>
                    <li>🎁 <span class="text-[#085C54] font-bold">Diskon Khusus</span></li>
                </ul>
            </div>
            <div class="p-5 rounded-2xl bg-blue-50/60 border border-blue-200 {{ $tier == 'platinum' ? 'ring-2 ring-blue-500' : '' }}">
                <div class="text-3xl mb-3">💎</div>
                <h4 class="font-bold text-blue-900 mb-2">Platinum <span class="text-slate-500 text-[10px] ml-1 font-normal">(20+ Transaksi)</span></h4>
                <ul class="text-xs text-slate-800 font-semibold space-y-2">
                    <li>✓ Benefit Gold</li>
                    <li>✓ Layanan VIP Khusus</li>
                </ul>
            </div>
        </div>
    </div>
</x-customer-app>
