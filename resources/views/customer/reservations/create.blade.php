<x-customer-app>
    <x-slot name="pageTitle">Buat Reservasi Baru</x-slot>
    <x-slot name="breadcrumb">Daftarkan minat transaksi Anda</x-slot>

    @php
        $productsMap = $products->keyBy('id')->map(fn($p) => [
            'id'          => $p->id,
            'name'        => $p->name,
            'weight_gram' => (float) $p->weight_gram,
            'gold_purity' => $p->gold_purity ?: '24K',
            'base_price'  => (float) $p->base_price,
            'stock'       => (int) $p->stock,
            'image'       => $p->thumbnail_url ?: '/images/products/cincin.png',
            'category'    => $p->category?->name ?? 'Perhiasan Emas',
        ]);

        $paymentMethodsMap = (isset($paymentMethods) ? $paymentMethods : collect())->keyBy('code')->map(fn($pm) => [
            'code'           => $pm->code,
            'name'           => $pm->name,
            'type'           => $pm->type,
            'bank_name'      => $pm->bank_name,
            'account_number' => $pm->account_number,
            'account_name'   => $pm->account_name,
            'instructions'   => $pm->instructions,
        ]);

        $selectedProdId = old('product_id', $product->id ?? request('product_id', ''));
    @endphp

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

            <form action="{{ route('customer.reservations.store') }}" method="POST" onsubmit="if(this.dataset.submitted) return false; this.dataset.submitted = true;">
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
                        <select name="type" id="reservation_type" required class="input-field cursor-pointer font-bold">
                            <option value="purchase" {{ old('type', request('type') == 'beli' ? 'purchase' : 'purchase') == 'purchase' ? 'selected' : '' }}>🛒 Pembelian Emas (Beli Lunas / Pembayaran Langsung)</option>
                            <option value="installment" {{ old('type', request('type') == 'cicilan' ? 'installment' : '') == 'installment' ? 'selected' : '' }}>📅 Pembelian Emas (Cicilan)</option>
                            <option value="buyback" {{ old('type', request('type') == 'jual' ? 'buyback' : '') == 'buyback' ? 'selected' : '' }}>💰 Jual Emas ke Toko (Buyback)</option>
                            <option value="pawn" {{ old('type', request('type') == 'gadai' ? 'pawn' : '') == 'pawn' ? 'selected' : '' }}>🏦 Gadai Emas (Pengajuan Pinjaman)</option>
                        </select>
                    </div>

                    {{-- Product Selection & Qty (untuk Beli & Cicilan) --}}
                    <div id="product_fields" class="space-y-5">
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="input-label mb-0">Pilih Produk Emas <span class="text-red-600">*</span></label>
                                <a href="{{ route('customer.catalog.index') }}" class="text-xs font-bold text-[#085C54] hover:underline flex items-center gap-1">
                                    🔍 Buka Katalog Lengkap
                                </a>
                            </div>
                            <select name="product_id" id="product_id" {{ isset($negotiation) && $negotiation ? 'disabled' : '' }} required class="input-field cursor-pointer font-bold {{ $errors->has('product_id') ? 'border-red-500 ring-2 ring-red-200 bg-red-50/50' : '' }}" onchange="updateSelectedProductCard(this.value)">
                                <option value="" disabled {{ empty($selectedProdId) ? 'selected' : '' }}>-- Klik disini untuk memilih perhiasan emas --</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}" {{ $selectedProdId == $p->id ? 'selected' : '' }}>
                                        💍 {{ $p->name }} ({{ number_format($p->weight_gram, 3) }} gram) - Rp {{ number_format($p->base_price, 0, ',', '.') }} (Stok: {{ $p->stock }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-slate-500 font-medium mt-1">💡 Pilih produk perhiasan di atas untuk melihat foto asli & kalkulasi harga.</p>
                            @error('product_id')
                            <p class="text-xs text-red-600 font-bold mt-1.5 flex items-center gap-1">
                                <span>⚠️</span> {{ $message }}
                            </p>
                            @enderror
                            @if(isset($negotiation) && $negotiation)
                            <p class="text-xs text-amber-700 font-semibold mt-1">🔒 Produk telah terkunci sesuai hasil penawaran tawar harga.</p>
                            @endif
                        </div>

                        {{-- Card Tampilan Produk Terpilih (Revisi Poin 5 & 6) --}}
                        <div id="selected_product_card" class="rounded-2xl p-4 sm:p-5 bg-gradient-to-br from-[#F4EDD9]/60 via-amber-50/50 to-white border-2 border-[#C6A443]/60 shadow-md transition-all" style="display: none;">
                            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4">
                                {{-- Foto Produk HD dengan Lightbox Modal --}}
                                <div class="relative group cursor-pointer shrink-0" onclick="openImageLightbox()" title="Klik untuk perbesar gambar">
                                    <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-2xl overflow-hidden border-2 border-amber-300 shadow-md bg-white flex items-center justify-center relative">
                                        <img id="preview_product_img" src="" alt="Produk Emas" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" style="image-rendering: -webkit-optimize-contrast;">
                                        <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                            <span class="bg-white/90 text-slate-900 text-[11px] font-bold px-2.5 py-1 rounded-full shadow-md">🔍 Perbesar</span>
                                        </div>
                                    </div>
                                    <span class="absolute bottom-1 right-1 bg-[#042623]/80 text-[#E3D193] text-[10px] font-bold px-2 py-0.5 rounded-md backdrop-blur-sm">
                                        HD 24K
                                    </span>
                                </div>

                                <div class="flex-1 text-center sm:text-left space-y-1.5 w-full">
                                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                                        <span id="preview_product_purity" class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-amber-500 text-slate-950 shadow-sm">24K Murni</span>
                                        <span id="preview_product_category" class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white text-slate-700 border border-slate-200">Perhiasan</span>
                                        <span id="preview_product_stock" class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">Stok Tersedia</span>
                                    </div>
                                    <h4 id="preview_product_name" class="font-bold text-slate-900 text-base sm:text-lg font-playfair pt-1">Nama Produk</h4>
                                    <p id="preview_product_weight" class="text-xs text-slate-600 font-semibold">Berat: 0.000 gram</p>

                                    <div class="pt-3 flex flex-col sm:flex-row items-center justify-between gap-2 border-t border-amber-200/80 mt-2">
                                        <div class="text-center sm:text-left">
                                            <span class="text-[11px] text-slate-500 uppercase font-bold">Harga Satuan:</span>
                                            <p id="preview_product_unit_price" class="text-sm font-bold text-slate-800">Rp 0</p>
                                        </div>
                                        <div class="bg-white px-4 py-2 rounded-xl border border-amber-300 shadow-sm text-center sm:text-right w-full sm:w-auto">
                                            <span class="text-[11px] text-amber-900 uppercase font-extrabold">Total Harga Reservasi:</span>
                                            <p id="preview_product_total_price" class="text-lg font-extrabold text-[#C6A443]">Rp 0</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="input-label">Jumlah Pembelian (Qty) *</label>
                            <input type="number" name="quantity" id="quantity_input" min="1" max="100" value="{{ old('quantity', $negotiation->quantity ?? 1) }}"
                                   class="input-field font-bold text-slate-900" oninput="updatePricesAndSimulations()">
                        </div>
                    </div>

                    {{-- Jual Emas (Buyback) Fields — Murni Reservasi O2O --}}
                    <div id="buyback_fields" class="space-y-5 p-5 rounded-2xl bg-emerald-50/70 border border-emerald-200" style="display: none;">
                        <div class="flex items-center gap-2 pb-2 border-b border-emerald-200">
                            <span class="text-xl">💰</span>
                            <h4 class="text-sm font-bold text-emerald-950">Informasi Kunjungan Buyback (Jual Emas ke Toko)</h4>
                        </div>

                        {{-- Info Konsep O2O --}}
                        <div class="p-4 rounded-xl bg-white border-2 border-emerald-300 shadow-sm">
                            <div class="flex items-start gap-3">
                                <span class="text-2xl">🏪</span>
                                <div>
                                    <p class="font-extrabold text-emerald-950 text-sm">Reservasi Janji Temu Toko (Murni O2O)</p>
                                    <p class="text-xs text-emerald-800 mt-1 leading-relaxed">
                                        Reservasi ini <strong>hanya untuk membuat janji kunjungan</strong> ke toko. Penilaian kondisi fisik barang, penimbangan berat riil, pengujian kadar, penentuan harga beli, dan pembayaran uang tunai — semuanya dilakukan <strong>langsung di Toko Sinar Baru II</strong> saat Anda datang.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Hanya deskripsi barang yang akan dijual --}}
                        <div>
                            <label class="input-label">Jenis Perhiasan / Emas yang Ingin Dijual <span class="text-red-600">*</span></label>
                            <select name="pawn_gold_description" id="buyback_gold_description" class="input-field cursor-pointer font-bold {{ $errors->has('pawn_gold_description') ? 'border-red-500 ring-2 ring-red-200 bg-red-50/50' : '' }}">
                                <option value="" disabled {{ old('pawn_gold_description') ? '' : 'selected' }}>-- Pilih Jenis Perhiasan / Emas yang Mau Dijual --</option>
                                <optgroup label="📋 Dari Koleksi Toko">
                                    @foreach($products as $p)
                                    <option value="{{ $p->name }}" {{ old('pawn_gold_description') == $p->name ? 'selected' : '' }}>
                                        💍 {{ $p->name }}
                                    </option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="✨ Jenis Perhiasan Umum">
                                    <option value="Cincin Emas" {{ old('pawn_gold_description') == 'Cincin Emas' ? 'selected' : '' }}>💍 Cincin Emas</option>
                                    <option value="Kalung Emas" {{ old('pawn_gold_description') == 'Kalung Emas' ? 'selected' : '' }}>📿 Kalung Emas</option>
                                    <option value="Gelang Emas" {{ old('pawn_gold_description') == 'Gelang Emas' ? 'selected' : '' }}>🪙 Gelang Emas</option>
                                    <option value="Anting Emas" {{ old('pawn_gold_description') == 'Anting Emas' ? 'selected' : '' }}>✨ Anting Emas</option>
                                    <option value="Logam Mulia / Emas Batangan" {{ old('pawn_gold_description') == 'Logam Mulia / Emas Batangan' ? 'selected' : '' }}>🧱 Logam Mulia / Emas Batangan</option>
                                    <option value="Perhiasan Emas Lainnya" {{ old('pawn_gold_description') == 'Perhiasan Emas Lainnya' ? 'selected' : '' }}>🏷️ Perhiasan Emas Lainnya</option>
                                </optgroup>
                            </select>
                            <p class="text-xs text-slate-500 font-medium mt-1">ℹ️ Kadar, berat riil, dan harga beli akan dinilai & ditentukan langsung oleh kasir/penilai toko saat kunjungan.</p>
                            @error('pawn_gold_description')
                            <p class="text-xs text-red-600 font-bold mt-1.5 flex items-center gap-1">
                                <span>⚠️</span> {{ $message }}
                            </p>
                            @enderror
                        </div>
                    </div>

                    {{-- Installment Fields (Cicilan tanpa DP) --}}
                    <div id="installment_fields" class="space-y-5 p-5 rounded-2xl bg-blue-50/70 border border-blue-200" style="display: none;">
                        <div class="flex items-center gap-2 pb-2 border-b border-blue-200">
                            <span class="text-xl">📅</span>
                            <h4 class="text-sm font-bold text-blue-950">Rencana Cicilan Emas</h4>
                        </div>
                        <div>
                            <label class="input-label">Tenor Cicilan <span class="text-red-600">*</span></label>
                            <select name="installment_tenure" id="installment_tenure" class="input-field cursor-pointer font-bold" onchange="updatePricesAndSimulations()">
                                <option value="3" {{ old('installment_tenure') == 3 ? 'selected' : '' }}>3 Bulan (Tenor Singkat)</option>
                                <option value="6" {{ old('installment_tenure') == 6 ? 'selected' : '' }}>6 Bulan (Tenor Menengah)</option>
                                <option value="12" {{ old('installment_tenure', 12) == 12 ? 'selected' : '' }}>12 Bulan (Tenor 1 Tahun)</option>
                            </select>
                        </div>

                        {{-- Simulasi Angsuran (Tanpa DP) --}}
                        <div class="p-4 rounded-xl bg-white border border-blue-200 shadow-sm space-y-2">
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-slate-600 font-semibold">Total Nilai Emas:</span>
                                <span id="inst_total_display" class="font-bold text-slate-900">Rp 0</span>
                            </div>
                            <div class="flex justify-between items-center text-xs pt-1 border-t border-slate-100">
                                <span class="text-slate-700 font-bold">Estimasi Angsuran / Bulan:</span>
                                <span id="inst_monthly_display" class="font-extrabold text-sm text-[#085C54]">Rp 0 / bulan</span>
                            </div>
                        </div>

                        {{-- Keterangan Aturan Pengambilan Emas Cicilan --}}
                        <div class="p-4 rounded-xl bg-amber-50 border border-amber-300 text-xs text-amber-900 leading-relaxed shadow-sm">
                            <div class="flex items-start gap-2.5">
                                <span class="text-base shrink-0">⚠️</span>
                                <div>
                                    <p class="font-bold">Ketentuan Pengambilan Emas untuk Pembelian Cicilan:</p>
                                    <p class="mt-1">
                                        Perhiasan emas fisik disimpan aman di toko selama periode angsuran. <strong>Anda tidak perlu mengisi jadwal tanggal kunjungan fisik saat ini.</strong> Jadwal reservasi pengambilan emas fisik baru akan dibuka secara otomatis di Halaman Detail Cicilan Anda ketika sisa angsuran menyisakan 1 bulan lagi.
                                    </p>
                                </div>
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
                            <label class="input-label">Pilih Jenis / Produk Emas yang Digadai <span class="text-red-600">*</span></label>
                            <select name="pawn_gold_description" id="pawn_gold_description" class="input-field cursor-pointer font-bold {{ $errors->has('pawn_gold_description') ? 'border-red-500 ring-2 ring-red-200 bg-red-50/50' : '' }}" onchange="onPawnProductChange(this)">
                                <option value="" disabled {{ old('pawn_gold_description') ? '' : 'selected' }}>-- Pilih Jenis / Produk Emas yang Mau Digadai --</option>
                                <optgroup label="📋 Koleksi Produk Toko Sinar Baru II">
                                    @foreach($products as $p)
                                    <option value="{{ $p->name }} (24K)" data-weight="{{ $p->weight_gram }}" {{ old('pawn_gold_description') == ($p->name . ' (24K)') ? 'selected' : '' }}>
                                        💍 {{ $p->name }} (Standar: {{ number_format($p->weight_gram, 2) }} gram)
                                    </option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="✨ Jenis Perhiasan Umum (24K Murni)">
                                    <option value="Cincin Emas 24K" {{ old('pawn_gold_description') == 'Cincin Emas 24K' ? 'selected' : '' }}>💍 Cincin Emas 24K</option>
                                    <option value="Kalung Emas 24K" {{ old('pawn_gold_description') == 'Kalung Emas 24K' ? 'selected' : '' }}>📿 Kalung Emas 24K</option>
                                    <option value="Gelang Emas 24K" {{ old('pawn_gold_description') == 'Gelang Emas 24K' ? 'selected' : '' }}>🪙 Gelang Emas 24K</option>
                                    <option value="Anting Emas 24K" {{ old('pawn_gold_description') == 'Anting Emas 24K' ? 'selected' : '' }}>✨ Anting Emas 24K</option>
                                    <option value="Logam Mulia / Emas Batangan 24K" {{ old('pawn_gold_description') == 'Logam Mulia / Emas Batangan 24K' ? 'selected' : '' }}>🧱 Logam Mulia / Emas Batangan 24K</option>
                                    <option value="Perhiasan Emas 24K Lainnya" {{ old('pawn_gold_description') == 'Perhiasan Emas 24K Lainnya' ? 'selected' : '' }}>🏷️ Perhiasan Emas 24K Lainnya</option>
                                </optgroup>
                            </select>
                            <p class="text-xs text-slate-500 font-medium mt-1">💡 Pilih dari produk katalog toko atau kategori jenis perhiasan di atas.</p>
                            @error('pawn_gold_description')
                            <p class="text-xs text-red-600 font-bold mt-1.5 flex items-center gap-1">
                                <span>⚠️</span> {{ $message }}
                            </p>
                            @enderror
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="input-label">Kadar Emas <span class="text-red-600">*</span></label>
                                <select name="pawn_gold_purity" id="pawn_gold_purity" class="input-field font-bold bg-slate-100 cursor-not-allowed">
                                    <option value="24K" selected>24 Karat (24K - Murni)</option>
                                </select>
                            </div>
                            <div>
                                <label class="input-label">Berat Emas (Gram) <span class="text-red-600">*</span></label>
                                <input type="number" step="0.01" name="pawn_weight_gram" id="pawn_weight_gram" value="{{ old('pawn_weight_gram') }}" min="0.01" placeholder="0.00"
                                       class="input-field font-bold {{ $errors->has('pawn_weight_gram') ? 'border-red-500 ring-2 ring-red-200' : '' }}">
                                @error('pawn_weight_gram')
                                <p class="text-xs text-red-600 font-bold mt-1.5 flex items-center gap-1">
                                    <span>⚠️</span> {{ $message }}
                                </p>
                                @enderror
                            </div>
                            <div>
                                <label class="input-label">Pengajuan Pinjaman (Rp) <span class="text-red-600">*</span></label>
                                <input type="text" inputmode="numeric" name="pawn_amount_requested" value="{{ old('pawn_amount_requested') }}" placeholder="cth: 5.000.000"
                                       class="input-field format-rupiah font-bold {{ $errors->has('pawn_amount_requested') ? 'border-red-500 ring-2 ring-red-200' : '' }}">
                                @error('pawn_amount_requested')
                                <p class="text-xs text-red-600 font-bold mt-1.5 flex items-center gap-1">
                                    <span>⚠️</span> {{ $message }}
                                </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Metode Pembayaran (Revisi Poin 2) --}}
                    <div id="payment_fields" class="space-y-4">
                        <div id="payment_method_select_wrapper">
                            <label class="input-label" id="payment_method_label">Pilih Metode Pembayaran <span class="text-red-600">*</span></label>
                            <select name="payment_method" id="payment_method" class="input-field cursor-pointer font-bold" onchange="onPaymentMethodChange(this.value)">
                                @if(isset($paymentMethods) && $paymentMethods->count())
                                    @php
                                        $transferMethods = $paymentMethods->filter(fn($m) => $m->type === 'bank_transfer');
                                        $cashMethods = $paymentMethods->filter(fn($m) => $m->type === 'cash');
                                        $otherMethods = $paymentMethods->filter(fn($m) => !in_array($m->type, ['cash', 'bank_transfer']));
                                    @endphp

                                    @if($transferMethods->count())
                                    <optgroup label="💳 Transfer Bank (Non-Tunai)">
                                        @foreach($transferMethods as $pm)
                                        <option value="{{ $pm->code }}" {{ old('payment_method', 'bca') == $pm->code ? 'selected' : '' }}>
                                            🏦 {{ $pm->name }} (No. Rek: {{ $pm->account_number }})
                                        </option>
                                        @endforeach
                                    </optgroup>
                                    @endif

                                    @if($otherMethods->count())
                                    <optgroup label="📲 E-Wallet / QRIS / EDC Kasir">
                                        @foreach($otherMethods as $pm)
                                        <option value="{{ $pm->code }}" {{ old('payment_method') == $pm->code ? 'selected' : '' }}>
                                            📲 {{ $pm->name }} {{ $pm->account_number ? '('.$pm->account_number.')' : '' }}
                                        </option>
                                        @endforeach
                                    </optgroup>
                                    @endif

                                    @if($cashMethods->count())
                                    <optgroup label="💵 Pembayaran Tunai (Cash di Toko)">
                                        @foreach($cashMethods as $pm)
                                        <option value="{{ $pm->code }}" {{ old('payment_method') == $pm->code ? 'selected' : '' }}>
                                            💵 {{ $pm->name }}
                                        </option>
                                        @endforeach
                                    </optgroup>
                                    @endif
                                @else
                                    <optgroup label="💳 Transfer Bank">
                                        <option value="bca" {{ old('payment_method', 'bca') == 'bca' ? 'selected' : '' }}>Transfer Bank BCA (8820 9182 34)</option>
                                        <option value="mandiri" {{ old('payment_method') == 'mandiri' ? 'selected' : '' }}>Transfer Bank Mandiri (113 00 1829 4432)</option>
                                        <option value="bri" {{ old('payment_method') == 'bri' ? 'selected' : '' }}>Transfer Bank BRI (0089 01 028472 50 1)</option>
                                    </optgroup>
                                    <optgroup label="💵 Pembayaran Tunai">
                                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Tunai (Cash di Toko)</option>
                                    </optgroup>
                                    <optgroup label="💳 Transfer Bank">
                                        <option value="bca" {{ old('payment_method') == 'bca' ? 'selected' : '' }}>Transfer Bank BCA (8820 9182 34)</option>
                                        <option value="mandiri" {{ old('payment_method') == 'mandiri' ? 'selected' : '' }}>Transfer Bank Mandiri (113 00 1829 4432)</option>
                                        <option value="bri" {{ old('payment_method') == 'bri' ? 'selected' : '' }}>Transfer Bank BRI (0089 01 028472 50 1)</option>
                                    </optgroup>
                                @endif
                            </select>
                        </div>


                        {{-- Card Rincian Rekening Pembayaran Toko (Poin 2) --}}
                        <div id="payment_method_info" class="p-4 rounded-2xl bg-white border border-[#085C54]/30 shadow-sm transition-all" style="display: none;">
                            <div class="flex items-start gap-3">
                                <div id="pm_icon" class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-800 flex items-center justify-center text-lg shrink-0 border border-emerald-200">
                                    🏦
                                </div>
                                <div class="flex-1 text-xs">
                                    <p id="pm_bank_name" class="font-bold text-slate-500 uppercase">Transfer Bank</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span id="pm_account_number" class="text-base font-extrabold font-mono text-[#085C54] tracking-wide">8820 9182 34</span>
                                        <button type="button" id="copy_rekening_btn" onclick="copyRekeningNumber()" class="px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition border border-slate-300 shadow-sm">
                                            📋 Salin
                                        </button>
                                    </div>
                                    <p id="pm_account_name" class="font-semibold text-slate-700 mt-1">a.n. TOKO EMAS SINAR BARU II</p>
                                    <p id="pm_instructions" class="text-slate-600 mt-2 italic bg-slate-50 p-2.5 rounded-xl border border-slate-200 leading-relaxed"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="schedule_fields" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Tanggal Kunjungan --}}
                        <div>
                            <label class="input-label">Rencana Tanggal Kunjungan *</label>
                            <input type="date" name="preferred_date" id="preferred_date" min="{{ date('Y-m-d') }}" value="{{ old('preferred_date', date('Y-m-d', strtotime('+1 day'))) }}" required
                                   class="input-field font-bold">
                        </div>

                        {{-- Jam Kunjungan --}}
                        <div>
                            <label class="input-label">Perkiraan Jam (08:00 - 17:00) *</label>
                            <input type="time" name="preferred_time" id="preferred_time" value="{{ old('preferred_time', '10:00') }}" required
                                   class="input-field font-bold">
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

    {{-- Lightbox Modal Perbesar Gambar HD --}}
    <div id="image_lightbox_modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 backdrop-blur-sm p-4" onclick="closeImageLightbox()">
        <div class="relative max-w-lg w-full bg-white rounded-3xl overflow-hidden shadow-2xl p-4" onclick="event.stopPropagation()">
            <button onclick="closeImageLightbox()" class="absolute top-4 right-4 w-9 h-9 rounded-full bg-black/60 text-white hover:bg-black text-lg flex items-center justify-center transition z-10">
                ✕
            </button>
            <div class="rounded-2xl overflow-hidden bg-slate-100 border border-slate-200">
                <img id="lightbox_img" src="" class="w-full h-80 sm:h-96 object-contain" style="image-rendering: -webkit-optimize-contrast;">
            </div>
            <div class="mt-3 text-center">
                <h4 id="lightbox_title" class="font-bold text-slate-900 text-base font-playfair">Perhiasan Emas 24K</h4>
                <p id="lightbox_subtitle" class="text-xs text-slate-500 font-semibold mt-0.5">Kadar 24K Murni • Foto Studio HD</p>
            </div>
        </div>
    </div>

    <script>
        const PRODUCTS_DATA = @json($productsMap);
        const PAYMENT_METHODS_DATA = @json($paymentMethodsMap);
        const BUY_PRICE_PER_GRAM = {{ $todayGoldPrice->buy_price_per_gram ?? 1580000 }};
        const AGREED_PRICE = {{ isset($agreedPrice) && $agreedPrice ? $agreedPrice : (isset($negotiation) && $negotiation ? $negotiation->agreed_price : 'null') }};

        function updateSelectedProductCard(productId) {
            const card = document.getElementById('selected_product_card');
            if (!card) return;

            const prod = PRODUCTS_DATA[productId];
            if (!prod) {
                card.style.display = 'none';
                return;
            }

            card.style.display = 'block';
            const imgEl = document.getElementById('preview_product_img');
            if (imgEl) imgEl.src = prod.image;

            const nameEl = document.getElementById('preview_product_name');
            if (nameEl) nameEl.textContent = prod.name;

            const purityEl = document.getElementById('preview_product_purity');
            if (purityEl) purityEl.textContent = (prod.gold_purity || '24K') + ' Murni';

            const catEl = document.getElementById('preview_product_category');
            if (catEl) catEl.textContent = prod.category || 'Perhiasan Emas';

            const weightEl = document.getElementById('preview_product_weight');
            if (weightEl) weightEl.textContent = 'Berat: ' + parseFloat(prod.weight_gram).toFixed(3) + ' gram';

            const stockEl = document.getElementById('preview_product_stock');
            if (stockEl) {
                if (prod.stock > 0) {
                    stockEl.className = 'px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300';
                    stockEl.textContent = 'Stok: ' + prod.stock + ' unit';
                } else {
                    stockEl.className = 'px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-300';
                    stockEl.textContent = 'Stok Habis';
                }
            }

            updatePricesAndSimulations();
        }

        function updatePricesAndSimulations() {
            const prodSelect = document.getElementById('product_id');
            const qtyInput = document.getElementById('quantity_input');
            const qty = Math.max(1, parseInt(qtyInput?.value || 1));
            const productId = prodSelect?.value;
            const prod = PRODUCTS_DATA[productId];

            let unitPrice = 0;
            if (AGREED_PRICE && AGREED_PRICE > 0) {
                unitPrice = AGREED_PRICE;
            } else if (prod) {
                unitPrice = prod.base_price;
            }

            const totalPrice = unitPrice * qty;

            const unitPriceEl = document.getElementById('preview_product_unit_price');
            if (unitPriceEl) unitPriceEl.textContent = 'Rp ' + unitPrice.toLocaleString('id-ID');

            const totalPriceEl = document.getElementById('preview_product_total_price');
            if (totalPriceEl) totalPriceEl.textContent = 'Rp ' + totalPrice.toLocaleString('id-ID');

            // Update Installment Simulation
            const instTotalDisplay = document.getElementById('inst_total_display');
            if (instTotalDisplay) instTotalDisplay.textContent = 'Rp ' + totalPrice.toLocaleString('id-ID');

            const dpInput = document.getElementById('installment_down_payment');
            const dp = parseFloat(dpInput?.value || 0);

            const instDpDisplay = document.getElementById('inst_dp_display');
            if (instDpDisplay) instDpDisplay.textContent = 'Rp ' + dp.toLocaleString('id-ID');

            const tenureSelect = document.getElementById('installment_tenure');
            const tenure = parseInt(tenureSelect?.value || 12);

            const principal = Math.max(0, totalPrice - dp);
            const monthly = tenure > 0 ? Math.round(principal / tenure) : 0;

            const monthlyDisplay = document.getElementById('inst_monthly_display');
            if (monthlyDisplay) {
                monthlyDisplay.textContent = 'Rp ' + monthly.toLocaleString('id-ID') + ' / bulan (' + tenure + 'x)';
            }
        }

        function onPaymentMethodChange(code) {
            const infoBox = document.getElementById('payment_method_info');
            if (!infoBox) return;

            const pm = PAYMENT_METHODS_DATA[code];
            if (!pm || pm.type === 'cash') {
                if (pm && pm.type === 'cash') {
                    infoBox.style.display = 'block';
                    const resType = document.getElementById('reservation_type')?.value;
                    const isBuyback = resType === 'buyback';

                    document.getElementById('pm_icon').textContent = '💵';
                    document.getElementById('pm_bank_name').textContent = isBuyback ? 'Penyerahan Tunai (Cash)' : 'Tunai (Cash di Toko)';
                    document.getElementById('pm_account_number').textContent = isBuyback ? 'Penyerahan Tunai oleh Kasir Toko' : 'Bayar Langsung di Kasir Toko';
                    document.getElementById('copy_rekening_btn').style.display = 'none';
                    document.getElementById('pm_account_name').textContent = 'Toko Emas Sinar Baru II — Teluk Lubuk';
                    document.getElementById('pm_instructions').textContent = isBuyback
                        ? 'Dana buyback akan diserahkan secara TUNAI (Cash) oleh admin/kasir toko langsung kepada Anda saat penimbangan dan pengujian kadar emas di toko.'
                        : (pm.instructions || 'Selesaikan pembayaran tunai langsung di kasir toko saat verifikasi fisik barang.');
                } else {
                    infoBox.style.display = 'none';
                }
                return;
            }

            infoBox.style.display = 'block';
            document.getElementById('pm_icon').textContent = pm.type === 'bank_transfer' ? '🏦' : (pm.type === 'qris' ? '📱' : '💳');
            document.getElementById('pm_bank_name').textContent = pm.bank_name || pm.name;

            const accNumEl = document.getElementById('pm_account_number');
            const copyBtn = document.getElementById('copy_rekening_btn');
            if (pm.account_number) {
                accNumEl.textContent = pm.account_number;
                copyBtn.style.display = 'inline-block';
            } else {
                accNumEl.textContent = 'Gunakan Mesin EDC Toko';
                copyBtn.style.display = 'none';
            }

            document.getElementById('pm_account_name').textContent = pm.account_name ? 'a.n. ' + pm.account_name : '';
            document.getElementById('pm_instructions').textContent = pm.instructions || 'Harap simpan bukti pembayaran dan tunjukkan saat serah terima barang di toko.';
        }

        function copyRekeningNumber() {
            const accNum = document.getElementById('pm_account_number')?.textContent;
            if (accNum) {
                navigator.clipboard.writeText(accNum.replace(/\s+/g, ''));
                const btn = document.getElementById('copy_rekening_btn');
                const orig = btn.textContent;
                btn.textContent = '✔ Tersalin!';
                setTimeout(() => { btn.textContent = orig; }, 2000);
            }
        }

        function openImageLightbox() {
            const prodSelect = document.getElementById('product_id');
            const prod = PRODUCTS_DATA[prodSelect?.value];
            if (!prod) return;

            const modal = document.getElementById('image_lightbox_modal');
            const img = document.getElementById('lightbox_img');
            const title = document.getElementById('lightbox_title');
            const sub = document.getElementById('lightbox_subtitle');

            if (img) img.src = prod.image;
            if (title) title.textContent = prod.name;
            if (sub) sub.textContent = 'Kadar: ' + (prod.gold_purity || '24K') + ' Murni • Berat: ' + prod.weight_gram + ' gram • Harga: Rp ' + prod.base_price.toLocaleString('id-ID');

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeImageLightbox() {
            const modal = document.getElementById('image_lightbox_modal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        function calculateBuybackEstimate(weight) {
            const w = parseFloat(weight) || 0;
            const total = Math.round(w * BUY_PRICE_PER_GRAM);
            const formatted = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(total);
            const display = document.getElementById('buyback-estimate-display');
            if (display) {
                display.textContent = formatted.replace('IDR', 'Rp');
            }
        }

        function onBuybackProductChange(select) {
            const opt = select.options[select.selectedIndex];
            const weight = opt.getAttribute('data-weight');
            const weightInput = document.getElementById('buyback_weight_gram');
            if (weight && weightInput) {
                weightInput.value = parseFloat(weight).toFixed(3);
                calculateBuybackEstimate(weight);
            }
        }

        function onPawnProductChange(select) {
            const opt = select.options[select.selectedIndex];
            const weight = opt.getAttribute('data-weight');
            const weightInput = document.getElementById('pawn_weight_gram');
            if (weight && weightInput) {
                weightInput.value = parseFloat(weight).toFixed(2);
            }
        }

        // Expose functions globally
        window.updateSelectedProductCard = updateSelectedProductCard;
        window.updatePricesAndSimulations = updatePricesAndSimulations;
        window.onPaymentMethodChange = onPaymentMethodChange;
        window.copyRekeningNumber = copyRekeningNumber;
        window.openImageLightbox = openImageLightbox;
        window.closeImageLightbox = closeImageLightbox;
        window.calculateBuybackEstimate = calculateBuybackEstimate;
        window.onBuybackProductChange = onBuybackProductChange;
        window.onPawnProductChange = onPawnProductChange;

        // Initialize on DOM load
        document.addEventListener('DOMContentLoaded', () => {
            const prodSelect = document.getElementById('product_id');
            if (prodSelect && prodSelect.value) {
                updateSelectedProductCard(prodSelect.value);
            }

            const pmSelect = document.getElementById('payment_method');
            if (pmSelect && pmSelect.value) {
                onPaymentMethodChange(pmSelect.value);
            }
        });
    </script>

    <x-slot name="scripts">
        @vite('resources/js/customer/reservations.js')
    </x-slot>
</x-customer-app>
