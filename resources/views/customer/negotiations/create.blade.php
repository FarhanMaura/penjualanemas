<x-customer-app>
    <x-slot name="pageTitle">Ajukan Tawar Harga</x-slot>
    <x-slot name="breadcrumb">Isi formulir di bawah ini untuk mengajukan penawaran harga kepada admin</x-slot>

    <div class="max-w-3xl mx-auto">
        <a href="{{ route('customer.negotiations.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-[#085C54] hover:underline mb-6">
            ← Kembali ke Daftar Tawar Harga
        </a>

        @if($errors->any())
        <div class="rounded-2xl p-4 mb-6 bg-red-50 border border-red-300 text-red-900 text-sm shadow-sm">
            <ul class="list-disc list-inside space-y-1 font-semibold">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('customer.negotiations.store') }}" onsubmit="if(this.dataset.submitted) return false; this.dataset.submitted = true;" class="glass rounded-2xl p-6 sm:p-8 space-y-6 bg-white border border-[#e8e3d5] shadow-lg">
            @csrf

            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2 pb-4 border-b border-slate-200">
                <span>🤝</span> Formulir Pengajuan Tawar Harga
            </h3>

            {{-- Pilih Produk --}}
            <div>
                <label for="product_id" class="input-label">
                    Pilih Produk Emas <span class="text-red-600">*</span>
                </label>
                <select name="product_id" id="product_id" required onchange="updateProductDetails(this)"
                        class="input-field cursor-pointer">
                    <option value="" class="text-slate-500">-- Pilih Produk Emas --</option>
                    @foreach($products as $p)
                        @php
                            $hargaProduk = $goldPrice
                                ? round($goldPrice->sell_price_per_gram * $p->weight_gram, -3)
                                : $p->base_price;
                        @endphp
                        <option value="{{ $p->id }}"
                                data-price="{{ $hargaProduk }}"
                                data-weight="{{ number_format($p->weight_gram, 3) }}"
                                data-purity="{{ $p->gold_purity }}"
                                {{ (old('product_id', $selectedProduct->id ?? null) == $p->id) ? 'selected' : '' }}
                                class="text-slate-900">
                            {{ $p->name }} ({{ number_format($p->weight_gram, 3) }}g) - Rp {{ number_format($hargaProduk, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Info Ringkasan Produk --}}
            <div id="product_info_card" class="rounded-xl p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-amber-50/80 border border-amber-200 shadow-sm">
                <div>
                    <span class="text-xs text-slate-600 font-bold uppercase tracking-wider">Harga Normal Saat Ini</span>
                    <p id="display_normal_price" class="text-2xl font-extrabold text-[#C6A443]">Rp 0</p>
                </div>
                <div class="text-left sm:text-right">
                    <span id="display_purity" class="text-xs px-3 py-1 rounded-full bg-white text-slate-800 font-bold border border-amber-300 shadow-sm">
                        Kemurnian Emas
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                {{-- Jumlah Qty --}}
                <div>
                    <label for="quantity" class="input-label">
                        Jumlah (Qty) <span class="text-red-600">*</span>
                    </label>
                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity', 1) }}" min="1" max="100" required
                           oninput="calculateDiscount()"
                           class="input-field">
                </div>

                {{-- Harga Penawaran Pembeli --}}
                <div>
                    <label for="offered_price" class="input-label">
                        Harga Penawaran Anda (Rp) <span class="text-red-600">*</span>
                    </label>
                    <input type="text" inputmode="numeric" name="offered_price" id="offered_price" value="{{ old('offered_price') }}" required
                           placeholder="Contoh: 3.900.000"
                           oninput="calculateDiscount()"
                           class="input-field format-rupiah font-extrabold text-slate-900">
                </div>
            </div>

            {{-- Realtime Discount Summary --}}
            <div id="discount_summary" class="hidden rounded-xl p-4 border border-emerald-300 bg-emerald-50 text-sm shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-emerald-950 font-bold">Potongan Harga Penawaran:</span>
                    <span id="diff_amount" class="font-black text-[#085C54] text-base">Rp 0</span>
                </div>
            </div>

            {{-- Catatan Pembeli --}}
            <div>
                <label for="notes" class="input-label">
                    Catatan Pembeli (Opsional)
                </label>
                <textarea name="notes" id="notes" rows="3"
                          placeholder="Tambahkan catatan jika ada..."
                          class="input-field text-sm">{{ old('notes') }}</textarea>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-col sm:flex-row gap-4 pt-4 border-t border-slate-200">
                <button type="submit" class="flex-1 py-3 px-6 rounded-xl font-bold text-[#042623] gold-gradient border border-[#C6A443] shadow-md hover:brightness-110 transition text-center text-sm">
                    🤝 Kirim Penawaran Harga
                </button>
                <a href="{{ route('customer.negotiations.index') }}" class="px-6 py-3 rounded-xl font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition text-center text-sm">
                    Batal
                </a>
            </div>
        </form>
    </div>

    <script>
        function updateProductDetails(select) {
            const selectedOption = select.options[select.selectedIndex];
            const price = parseFloat(selectedOption.getAttribute('data-price') || 0);
            const weight = selectedOption.getAttribute('data-weight') || '';
            const purity = selectedOption.getAttribute('data-purity') || '';

            if (price > 0) {
                document.getElementById('display_normal_price').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(price);
                document.getElementById('display_purity').innerText = 'Kadar: ' + purity;
            } else {
                document.getElementById('display_normal_price').innerText = 'Rp 0';
            }
            calculateDiscount();
        }

        function calculateDiscount() {
            const select = document.getElementById('product_id');
            const selectedOption = select.options[select.selectedIndex];
            const unitPrice = parseFloat(selectedOption.getAttribute('data-price') || 0);
            const qty = parseInt(document.getElementById('quantity').value || 1);
            const totalNormalPrice = unitPrice * qty;

            const parseR = window.parseRupiah || function(v){ return parseFloat(v.replace(/\./g, ''))||0; };
            const offeredPrice = parseR(document.getElementById('offered_price').value);
            const summaryBox = document.getElementById('discount_summary');
            const diffAmountSpan = document.getElementById('diff_amount');

            if (totalNormalPrice > 0 && offeredPrice > 0) {
                const diff = totalNormalPrice - offeredPrice;
                if (diff > 0) {
                    diffAmountSpan.innerText = 'Hemat Rp ' + new Intl.NumberFormat('id-ID').format(diff);
                    diffAmountSpan.className = 'font-black text-[#085C54] text-base';
                    summaryBox.classList.remove('hidden');
                } else if (diff < 0) {
                    diffAmountSpan.innerText = 'Lebih tinggi Rp ' + new Intl.NumberFormat('id-ID').format(Math.abs(diff));
                    diffAmountSpan.className = 'font-black text-[#C6A443] text-base';
                    summaryBox.classList.remove('hidden');
                } else {
                    summaryBox.classList.add('hidden');
                }
            } else {
                summaryBox.classList.add('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const select = document.getElementById('product_id');
            if (select && select.value) {
                updateProductDetails(select);
            }
        });
    </script>
</x-customer-app>

