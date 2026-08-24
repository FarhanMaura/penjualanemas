<x-admin-app>
<x-slot name="pageTitle">Detail Reservasi</x-slot>

<div class="max-w-3xl mx-auto flex flex-col justify-center items-center w-full">
    <div class="w-full mb-4 flex justify-between items-center">
        <a href="{{ route('admin.reservations.index') }}" class="text-sm font-bold text-[#085C54] hover:underline">← Kembali ke Reservasi</a>
    </div>

    @if(session('success'))
    <div class="w-full p-4 rounded-xl text-sm font-bold bg-emerald-50 border border-emerald-300 text-emerald-900 mb-4 shadow-sm">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="w-full p-4 rounded-xl text-sm font-bold bg-red-50 border border-red-300 text-red-900 mb-4 shadow-sm">❌ {{ session('error') }}</div>
    @endif

    <div class="glass rounded-2xl p-6 sm:p-8 mb-6 w-full bg-white border border-[#e8e3d5] shadow-md">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-200">
            <div>
                <h2 class="font-display text-2xl font-bold text-slate-900 font-playfair">{{ $reservation->reservation_code }}</h2>
                <p class="text-xs text-slate-500 mt-1 font-medium">Dibuat {{ $reservation->created_at->isoFormat('D MMMM Y, HH:mm') }} WIB</p>
            </div>
            @php
                $statusBadge = match($reservation->status) {
                    'pending'   => 'bg-amber-100 text-amber-900 border-amber-300',
                    'confirmed' => 'bg-emerald-100 text-emerald-900 border-emerald-300',
                    'completed' => 'bg-blue-100 text-blue-900 border-blue-300',
                    default     => 'bg-red-100 text-red-900 border-red-300',
                };
            @endphp
            <span class="px-3.5 py-1.5 rounded-full text-xs font-bold border {{ $statusBadge }}">
                {{ ucfirst($reservation->status) }}
            </span>
        </div>

        {{-- Highlight Tawar Harga jika ada --}}
        @if($reservation->agreed_price || $reservation->priceNegotiation)
        <div class="rounded-xl p-4 mb-6 bg-amber-50/80 border border-amber-200 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <span class="text-xs font-bold text-amber-900 uppercase tracking-wider">🤝 Hasil Kesepakatan Tawar Harga</span>
                    <p class="text-xs text-slate-700 mt-0.5 font-semibold">Kode Tawar: <span class="font-mono text-[#085C54]">{{ $reservation->priceNegotiation->negotiation_code ?? '-' }}</span></p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-slate-600 font-semibold">Harga Disetujui</p>
                    <p class="text-xl font-extrabold text-[#C6A443]">Rp {{ number_format($reservation->agreed_price ?? $reservation->priceNegotiation->agreed_price, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-sm">
            <div>
                <p class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-0.5">Pelanggan</p>
                <p class="text-slate-900 font-bold text-base">{{ $reservation->user->name }}</p>
                <p class="text-slate-600 text-xs font-medium">{{ $reservation->user->email }}</p>
                <p class="text-slate-600 text-xs font-medium">No. HP: {{ $reservation->user->profile?->phone ?? '-' }}</p>
            </div>
            <div>
                <p class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-0.5">Tipe Pengajuan</p>
                <p class="text-[#085C54] font-extrabold text-base">
                    {{
                        match($reservation->type) {
                            'purchase'    => 'Pembelian Emas (Tunai)',
                            'buyback'     => 'Jual Emas ke Toko (Buyback)',
                            'installment' => 'Pembelian Emas (Cicilan)',
                            'pawn'        => 'Gadai Emas (Pinjaman)',
                            default       => ucfirst($reservation->type ?? 'Pembelian Emas')
                        }
                    }}
                </p>
            </div>

            @if($reservation->payment_method)
            <div>
                <p class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-0.5">Metode Bayar / Penerimaan</p>
                <p class="text-slate-900 font-bold uppercase">{{ $reservation->payment_method }}</p>
            </div>
            @endif

            @if(in_array($reservation->type, ['purchase', 'installment']))
            <div>
                <p class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-0.5">Produk Toko</p>
                <p class="text-slate-900 font-bold">{{ $reservation->product->name ?? 'Produk dihapus' }}</p>
                <p class="text-slate-600 text-xs font-semibold">{{ $reservation->product->gold_purity ?? '' }} • {{ $reservation->product->weight_gram ?? '' }}g</p>
            </div>
            <div>
                <p class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-0.5">Jumlah (Qty)</p>
                <p class="text-slate-900 font-extrabold">{{ $reservation->quantity }} pcs</p>
            </div>
            @endif

            @if($reservation->type === 'buyback')
            <div class="col-span-1 sm:col-span-2 p-4 rounded-xl bg-emerald-50/80 border border-emerald-200">
                <p class="font-bold text-emerald-950 mb-2">💰 Rincian Emas yang Dijual Pelanggan (Buyback)</p>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                    <div>
                        <span class="text-slate-600 block">Deskripsi:</span>
                        <span class="text-slate-900 font-bold">{{ $reservation->pawn_gold_description }}</span>
                    </div>
                    <div>
                        <span class="text-slate-600 block">Kadar Emas:</span>
                        <span class="text-slate-900 font-bold">{{ $reservation->pawn_gold_purity }}</span>
                    </div>
                    <div>
                        <span class="text-slate-600 block">Berat Emas:</span>
                        <span class="text-slate-900 font-bold">{{ number_format($reservation->pawn_weight_gram, 3) }} gram</span>
                    </div>
                </div>
            </div>
            @endif

            @if($reservation->type === 'pawn')
            <div class="col-span-1 sm:col-span-2 p-4 rounded-xl bg-amber-50/80 border border-amber-200">
                <p class="font-bold text-amber-950 mb-2">💎 Detail Gadai yang Diajukan</p>
                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div>
                        <span class="text-slate-600 block">Deskripsi Emas:</span>
                        <span class="text-slate-900 font-bold">{{ $reservation->pawn_gold_description }}</span>
                    </div>
                    <div>
                        <span class="text-slate-600 block">Kadar Emas:</span>
                        <span class="text-slate-900 font-bold">{{ $reservation->pawn_gold_purity }}</span>
                    </div>
                    <div>
                        <span class="text-slate-600 block">Berat Emas:</span>
                        <span class="text-slate-900 font-bold">{{ number_format($reservation->pawn_weight_gram, 2) }} gram</span>
                    </div>
                    <div>
                        <span class="text-slate-600 block">Pengajuan Pinjaman:</span>
                        <span class="text-[#085C54] font-extrabold text-sm">Rp {{ number_format($reservation->pawn_amount_requested, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            @endif

            @if($reservation->type === 'installment')
            <div class="col-span-1 sm:col-span-2 p-4 rounded-xl bg-blue-50/80 border border-blue-200">
                <p class="font-bold text-blue-950 mb-2">📅 Rencana Cicilan yang Diajukan</p>
                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div>
                        <span class="text-slate-600 block">Tenor:</span>
                        <span class="text-slate-900 font-bold">{{ $reservation->installment_tenure }} Bulan</span>
                    </div>
                    <div>
                        <span class="text-slate-600 block">Uang Muka (DP):</span>
                        <span class="text-slate-900 font-bold">Rp {{ number_format($reservation->installment_down_payment, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            @endif

            <div>
                <p class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-0.5">Tanggal Kunjungan</p>
                <p class="text-slate-900 font-bold">{{ \Carbon\Carbon::parse($reservation->preferred_date)->isoFormat('dddd, D MMMM Y') }}</p>
                @if($reservation->preferred_time)
                <p class="text-slate-600 font-medium">Jam {{ $reservation->preferred_time }} WIB</p>
                @endif
            </div>
            <div></div>

            @if($reservation->notes)
            <div class="col-span-1 sm:col-span-2">
                <p class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-0.5">Catatan Pelanggan</p>
                <p class="text-slate-800 bg-slate-50 p-3 rounded-xl border border-slate-200 italic">"{{ $reservation->notes }}"</p>
            </div>
            @endif
            @if($reservation->admin_notes)
            <div class="col-span-1 sm:col-span-2">
                <p class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-0.5">Catatan Admin</p>
                <p class="text-slate-800 bg-slate-50 p-3 rounded-xl border border-slate-200">{{ $reservation->admin_notes }}</p>
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
            <button type="submit" class="btn-confirm w-full py-3 text-sm font-bold">
                ✓ Konfirmasi Saja
            </button>
        </form>
        <form method="POST" action="{{ route('admin.reservations.reject', $reservation) }}" class="flex-1">
            @csrf
            <button type="submit" class="btn-danger w-full py-3 text-sm font-bold" onclick="return confirm('Yakin ingin menolak reservasi ini?');">
                ✗ Tolak Reservasi
            </button>
        </form>
    </div>
    @elseif($reservation->status === 'confirmed')
    <div class="w-full">
        <a href="{{ route('admin.transactions.create', ['reservation_id'=>$reservation->id]) }}" class="btn-orange w-full text-center block py-3.5 text-sm font-extrabold shadow-lg">
            ⚡ Proses Jadi Transaksi Kasir (Otomatis Terisi) →
        </a>
    </div>
    @endif
</div>

<x-slot name="scripts">
    @vite('resources/js/admin/reservations.js')
</x-slot>
</x-admin-app>

