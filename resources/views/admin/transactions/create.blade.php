<x-admin-app>
<x-slot name="pageTitle">Catat Transaksi Baru</x-slot>

@if(session('error'))
<div class="flash-error" data-flash>❌ {{ session('error') }}</div>
@endif
@if($errors->any())
<div class="flash-error" data-flash>
    <ul class="list-disc list-inside text-sm space-y-1">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

<div class="max-w-4xl">
<form method="POST" action="{{ route('admin.transactions.store') }}" id="trx-form">
@csrf
<div class="space-y-4">

    {{-- Identitas --}}
    <div class="glass rounded-2xl p-6">
        <h3 class="font-semibold text-yellow-400 mb-4">📋 Informasi Transaksi</h3>
        <div class="grid grid-cols-2 gap-4">
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
                    @foreach(['purchase'=>'Pembelian Emas','buyback'=>'Jual Kembali (Buyback)','installment'=>'Cicilan','pawn'=>'Gadai'] as $val=>$lbl)
                    <option value="{{ $val }}" {{ old('type', $selectedReservation?->type ?? 'purchase') == $val ? 'selected':'' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="input-label">Harga Emas Referensi</label>
                <select name="gold_price_id" class="input-field">
                    <option value="">-- Pilih Harga --</option>
                    @foreach($goldPrices as $gp)
                    <option value="{{ $gp->id }}">{{ \Carbon\Carbon::parse($gp->price_date)->isoFormat('D MMM Y') }} — Jual: Rp {{ number_format($gp->sell_price_per_gram,0,',','.') }}/g</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="input-label">Reservasi Terkait</label>
                <select name="reservation_id" class="input-field">
                    <option value="">-- Tidak Ada --</option>
                    @foreach($reservations as $res)
                    <option value="{{ $res->id }}" {{ old('reservation_id', $selectedReservation?->id) == $res->id ? 'selected':'' }}>
                        {{ $res->reservation_code }} — {{ $res->user->name }} — {{ $res->product->name ?? ($res->pawn_gold_description ?? 'Gadai Emas') }}
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
                <label class="input-label">Tanggal Bayar *</label>
                <input type="date" name="payment_date" value="{{ old('payment_date', today()->toDateString()) }}" class="input-field" required>
            </div>
            <div class="col-span-2">
                <label class="input-label">Catatan</label>
                <textarea name="notes" rows="2" class="input-field" placeholder="Opsional...">{{ old('notes') }}</textarea>
            </div>

            {{-- Installment Extra Fields --}}
            <div id="installment-extra-fields" class="col-span-2 grid grid-cols-2 gap-4" style="display: none;">
                <div class="col-span-2">
                    <hr class="border-gray-800 my-2">
                    <h4 class="text-sm font-semibold text-blue-400">📅 Detail Cicilan</h4>
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
            <div id="pawn-extra-fields" class="col-span-2 grid grid-cols-2 gap-4" style="display: none;">
                <div class="col-span-2">
                    <hr class="border-gray-800 my-2">
                    <h4 class="text-sm font-semibold text-purple-400">🏦 Detail Gadai</h4>
                </div>
                <div class="col-span-2">
                    <label class="input-label">Deskripsi Emas *</label>
                    <input type="text" name="pawn_gold_description" id="pawn_gold_description" value="{{ old('pawn_gold_description', $selectedReservation?->pawn_gold_description) }}" placeholder="Contoh: Kalung Emas Rantai 10 Gram" class="input-field">
                </div>
                <div>
                    <label class="input-label">Kadar Emas *</label>
                    <select name="pawn_gold_purity" id="pawn_gold_purity" class="input-field">
                        @foreach(['24K','22K','18K','14K','9K'] as $pur)
                        <option value="{{ $pur }}" {{ old('pawn_gold_purity', $selectedReservation?->pawn_gold_purity) == $pur ? 'selected' : '' }}>{{ $pur }}</option>
                        @endforeach
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
    <div class="glass rounded-2xl p-6" id="items-section">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-yellow-400">📦 Item Produk</h3>
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
                                {{ old('items.0.product_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->name }} ({{ $p->gold_purity }}, {{ number_format($p->weight_gram, 2) }}g) — Stok: {{ $p->stock }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="input-label">Qty</label>
                    <input type="number" name="items[0][quantity]" value="{{ old('items.0.quantity', 1) }}"
                           class="input-field item-qty" min="1" required>
                </div>
                <div class="col-span-4">
                    <label class="input-label">Harga Satuan (Rp)</label>
                    <input type="number" name="items[0][unit_price]" value="{{ old('items.0.unit_price', 0) }}"
                           class="input-field item-price" min="0" placeholder="0" required>
                </div>
                <div class="col-span-1 flex items-end pb-0.5">
                    <button type="button" class="btn-danger w-full remove-item-btn" style="display:none;">✕</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Kalkulasi --}}
    <div class="glass rounded-2xl p-6">
        <h3 class="font-semibold text-yellow-400 mb-4">🧮 Kalkulasi</h3>
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="input-label">Admin Fee (Rp)</label>
                <input type="number" name="admin_fee" value="{{ old('admin_fee',0) }}" class="input-field" min="0">
            </div>
            <div>
                <label class="input-label">Diskon (Rp)</label>
                <input type="number" name="discount" value="{{ old('discount',0) }}" class="input-field" min="0">
            </div>
            <div class="glass rounded-xl p-4 flex flex-col justify-center">
                <p class="text-xs text-gray-400">Total Transaksi</p>
                <p class="text-xl font-bold text-yellow-400" id="total-display">Rp 0</p>
            </div>
        </div>
    </div>

    <div class="flex gap-3">
        <button type="submit" class="btn-orange">Simpan Transaksi</button>
        <a href="{{ route('admin.transactions.index') }}" class="btn-ghost">Batal</a>
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
@endphp

<script>
window.PRODUCTS_DATA = @json($productsJson);
</script>
@vite('resources/js/admin/transactions-create.js')
</x-admin-app>
