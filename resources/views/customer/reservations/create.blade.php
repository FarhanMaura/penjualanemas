<x-customer-app>
    <x-slot name="pageTitle">Buat Reservasi Baru</x-slot>
    <x-slot name="breadcrumb">Daftarkan minat transaksi Anda</x-slot>

    <div class="max-w-3xl mx-auto flex flex-col justify-center items-center w-full">
        <div class="glass rounded-3xl p-6 sm:p-8 w-full shadow-2xl" style="border-color:rgba(245,158,11,0.2);">
            
            <div class="flex items-center gap-4 mb-6 pb-4 border-b border-white/10">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl shadow-lg" style="background:linear-gradient(135deg,#f59e0b,#d97706);">
                    📅
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white font-playfair">Formulir Reservasi</h2>
                    <p class="text-sm text-gray-400">Pilih produk dan jadwal kunjungan Anda ke toko</p>
                </div>
            </div>

            {{-- Banner Khusus jika Reservasi dari Tawar Harga Disetujui --}}
            @if(isset($negotiation) && $negotiation)
            <div class="glass rounded-2xl p-5 mb-6 bg-gradient-to-r from-amber-500/20 via-yellow-500/10 to-amber-500/20 border-2 border-amber-500/40 shadow-xl">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500 text-gray-950 shadow">
                            🤝 Harga Tawar Disetujui Admin (ACC)
                        </span>
                        <h4 class="font-bold text-white text-lg mt-2">{{ $negotiation->product->name }}</h4>
                        <p class="text-xs text-gray-300 mt-0.5">Kode Tawar: <span class="font-mono text-amber-400 font-bold">{{ $negotiation->negotiation_code }}</span></p>
                        <p class="text-xs text-gray-400 mt-1">Harga Normal: <span class="line-through text-gray-500">Rp {{ number_format($negotiation->original_price, 0, ',', '.') }}</span></p>
                    </div>
                    <div class="text-left sm:text-right bg-black/40 px-4 py-3 rounded-xl border border-amber-500/30">
                        <p class="text-xs text-amber-300 font-semibold">Harga Kesepakatan (Disetujui)</p>
                        <p class="text-2xl font-extrabold text-amber-400">Rp {{ number_format($negotiation->agreed_price, 0, ',', '.') }}</p>
                        <span class="text-[11px] text-green-400 font-semibold">✔ Harga ini yang berlaku untuk transaksi Anda</span>
                    </div>
                </div>
            </div>
            @endif

            @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl" style="background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.3);">
                <p class="text-sm text-red-400 font-semibold mb-2">Mohon periksa kembali form Anda:</p>
                <ul class="list-disc list-inside text-xs text-red-400 space-y-1">
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
                        <label class="block text-sm font-medium text-gray-300 mb-2">Tipe Pengajuan / Reservasi *</label>
                        <select name="type" id="reservation_type" required class="w-full rounded-xl px-4 py-3 text-sm text-white outline-none focus:ring-2"
                                style="background:rgba(255,255,255,0.05); border:1px solid rgba(245,158,11,0.2);">
                            <option value="purchase" {{ old('type', 'purchase') == 'purchase' ? 'selected' : '' }} class="text-gray-900">Pembelian Emas (Tunai)</option>
                            <option value="installment" {{ old('type') == 'installment' ? 'selected' : '' }} class="text-gray-900">Pembelian Emas (Cicilan)</option>
                            <option value="pawn" {{ old('type') == 'pawn' ? 'selected' : '' }} class="text-gray-900">Gadai Emas (Pengajuan Pinjaman)</option>
                        </select>
                    </div>

                    {{-- Product Selection & Qty --}}
                    <div id="product_fields" class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Pilih Produk Emas *</label>
                            <select {{ isset($negotiation) && $negotiation ? 'disabled' : 'name=product_id' }} id="product_id" class="w-full rounded-xl px-4 py-3 text-sm text-white outline-none focus:ring-2"
                                    style="background:rgba(255,255,255,0.05); border:1px solid rgba(245,158,11,0.2);">
                                <option value="" class="text-gray-900">-- Pilih Produk Emas --</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}" {{ (old('product_id') ?? ($product->id ?? '')) == $p->id ? 'selected' : '' }} class="text-gray-900">
                                        {{ $p->name }} ({{ number_format($p->weight_gram, 3) }} gram) - Stok: {{ $p->stock }}
                                    </option>
                                @endforeach
                            </select>
                            @if(isset($negotiation) && $negotiation)
                            <p class="text-xs text-amber-400 mt-1">🔒 Produk telah terkunci sesuai hasil penawaran tawar harga.</p>
                            @endif
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Jumlah Pembelian (Qty) *</label>
                            <input type="number" name="quantity" min="1" max="100" value="{{ old('quantity', $negotiation->quantity ?? 1) }}"
                                   class="w-full rounded-xl px-4 py-3 text-sm text-white outline-none focus:ring-2"
                                   style="background:rgba(255,255,255,0.05); border:1px solid rgba(245,158,11,0.2);">
                        </div>
                    </div>

                    {{-- Metode Pembayaran --}}
                    <div id="payment_fields" class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Pilih Metode Pembayaran *</label>
                            <select name="payment_method" id="payment_method" class="w-full rounded-xl px-4 py-3 text-sm text-white outline-none focus:ring-2"
                                    style="background:rgba(255,255,255,0.05); border:1px solid rgba(245,158,11,0.2);">
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }} class="text-gray-900">Tunai (Cash)</option>
                                <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }} class="text-gray-900">Transfer Bank</option>
                                <option value="debit" {{ old('payment_method') == 'debit' ? 'selected' : '' }} class="text-gray-900">Kartu Debit</option>
                                <option value="credit" {{ old('payment_method') == 'credit' ? 'selected' : '' }} class="text-gray-900">Kartu Kredit</option>
                            </select>
                        </div>
                    </div>

                    {{-- Installment Fields --}}
                    <div id="installment_fields" class="space-y-5" style="display: none;">
                        <div class="grid grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Tenor Cicilan</label>
                                <select name="installment_tenure" class="w-full rounded-xl px-4 py-3 text-sm text-white outline-none focus:ring-2"
                                        style="background:rgba(255,255,255,0.05); border:1px solid rgba(245,158,11,0.2);">
                                    <option value="3" {{ old('installment_tenure') == 3 ? 'selected' : '' }} class="text-gray-900">3 Bulan</option>
                                    <option value="6" {{ old('installment_tenure') == 6 ? 'selected' : '' }} class="text-gray-900">6 Bulan</option>
                                    <option value="12" {{ old('installment_tenure', 12) == 12 ? 'selected' : '' }} class="text-gray-900">12 Bulan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Uang Muka / DP (Rp)</label>
                                <input type="number" name="installment_down_payment" value="{{ old('installment_down_payment', 0) }}" min="0"
                                       class="w-full rounded-xl px-4 py-3 text-sm text-white outline-none focus:ring-2"
                                       style="background:rgba(255,255,255,0.05); border:1px solid rgba(245,158,11,0.2);">
                            </div>
                        </div>
                    </div>

                    {{-- Pawn Fields --}}
                    <div id="pawn_fields" class="space-y-5" style="display: none;">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Deskripsi Emas yang Ingin Digadai</label>
                            <input type="text" name="pawn_gold_description" value="{{ old('pawn_gold_description') }}" placeholder="Contoh: Kalung Emas Rantai 10 Gram"
                                   class="w-full rounded-xl px-4 py-3 text-sm text-white outline-none focus:ring-2"
                                   style="background:rgba(255,255,255,0.05); border:1px solid rgba(245,158,11,0.2);">
                        </div>
                        <div class="grid grid-cols-3 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Kadar Emas</label>
                                <select name="pawn_gold_purity" class="w-full rounded-xl px-4 py-3 text-sm text-white outline-none focus:ring-2"
                                        style="background:rgba(255,255,255,0.05); border:1px solid rgba(245,158,11,0.2);">
                                    <option value="24K" selected class="text-gray-900">24K</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Berat Emas (g)</label>
                                <input type="number" step="0.01" name="pawn_weight_gram" value="{{ old('pawn_weight_gram') }}" min="0.01" placeholder="0.00"
                                       class="w-full rounded-xl px-4 py-3 text-sm text-white outline-none focus:ring-2"
                                       style="background:rgba(255,255,255,0.05); border:1px solid rgba(245,158,11,0.2);">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Pinjaman (Rp)</label>
                                <input type="number" name="pawn_amount_requested" value="{{ old('pawn_amount_requested') }}" min="1000" placeholder="0"
                                       class="w-full rounded-xl px-4 py-3 text-sm text-white outline-none focus:ring-2"
                                       style="background:rgba(255,255,255,0.05); border:1px solid rgba(245,158,11,0.2);">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-5">
                        {{-- Tanggal Kunjungan --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Rencana Tanggal Kunjungan *</label>
                            <input type="date" name="preferred_date" min="{{ date('Y-m-d') }}" value="{{ old('preferred_date', date('Y-m-d', strtotime('+1 day'))) }}" required
                                   class="w-full rounded-xl px-4 py-3 text-sm text-white outline-none focus:ring-2"
                                   style="background:rgba(255,255,255,0.05); border:1px solid rgba(245,158,11,0.2); color-scheme: dark;">
                        </div>

                        {{-- Jam Kunjungan --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Perkiraan Jam (08:00 - 17:00) *</label>
                            <input type="time" name="preferred_time" value="{{ old('preferred_time', '10:00') }}" required
                                   class="w-full rounded-xl px-4 py-3 text-sm text-white outline-none focus:ring-2"
                                   style="background:rgba(255,255,255,0.05); border:1px solid rgba(245,158,11,0.2); color-scheme: dark;">
                        </div>
                    </div>

                    {{-- Catatan --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Catatan Tambahan (Opsional)</label>
                        <textarea name="notes" rows="3" placeholder="Tuliskan catatan tambahan jika ada..."
                                  class="w-full rounded-xl px-4 py-3 text-sm text-white outline-none focus:ring-2"
                                  style="background:rgba(255,255,255,0.05); border:1px solid rgba(245,158,11,0.2);">{{ old('notes', isset($negotiation) ? 'Reservasi dari pengajuan tawar harga ' . $negotiation->negotiation_code . ' (Harga Disetujui: Rp ' . number_format($negotiation->agreed_price, 0, ',', '.') . ')' : '') }}</textarea>
                    </div>
                </div>

                <div class="mt-8 flex gap-4">
                    <button type="submit" class="flex-1 py-3 rounded-xl font-bold text-gray-950 transition hover:brightness-110 shadow-lg"
                            style="background:linear-gradient(135deg,#f59e0b,#d97706);">
                        Kirim Pengajuan Reservasi
                    </button>
                    <a href="{{ route('customer.reservations.index') }}" class="px-6 py-3 rounded-xl font-bold text-gray-300 glass hover:bg-white/10 transition text-center">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <x-slot name="scripts">
        @vite('resources/js/customer/reservations.js')
    </x-slot>
</x-customer-app>
