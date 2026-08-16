<x-customer-app>
    <x-slot name="pageTitle">Ajukan Tawar Harga</x-slot>
    <x-slot name="breadcrumb">Isi formulir di bawah ini untuk mengajukan penawaran harga kepada admin</x-slot>

    <div class="max-w-3xl mx-auto">
        <a href="{{ route('customer.negotiations.index') }}" class="inline-flex items-center gap-2 text-xs text-amber-400 hover:underline mb-6">
            ← Kembali ke Daftar Tawar Harga
        </a>

        @if($errors->any())
        <div class="glass rounded-xl p-4 mb-6 border-red-500/30 text-red-400 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('customer.negotiations.store') }}" onsubmit="if(this.dataset.submitted) return false; this.dataset.submitted = true;" class="glass rounded-2xl p-6 sm:p-8 space-y-6">
            @csrf

            <h3 class="text-lg font-bold text-white flex items-center gap-2 pb-4 border-b border-white/10">
                <span>🤝</span> Form Pengajuan Tawar Harga
            </h3>

            {{-- Pilih Produk --}}
            <div>
                <label for="product_id" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-2">
                    Pilih Produk Emas <span class="text-red-400">*</span>
                </label>
                <select name="product_id" id="product_id" required onchange="updateProductDetails(this)"
                        class="w-full rounded-xl px-4 py-3 text-sm text-white outline-none focus:ring-2 focus:ring-amber-500"
                        style="background:rgba(255,255,255,0.06); border:1px solid rgba(245,158,11,0.2);">
                    <option value="" class="bg-gray-900 text-gray-400">-- Pilih Produk --</option>
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
                                class="bg-gray-900 text-white">
                            {{ $p->name }} ({{ number_format($p->weight_gram, 3) }}g) - Rp {{ number_format($hargaProduk, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Info Ringkasan Produk --}}
            <div id="product_info_card" class="glass rounded-xl p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-amber-500/5">
                <div>
                    <span class="text-xs text-gray-400">Harga Normal Saat Ini</span>
                    <p id="display_normal_price" class="text-xl font-extrabold text-amber-400">Rp 0</p>
                </div>
                <div class="text-left sm:text-right">
                    <span id="display_purity" class="text-xs px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-300 font-semibold border border-amber-500/20">
                        Kemurnian Emas
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                {{-- Jumlah Qty --}}
                <div>
                    <label for="quantity" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-2">
                        Jumlah (Qty) <span class="text-red-400">*</span>
                    </label>
                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity', 1) }}" min="1" max="100" required
                           oninput="calculateDiscount()"
                           class="w-full rounded-xl px-4 py-3 text-sm text-white outline-none focus:ring-2 focus:ring-amber-500"
                           style="background:rgba(255,255,255,0.06); border:1px solid rgba(245,158,11,0.2);">
                </div>

                {{-- Harga Penawaran Pembeli --}}
                <div>
                    <label for="offered_price" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-2">
                        Harga Penawaran Anda (Rp) <span class="text-red-400">*</span>
                    </label>
                    <input type="number" name="offered_price" id="offered_price" value="{{ old('offered_price') }}" required step="1000" min="10000"
                           placeholder="Contoh: 3900000"
                           oninput="calculateDiscount()"
                           class="w-full rounded-xl px-4 py-3 text-sm text-white font-bold outline-none focus:ring-2 focus:ring-amber-500"
                           style="background:rgba(255,255,255,0.06); border:1px solid rgba(245,158,11,0.2);">
                </div>
            </div>

            {{-- Realtime Discount Summary --}}
            <div id="discount_summary" class="hidden glass rounded-xl p-4 border-green-500/20 bg-green-500/5 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-gray-300">Potongan Harga Penawaran:</span>
                    <span id="diff_amount" class="font-extrabold text-green-400">Rp 0</span>
                </div>
            </div>

            {{-- Catatan Pembeli --}}
            <div>
                <label for="notes" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-2">
                    Catatan Pembeli (Opsional)
                </label>
                <textarea name="notes" id="notes" rows="3"
                          placeholder="Alasan penawaran atau keterangan tambahan..."
                          class="w-full rounded-xl px-4 py-3 text-sm text-white outline-none focus:ring-2 focus:ring-amber-500"
                          style="background:rgba(255,255,255,0.06); border:1px solid rgba(245,158,11,0.2);">{{ old('notes') }}</textarea>
            </div>

            {{-- Action Submit --}}
            <div class="pt-4 border-t border-white/10 flex justify-end gap-3">
                <a href="{{ route('customer.negotiations.index') }}" class="px-5 py-2.5 rounded-xl text-sm text-gray-400 glass hover:bg-white/10 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-gray-950 shadow-lg hover:brightness-110 transition"
                        style="background:linear-gradient(135deg,#f59e0b,#d97706);">
                    🚀 Kirim Penawaran
                </button>
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
                document.getElementById('display_purity').innerText = purity + ' Murni (' + weight + 'g)';
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

            const offeredPrice = parseFloat(document.getElementById('offered_price').value || 0);
            const summaryBox = document.getElementById('discount_summary');
            const diffAmountSpan = document.getElementById('diff_amount');

            if (totalNormalPrice > 0 && offeredPrice > 0) {
                const diff = totalNormalPrice - offeredPrice;
                if (diff > 0) {
                    diffAmountSpan.innerText = 'Hemat Rp ' + new Intl.NumberFormat('id-ID').format(diff);
                    diffAmountSpan.className = 'font-extrabold text-green-400';
                    summaryBox.classList.remove('hidden');
                } else if (diff < 0) {
                    diffAmountSpan.innerText = 'Lebih tinggi Rp ' + new Intl.NumberFormat('id-ID').format(Math.abs(diff));
                    diffAmountSpan.className = 'font-extrabold text-amber-400';
                    summaryBox.classList.remove('hidden');
                } else {
                    summaryBox.classList.add('hidden');
                }
            } else {
                summaryBox.classList.add('hidden');
            }
        }

        // Trigger on load if initial product selected
        document.addEventListener('DOMContentLoaded', () => {
            const select = document.getElementById('product_id');
            if (select && select.value) {
                updateProductDetails(select);
            }
        });
    </script>
</x-customer-app>
