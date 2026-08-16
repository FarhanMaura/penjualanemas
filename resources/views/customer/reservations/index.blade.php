<x-customer-app>
    <x-slot name="pageTitle">Daftar Reservasi</x-slot>
    <x-slot name="breadcrumb">Kelola jadwal kunjungan dan reservasi produk Anda</x-slot>

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

    {{-- Filter Bar --}}
    <div class="glass rounded-2xl p-4 mb-6 flex flex-wrap gap-4 items-center justify-between">
        <form method="GET" action="{{ route('customer.reservations.index') }}" class="flex gap-2">
            <select name="status" class="rounded-xl px-4 py-2 text-sm text-white outline-none focus:ring-2"
                    style="background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); --tw-ring-color:#f59e0b;"
                    onchange="this.form.submit()">
                <option value="" class="text-gray-900">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }} class="text-gray-900">Menunggu (Pending)</option>
                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }} class="text-gray-900">Dikonfirmasi</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }} class="text-gray-900">Selesai (Completed)</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }} class="text-gray-900">Dibatalkan</option>
            </select>
        </form>
        <a href="{{ route('customer.reservations.create') }}" class="px-5 py-2 rounded-xl text-sm font-semibold text-white shadow-lg transition hover:brightness-110"
           style="background:linear-gradient(135deg,#f59e0b,#d97706);">
            + Buat Reservasi Baru
        </a>
    </div>

    {{-- Reservasi List --}}
    @if($reservations->isEmpty())
    <div class="glass rounded-3xl p-12 text-center mt-6">
        <span class="text-6xl">📋</span>
        <p class="text-gray-400 text-lg mt-4 font-semibold">Tidak Ada Reservasi</p>
        <p class="text-gray-500 text-sm mt-2">Anda belum memiliki reservasi dengan status tersebut.</p>
        <a href="{{ route('customer.reservations.create') }}" class="mt-6 inline-block text-yellow-400 hover:underline">Mulai Reservasi Pertama →</a>
    </div>
    @else
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @foreach($reservations as $r)
        @php
            // Setup status colors
            $statusStyles = [
                'pending'   => ['bg'=>'rgba(245,158,11,0.1)','border'=>'rgba(245,158,11,0.3)','text'=>'#f59e0b','label'=>'Menunggu ⏳'],
                'confirmed' => ['bg'=>'rgba(59,130,246,0.1)','border'=>'rgba(59,130,246,0.3)','text'=>'#60a5fa','label'=>'Dikonfirmasi ✓'],
                'completed' => ['bg'=>'rgba(16,185,129,0.1)','border'=>'rgba(16,185,129,0.3)','text'=>'#34d399','label'=>'Selesai 🏁'],
                'cancelled' => ['bg'=>'rgba(239,68,68,0.1)','border'=>'rgba(239,68,68,0.3)','text'=>'#f87171','label'=>'Batal ❌'],
                'expired'   => ['bg'=>'rgba(156,163,175,0.1)','border'=>'rgba(156,163,175,0.3)','text'=>'#9ca3af','label'=>'Kedaluwarsa ⌛'],
            ];
            $style = $statusStyles[$r->status] ?? $statusStyles['pending'];
        @endphp
        
        <div class="glass rounded-2xl overflow-hidden relative flex flex-col" style="border-color:rgba(255,255,255,0.05);">
            {{-- Header Card --}}
            <div class="p-5" style="border-bottom:1px solid rgba(255,255,255,0.05);">
                <div class="flex justify-between items-start mb-2">
                    <span class="font-mono text-xs text-gray-400 bg-black/30 px-2 py-1 rounded">{{ $r->reservation_code }}</span>
                    <span class="text-xs font-semibold px-2 py-1 rounded-full"
                          style="background:{{ $style['bg'] }}; border:1px solid {{ $style['border'] }}; color:{{ $style['text'] }};">
                        {{ $style['label'] }}
                    </span>
                </div>
                <h3 class="font-bold text-white text-lg leading-tight mt-3">{{ $r->product->name ?? ($r->pawn_gold_description ?? 'Produk Dihapus') }}</h3>
                <p class="text-xs text-gray-400 mt-1">
                    Tipe: 
                    <span class="text-yellow-400 font-semibold">
                        {{
                            match($r->type) {
                                'purchase'    => 'Pembelian (Tunai)',
                                'installment' => 'Pembelian (Cicilan)',
                                'pawn'        => 'Gadai Emas (Pinjaman)',
                                default       => ucfirst($r->type ?? 'Pembelian')
                            }
                        }}
                    </span>
                </p>
            </div>

            {{-- Body Card --}}
            <div class="p-5 flex-1 space-y-3">
                @if(($r->type ?? 'purchase') !== 'pawn')
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Jumlah</span>
                    <span class="text-white font-medium">{{ $r->quantity }} item</span>
                </div>
                <div class="flex justify-between items-center text-sm pt-1 border-t border-white/5">
                    <span class="text-gray-400">Total Harga</span>
                    @if($r->agreed_price || $r->priceNegotiation)
                    <div class="text-right">
                        <span class="text-amber-400 font-extrabold text-base">Rp {{ number_format($r->agreed_price ?? $r->priceNegotiation->agreed_price, 0, ',', '.') }}</span>
                        <span class="block text-[10px] text-amber-300 font-semibold bg-amber-500/10 px-1.5 py-0.5 rounded border border-amber-500/20 mt-0.5">🤝 Tawar Harga ACC</span>
                    </div>
                    @elseif($r->product)
                    <span class="text-yellow-400 font-bold">Rp {{ number_format($r->product->base_price * $r->quantity, 0, ',', '.') }}</span>
                    @endif
                </div>
                @if($r->payment_method)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Metode Bayar</span>
                    <span class="text-white font-medium uppercase">{{ $r->payment_method }}</span>
                </div>
                @endif
                @endif

                @if(($r->type ?? 'purchase') === 'pawn')
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Kadar Emas</span>
                    <span class="text-white font-medium">{{ $r->pawn_gold_purity }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Berat Emas</span>
                    <span class="text-white font-medium">{{ number_format($r->pawn_weight_gram, 2) }} g</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Pengajuan Pinjaman</span>
                    <span class="text-yellow-400 font-bold">Rp {{ number_format($r->pawn_amount_requested, 0, ',', '.') }}</span>
                </div>
                @endif

                @if(($r->type ?? 'purchase') === 'installment')
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Tenor Cicilan</span>
                    <span class="text-white font-medium">{{ $r->installment_tenure }} Bulan</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Uang Muka (DP)</span>
                    <span class="text-white font-medium">Rp {{ number_format($r->installment_down_payment, 0, ',', '.') }}</span>
                </div>
                @endif

                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Tgl. Kunjungan</span>
                    <span class="text-white font-medium">{{ \Carbon\Carbon::parse($r->preferred_date)->isoFormat('D MMM Y') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Jam Kunjungan</span>
                    <span class="text-white font-medium">{{ \Carbon\Carbon::parse($r->preferred_time)->format('H:i') }} WIB</span>
                </div>
                @if($r->notes)
                <div class="pt-2">
                    <p class="text-xs text-gray-500 mb-1">Catatan Anda:</p>
                    <p class="text-xs text-gray-300 italic glass p-2 rounded-lg">"{{ $r->notes }}"</p>
                </div>
                @endif
                <div class="pt-2">
                    <p class="text-xs text-gray-500 mb-1">Kedaluwarsa:</p>
                    <p class="text-xs text-red-400">{{ \Carbon\Carbon::parse($r->expired_at)->isoFormat('D MMM Y, H:i') }}</p>
                </div>
            </div>

            {{-- Footer Actions --}}
            @if(in_array($r->status, ['pending', 'confirmed']))
            <div class="p-4" style="border-top:1px solid rgba(255,255,255,0.05); background:rgba(0,0,0,0.2);">
                <form action="{{ route('customer.reservations.cancel', $r) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan reservasi ini?');">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full text-xs font-semibold text-red-400 py-2 rounded-lg hover:bg-red-900/30 transition border border-transparent hover:border-red-900/50">
                        Batalkan Reservasi
                    </button>
                </form>
            </div>
            @endif
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="flex justify-center">
        {{ $reservations->links() }}
    </div>
    @endif
</x-customer-app>
