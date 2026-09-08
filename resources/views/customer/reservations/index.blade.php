<x-customer-app>
    <x-slot name="pageTitle">Daftar Reservasi</x-slot>
    <x-slot name="breadcrumb">Kelola jadwal kunjungan dan reservasi produk Anda</x-slot>

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

    {{-- Filter Bar --}}
    <div class="glass rounded-2xl p-4 mb-6 flex flex-wrap gap-4 items-center justify-between bg-white border border-[#e8e3d5] shadow-sm">
        <form method="GET" action="{{ route('customer.reservations.index') }}" class="flex gap-2">
            <select name="status" class="rounded-xl px-4 py-2 text-sm text-slate-900 font-semibold bg-[#F4EDD9]/60 border border-[#e8e3d5] outline-none focus:ring-2 focus:ring-[#085C54] cursor-pointer"
                    onchange="this.form.submit()">
                <option value="" class="text-slate-900">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }} class="text-slate-900">Menunggu (Pending)</option>
                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }} class="text-slate-900">Dikonfirmasi</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }} class="text-slate-900">Selesai (Completed)</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }} class="text-slate-900">Dibatalkan</option>
            </select>
        </form>
        <a href="{{ route('customer.reservations.create') }}" class="px-5 py-2.5 rounded-xl text-xs sm:text-sm font-extrabold text-[#042623] gold-gradient border border-[#C6A443] shadow-md hover:brightness-110 transition">
            + Buat Reservasi Baru
        </a>
    </div>

    {{-- Reservasi List --}}
    @if($reservations->isEmpty())
    <div class="glass rounded-3xl p-12 text-center mt-6 bg-white border border-[#e8e3d5] shadow-md">
        <span class="text-6xl">📋</span>
        <p class="text-slate-900 text-lg mt-4 font-bold">Tidak Ada Reservasi</p>
        <p class="text-slate-600 text-sm mt-2">Anda belum memiliki reservasi dengan status tersebut.</p>
        <a href="{{ route('customer.reservations.create') }}" class="mt-6 inline-block font-bold text-[#085C54] hover:underline">Mulai Reservasi Pertama →</a>
    </div>
    @else
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @foreach($reservations as $r)
        @php
            $statusStyles = [
                'pending'   => ['bg'=>'bg-amber-100','border'=>'border-amber-300','text'=>'text-amber-900','label'=>'Menunggu ⏳'],
                'confirmed' => ['bg'=>'bg-blue-100','border'=>'border-blue-300','text'=>'text-blue-900','label'=>'Dikonfirmasi ✓'],
                'completed' => ['bg'=>'bg-emerald-100','border'=>'border-emerald-300','text'=>'text-emerald-900','label'=>'Selesai 🏁'],
                'cancelled' => ['bg'=>'bg-red-100','border'=>'border-red-300','text'=>'text-red-900','label'=>'Batal ❌'],
                'expired'   => ['bg'=>'bg-slate-100','border'=>'border-slate-300','text'=>'text-slate-700','label'=>'Kedaluwarsa ⌛'],
            ];
            $style = $statusStyles[$r->status] ?? $statusStyles['pending'];
        @endphp
        
        <div class="glass rounded-2xl overflow-hidden relative flex flex-col bg-white border border-[#e8e3d5] shadow-md hover:border-[#085C54]/40 transition">
            {{-- Header Card --}}
            <div class="p-5 bg-[#F4EDD9]/40 border-b border-[#e8e3d5]">
                <div class="flex justify-between items-start mb-2">
                    <span class="font-mono text-xs text-[#085C54] font-bold bg-white border border-[#e8e3d5] px-2.5 py-1 rounded-md">{{ $r->reservation_code }}</span>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full border {{ $style['bg'] }} {{ $style['border'] }} {{ $style['text'] }}">
                        {{ $style['label'] }}
                    </span>
                </div>
                <h3 class="font-bold text-slate-900 text-lg leading-tight mt-3">
                    @if(str_starts_with($r->reservation_code, 'RSV-PKP-'))
                        📦 {{ $r->product->name ?? 'Pengambilan Emas Fisik' }}
                    @elseif($r->type === 'buyback')
                        💰 {{ $r->pawn_gold_description ?? 'Jual Emas ke Toko' }}
                    @elseif($r->type === 'pawn')
                        🏦 {{ $r->pawn_gold_description ?? 'Gadai Emas' }}
                    @else
                        💍 {{ $r->product->name ?? 'Produk Dihapus' }}
                    @endif
                </h3>
                <p class="text-xs text-slate-600 font-semibold mt-1">
                    Tipe: 
                    <span class="text-[#085C54] font-bold">
                        {{
                            match(true) {
                                str_starts_with($r->reservation_code, 'RSV-PKP-') => '📦 Pengambilan Emas Fisik (Cicilan Lunas)',
                                $r->type === 'installment' && $r->transaction_id  => 'Pengajuan Cicilan (Sudah Aktif di Cicilan Saya)',
                                $r->type === 'purchase'    => 'Pembelian Emas (Beli Lunas)',
                                $r->type === 'buyback'     => 'Jual Emas (Buyback)',
                                $r->type === 'installment' => 'Pembelian (Cicilan)',
                                $r->type === 'pawn'        => 'Gadai Emas (Pinjaman)',
                                default                    => ucfirst($r->type ?? 'Pembelian')
                            }
                        }}
                    </span>
                </p>
            </div>

            {{-- Body Card --}}
            <div class="p-5 flex-1 space-y-3">
                @if(in_array($r->type, ['purchase', 'installment']))
                <div class="flex justify-between text-sm">
                    <span class="text-slate-600 font-semibold">Jumlah</span>
                    <span class="text-slate-900 font-bold">{{ $r->quantity }} item</span>
                </div>
                <div class="flex justify-between items-center text-sm pt-1 border-t border-slate-100">
                    <span class="text-slate-600 font-semibold">Total Harga</span>
                    @if($r->agreed_price || $r->priceNegotiation)
                    <div class="text-right">
                        <span class="text-[#C6A443] font-extrabold text-base">Rp {{ number_format($r->agreed_price ?? $r->priceNegotiation->agreed_price, 0, ',', '.') }}</span>
                        <span class="block text-[10px] text-amber-900 font-bold bg-amber-100 px-1.5 py-0.5 rounded border border-amber-300 mt-0.5">🤝 Tawar Harga ACC</span>
                    </div>
                    @elseif($r->product)
                    <span class="text-slate-900 font-extrabold">Rp {{ number_format($r->product->base_price * $r->quantity, 0, ',', '.') }}</span>
                    @endif
                </div>

                @if(str_starts_with($r->reservation_code, 'RSV-PKP-'))
                <div class="pt-2 border-t border-slate-100 space-y-1.5">
                    <div class="flex justify-between items-center text-xs sm:text-sm">
                        <span class="text-slate-600 font-semibold">Metode Pembayaran:</span>
                        <span class="text-emerald-800 font-extrabold bg-emerald-100 px-2.5 py-0.5 rounded-full border border-emerald-300">
                            ✅ Cicilan Lunas
                        </span>
                    </div>
                    <div class="px-2.5 py-1.5 rounded-lg bg-emerald-50 text-emerald-950 border border-emerald-200 text-xs font-semibold flex items-center gap-1.5">
                        📦 <span>Emas Fisik Siap Diambil di Toko Tanpa Biaya Tambahan</span>
                    </div>
                </div>
                @elseif($r->payment_method)
                @php $pm = $r->paymentMethodDetail ?? $r->payment_method_model; @endphp
                <div class="pt-2 border-t border-slate-100 space-y-1.5">
                    <div class="flex justify-between items-center text-xs sm:text-sm">
                        <span class="text-slate-600 font-semibold">Metode Bayar:</span>
                        <span class="text-slate-900 font-bold">{{ $pm->name ?? strtoupper($r->payment_method) }}</span>
                    </div>
                    @if($pm && $pm->account_number)
                    <div class="p-2.5 rounded-xl bg-amber-50/80 border border-amber-200 text-xs space-y-0.5">
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] text-amber-900 font-extrabold uppercase">No. Rekening Toko ({{ $pm->bank_name ?? 'Bank' }}):</span>
                            <span class="text-[10px] font-bold text-emerald-800 bg-emerald-100 px-1.5 py-0.5 rounded border border-emerald-300">Transfer</span>
                        </div>
                        <p class="font-mono text-sm font-extrabold text-[#085C54] tracking-wider">{{ $pm->account_number }}</p>
                        @if($pm->account_name)
                        <p class="text-[11px] text-slate-600 font-medium">a.n. {{ $pm->account_name }}</p>
                        @endif
                    </div>
                    @elseif($pm && $pm->isCash())
                    <div class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-900 border border-emerald-200 text-xs font-semibold flex items-center gap-1">
                        💵 <span>Bayar Tunai di Kasir Toko</span>
                    </div>
                    @endif
                </div>
                @endif
                @endif

                @if($r->type === 'buyback')
                <div class="flex justify-between text-sm">
                    <span class="text-slate-600 font-semibold">Barang yang Dijual</span>
                    <span class="text-slate-900 font-bold">{{ $r->pawn_gold_description }}</span>
                </div>
                {{-- Buyback: tidak ada harga & metode pembayaran di web, semua di toko --}}
                <div class="mt-2 px-3 py-2.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs flex items-start gap-2">
                    <span class="text-base shrink-0">🏪</span>
                    <span class="text-emerald-900 font-semibold leading-relaxed">
                        <strong>Murni O2O (Di Toko):</strong> Penilaian fisik emas, penimbangan berat, pengujian kadar, penentuan harga beli, dan pembayaran dana dilakukan langsung di toko saat kunjungan.
                    </span>
                </div>
                @endif

                @if($r->type === 'pawn')
                <div class="flex justify-between text-sm">
                    <span class="text-slate-600 font-semibold">Kadar Emas</span>
                    <span class="text-slate-900 font-bold">{{ $r->pawn_gold_purity }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-600 font-semibold">Berat Emas</span>
                    <span class="text-slate-900 font-bold">{{ number_format($r->pawn_weight_gram, 3) }} gram</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-600 font-semibold">Pengajuan Pinjaman</span>
                    <span class="text-[#085C54] font-extrabold">Rp {{ number_format($r->pawn_amount_requested, 0, ',', '.') }}</span>
                </div>
                @endif

                @if($r->type === 'installment' && $r->installment_tenure)
                <div class="flex justify-between text-sm">
                    <span class="text-slate-600 font-semibold">Tenor Cicilan</span>
                    <span class="text-slate-900 font-bold">{{ $r->installment_tenure }} Bulan</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-600 font-semibold">Uang Muka (DP)</span>
                    <span class="text-[#085C54] font-extrabold">Rp {{ number_format($r->installment_down_payment, 0, ',', '.') }}</span>
                </div>
                @endif

                @if($r->type === 'installment' && $r->transaction_id && !str_starts_with($r->reservation_code, 'RSV-PKP-'))
                <div class="pt-2">
                    <a href="{{ route('customer.installments.index') }}" class="w-full inline-flex items-center justify-center gap-1 text-xs font-bold text-[#085C54] bg-[#F4EDD9] hover:bg-[#e8e0c8] py-2 px-3 rounded-xl border border-[#C6A443]/40 transition shadow-sm">
                        <span>📅 Buka Menu Cicilan Saya →</span>
                    </a>
                </div>
                @endif

                <div class="flex justify-between text-sm pt-2 border-t border-slate-100">
                    <span class="text-slate-600 font-semibold">Tgl. Kunjungan</span>
                    <span class="text-slate-900 font-bold">{{ \Carbon\Carbon::parse($r->preferred_date)->isoFormat('D MMM Y') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-600 font-semibold">Jam Kunjungan</span>
                    <span class="text-slate-900 font-bold">{{ \Carbon\Carbon::parse($r->preferred_time)->format('H:i') }} WIB</span>
                </div>
                @if($r->notes)
                <div class="pt-2">
                    <p class="text-xs text-slate-500 font-bold uppercase mb-1">Catatan Anda:</p>
                    <p class="text-xs text-slate-700 italic bg-slate-50 p-2.5 rounded-xl border border-slate-200">"{{ $r->notes }}"</p>
                </div>
                @endif
                <div class="pt-1">
                    <p class="text-xs text-slate-500 font-medium">Batas Konfirmasi: <span class="text-red-700 font-semibold">{{ \Carbon\Carbon::parse($r->expired_at)->isoFormat('D MMM Y, HH:mm') }}</span></p>
                </div>
            </div>

            {{-- Footer Actions --}}
            @if(in_array($r->status, ['pending', 'confirmed']))
            <div class="p-4 bg-slate-50 border-t border-slate-100">
                <form action="{{ route('customer.reservations.cancel', $r) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan reservasi ini?');">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full text-xs font-bold text-red-700 py-2 rounded-xl bg-red-50 hover:bg-red-100 transition border border-red-200">
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

