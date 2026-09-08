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
                        match(true) {
                            str_starts_with($reservation->reservation_code, 'RSV-PKP-') => '📦 Pengambilan Emas Fisik (Cicilan Lunas)',
                            $reservation->type === 'purchase'    => 'Pembelian Emas (Beli Lunas)',
                            $reservation->type === 'buyback'     => 'Jual Emas ke Toko (Buyback)',
                            $reservation->type === 'installment' => 'Pembelian Emas (Cicilan)',
                            $reservation->type === 'pawn'        => 'Gadai Emas (Pinjaman)',
                            default                              => ucfirst($reservation->type ?? 'Pembelian Emas')
                        }
                    }}
                </p>
            </div>

            @if(str_starts_with($reservation->reservation_code, 'RSV-PKP-'))
            <div>
                <p class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-0.5">Status Pembayaran</p>
                <p class="text-emerald-800 font-bold text-base">✅ Cicilan Lunas (Bebas Biaya di Toko)</p>
                <span class="inline-block mt-0.5 text-xs font-semibold px-2 py-0.5 rounded bg-emerald-100 text-emerald-900 border border-emerald-300">
                    📦 Serah Terima Emas Fisik
                </span>
            </div>
            @elseif($reservation->payment_method)
            @php $pm = $reservation->paymentMethodDetail ?? $reservation->payment_method_model; @endphp
            <div>
                <p class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-0.5">Metode Bayar / Penerimaan</p>
                <p class="text-slate-900 font-bold text-base">{{ $pm->name ?? strtoupper($reservation->payment_method) }}</p>
                @if($pm && $pm->account_number)
                <p class="text-xs font-mono font-bold text-[#085C54] mt-0.5">
                    No. Rek: {{ $pm->account_number }} {{ $pm->account_name ? '(a.n. '.$pm->account_name.')' : '' }}
                </p>
                @elseif($pm && $pm->isCash())
                <span class="inline-block mt-0.5 text-xs font-semibold px-2 py-0.5 rounded bg-emerald-100 text-emerald-900 border border-emerald-300">
                    💵 Pembayaran Tunai (Cash di Toko)
                </span>
                @endif
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
                <p class="font-bold text-emerald-950 mb-2">💰 Rincian Emas yang Ingin Dijual Pelanggan (Buyback O2O)</p>
                <div class="mb-3 text-xs">
                    <span class="text-slate-600 block mb-1 font-semibold">Jenis / Deskripsi Perhiasan yang Dibawa:</span>
                    <span class="text-slate-900 font-bold text-sm bg-white px-3 py-2 rounded-lg border border-emerald-200 inline-block">
                        💍 {{ $reservation->pawn_gold_description ?? 'Perhiasan Emas' }}
                    </span>
                </div>
                {{-- Info: Murni O2O, tidak ada transaksi web --}}
                <div class="flex items-start gap-2 px-3.5 py-3 rounded-xl bg-white border border-emerald-300 text-xs shadow-sm">
                    <span class="text-base shrink-0">🏪</span>
                    <span class="text-emerald-900 font-semibold leading-relaxed">
                        <strong>Murni Reservasi Online-to-Offline (O2O):</strong> Penilaian fisik barang, penimbangan berat riil, pengujian kadar, penentuan harga beli, dan pembayaran dana diselesaikan langsung di Toko Sinar Baru II saat pelanggan datang berkunjung. <em>Reservasi buyback tidak menghasilkan transaksi kasir di website.</em>
                    </span>
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

    @php
        $isPickupReservation = str_starts_with($reservation->reservation_code, 'RSV-PKP-');
    @endphp

    @if($isPickupReservation)
        {{-- Khusus Reservasi Pengambilan Emas Fisik Cicilan (Transaksi Utama Sudah Ada) --}}
        @if(in_array($reservation->status, ['pending', 'confirmed']))
        <div class="flex flex-col sm:flex-row gap-3 w-full">
            @if($reservation->status === 'pending')
            <form method="POST" action="{{ route('admin.reservations.confirm', $reservation) }}" class="flex-1">
                @csrf
                <button type="submit" class="btn-confirm w-full py-3.5 text-sm font-bold shadow-md">
                    ✓ Konfirmasi Jadwal Kunjungan
                </button>
            </form>
            @endif

            <form method="POST" action="{{ route('admin.reservations.complete', $reservation) }}" class="flex-1" onsubmit="return confirm('Apakah Anda yakin serah terima perhiasan emas fisik telah selesai dilakukan?');">
                @csrf
                <button type="submit" class="w-full py-3.5 px-4 rounded-xl font-extrabold text-sm text-[#042623] gold-gradient border border-[#C6A443] shadow-lg hover:brightness-110 transition flex items-center justify-center gap-2">
                    <span>🎉</span> <span>Tandai Selesai (Serah Terima Emas Fisik)</span>
                </button>
            </form>

            @if($reservation->status === 'pending')
            <form method="POST" action="{{ route('admin.reservations.reject', $reservation) }}" class="flex-1">
                @csrf
                <button type="submit" class="btn-danger w-full py-3.5 text-sm font-bold shadow-sm" onclick="return confirm('Yakin ingin menolak reservasi ini?');">
                    ✗ Tolak Reservasi
                </button>
            </form>
            @endif
        </div>
        @elseif($reservation->status === 'completed')
        <div class="w-full p-4 rounded-2xl bg-blue-50 border border-blue-300 text-blue-950 text-center font-bold text-sm shadow-sm flex items-center justify-center gap-2">
            <span>🎉</span> <span>Reservasi & Serah Terima Perhiasan Emas Fisik Telah Selesai sepenuhnya.</span>
        </div>
        @endif
    @else
        {{-- Pengajuan Cicilan Baru / Pembelian Biasa / Buyback / Gadai --}}
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
        <div class="flex flex-col sm:flex-row gap-3 w-full">
            <a href="{{ route('admin.transactions.create', ['reservation_id'=>$reservation->id]) }}" class="btn-orange flex-1 text-center block py-3.5 text-sm font-extrabold shadow-lg">
                ⚡ Proses Jadi Transaksi Kasir (Otomatis Terisi) →
            </a>
            <form method="POST" action="{{ route('admin.reservations.complete', $reservation) }}" class="flex-1" onsubmit="return confirm('Apakah Anda yakin transaksi / reservasi ini telah selesai?');">
                @csrf
                <button type="submit" class="w-full py-3.5 px-4 rounded-xl font-extrabold text-sm text-[#042623] gold-gradient border border-[#C6A443] shadow-lg hover:brightness-110 transition flex items-center justify-center gap-2">
                    <span>🎉</span> <span>Tandai Selesai</span>
                </button>
            </form>
        </div>
        @elseif($reservation->status === 'completed')
        <div class="w-full p-4 rounded-2xl bg-blue-50 border border-blue-300 text-blue-950 text-center font-bold text-sm shadow-sm flex items-center justify-center gap-2">
            <span>🎉</span> <span>Reservasi ini telah Selesai diproses.</span>
        </div>
        @elseif(in_array($reservation->status, ['cancelled', 'expired']))
        <div class="w-full p-4 rounded-2xl bg-red-50 border border-red-300 text-red-950 text-center font-bold text-sm shadow-sm flex items-center justify-center gap-2">
            <span>❌</span> <span>Reservasi ini berstatus {{ ucfirst($reservation->status) }}.</span>
        </div>
        @endif
    @endif

    {{-- Opsi Hapus Reservasi (Admin) --}}
    <div class="pt-6 border-t border-slate-200 mt-6 flex justify-between items-center w-full">
        <span class="text-xs text-slate-500 font-medium">Hapus riwayat reservasi yang dibatalkan / selesai / data uji coba</span>
        <form method="POST" action="{{ route('admin.reservations.destroy', $reservation) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus reservasi #{{ $reservation->reservation_code }} secara permanen?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2.5 rounded-xl text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 hover:text-red-700 border border-red-200 transition flex items-center gap-1.5 shadow-sm">
                <span>🗑️</span> <span>Hapus Reservasi</span>
            </button>
        </form>
    </div>
</div>

<x-slot name="scripts">
    @vite('resources/js/admin/reservations.js')
</x-slot>
</x-admin-app>

