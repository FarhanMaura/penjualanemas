<x-customer-app>
    <x-slot name="styles">
        <style>
            @media print {
                /* Sembunyikan sidebar, topbar, footer, tombol print, dan kembali */
                aside,
                header,
                footer,
                a[href*="customer/transactions"],
                .p-6.flex.flex-col.sm\:flex-row.justify-between.items-center.gap-4 {
                    display: none !important;
                }

                /* Atur area main untuk full print */
                .ml-64 {
                    margin-left: 0 !important;
                }
                main.flex-1 {
                    padding: 0 !important;
                }

                /* Desain Invoice Bersih Minimalis */
                .glass {
                    border: 1px solid #cccccc !important;
                    background: #ffffff !important;
                    box-shadow: none !important;
                    color: #000000 !important;
                    border-radius: 0.5rem !important;
                }

                body {
                    background: #ffffff !important;
                    color: #000000 !important;
                }

                .text-white, .text-yellow-400, .text-green-400, .text-blue-400, .text-gray-300, .text-gray-400, .text-gray-500 {
                    color: #000000 !important;
                }

                /* Header invoice print background */
                div[style*="background:linear-gradient"] {
                    background: #f3f4f6 !important;
                    border-bottom: 2px solid #000000 !important;
                    color: #000000 !important;
                }

                th {
                    color: #000000 !important;
                    border-bottom: 2px solid #000000 !important;
                }

                td {
                    color: #000000 !important;
                    border-bottom: 1px solid #e5e7eb !important;
                }
            }
        </style>
    </x-slot>

    <x-slot name="pageTitle">Detail Transaksi</x-slot>
    <x-slot name="breadcrumb">Invoice dan rincian transaksi #{{ $transaction->transaction_code }}</x-slot>

    @php
        $typeInfo = [
            'purchase'    => ['label'=>'Pembelian', 'bg'=>'rgba(245,158,11,0.1)', 'color'=>'#f59e0b'],
            'buyback'     => ['label'=>'Penjualan (Buyback)', 'bg'=>'rgba(16,185,129,0.1)', 'color'=>'#34d399'],
            'installment' => ['label'=>'Cicilan', 'bg'=>'rgba(59,130,246,0.1)', 'color'=>'#60a5fa'],
            'pawn'        => ['label'=>'Gadai', 'bg'=>'rgba(168,85,247,0.1)', 'color'=>'#c084fc'],
        ][$transaction->type] ?? ['label'=>$transaction->type, 'bg'=>'rgba(156,163,175,0.1)', 'color'=>'#9ca3af'];
        
        $statusInfo = [
            'pending'     => ['label'=>'Pending', 'class'=>'text-yellow-400 bg-yellow-900/40 border-yellow-700/50'],
            'in_progress' => ['label'=>'Berjalan', 'class'=>'text-blue-400 bg-blue-900/40 border-blue-700/50'],
            'completed'   => ['label'=>'Selesai', 'class'=>'text-green-400 bg-green-900/40 border-green-700/50'],
            'cancelled'   => ['label'=>'Batal', 'class'=>'text-red-400 bg-red-900/40 border-red-700/50'],
        ][$transaction->status] ?? ['label'=>$transaction->status, 'class'=>'text-gray-400 bg-gray-900/40 border-gray-700/50'];
    @endphp

    <div class="max-w-4xl mx-auto">
        <div class="glass rounded-3xl overflow-hidden shadow-2xl relative" style="border-color:rgba(255,255,255,0.1);">
            
            {{-- Header Invoice --}}
            <div class="p-8" style="background:linear-gradient(135deg, rgba(245,158,11,0.1), rgba(0,0,0,0.5)); border-bottom:1px solid rgba(255,255,255,0.1);">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-3xl font-bold font-playfair text-white">Invoice</h2>
                        <p class="text-sm text-gray-400 mt-1">#{{ $transaction->transaction_code }}</p>
                    </div>
                    <div class="text-left md:text-right">
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold mb-2" style="background:{{ $typeInfo['bg'] }}; color:{{ $typeInfo['color'] }};">
                            {{ $typeInfo['label'] }}
                        </span>
                        <br>
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold border {{ $statusInfo['class'] }}">
                            Status: {{ $statusInfo['label'] }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Body Invoice --}}
            <div class="p-8">
                <div class="grid md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-widest mb-2">Informasi Toko</p>
                        <h3 class="font-bold text-white text-lg">Toko Emas Sinar Baru II</h3>
                        <p class="text-sm text-gray-400 mt-1">Jl. Contoh No. 123, Kota Anda</p>
                        <p class="text-sm text-gray-400">Telp: +62 812-3456-7890</p>
                    </div>
                    <div class="md:text-right">
                        <p class="text-xs text-gray-500 uppercase tracking-widest mb-2">Informasi Pelanggan</p>
                        <h3 class="font-bold text-white text-lg">{{ $transaction->user->name ?? 'Pelanggan' }}</h3>
                        <p class="text-sm text-gray-400 mt-1">{{ $transaction->user->email ?? '-' }}</p>
                        <p class="text-sm text-gray-400">Tanggal: {{ $transaction->created_at->isoFormat('D MMMM Y, H:mm') }}</p>
                    </div>
                </div>

                {{-- Table Rincian --}}
                <div class="overflow-x-auto mb-8">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.1);">
                                <th class="py-3 px-4 text-xs font-semibold text-gray-400 uppercase">Item</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-400 uppercase text-center">Harga</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-400 uppercase text-center">Qty</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-400 uppercase text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transaction->items as $item)
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.05);">
                                <td class="py-4 px-4">
                                    <p class="font-bold text-white text-sm">{{ $item->product_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $item->gold_purity ?? '' }} • {{ number_format($item->weight_gram ?? 0, 3) }} gram</p>
                                </td>
                                <td class="py-4 px-4 text-center text-sm text-gray-300">
                                    Rp {{ number_format($item->price_per_unit, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-4 text-center text-sm text-gray-300">
                                    {{ $item->quantity }}
                                </td>
                                <td class="py-4 px-4 text-right text-sm font-semibold text-yellow-400">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Totals --}}
                <div class="flex justify-end">
                    <div class="w-full md:w-1/2 space-y-3">
                        <div class="flex justify-between text-sm text-gray-400">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-400 pb-3" style="border-bottom:1px solid rgba(255,255,255,0.1);">
                            <span>Biaya Layanan/Lainnya</span>
                            <span>Rp 0</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold">
                            <span class="text-white">Total Akhir</span>
                            <span style="color:{{ $typeInfo['color'] }};">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                @if($transaction->notes)
                <div class="mt-8 p-4 rounded-xl" style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.1);">
                    <p class="text-xs font-semibold text-gray-400 mb-1">Catatan Transaksi:</p>
                    <p class="text-sm text-gray-300">{{ $transaction->notes }}</p>
                </div>
                @endif
            </div>

            {{-- Footer Aksi --}}
            <div class="p-6 flex flex-col sm:flex-row justify-between items-center gap-4" style="background:rgba(0,0,0,0.2); border-top:1px solid rgba(255,255,255,0.1);">
                <a href="{{ route('customer.transactions.index') }}" class="text-sm text-gray-400 hover:text-white transition">
                    ← Kembali ke Riwayat
                </a>
                <button onclick="window.print()" class="px-6 py-2.5 rounded-xl font-bold text-white transition hover:scale-105"
                        style="background:linear-gradient(135deg,#f59e0b,#d97706);">
                    🖨️ Cetak Bukti
                </button>
            </div>
        </div>
    </div>
</x-customer-app>
