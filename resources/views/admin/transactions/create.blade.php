<x-admin-app>
<x-slot name="pageTitle">Catat Transaksi Baru</x-slot>

<div class="max-w-4xl mx-auto flex flex-col justify-center items-center w-full">
    <div class="w-full mb-4 flex justify-between items-center">
        <a href="{{ route('admin.transactions.index') }}" class="text-sm text-gray-400 hover:text-yellow-400 transition">← Kembali ke Daftar Transaksi</a>
    </div>

    @if(session('error'))
    <div class="w-full flash-error mb-4" data-flash>❌ {{ session('error') }}</div>
    @endif
    @if($errors->any())
    <div class="w-full flash-error mb-4" data-flash>
        <ul class="list-disc list-inside text-sm space-y-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    @php
        $agreedPriceTotal = $selectedReservation?->agreed_price ?? $selectedReservation?->priceNegotiation?->agreed_price;
        $defaultQty = old('items.0.quantity', $selectedReservation?->quantity ?? 1);
        if ($agreedPriceTotal && $agreedPriceTotal > 0) {
            $defaultUnitPrice = $defaultQty > 0 ? ($agreedPriceTotal / $defaultQty) : $agreedPriceTotal;
        } elseif ($selectedReservation?->product) {
            $defaultUnitPrice = $selectedReservation->product->base_price;
        } else {
            $defaultUnitPrice = 0;
        }
        $initialUnitPrice = old('items.0.unit_price', $defaultUnitPrice);
    @endphp

    @if($agreedPriceTotal)
    <div class="w-full glass rounded-2xl p-4 mb-4 bg-amber-500/10 border-amber-500/30 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                🤝 Transaksi Hasil Tawar Harga Disetujui
            </span>
            <p class="text-sm font-semibold text-white mt-1">Reservasi #{{ $selectedReservation->reservation_code }} ({{ $selectedReservation->user->name }})</p>
        </div>
        <div class="text-left sm:text-right">
            <p class="text-xs text-gray-400">Total Harga Kesepakatan</p>
            <p class="text-xl font-extrabold text-amber-400">Rp {{ number_format($agreedPriceTotal, 0, ',', '.') }}</p>
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.transactions.store') }}" id="trx-form" class="w-full">
        @csrf
        <div class="space-y-6">

            {{-- Identitas & Informasi Transaksi --}}
            <div class="glass rounded-2xl p-6 shadow-xl">
                <h3 class="font-semibold text-yellow-400 mb-4 flex items-center gap-2">
                    <span>📋</span> Informasi Transaksi
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="input-label">Pilih Reservasi Terkait (Otomatis Mengisi Form)</label>
                        <select name="reservation_id" id="reservation_select" class="input-field">
                            <option value="">-- Bebas (Tanpa Reservasi) --</option>
                            @foreach($reservations as $res)
                            <option value="{{ $res->id }}" {{ old('reservation_id', $selectedReservation?->id) == $res->id ? 'selected':'' }}>
                                {{ $res->reservation_code }} — {{ $res->user->name }} — {{ $res->product->name ?? ($res->pawn_gold_description ?? 'Gadai Emas') }}
                                @if($res->agreed_price || $res->priceNegotiation)
                                (Tawar Harga: Rp {{ number_format($res->agreed_price ?? $res->priceNegotiation->agreed_price, 0, ',', '.') }})
                                @endif
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="input-label">Pelanggan *</label>
                        <select name="user_id" class="input-field" required>
                            <option value="">-- Pilih Pelanggan --</option>
                            @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ old('user_id', $selectedReservation?->user_id) == $c->id ? 'selected':'' }}>
                                {{ $c->name }} ({{ $c->email }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="input-label">Tipe Transaksi *</label>
                        <select name="type" id="transaction_type" class="input-field" required>
                            @foreach(['purchase'=>'Pembelian Emas (Tunai)','buyback'=>'Jual Kembali (Buyback)','installment'=>'Cicilan','pawn'=>'Gadai'] as $val=>$lbl)
                            <option value="{{ $val }}" {{ old('type', $selectedReservation?->type ?? 'purchase') == $val ? 'selected':'' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="input-label">Harga Emas Referensi Hari Ini</label>
                        <select name="gold_price_id" class="input-field">
                            <option value="">-- Pilih Harga Referensi --</option>
                            @foreach($goldPrices as $gp)
                            <option value="{{ $gp->id }}" {{ $loop->first ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::parse($gp->price_date)->isoFormat('D MMM Y') }} — Jual: Rp {{ number_format($gp->sell_price_per_gram,0,',','.') }}/g
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="input-label">Metode Pembayaran *</label>
                        <select name="payment_method" id="transaction_payment_method" class="input-field" required>
                            @foreach(['cash'=>'Tunai (Cash)','transfer'=>'Transfer Bank','debit'=>'Kartu Debit','credit'=>'Kartu Kredit'] as $val=>$lbl)
                            <option value="{{ $val }}" {{ old('payment_method', $selectedReservation?->payment_method ?? 'cash')==$val ? 'selected':'' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="input-label">Tanggal Transaksi / Bayar *</label>
                        <input type="date" name="payment_date" value="{{ old('payment_date', today()->toDateString()) }}" class="input-field" required>
                    </div>

                    <div class="col-span-1 sm:col-span-2">
                        <label class="input-label">Catatan Transaksi</label>
                        <textarea name="notes" rows="2" class="input-field" placeholder="Opsional...">{{ old('notes', $selectedReservation?->notes) }}</textarea>
                    </div>

                    {{-- Installment Extra Fields --}}
                    <div id="installment-extra-fields" class="col-span-1 sm:col-span-2 grid grid-cols-2 gap-4" style="display: none;">
                        <div class="col-span-2">
                            <hr class="border-gray-800 my-2">
                            <h4 class="text-sm font-semibold text-blue-400">📅 Detail Rencana Cicilan</h4>
                        </div>
                        <div>
                            <label class="input-label">Tenor (Bulan) *</label>
                            <select name="installment_tenure" id="installment_tenure" class="input-field">
                                <option value="3" {{ old('installment_tenure', $selectedReservation?->installment_tenure) == 3 ? 'selected' : '' }}>3 Bulan</option>
                                <option value="6" {{ old('installment_tenure', $selectedReservation?->installment_tenure) == 6 ? 'selected' : '' }}>6 Bulan</option>
                                <option value="12" {{ old('installment_tenure', $selectedReservation?->installment_tenure ?? 12) == 12 ? 'selected' : '' }}>12 Bulan</option>
                            </select>
                        </div>
                        <div>
                            <label class="input-label">Uang Muka / Down Payment (Rp) *</label>
                            <input type="number" name="installment_down_payment" id="installment_down_payment" value="{{ old('installment_down_payment', $selectedReservation?->installment_down_payment ?? 0) }}" class="input-field" min="0">
                        </div>
                    </div>

                    {{-- Pawn Extra Fields --}}
                    <div id="pawn-extra-fields" class="col-span-1 sm:col-span-2 grid grid-cols-2 gap-4" style="display: none;">
                        <div class="col-span-2">
                            <hr class="border-gray-800 my-2">
                            <h4 class="text-sm font-semibold text-purple-400">🏦 Detail Pengajuan Gadai</h4>
                        </div>
                        <div class="col-span-2">
                            <label class="input-label">Deskripsi Emas *</label>
                            <input type="text" name="pawn_gold_description" id="pawn_gold_description" value="{{ old('pawn_gold_description', $selectedReservation?->pawn_gold_description) }}" placeholder="Contoh: Kalung Emas Rantai 10 Gram" class="input-field">
                        </div>
                        <div>
                            <label class="input-label">Kadar Emas *</label>
                            <select name="pawn_gold_purity" id="pawn_gold_purity" class="input-field">
                                <option value="24K" selected>24K</option>
                            </select>
                        </div>
                        <div>
                            <label class="input-label">Berat Emas (Gram) *</label>
                            <input type="number" step="0.001" name="pawn_weight_gram" id="pawn_weight_gram" value="{{ old('pawn_weight_gram', $selectedReservation?->pawn_weight_gram) }}" class="input-field" min="0.001">
                        </div>
                        <div>
                            <label class="input-label">Nilai Taksiran (Rp) *</label>
                            <input type="number" name="pawn_appraised_value" id="pawn_appraised_value" value="{{ old('pawn_appraised_value', $selectedReservation?->pawn_amount_requested) }}" class="input-field" min="0">
                        </div>
                        <div>
                            <label class="input-label">Jumlah Pinjaman (Rp) *</label>
                            <input type="number" name="pawn_loan_amount" id="pawn_loan_amount" value="{{ old('pawn_loan_amount', $selectedReservation?->pawn_amount_requested) }}" class="input-field" min="0">
                        </div>
                        <div>
                            <label class="input-label">Suku Bunga (% per bulan) *</label>
                            <input type="number" step="0.01" name="pawn_interest_rate" value="{{ old('pawn_interest_rate', 1.5) }}" class="input-field" min="0">
                        </div>
                        <div>
                            <label class="input-label">Tanggal Jatuh Tempo *</label>
                            <input type="date" name="pawn_due_date" value="{{ old('pawn_due_date', today()->addMonths(4)->toDateString()) }}" class="input-field">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Items Produk --}}
            <div class="glass rounded-2xl p-6 shadow-xl" id="items-section">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-yellow-400 flex items-center gap-2">
                        <span>📦</span> Item Produk Transaksi
                    </h3>
                    <button type="button" class="btn-ghost text-sm" id="add-item-btn">+ Tambah Item</button>
                </div>
                <div id="items-container" class="space-y-3">
                    <div class="item-row grid grid-cols-12 gap-3 items-end p-3 rounded-xl" style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06);">
                        <div class="col-span-5">
                            <label class="input-label">Produk *</label>
                            <select name="items[0][product_id]" class="input-field item-product" required onchange="fillPrice(this, 0)">
                                <option value="">-- Pilih Produk --</option>
                                @foreach($products as $p)
                                <option value="{{ $p->id }}"
                                        data-price="{{ $p->base_price }}"
                                        data-stock="{{ $p->stock }}"
                                        {{ old('items.0.product_id', $selectedReservation?->product_id) == $p->id ? 'selected' : '' }}>
                                    {{ $p->name }} ({{ $p->gold_purity }}, {{ number_format($p->weight_gram, 2) }}g) — Stok: {{ $p->stock }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="input-label">Qty *</label>
                            <input type="number" name="items[0][quantity]" value="{{ old('items.0.quantity', $selectedReservation?->quantity ?? 1) }}"
                                   class="input-field item-qty" min="1" required>
                        </div>
                        <div class="col-span-4">
                            <label class="input-label">Harga Satuan (Rp) *</label>
                            <input type="number" name="items[0][unit_price]" value="{{ $initialUnitPrice }}"
                                   class="input-field item-price" min="0" placeholder="0" required>
                        </div>
                        <div class="col-span-1 flex items-end pb-0.5">
                            <button type="button" class="btn-danger w-full remove-item-btn" style="display:none;">✕</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kalkulasi --}}
            <div class="glass rounded-2xl p-6 shadow-xl">
                <h3 class="font-semibold text-yellow-400 mb-4 flex items-center gap-2">
                    <span>🧮</span> Ringkasan Kalkulasi
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="input-label">Admin Fee (Rp)</label>
                        <input type="number" name="admin_fee" value="{{ old('admin_fee',0) }}" class="input-field" min="0">
                    </div>
                    <div>
                        <label class="input-label">Diskon (Rp)</label>
                        <input type="number" name="discount" value="{{ old('discount',0) }}" class="input-field" min="0">
                    </div>
                    <div class="glass rounded-xl p-4 flex flex-col justify-center bg-amber-500/10 border-amber-500/30">
                        <p class="text-xs text-gray-400">Total Akhir Transaksi</p>
                        <p class="text-2xl font-extrabold text-amber-400" id="total-display">Rp 0</p>
                    </div>
                </div>
            </div>

            <div class="flex gap-4 pt-2">
                <button type="submit" class="btn-orange flex-1 py-3 font-bold text-sm text-gray-950 shadow-lg">
                    💾 Simpan Transaksi & Selesaikan
                </button>
                <a href="{{ route('admin.transactions.index') }}" class="btn-ghost px-6 py-3">Batal</a>
            </div>
        </div>
    </form>
</div>

@php
    // Encode produk ke JSON untuk dipakai JS auto-fill harga
    $productsJson = $products->keyBy('id')->map(fn($p) => [
        'id'         => $p->id,
        'name'       => $p->name,
        'base_price' => (float) $p->base_price,
        'stock'      => $p->stock,
    ])->values();

    // Encode reservasi ke JSON untuk dipakai JS auto-fill tanpa page reload
    $reservationsJson = $reservations->keyBy('id')->map(fn($r) => [
        'id'             => $r->id,
        'user_id'        => $r->user_id,
        'type'           => $r->type,
        'payment_method' => $r->payment_method ?? 'cash',
        'product_id'     => $r->product_id,
        'quantity'       => $r->quantity,
        'agreed_price'   => $r->agreed_price ?? $r->priceNegotiation?->agreed_price,
        'unit_price'     => ($r->agreed_price ?? $r->priceNegotiation?->agreed_price)
                              ? (($r->agreed_price ?? $r->priceNegotiation?->agreed_price) / max(1, $r->quantity))
                              : ($r->product?->base_price ?? 0),
        'notes'          => $r->notes,
        'installment_tenure'       => $r->installment_tenure,
        'installment_down_payment' => $r->installment_down_payment,
        'pawn_gold_description'    => $r->pawn_gold_description,
        'pawn_gold_purity'         => $r->pawn_gold_purity,
        'pawn_weight_gram'         => $r->pawn_weight_gram,
        'pawn_amount_requested'    => $r->pawn_amount_requested,
    ])->values();
@endphp

<script>
window.PRODUCTS_DATA = @json($productsJson);
window.RESERVATIONS_DATA = @json($reservationsJson);
</script>
<x-slot name="scripts">
    @vite('resources/js/admin/transactions-create.js')
</x-slot>
</x-admin-app>
