<x-admin-app>
<x-slot name="pageTitle">Detail Reservasi</x-slot>

<div class="max-w-3xl mx-auto flex flex-col justify-center items-center w-full">
    <div class="w-full mb-4 flex justify-between items-center">
        <a href="{{ route('admin.reservations.index') }}" class="text-sm text-gray-400 hover:text-yellow-400 transition">← Kembali ke Reservasi</a>
    </div>

    @if(session('success'))
    <div class="w-full flash-success mb-4" data-flash>✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="w-full flash-error mb-4" data-flash>❌ {{ session('error') }}</div>
    @endif

    <div class="glass rounded-2xl p-6 mb-6 w-full shadow-2xl">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-white/10">
            <div>
                <h2 class="font-display text-xl text-white">{{ $reservation->reservation_code }}</h2>
                <p class="text-xs text-gray-500 mt-1">Dibuat {{ $reservation->created_at->isoFormat('D MMMM Y, HH:mm') }}</p>
            </div>
            <span class="badge text-sm px-3 py-1 {{
                match($reservation->status) {
                    'pending'   => 'badge-yellow',
                    'confirmed' => 'badge-green',
                    'completed' => 'badge-blue',
                    default     => 'badge-red',
                }
            }}">{{ ucfirst($reservation->status) }}</span>
        </div>

        {{-- Highlight Tawar Harga jika ada --}}
        @if($reservation->agreed_price || $reservation->priceNegotiation)
        <div class="glass rounded-xl p-4 mb-6 bg-amber-500/10 border-amber-500/30">
            <div class="flex justify-between items-center">
                <div>
                    <span class="text-xs font-bold text-amber-400 uppercase tracking-wider">🤝 Hasil Kesepakatan Tawar Harga</span>
                    <p class="text-xs text-gray-300 mt-0.5">Kode Tawar: {{ $reservation->priceNegotiation->negotiation_code ?? '-' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-400">Harga Disetujui</p>
                    <p class="text-xl font-extrabold text-amber-400">Rp {{ number_format($reservation->agreed_price ?? $reservation->priceNegotiation->agreed_price, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-400 mb-0.5">Pelanggan</p>
                <p class="text-white font-medium">{{ $reservation->user->name }}</p>
                <p class="text-gray-500">{{ $reservation->user->email }}</p>
            </div>
            <div>
                <p class="text-gray-400 mb-0.5">Tipe Pengajuan</p>
                <p class="text-white font-medium">
                    {{
                        match($reservation->type) {
                            'purchase'    => 'Pembelian Emas (Tunai)',
                            'installment' => 'Pembelian Emas (Cicilan)',
                            'pawn'        => 'Gadai Emas (Pinjaman)',
                            default       => ucfirst($reservation->type ?? 'Pembelian Emas')
                        }
                    }}
                </p>
            </div>

            @if($reservation->payment_method)
            <div>
                <p class="text-gray-400 mb-0.5">Metode Bayar Diajukan</p>
                <p class="text-white font-medium uppercase">{{ $reservation->payment_method }}</p>
            </div>
            @endif

            @if(($reservation->type ?? 'purchase') !== 'pawn')
            <div>
                <p class="text-gray-400 mb-0.5">Produk</p>
                <p class="text-white font-medium">{{ $reservation->product->name ?? 'Produk dihapus' }}</p>
                <p class="text-gray-500">{{ $reservation->product->gold_purity ?? '' }} • {{ $reservation->product->weight_gram ?? '' }}g</p>
            </div>
            <div>
                <p class="text-gray-400 mb-0.5">Jumlah (Qty)</p>
                <p class="text-white font-bold">{{ $reservation->quantity }} pcs</p>
            </div>
            @endif

            @if(($reservation->type ?? 'purchase') === 'pawn')
            <div class="col-span-2 glass p-4 rounded-xl" style="background:rgba(245,158,11,0.05); border-color:rgba(245,158,11,0.15);">
                <p class="font-bold text-yellow-400 mb-2">💎 Detail Gadai yang Diajukan</p>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <span class="text-gray-400">Deskripsi Emas:</span>
                        <span class="text-white font-medium">{{ $reservation->pawn_gold_description }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400">Kadar Emas:</span>
                        <span class="text-white font-medium">{{ $reservation->pawn_gold_purity }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400">Berat Emas:</span>
                        <span class="text-white font-medium">{{ number_format($reservation->pawn_weight_gram, 2) }} gram</span>
                    </div>
                    <div>
                        <span class="text-gray-400">Pengajuan Pinjaman:</span>
                        <span class="text-yellow-400 font-bold">Rp {{ number_format($reservation->pawn_amount_requested, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            @endif

            @if(($reservation->type ?? 'purchase') === 'installment')
            <div class="col-span-2 glass p-4 rounded-xl" style="background:rgba(59,130,246,0.05); border-color:rgba(59,130,246,0.15);">
                <p class="font-bold text-blue-400 mb-2">📅 Rencana Cicilan yang Diajukan</p>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <span class="text-gray-400">Tenor:</span>
                        <span class="text-white font-medium">{{ $reservation->installment_tenure }} Bulan</span>
                    </div>
                    <div>
                        <span class="text-gray-400">Uang Muka (DP):</span>
                        <span class="text-white font-medium">Rp {{ number_format($reservation->installment_down_payment, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            @endif

            <div>
                <p class="text-gray-400 mb-0.5">Tanggal Kunjungan</p>
                <p class="text-white">{{ \Carbon\Carbon::parse($reservation->preferred_date)->isoFormat('dddd, D MMMM Y') }}</p>
                @if($reservation->preferred_time)
                <p class="text-gray-500">Jam {{ $reservation->preferred_time }}</p>
                @endif
            </div>
            <div>
                <!-- empty alignment -->
            </div>
            @if($reservation->notes)
            <div class="col-span-2">
                <p class="text-gray-400 mb-0.5">Catatan Pelanggan</p>
                <p class="text-gray-300">{{ $reservation->notes }}</p>
            </div>
            @endif
            @if($reservation->admin_notes)
            <div class="col-span-2">
                <p class="text-gray-400 mb-0.5">Catatan Admin</p>
                <p class="text-gray-300">{{ $reservation->admin_notes }}</p>
            </div>
            @endif
        </div>
    </div>

    @if($reservation->status === 'pending')
    <div class="flex flex-col sm:flex-row gap-3 w-full">
        <form method="POST" action="{{ route('admin.reservations.confirm', $reservation) }}" class="flex-1">
            @csrf
            <input type="hidden" name="process_transaction" value="1">
            <button type="submit" class="btn-orange w-full py-3 text-sm font-bold shadow-lg">
                ⚡ Konfirmasi & Langsung Transaksi →
            </button>
        </form>
        <form method="POST" action="{{ route('admin.reservations.confirm', $reservation) }}" class="flex-1">
            @csrf
            <button type="submit" class="btn-confirm w-full py-3 text-sm">
                ✓ Konfirmasi Saja
            </button>
        </form>
        <form method="POST" action="{{ route('admin.reservations.reject', $reservation) }}" class="flex-1">
            @csrf
            <button type="submit" class="btn-danger w-full py-3 text-sm" data-confirm-reject data-customer-name="{{ $reservation->user->name }}">
                ✗ Tolak Reservasi
            </button>
        </form>
    </div>
    @elseif($reservation->status === 'confirmed')
    <div class="w-full">
        <a href="{{ route('admin.transactions.create', ['reservation_id'=>$reservation->id]) }}" class="btn-orange w-full text-center block py-3 text-sm font-bold shadow-lg">
            ⚡ Proses Jadi Transaksi (Otomatis Terisi) →
        </a>
    </div>
    @endif
</div>

<x-slot name="scripts">
    @vite('resources/js/admin/reservations.js')
</x-slot>
</x-admin-app>
