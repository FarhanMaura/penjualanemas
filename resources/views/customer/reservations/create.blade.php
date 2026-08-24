<x-customer-app>
    <x-slot name="pageTitle">Buat Reservasi Baru</x-slot>
    <x-slot name="breadcrumb">Daftarkan minat transaksi Anda</x-slot>

    <div class="max-w-3xl mx-auto flex flex-col justify-center items-center w-full">
        <div class="glass rounded-3xl p-6 sm:p-8 w-full bg-white border border-[#e8e3d5] shadow-lg">
            
            <div class="flex items-center gap-4 mb-6 pb-4 border-b border-slate-200">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl shadow-md gold-gradient border border-[#C6A443]">
                    📅
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-900 font-playfair">Formulir Reservasi Toko</h2>
                    <p class="text-sm text-slate-600 font-medium">Pilih jenis transaksi dan jadwal kunjungan Anda ke Toko Sinar Baru II</p>
                </div>
            </div>

            {{-- Banner Khusus jika Reservasi dari Tawar Harga Disetujui --}}
            @if(isset($negotiation) && $negotiation)
            <div class="rounded-2xl p-5 mb-6 bg-amber-50/80 border-2 border-amber-300 shadow-md">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500 text-slate-950 shadow-sm">
                            🤝 Harga Tawar Disetujui Admin (ACC)
                        </span>
                        <h4 class="font-bold text-slate-900 text-lg mt-2">{{ $negotiation->product->name }}</h4>
                        <p class="text-xs text-slate-600 mt-0.5">Kode Tawar: <span class="font-mono text-[#085C54] font-bold">{{ $negotiation->negotiation_code }}</span></p>
                        <p class="text-xs text-slate-500 mt-1">Harga Normal: <span class="line-through text-slate-400">Rp {{ number_format($negotiation->original_price, 0, ',', '.') }}</span></p>
                    </div>
                    <div class="text-left sm:text-right bg-white px-4 py-3 rounded-xl border border-amber-200 shadow-sm">
                        <p class="text-xs text-amber-900 font-bold uppercase tracking-wider">Harga Kesepakatan</p>
                        <p class="text-2xl font-extrabold text-[#C6A443]">Rp {{ number_format($negotiation->agreed_price, 0, ',', '.') }}</p>
                        <span class="text-[11px] text-emerald-700 font-bold">✔ Harga ini yang berlaku untuk transaksi Anda</span>
                    </div>
                </div>
            </div>
            @endif

            @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-300 shadow-sm">
                <p class="text-sm text-red-900 font-bold mb-2">Mohon periksa kembali form Anda:</p>
                <ul class="list-disc list-inside text-xs text-red-800 font-medium space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('customer.reservations.store') }}" method="POST">
                @csrf
                
                @if(isset($negotiation) && $negotiation)
                <input type="hidden" name="price_negotiation_id" value="{{ $negotiation->id }}">
                <input type="hidden" name="agreed_price" value="{{ $negotiation->agreed_price }}">
                <input type="hidden" name="product_id" value="{{ $negotiation->product_id }}">
                @endif

                <div class="space-y-5">
                    {{-- Tipe Reservasi --}}
                    <div>
                        <label class="input-label">Tipe Pengajuan / Reservasi *</label>
                        <select name="type" id="reservation_type" required class="input-field cursor-pointer">
                            <option value="purchase" {{ old('type', request('type') == 'beli' ? 'purchase' : 'purchase') == 'purchase' ? 'selected' : '' }}>🛒 Pembelian Emas (Tunai / Transfer)</option>
                            <option value="buyback" {{ old('type', request('type') == 'jual' ? 'buyback' : '') == 'buyback' ? 'selected' : '' }}>💰 Jual Emas ke Toko (Buyback)</option>
                            <option value="installment" {{ old('type', request('type') == 'cicilan' ? 'installment' : '') == 'installment' ? 'selected' : '' }}>📅 Pembelian Emas (Cicilan)</option>
                            <option value="pawn" {{ old('type', request('type') == 'gadai' ? 'pawn' : '') == 'pawn' ? 'selected' : '' }}>🏦 Gadai Emas (Pengajuan Pinjaman)</option>
                        </select>
                    </div>

                    {{-- Product Selection & Qty (untuk Beli & Cicilan) --}}
                    <div id="product_fields" class="space-y-5">
                        <div>
                            <label class="input-label">Pilih Produk Emas <span class="text-red-600">*</span></label>
                            <select name="product_id" id="product_id" {{ isset($negotiation) && $negotiation ? 'disabled' : '' }} required class="input-field cursor-pointer font-bold {{ $errors->has('product_id') ? 'border-red-500 ring-2 ring-red-200 bg-red-50/50' : '' }}">
                                <option value="" disabled {{ old('product_id', $product->id ?? '') ? '' : 'selected' }}>-- Klik disini untuk memilih perhiasan emas --</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}" {{ (old('product_id') ?? ($product->id ?? '')) == $p->id ? 'selected' : '' }}>
                                        💍 {{ $p->name }} ({{ number_format($p->weight_gram, 3) }} gram) - Rp {{ number_format($p->base_price, 0, ',', '.') }} (Stok: {{ $p->stock }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-slate-500 font-medium mt-1">💡 Klik menu di atas untuk memilih item perhiasan dari katalog toko.</p>
                            @error('product_id')
                            <p class="text-xs text-red-600 font-bold mt-1.5 flex items-center gap-1">
                                <span>⚠️</span> {{ $message }}
                            </p>
                            @enderror
                            @if(isset($negotiation) && $negotiation)
                            <p class="text-xs text-amber-700 font-semibold mt-1">🔒 Produk telah terkunci sesuai hasil penawaran tawar harga.</p>
                            @endif
                        </div>

                        <div>
                            <label class="input-label">Jumlah Pembelian (Qty) *</label>
                            <input type="number" name="quantity" min="1" max="100" value="{{ old('quantity', $negotiation->quantity ?? 1) }}"
                                   class="input-field">
                        </div>
                    </div>

                    {{-- Jual Emas (Buyback) Fields --}}
                    <div id="buyback_fields" class="space-y-5 p-5 rounded-2xl bg-emerald-50/70 border border-emerald-200" style="display: none;">
                        <div class="flex items-center gap-2 pb-2 border-b border-emerald-200">
                            <span class="text-xl">💰</span>
                            <h4 class="text-sm font-bold text-emerald-950">Rincian Emas yang Ingin Anda Jual ke Toko</h4>
                        </div>
                        <div>
                            <label class="input-label">Deskripsi / Jenis Perhiasan Emas *</label>
                            <input type="text" name="pawn_gold_description" id="buyback_gold_description" value="{{ old('pawn_gold_description') }}"
                                   placeholder="Contoh: Kalung Emas Model Medan 1 Suku (6.7 gram)" class="input-field">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="input-label">Kadar Emas *</label>
                                <select name="pawn_gold_purity" id="buyback_gold_purity" class="input-field">
                                    <option value="24K" selected>24 Karat (999 Murni)</option>
                                    <option value="22K">22 Karat (916)</option>
                                    <option value="18K">18 Karat (750)</option>
                                    <option value="16K">16 Karat (700)</option>
                                </select>
                            </div>
                            <div>
                                <label class="input-label">Perkiraan Berat Emas (Gram) *</label>
                                <input type="number" step="0.001" name="pawn_weight_gram" id="buyback_weight_gram" value="{{ old('pawn_weight_gram') }}"
                                       min="0.001" placeholder="cth: 6.700" class="input-field" oninput="calculateBuybackEstimate(this.value)">
                            </div>
                        </div>
                        <div class="p-4 rounded-xl bg-white border border-emerald-300 shadow-sm flex justify-between items-center">
                            <div>
                                <p class="text-xs text-slate-500 font-bold uppercase">Estimasi Penerimaan Dana</p>
                                <p class="text-xs text-emerald-800 font-medium mt-0.5">Berdasarkan harga beli emas hari ini (Rp {{ number_format($todayGoldPrice->buy_price_per_gram ?? 1580000, 0, ',', '.') }}/g)</p>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-extrabold text-[#085C54]" id="buyback-estimate-display">Rp 0</p>
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 italic">* Nilai final akan ditimbang & diuji kadar secara transparan langsung di toko saat kunjungan.</p>
                    </div>

                    {{-- Metode Pembayaran --}}
                    <div id="payment_fields" class="space-y-5">
                        <div>
                            <label class="input-label" id="payment_method_label">Pilih Metode Pembayaran *</label>
                            <select name="payment_method" id="payment_method" class="input-field cursor-pointer">
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Tunai (Cash di Toko)</option>
                                <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                <option value="debit" {{ old('payment_method') == 'debit' ? 'selected' : '' }}>Kartu Debit</option>
                                <option value="credit" {{ old('payment_method') == 'credit' ? 'selected' : '' }}>Kartu Kredit</option>
                            </select>
                        </div>
                    </div>

                    {{-- Installment Fields --}}
                    <div id="installment_fields" class="space-y-5 p-5 rounded-2xl bg-blue-50/70 border border-blue-200" style="display: none;">
                        <div class="flex items-center gap-2 pb-2 border-b border-blue-200">
                            <span class="text-xl">📅</span>
                            <h4 class="text-sm font-bold text-blue-950">Rencana Cicilan Emas</h4>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="input-label">Tenor Cicilan</label>
                                <select name="installment_tenure" class="input-field cursor-pointer">
                                    <option value="3" {{ old('installment_tenure') == 3 ? 'selected' : '' }}>3 Bulan</option>
                                    <option value="6" {{ old('installment_tenure') == 6 ? 'selected' : '' }}>6 Bulan</option>
                                    <option value="12" {{ old('installment_tenure', 12) == 12 ? 'selected' : '' }}>12 Bulan</option>
                                </select>
                            </div>
                            <div>
                                <label class="input-label">Uang Muka / DP (Rp)</label>
                                <input type="number" name="installment_down_payment" value="{{ old('installment_down_payment', 0) }}" min="0"
                                       class="input-field">
                            </div>
                        </div>
                    </div>

                    {{-- Pawn Fields --}}
                    <div id="pawn_fields" class="space-y-5 p-5 rounded-2xl bg-amber-50/70 border border-amber-200" style="display: none;">
                        <div class="flex items-center gap-2 pb-2 border-b border-amber-200">
                            <span class="text-xl">🏦</span>
                            <h4 class="text-sm font-bold text-amber-950">Rincian Emas yang Ingin Digadai</h4>
                        </div>
                        <div>
                            <label class="input-label">Deskripsi Emas yang Ingin Digadai *</label>
                            <input type="text" name="pawn_gold_description" id="pawn_gold_description" value="{{ old('pawn_gold_description') }}"
                                   placeholder="Contoh: Kalung Emas Rantai 10 Gram" class="input-field">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="input-label">Kadar Emas *</label>
                                <select name="pawn_gold_purity" id="pawn_gold_purity" class="input-field">
                                    <option value="24K" selected>24 Karat (Murni)</option>
                                    <option value="22K">22 Karat</option>
                                    <option value="18K">18 Karat</option>
                                </select>
                            </div>
                            <div>
                                <label class="input-label">Berat Emas (Gram) *</label>
                                <input type="number" step="0.01" name="pawn_weight_gram" id="pawn_weight_gram" value="{{ old('pawn_weight_gram') }}" min="0.01" placeholder="0.00"
                                       class="input-field">
                            </div>
                            <div>
                                <label class="input-label">Pengajuan Pinjaman (Rp) *</label>
                                <input type="number" name="pawn_amount_requested" value="{{ old('pawn_amount_requested') }}" min="1000" placeholder="cth: 5000000"
                                       class="input-field">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Tanggal Kunjungan --}}
                        <div>
                            <label class="input-label">Rencana Tanggal Kunjungan *</label>
                            <input type="date" name="preferred_date" min="{{ date('Y-m-d') }}" value="{{ old('preferred_date', date('Y-m-d', strtotime('+1 day'))) }}" required
                                   class="input-field">
                        </div>

                        {{-- Jam Kunjungan --}}
                        <div>
                            <label class="input-label">Perkiraan Jam (08:00 - 17:00) *</label>
                            <input type="time" name="preferred_time" value="{{ old('preferred_time', '10:00') }}" required
                                   class="input-field">
                        </div>
                    </div>

                    {{-- Catatan --}}
                    <div>
                        <label class="input-label">Catatan Tambahan (Opsional)</label>
                        <textarea name="notes" rows="3" placeholder="Tuliskan catatan tambahan jika ada..."
                                  class="input-field">{{ old('notes', isset($negotiation) ? 'Reservasi dari pengajuan tawar harga ' . $negotiation->negotiation_code . ' (Harga Disetujui: Rp ' . number_format($negotiation->agreed_price, 0, ',', '.') . ')' : '') }}</textarea>
                    </div>
                </div>

                <div class="mt-8 flex gap-4">
                    <button type="submit" class="flex-1 py-3.5 rounded-xl font-extrabold text-[#042623] gold-gradient border border-[#C6A443] shadow-md hover:brightness-110 transition text-sm">
                        Kirim Pengajuan Reservasi →
                    </button>
                    <a href="{{ route('customer.reservations.index') }}" class="px-6 py-3.5 rounded-xl font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition text-center text-sm shadow-sm">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        const BUY_PRICE_PER_GRAM = {{ $todayGoldPrice->buy_price_per_gram ?? 1580000 }};
        function calculateBuybackEstimate(weight) {
            const w = parseFloat(weight) || 0;
            const total = Math.round(w * BUY_PRICE_PER_GRAM);
            const formatted = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(total);
            document.getElementById('buyback-estimate-display').textContent = formatted.replace('IDR', 'Rp');
        }
    </script>

    <x-slot name="scripts">
        @vite('resources/js/customer/reservations.js')
    </x-slot>
</x-customer-app>
