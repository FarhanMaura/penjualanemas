<x-customer-app>
    <x-slot name="styles">
        <style>
            @media print {
                aside, header, footer, a[href*="customer/transactions"],
                .p-6.flex.flex-col.sm\:flex-row.justify-between.items-center.gap-4 {
                    display: none !important;
                }
                .ml-64 { margin-left: 0 !important; }
                main.flex-1 { padding: 0 !important; }
                .glass {
                    border: 1px solid #cccccc !important;
                    background: #ffffff !important;
                    box-shadow: none !important;
                    color: #000000 !important;
                    border-radius: 0.5rem !important;
                }
                body { background: #ffffff !important; color: #000000 !important; }
            }
        </style>
    </x-slot>

    <x-slot name="pageTitle">Detail Transaksi</x-slot>
    <x-slot name="breadcrumb">Invoice dan rincian transaksi #{{ $transaction->transaction_code }}</x-slot>

    @php
        $typeInfo = [
            'purchase'    => ['label'=>'Pembelian', 'bg'=>'#fef3c7', 'color'=>'#92400e', 'border'=>'#fde68a'],
            'buyback'     => ['label'=>'Penjualan (Buyback)', 'bg'=>'#d1fae5', 'color'=>'#065f46', 'border'=>'#a7f3d0'],
            'installment' => ['label'=>'Cicilan', 'bg'=>'#dbeafe', 'color'=>'#1e40af', 'border'=>'#bfdbfe'],
            'pawn'        => ['label'=>'Gadai', 'bg'=>'#f3e8ff', 'color'=>'#6b21a8', 'border'=>'#e9d5ff'],
        ][$transaction->type] ?? ['label'=>$transaction->type, 'bg'=>'#f1f5f9', 'color'=>'#334155', 'border'=>'#e2e8f0'];
        
        $statusInfo = [
            'pending'     => ['label'=>'Pending', 'class'=>'bg-amber-100 text-amber-900 border-amber-300'],
            'in_progress' => ['label'=>'Berjalan', 'class'=>'bg-blue-100 text-blue-900 border-blue-300'],
            'completed'   => ['label'=>'Selesai', 'class'=>'bg-emerald-100 text-emerald-900 border-emerald-300'],
            'cancelled'   => ['label'=>'Batal', 'class'=>'bg-red-100 text-red-900 border-red-300'],
        ][$transaction->status] ?? ['label'=>$transaction->status, 'class'=>'bg-slate-100 text-slate-800 border-slate-300'];
    @endphp

    <div class="max-w-4xl mx-auto">
        <div class="glass rounded-3xl overflow-hidden shadow-md bg-white border border-[#e8e3d5] relative">
            
            {{-- Header Invoice --}}
            <div class="p-8 bg-[#F4EDD9]/60 border-b border-[#e8e3d5]">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-3xl font-extrabold font-playfair text-[#042623]">Invoice Transaksi</h2>
                        <p class="text-sm font-mono font-bold text-[#085C54] mt-1">#{{ $transaction->transaction_code }}</p>
                    </div>
                    <div class="text-left md:text-right">
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold mb-2 border" style="background:{{ $typeInfo['bg'] }}; color:{{ $typeInfo['color'] }}; border-color:{{ $typeInfo['border'] }};">
                            {{ $typeInfo['label'] }}
                        </span>
                        <br>
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold border {{ $statusInfo['class'] }}">
                            Status: {{ $statusInfo['label'] }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Body Invoice --}}
            <div class="p-8">
                <div class="grid md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Informasi Toko</p>
                        <h3 class="font-bold text-slate-900 text-lg">Toko Emas Sinar Baru II</h3>
                        <p class="text-sm text-slate-600 font-medium mt-1">Teluk Lubuk, Kec. Belimbing, Kab. Muara Enim</p>
                        <p class="text-sm text-slate-600 font-medium">Sumatera Selatan</p>
                    </div>
                    <div class="md:text-right">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Informasi Pelanggan</p>
                        <h3 class="font-bold text-slate-900 text-lg">{{ $transaction->user->name ?? 'Pelanggan' }}</h3>
                        <p class="text-sm text-slate-600 font-medium mt-1">{{ $transaction->user->email ?? '-' }}</p>
                        <p class="text-sm text-slate-600 font-medium">Tanggal: {{ $transaction->created_at->isoFormat('D MMMM Y, HH:mm') }} WIB</p>
                    </div>
                </div>

                {{-- Table Rincian --}}
                <div class="overflow-x-auto mb-8">
                    <table class="w-full text-left border-collapse min-w-[500px]">
                        <thead>
                            <tr class="border-b border-[#e8e3d5] bg-[#F4EDD9]/40">
                                <th class="py-3 px-4 text-xs font-bold text-slate-700 uppercase">Item Produk</th>
                                <th class="py-3 px-4 text-xs font-bold text-slate-700 uppercase text-center">Harga Satuan</th>
                                <th class="py-3 px-4 text-xs font-bold text-slate-700 uppercase text-center">Qty</th>
                                <th class="py-3 px-4 text-xs font-bold text-slate-700 uppercase text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-800 text-sm">
                            @foreach($transaction->items as $item)
                            <tr>
                                <td class="py-4 px-4">
                                    <p class="font-bold text-slate-900 text-sm">{{ $item->product_name }}</p>
                                    <p class="text-xs font-semibold text-slate-500">{{ $item->gold_purity ?? '' }} • {{ number_format($item->weight_gram ?? 0, 3) }} gram</p>
                                </td>
                                <td class="py-4 px-4 text-center font-semibold text-slate-700">
                                    Rp {{ number_format($item->price_per_unit, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-4 text-center font-bold text-slate-800">
                                    {{ $item->quantity }}
                                </td>
                                <td class="py-4 px-4 text-right font-extrabold text-[#C6A443]">
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
                        <div class="flex justify-between text-sm font-semibold text-slate-600">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm font-semibold text-slate-600 pb-3 border-b border-slate-200">
                            <span>Biaya Layanan/Lainnya</span>
                            <span>Rp 0</span>
                        </div>
                        <div class="flex justify-between text-xl font-extrabold text-slate-900 pt-1">
                            <span>Total Akhir</span>
                            <span class="text-[#042623]">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                @if($transaction->notes)
                <div class="mt-8 p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Catatan Transaksi:</p>
                    <p class="text-sm font-medium text-slate-700">{{ $transaction->notes }}</p>
                </div>
                @endif
            </div>

            {{-- Footer Aksi --}}
            <div class="p-6 flex flex-col sm:flex-row justify-between items-center gap-4 bg-slate-50 border-t border-slate-100">
                <a href="{{ route('customer.transactions.index') }}" class="text-sm font-bold text-[#085C54] hover:underline">
                    ← Kembali ke Riwayat Transaksi
                </a>
                <button onclick="window.print()" class="px-6 py-2.5 rounded-xl font-extrabold text-[#042623] gold-gradient border border-[#C6A443] shadow-md hover:brightness-110 transition">
                    🖨️ Cetak Bukti Invoice
                </button>
            </div>
        </div>
    </div>
</x-customer-app>
