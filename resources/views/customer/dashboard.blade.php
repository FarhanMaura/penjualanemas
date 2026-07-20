<x-customer-app>
    <x-slot name="pageTitle">Dashboard Pelanggan</x-slot>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="mb-6 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2"
         style="background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.3); color:#34d399;">
        ✅ {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-6 px-4 py-3 rounded-xl text-sm font-medium"
         style="background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.3); color:#f87171;">
        ❌ {{ session('error') }}
    </div>
    @endif

    {{-- Welcome Banner --}}
    <div class="grid grid-cols-2 gap-6 mb-8">
        {{-- Kartu Nama --}}
        <div class="glass rounded-3xl p-6 relative overflow-hidden" style="box-shadow:0 0 30px rgba(245,158,11,0.15);">
            <div class="absolute inset-0 pointer-events-none" style="background:linear-gradient(135deg, rgba(180,83,9,0.15) 0%, transparent 60%);"></div>
            <div class="relative flex items-center gap-5">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-2xl font-bold shadow-lg" style="background:linear-gradient(135deg,#f59e0b,#d97706,#92400e);">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div>
                    <p class="text-sm text-gray-400">Selamat datang kembali,</p>
                    <h2 class="text-2xl font-bold text-white" style="font-family:'Playfair Display',serif;">{{ auth()->user()->name }}</h2>
                    <p class="text-xs text-gray-500 mt-1">Member sejak {{ auth()->user()->created_at->isoFormat('MMM Y') }}</p>
                    <div class="mt-2 flex gap-3 text-xs text-gray-400">
                        <span>📧 {{ auth()->user()->email }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kartu Tier CRM --}}
        @php
            $tier        = $reward['tier'] ?? 'bronze';
            $tierIcons   = ['bronze'=>'🥉','silver'=>'🥈','gold'=>'🥇','platinum'=>'💎'];
            $tierNames   = ['bronze'=>'Bronze Member','silver'=>'Silver Member','gold'=>'Gold Member','platinum'=>'Platinum Member'];
            $tierColors  = ['bronze'=>'#b45309','silver'=>'#9ca3af','gold'=>'#f59e0b','platinum'=>'#60a5fa'];
            $txCount     = $reward['transaction_count'] ?? 0;
            $nextTarget  = $reward['next_target'] ?? 10;
            $progress    = $nextTarget > 0 ? min(100, ($txCount / $nextTarget) * 100) : 100;
            $tierIcon    = $tierIcons[$tier] ?? '🥉';
            $tierName    = $tierNames[$tier] ?? 'Bronze Member';
            $tierColor   = $tierColors[$tier] ?? '#b45309';
        @endphp
        <div class="glass rounded-3xl p-6">
            <div class="flex items-center gap-4">
                <div class="text-5xl">{{ $tierIcon }}</div>
                <div class="flex-1">
                    <p class="text-xs text-gray-400 mb-0.5">Status Tier</p>
                    <p class="font-bold text-lg text-white">{{ $tierName }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $txCount }} / {{ $nextTarget }} transaksi</p>
                    <div class="mt-2 h-2 rounded-full overflow-hidden" style="background:rgba(255,255,255,0.05);">
                        <div class="h-2 rounded-full transition-all" style="background:linear-gradient(90deg,#f59e0b,#d97706); width:{{ $progress }}%;"></div>
                    </div>
                    <div class="flex justify-between text-xs mt-1 text-gray-500">
                        <span>🥉 Bronze</span><span>🥈 Silver</span><span>🥇 Gold</span><span>💎 Platinum</span>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-4xl font-bold text-yellow-400">{{ $txCount }}</p>
                    <p class="text-xs text-gray-400">transaksi</p>
                    @if($reward['remaining'] ?? false)
                    <p class="text-xs text-yellow-400 mt-1">🎯 {{ $reward['remaining'] }} lagi!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-6">

        {{-- KOLOM KIRI (col-span-2) --}}
        <div class="col-span-2 space-y-6">

            {{-- Harga Emas Hari Ini --}}
            <div class="glass rounded-2xl p-5">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-yellow-400">💰 Harga Emas Hari Ini</h3>
                    <span class="text-xs text-gray-400 glass px-3 py-1 rounded-full">{{ now()->isoFormat('D MMM Y') }}</span>
                </div>
                @if($goldPrice)
                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-xl p-4 text-center" style="background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.2);">
                        <p class="text-xs text-green-400 mb-1">Harga Beli (Toko Beli dari Anda)</p>
                        <p class="font-bold text-white text-xl">Rp {{ number_format($goldPrice->buy_price_per_gram, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500">per gram • 24 Karat</p>
                    </div>
                    <div class="rounded-xl p-4 text-center" style="background:rgba(245,158,11,0.08); border:1px solid rgba(245,158,11,0.2);">
                        <p class="text-xs text-yellow-400 mb-1">Harga Jual (Toko Jual ke Anda)</p>
                        <p class="font-bold text-yellow-400 text-xl">Rp {{ number_format($goldPrice->sell_price_per_gram, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500">per gram • 24 Karat</p>
                    </div>
                </div>
                <p class="text-xs text-gray-500 text-center mt-3">📡 Sumber: {{ $goldPrice->source }}</p>
                @else
                <div class="text-center py-6">
                    <p class="text-gray-500 text-sm">Harga sedang diperbarui oleh admin.</p>
                </div>
                @endif
            </div>

            {{-- Buat Reservasi --}}
            <div class="glass rounded-2xl p-5">
                <h3 class="font-semibold text-yellow-400 mb-4">⚡ Buat Reservasi</h3>
                <div class="grid grid-cols-4 gap-3">
                    @foreach([
                        ['🛒','Beli Emas','beli'],
                        ['💰','Jual Emas','jual'],
                        ['📅','Cicilan','cicilan'],
                        ['🏦','Gadai Emas','gadai'],
                    ] as [$icon, $label, $type])
                    <a href="{{ route('customer.reservations.create', ['type' => $type]) }}"
                       class="flex flex-col items-center gap-2 glass rounded-xl p-4 hover:bg-white/10 transition cursor-pointer text-center group"
                       style="border-color:rgba(245,158,11,0.15);">
                        <span class="text-3xl group-hover:scale-110 transition-transform">{{ $icon }}</span>
                        <span class="text-xs font-medium text-gray-300">{{ $label }}</span>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Reservasi Aktif --}}
            <div class="glass rounded-2xl p-5">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-yellow-400">📋 Reservasi Aktif</h3>
                    <a href="{{ route('customer.reservations.index') }}" class="text-xs text-yellow-400 hover:underline">Lihat Semua →</a>
                </div>
                @if($activeReservations->isEmpty())
                <div class="text-center py-6">
                    <p class="text-gray-500 text-sm">Belum ada reservasi aktif.</p>
                    <p class="text-gray-600 text-xs mt-1">Buat reservasi di atas untuk mulai.</p>
                </div>
                @else
                <div class="space-y-3">
                    @foreach($activeReservations as $r)
                    <div class="flex items-center justify-between glass rounded-xl p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg"
                                 style="background:{{ $r->status === 'confirmed' ? 'rgba(16,185,129,0.2)' : 'rgba(245,158,11,0.15)' }};">
                                🛒
                            </div>
                            <div>
                                <p class="text-sm font-medium text-white">{{ $r->reservation_code }}</p>
                                <p class="text-xs text-gray-400">{{ $r->product->name ?? 'Produk' }} • Kunjungan: {{ \Carbon\Carbon::parse($r->preferred_date)->isoFormat('D MMM Y') }}</p>
                            </div>
                        </div>
                        @php
                            $statusColor = ['pending'=>'text-yellow-400 bg-yellow-900/40','confirmed'=>'text-green-400 bg-green-900/40','cancelled'=>'text-red-400 bg-red-900/40'][$r->status] ?? 'text-gray-400 bg-gray-900/40';
                            $statusText  = ['pending'=>'Menunggu ⏳','confirmed'=>'Dikonfirmasi ✓','cancelled'=>'Dibatalkan'][$r->status] ?? $r->status;
                        @endphp
                        <span class="text-xs px-3 py-1 rounded-full {{ $statusColor }}">{{ $statusText }}</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Riwayat Transaksi --}}
            <div class="glass rounded-2xl p-5">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-yellow-400">🧾 Riwayat Transaksi</h3>
                    <a href="{{ route('customer.transactions.index') }}" class="text-xs text-yellow-400 hover:underline">Lihat Semua →</a>
                </div>
                @if($recentTransactions->isEmpty())
                <div class="text-center py-6">
                    <p class="text-gray-500 text-sm">Belum ada transaksi.</p>
                    <p class="text-gray-600 text-xs mt-1">Riwayat akan muncul setelah transaksi pertama kamu di toko.</p>
                </div>
                @else
                <div class="space-y-2">
                    @foreach($recentTransactions as $tx)
                    <div class="flex items-center justify-between py-3" style="border-bottom:1px solid rgba(245,158,11,0.08);">
                        <div class="flex items-center gap-3">
                            <span class="text-lg">
                                @if($tx->type === 'sell') 💰
                                @elseif($tx->type === 'installment') 📅
                                @elseif($tx->type === 'pawn') 🏦
                                @else 🛒
                                @endif
                            </span>
                            <div>
                                <p class="text-sm text-white">
                                    {{ ucfirst($tx->type) }}
                                    @if($tx->items->first()) — {{ $tx->items->first()->product->name ?? '' }} @endif
                                </p>
                                <p class="text-xs text-gray-500">{{ $tx->created_at->isoFormat('D MMM Y') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold {{ $tx->type === 'sell' ? 'text-green-400' : 'text-red-400' }}">
                                {{ $tx->type === 'sell' ? '+' : '-' }} Rp {{ number_format($tx->total_amount, 0, ',', '.') }}
                            </p>
                            <p class="text-xs text-yellow-400">+1 poin ⭐</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- KOLOM KANAN --}}
        <div class="space-y-6">

            {{-- Reward Info --}}
            <div class="glass rounded-2xl p-5" style="border-color:rgba(245,158,11,0.3); background:rgba(120,53,15,0.1);">
                <h3 class="font-semibold text-yellow-400 mb-3">🎁 Program Reward</h3>
                <div class="space-y-2">
                    <div class="flex justify-between text-xs glass rounded-lg px-3 py-2">
                        <span class="text-gray-400">Transaksi saat ini</span>
                        <span class="text-yellow-400 font-bold">{{ $txCount }} transaksi</span>
                    </div>
                    <div class="flex justify-between text-xs glass rounded-lg px-3 py-2">
                        <span class="text-gray-400">Target berikutnya</span>
                        <span class="text-yellow-400 font-bold">{{ $nextTarget }} transaksi</span>
                    </div>
                    @if(($reward['remaining'] ?? 0) > 0)
                    <div class="flex justify-between text-xs glass rounded-lg px-3 py-2">
                        <span class="text-gray-400">Kurang</span>
                        <span class="text-white font-bold">{{ $reward['remaining'] }} lagi 🎯</span>
                    </div>
                    @endif
                </div>
                <div class="mt-3 p-3 rounded-xl" style="background:rgba(245,158,11,0.08); border:1px solid rgba(245,158,11,0.2);">
                    <p class="text-xs text-yellow-400 font-semibold">Hadiah menanti:</p>
                    <p class="text-xs text-gray-400 mt-1">✂️ Diskon khusus + 🫧 Cuci emas GRATIS!</p>
                </div>
                <a href="{{ route('customer.rewards.index') }}" class="block text-center mt-3 text-xs text-yellow-400 hover:underline">Lihat Detail Reward →</a>
            </div>

            {{-- Gadai Aktif --}}
            <div class="glass rounded-2xl p-5">
                <h3 class="font-semibold text-yellow-400 mb-4">🏦 Gadai Aktif</h3>
                @if($activePawns > 0)
                <div class="text-center py-2">
                    <p class="text-3xl font-bold text-yellow-400">{{ $activePawns }}</p>
                    <p class="text-xs text-gray-400 mt-1">gadai berjalan</p>
                    <a href="#" class="block mt-3 text-xs text-yellow-400 hover:underline">Lihat Detail →</a>
                </div>
                @else
                <div class="text-center py-4">
                    <p class="text-gray-500 text-sm">Tidak ada gadai aktif</p>
                </div>
                @endif
            </div>

            {{-- Cicilan Berjalan --}}
            <div class="glass rounded-2xl p-5">
                <h3 class="font-semibold text-yellow-400 mb-4">📅 Cicilan Berjalan</h3>
                @if($activeInstallments > 0)
                <div class="text-center py-2">
                    <p class="text-3xl font-bold text-yellow-400">{{ $activeInstallments }}</p>
                    <p class="text-xs text-gray-400 mt-1">cicilan aktif</p>
                    <a href="#" class="block mt-3 text-xs text-yellow-400 hover:underline">Lihat Detail →</a>
                </div>
                @else
                <div class="text-center py-4">
                    <p class="text-gray-500 text-sm">Tidak ada cicilan aktif</p>
                </div>
                @endif
            </div>

            {{-- Aksi Cepat --}}
            <div class="glass rounded-2xl p-5">
                <h3 class="font-semibold text-yellow-400 mb-4">🔗 Aksi Cepat</h3>
                <div class="space-y-2">
                    <a href="{{ route('customer.catalog.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/5 transition text-sm text-gray-300"
                       style="border:1px solid rgba(255,255,255,0.05);">
                        💍 Lihat Katalog Produk
                    </a>
                    <a href="{{ route('customer.reservations.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/5 transition text-sm text-gray-300"
                       style="border:1px solid rgba(255,255,255,0.05);">
                        📋 Semua Reservasi
                    </a>
                    <a href="{{ route('customer.transactions.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/5 transition text-sm text-gray-300"
                       style="border:1px solid rgba(255,255,255,0.05);">
                        🧾 Riwayat Transaksi
                    </a>
                    <a href="{{ route('customer.rewards.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/5 transition text-sm text-gray-300"
                       style="border:1px solid rgba(255,255,255,0.05);">
                        🎁 Program Reward
                    </a>
                </div>
            </div>
        </div>
    </div>

</x-customer-app>
