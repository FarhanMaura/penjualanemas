<x-customer-app>
    <x-slot name="pageTitle">Dashboard Pelanggan</x-slot>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="mb-6 px-4 py-3 rounded-xl text-sm font-semibold flex items-center gap-2 bg-emerald-50 border border-emerald-300 text-emerald-900 shadow-sm">
        ✅ {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-6 px-4 py-3 rounded-xl text-sm font-semibold bg-red-50 border border-red-300 text-red-900 shadow-sm">
        ❌ {{ session('error') }}
    </div>
    @endif

    {{-- Welcome Banner --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6 mb-8">
        {{-- Kartu Nama --}}
        <div class="glass rounded-3xl p-6 relative overflow-hidden border border-[#e8e3d5] shadow-md">
            <div class="relative flex items-center gap-5">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-2xl font-extrabold gold-gradient shadow-md border border-[#C6A443]">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">Selamat datang kembali,</p>
                    <h2 class="text-2xl font-bold text-[#042623] mt-0.5 font-playfair">{{ auth()->user()->name }}</h2>
                    <p class="text-xs text-slate-500 mt-1 font-normal">Member sejak {{ auth()->user()->created_at->isoFormat('MMM Y') }}</p>
                    <div class="mt-2 flex gap-3 text-xs text-slate-600 font-medium">
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
            $txCount     = $reward['transaction_count'] ?? 0;
            $nextTarget  = $reward['next_target'] ?? 10;
            $progress    = $nextTarget > 0 ? min(100, ($txCount / $nextTarget) * 100) : 100;
            $tierIcon    = $tierIcons[$tier] ?? '🥉';
            $tierName    = $tierNames[$tier] ?? 'Bronze Member';
        @endphp
        <div class="glass rounded-3xl p-6 border border-[#e8e3d5] shadow-md">
            <div class="flex items-center gap-4">
                <div class="text-5xl">{{ $tierIcon }}</div>
                <div class="flex-1">
                    <p class="text-xs text-slate-500 font-semibold mb-0.5">Status Tier</p>
                    <p class="font-bold text-lg text-[#042623]">{{ $tierName }}</p>
                    <p class="text-xs text-slate-500 font-medium mt-1">{{ $txCount }} / {{ $nextTarget }} transaksi</p>
                    <div class="mt-2 h-2.5 rounded-full overflow-hidden bg-[#e2f2f0] border border-[#085C54]/20">
                        <div class="h-full rounded-full transition-all progress-bar" style="width:{{ $progress }}%;"></div>
                    </div>
                    <div class="flex justify-between text-[11px] mt-1.5 text-slate-500 font-semibold">
                        <span>🥉 Bronze</span><span>🥈 Silver</span><span>🥇 Gold</span><span>💎 Platinum</span>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-4xl font-black text-[#085C54] font-playfair">{{ $txCount }}</p>
                    <p class="text-xs text-slate-500 font-semibold">transaksi</p>
                    @if($reward['remaining'] ?? false)
                    <p class="text-xs text-[#866a20] font-bold mt-1">🎯 {{ $reward['remaining'] }} lagi!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">

        {{-- KOLOM KIRI (col-span-2) --}}
        <div class="col-span-1 lg:col-span-2 space-y-6">

            {{-- Harga Emas Hari Ini --}}
            <div class="glass rounded-2xl p-5 border border-[#e8e3d5] shadow-md">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-[#042623] font-playfair text-base">💰 Harga Emas Hari Ini</h3>
                    <span class="text-xs font-bold text-[#085C54] bg-[#F4EDD9] border border-[#E3D193] px-3 py-1 rounded-full">{{ now()->isoFormat('D MMM Y') }}</span>
                </div>
                @if($goldPrice)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="rounded-xl p-4 text-center bg-emerald-50 border border-emerald-200 shadow-sm">
                        <p class="text-xs text-emerald-800 font-semibold mb-1">Harga Beli (Toko Beli dari Anda)</p>
                        <p class="font-black text-[#085C54] text-xl font-playfair">Rp {{ number_format($goldPrice->buy_price_per_gram, 0, ',', '.') }}</p>
                        <p class="text-xs text-emerald-700 font-medium mt-0.5">per gram • 24 Karat</p>
                    </div>
                    <div class="rounded-xl p-4 text-center bg-[#F4EDD9] border border-[#E3D193] shadow-sm">
                        <p class="text-xs text-[#866a20] font-semibold mb-1">Harga Jual (Toko Jual ke Anda)</p>
                        <p class="font-black text-[#C6A443] text-xl font-playfair">Rp {{ number_format($goldPrice->sell_price_per_gram, 0, ',', '.') }}</p>
                        <p class="text-xs text-[#866a20] font-medium mt-0.5">per gram • 24 Karat</p>
                    </div>
                </div>
                <p class="text-xs text-slate-500 text-center mt-3 font-medium">📡 Sumber: {{ $goldPrice->source }}</p>
                @else
                <div class="text-center py-6">
                    <p class="text-slate-600 text-sm font-medium">Harga sedang diperbarui oleh admin.</p>
                </div>
                @endif
            </div>

            {{-- Buat Reservasi --}}
            <div class="glass rounded-2xl p-5 border border-[#e8e3d5] shadow-md">
                <h3 class="font-bold text-[#042623] font-playfair text-base mb-4">⚡ Buat Reservasi</h3>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                    @foreach([
                        ['🛒','Beli Emas','beli'],
                        ['💰','Jual Emas','jual'],
                        ['📅','Cicilan','cicilan'],
                        ['🏦','Gadai Emas','gadai'],
                    ] as [$icon, $label, $type])
                    <a href="{{ route('customer.reservations.create', ['type' => $type]) }}"
                       class="flex flex-col items-center gap-2 rounded-xl p-4 bg-[#F4EDD9] hover:bg-white transition cursor-pointer text-center group border border-slate-200 shadow-sm hover:border-[#085C54] hover:scale-105">
                        <span class="text-3xl group-hover:scale-110 transition-transform">{{ $icon }}</span>
                        <span class="text-xs font-bold text-[#042623] group-hover:text-[#085C54]">{{ $label }}</span>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Reservasi Aktif --}}
            <div class="glass rounded-2xl p-5 border border-[#e8e3d5] shadow-md">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-[#042623] font-playfair text-base">📋 Reservasi Aktif</h3>
                    <a href="{{ route('customer.reservations.index') }}" class="text-xs text-[#085C54] font-bold hover:underline">Lihat Semua →</a>
                </div>
                @if($activeReservations->isEmpty())
                <div class="text-center py-6">
                    <p class="text-slate-600 text-sm font-medium">Belum ada reservasi aktif.</p>
                    <p class="text-slate-400 text-xs mt-1">Buat reservasi di atas untuk mulai.</p>
                </div>
                @else
                <div class="space-y-3">
                    @foreach($activeReservations as $r)
                    <div class="flex items-center justify-between rounded-xl p-4 bg-[#F4EDD9] border border-slate-200 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg bg-[#e2f2f0] border border-[#085C54]/20">
                                🛒
                            </div>
                            <div>
                                <p class="text-sm font-bold text-[#042623]">{{ $r->reservation_code }}</p>
                                <p class="text-xs text-slate-500">{{ $r->product->name ?? 'Produk' }} • Kunjungan: {{ \Carbon\Carbon::parse($r->preferred_date)->isoFormat('D MMM Y') }}</p>
                            </div>
                        </div>
                        @php
                            $statusColor = ['pending'=>'text-[#866a20] bg-[#F4EDD9] border-[#E3D193]','confirmed'=>'text-emerald-900 bg-emerald-100 border-emerald-300','cancelled'=>'text-red-900 bg-red-100 border-red-300'][$r->status] ?? 'text-slate-700 bg-slate-100 border-slate-300';
                            $statusText  = ['pending'=>'Menunggu ⏳','confirmed'=>'Dikonfirmasi ✓','cancelled'=>'Dibatalkan'][$r->status] ?? $r->status;
                        @endphp
                        <span class="text-xs font-bold px-3 py-1 rounded-full border {{ $statusColor }}">{{ $statusText }}</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Riwayat Transaksi --}}
            <div class="glass rounded-2xl p-5 border border-[#e8e3d5] shadow-md">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-[#042623] font-playfair text-base">🧾 Riwayat Transaksi</h3>
                    <a href="{{ route('customer.transactions.index') }}" class="text-xs text-[#085C54] font-bold hover:underline">Lihat Semua →</a>
                </div>
                @if($recentTransactions->isEmpty())
                <div class="text-center py-6">
                    <p class="text-slate-600 text-sm font-medium">Belum ada transaksi.</p>
                    <p class="text-slate-400 text-xs mt-1">Riwayat akan muncul setelah transaksi pertama kamu di toko.</p>
                </div>
                @else
                <div class="space-y-2">
                    @foreach($recentTransactions as $tx)
                    <div class="flex items-center justify-between py-3 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <span class="text-lg">
                                @if($tx->type === 'sell') 💰
                                @elseif($tx->type === 'installment') 📅
                                @elseif($tx->type === 'pawn') 🏦
                                @else 🛒
                                @endif
                            </span>
                            <div>
                                <p class="text-sm font-bold text-[#042623]">
                                    {{ ucfirst($tx->type) }}
                                    @if($tx->items->first()) — {{ $tx->items->first()->product->name ?? '' }} @endif
                                </p>
                                <p class="text-xs text-slate-500">{{ $tx->created_at->isoFormat('D MMM Y') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-extrabold {{ $tx->type === 'sell' ? 'text-emerald-700' : 'text-[#042623]' }}">
                                {{ $tx->type === 'sell' ? '+' : '-' }} Rp {{ number_format($tx->total_amount, 0, ',', '.') }}
                            </p>
                            <p class="text-xs text-[#C6A443] font-bold">+1 poin ⭐</p>
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
            <div class="glass rounded-2xl p-5 border border-[#E3D193] shadow-md bg-[#F4EDD9]">
                <h3 class="font-bold text-[#042623] font-playfair text-base mb-3">🎁 Program Reward</h3>
                <div class="space-y-2">
                    <div class="flex justify-between text-xs rounded-xl px-3 py-2 bg-white border border-[#e8e3d5]">
                        <span class="text-slate-600 font-medium">Transaksi saat ini</span>
                        <span class="text-[#085C54] font-bold">{{ $txCount }} transaksi</span>
                    </div>
                    <div class="flex justify-between text-xs rounded-xl px-3 py-2 bg-white border border-[#e8e3d5]">
                        <span class="text-slate-600 font-medium">Target berikutnya</span>
                        <span class="text-[#085C54] font-bold">{{ $nextTarget }} transaksi</span>
                    </div>
                    @if(($reward['remaining'] ?? 0) > 0)
                    <div class="flex justify-between text-xs rounded-xl px-3 py-2 bg-[#F4EDD9] border border-[#E3D193]">
                        <span class="text-[#866a20] font-semibold">Kurang</span>
                        <span class="text-[#085C54] font-extrabold">{{ $reward['remaining'] }} lagi 🎯</span>
                    </div>
                    @endif
                </div>
                <div class="mt-3 p-3 rounded-xl bg-[#F4EDD9] border border-[#E3D193]">
                    <p class="text-[#085C54] font-bold text-xs">Hadiah menanti:</p>
                    <p class="text-[#866a20] text-xs font-medium mt-1">✂️ Diskon khusus + 🫧 Cuci emas GRATIS!</p>
                </div>
                <a href="{{ route('customer.rewards.index') }}" class="block text-center mt-3 text-xs text-[#085C54] font-bold hover:underline">Lihat Detail Reward →</a>
            </div>

            {{-- Gadai Aktif --}}
            <div class="glass rounded-2xl p-5 border border-[#e8e3d5] shadow-md">
                <h3 class="font-bold text-[#042623] font-playfair text-base mb-4">🏦 Gadai Aktif</h3>
                @if($activePawns > 0)
                <div class="text-center py-2">
                    <p class="text-3xl font-black text-[#085C54] font-playfair">{{ $activePawns }}</p>
                    <p class="text-xs text-slate-500 font-medium mt-1">gadai berjalan</p>
                    <a href="{{ route('customer.pawns.index') }}" class="block mt-3 text-xs text-[#085C54] font-bold hover:underline">Lihat Detail →</a>
                </div>
                @else
                <div class="text-center py-4">
                    <p class="text-slate-500 text-sm font-normal">Tidak ada gadai aktif</p>
                </div>
                @endif
            </div>

            {{-- Cicilan Berjalan --}}
            <div class="glass rounded-2xl p-5 border border-[#e8e3d5] shadow-md">
                <h3 class="font-bold text-[#042623] font-playfair text-base mb-4">📅 Cicilan Berjalan</h3>
                @if($activeInstallments > 0)
                <div class="text-center py-2">
                    <p class="text-3xl font-black text-[#085C54] font-playfair">{{ $activeInstallments }}</p>
                    <p class="text-xs text-slate-500 font-medium mt-1">cicilan aktif</p>
                    <a href="{{ route('customer.installments.index') }}" class="block mt-3 text-xs text-[#085C54] font-bold hover:underline">Lihat Detail →</a>
                </div>
                @else
                <div class="text-center py-4">
                    <p class="text-slate-500 text-sm font-normal">Tidak ada cicilan aktif</p>
                </div>
                @endif
            </div>

            {{-- Aksi Cepat --}}
            <div class="glass rounded-2xl p-5 border border-[#e8e3d5] shadow-md">
                <h3 class="font-bold text-[#042623] font-playfair text-base mb-4">🔗 Aksi Cepat</h3>
                <div class="space-y-2">
                    <a href="{{ route('customer.catalog.index') }}" class="flex items-center gap-3 p-3 rounded-xl bg-[#F4EDD9] hover:bg-white transition text-sm font-semibold text-[#042623] border border-slate-200 shadow-sm hover:border-[#085C54]">
                        💍 Lihat Katalog Produk
                    </a>
                    <a href="{{ route('customer.reservations.index') }}" class="flex items-center gap-3 p-3 rounded-xl bg-[#F4EDD9] hover:bg-white transition text-sm font-semibold text-[#042623] border border-slate-200 shadow-sm hover:border-[#085C54]">
                        📋 Semua Reservasi
                    </a>
                    <a href="{{ route('customer.transactions.index') }}" class="flex items-center gap-3 p-3 rounded-xl bg-[#F4EDD9] hover:bg-white transition text-sm font-semibold text-[#042623] border border-slate-200 shadow-sm hover:border-[#085C54]">
                        🧾 Riwayat Transaksi
                    </a>
                    <a href="{{ route('customer.rewards.index') }}" class="flex items-center gap-3 p-3 rounded-xl bg-[#F4EDD9] hover:bg-white transition text-sm font-semibold text-[#042623] border border-slate-200 shadow-sm hover:border-[#085C54]">
                        🎁 Program Reward
                    </a>
                </div>
            </div>
        </div>
    </div>

</x-customer-app>
