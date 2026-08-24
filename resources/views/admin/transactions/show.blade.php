<x-admin-app>
    <x-slot name="styles">
        <style>
            @media print {
                aside#sidebar,
                main > div.flex.justify-between.items-center.mb-8,
                .px-4.py-2.rounded-lg.text-sm.glass,
                .px-8.py-4.flex.justify-end,
                .space-y-5,
                .mb-6 {
                    display: none !important;
                }

                main {
                    margin-left: 0 !important;
                    padding: 0 !important;
                    background: #ffffff !important;
                    color: #000000 !important;
                }

                .glass {
                    border: 1px solid #cccccc !important;
                    background: #ffffff !important;
                    box-shadow: none !important;
                    color: #000000 !important;
                }

                body {
                    background: #ffffff !important;
                    color: #000000 !important;
                }

                th {
                    color: #000000 !important;
                    border-bottom: 2px solid #000000 !important;
                }
            }
        </style>
    </x-slot>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold font-playfair text-slate-900">Detail Transaksi</h1>
            <p class="text-sm text-slate-500 font-semibold">Invoice #{{ $transaction->transaction_code }}</p>
        </div>
        <a href="{{ route('admin.transactions.index') }}" class="px-4 py-2 rounded-xl text-sm font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition shadow-sm">
            ← Kembali
        </a>
    </div>

    @php
        $typeMap = [
            'purchase'    => ['label'=>'Pembelian Emas', 'icon'=>'🛒', 'color'=>'#C6A443'],
            'buyback'     => ['label'=>'Penjualan (Buyback)', 'icon'=>'💰', 'color'=>'#085C54'],
            'installment' => ['label'=>'Cicilan', 'icon'=>'📅', 'color'=>'#2563eb'],
            'pawn'        => ['label'=>'Gadai', 'icon'=>'🏦', 'color'=>'#7c3aed'],
        ];
        $statusMap = [
            'pending'     => ['label'=>'Pending', 'class'=>'bg-amber-100 text-amber-900 border-amber-300'],
            'in_progress' => ['label'=>'Berjalan', 'class'=>'bg-blue-100 text-blue-900 border-blue-300'],
            'completed'   => ['label'=>'Selesai', 'class'=>'bg-emerald-100 text-emerald-900 border-emerald-300'],
            'cancelled'   => ['label'=>'Dibatalkan', 'class'=>'bg-red-100 text-red-900 border-red-300'],
        ];
        $type   = $typeMap[$transaction->type]   ?? ['label'=>$transaction->type, 'icon'=>'📄', 'color'=>'#475569'];
        $status = $statusMap[$transaction->status] ?? ['label'=>$transaction->status, 'class'=>'bg-slate-100 text-slate-800'];
    @endphp

    <div class="grid lg:grid-cols-3 gap-6">

        {{-- Invoice Panel (Main) --}}
        <div class="lg:col-span-2 glass rounded-3xl overflow-hidden bg-white border border-[#e8e3d5] shadow-lg">

            {{-- Header Invoice --}}
            <div class="px-8 py-6 flex justify-between items-start bg-[#F4EDD9]/60 border-b border-[#e8e3d5]">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-2xl">{{ $type['icon'] }}</span>
                        <span class="font-extrabold text-base" style="color:{{ $type['color'] }}">{{ $type['label'] }}</span>
                    </div>
                    <p class="font-mono text-sm font-bold text-[#085C54]">{{ $transaction->transaction_code }}</p>
                    <p class="text-xs text-slate-500 font-medium mt-1">{{ $transaction->created_at->isoFormat('D MMMM Y, H:mm') }} WIB</p>
                </div>
                <span class="px-3.5 py-1.5 rounded-full text-xs font-extrabold border {{ $status['class'] }}">
                    {{ $status['label'] }}
                </span>
            </div>

            {{-- Info Pelanggan & Toko --}}
            <div class="px-8 py-6 grid grid-cols-2 gap-6 border-b border-slate-100">
                <div>
                    <p class="text-xs uppercase tracking-widest text-slate-500 font-bold mb-2">Pelanggan</p>
                    <p class="font-bold text-slate-900 text-base">{{ $transaction->user->name }}</p>
                    <p class="text-sm text-slate-600 font-medium">{{ $transaction->user->email }}</p>
                    <p class="text-sm text-slate-600 font-medium">{{ $transaction->user->profile->phone ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-widest text-slate-500 font-bold mb-2">Diproses Oleh</p>
                    <p class="font-bold text-slate-900 text-base">
                        {{ $transaction->processedBy->name ?? 'Admin' }}
                    </p>
                    <p class="text-sm text-slate-500 font-bold mt-2">Metode Bayar</p>
                    <p class="text-sm font-extrabold text-slate-900 uppercase">{{ $transaction->payment_method }}</p>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Tgl Bayar: {{ $transaction->payment_date ? \Carbon\Carbon::parse($transaction->payment_date)->isoFormat('D MMM Y') : '-' }}</p>
                </div>
            </div>

            {{-- Tabel Item --}}
            <div class="px-8 py-6">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-xs text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                            <th class="py-3">Produk</th>
                            <th class="py-3 text-center">Harga / Unit</th>
                            <th class="py-3 text-center">Qty</th>
                            <th class="py-3 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($transaction->items as $item)
                        <tr>
                            <td class="py-4">
                                <p class="font-bold text-slate-900 text-sm">{{ $item->product_name }}</p>
                                <p class="text-xs text-slate-500 font-medium">{{ $item->gold_purity ?? '' }} • {{ number_format($item->weight_gram ?? 0, 3) }} gram</p>
                            </td>
                            <td class="py-4 text-center text-sm font-semibold text-slate-700">
                                Rp {{ number_format($item->price_per_unit, 0, ',', '.') }}
                            </td>
                            <td class="py-4 text-center text-sm font-bold text-slate-900">
                                {{ $item->quantity }}
                            </td>
                            <td class="py-4 text-right text-sm font-extrabold text-slate-900">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Total Breakdown --}}
                <div class="mt-6 flex justify-end">
                    <div class="w-80 space-y-2">
                        <div class="flex justify-between text-sm text-slate-600 font-medium">
                            <span>Subtotal</span>
                            <span class="font-bold text-slate-900">Rp {{ number_format($transaction->subtotal ?? $transaction->total_amount, 0, ',', '.') }}</span>
                        </div>
                        @if(($transaction->admin_fee ?? 0) > 0)
                        <div class="flex justify-between text-sm text-slate-600 font-medium">
                            <span>Biaya Admin</span>
                            <span class="font-bold text-slate-900">Rp {{ number_format($transaction->admin_fee, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        @if(($transaction->discount ?? 0) > 0)
                        <div class="flex justify-between text-sm text-emerald-700 font-bold">
                            <span>Diskon</span>
                            <span>- Rp {{ number_format($transaction->discount, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between text-lg font-black pt-3 border-t-2 border-slate-200">
                            <span class="text-slate-900">Total Akhir</span>
                            <span style="color:{{ $type['color'] }}">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                @if($transaction->notes)
                <div class="mt-6 p-4 rounded-xl text-sm bg-slate-50 border border-slate-200">
                    <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Catatan:</p>
                    <p class="text-slate-800 font-medium">{{ $transaction->notes }}</p>
                </div>
                @endif
            </div>

            {{-- Footer Print --}}
            <div class="px-8 py-4 flex justify-end bg-slate-50 border-t border-slate-100">
                <button onclick="window.print()" class="px-6 py-2.5 rounded-xl text-sm font-extrabold text-[#042623] gold-gradient border border-[#C6A443] shadow-md transition hover:scale-105">
                    🖨️ Cetak Invoice
                </button>
            </div>
        </div>

        {{-- Sidebar Info --}}
        <div class="space-y-5">

            {{-- Reservasi Terkait --}}
            @if($transaction->reservation)
            <div class="glass rounded-2xl p-5 bg-white border border-[#e8e3d5] shadow-sm">
                <h3 class="text-sm font-bold text-slate-900 mb-4">📋 Reservasi Terkait</h3>
                <div class="space-y-2.5 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-medium">Kode</span>
                        <span class="font-mono text-xs font-bold text-[#085C54]">{{ $transaction->reservation->reservation_code }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-medium">Tgl Kunjungan</span>
                        <span class="text-slate-800 font-bold">{{ \Carbon\Carbon::parse($transaction->reservation->preferred_date)->isoFormat('D MMM Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-medium">Status Reservasi</span>
                        <span class="text-emerald-700 font-bold">{{ $transaction->reservation->status }}</span>
                    </div>
                </div>
            </div>
            @endif

            {{-- Info Reward --}}
            <div class="glass rounded-2xl p-5 bg-white border border-[#e8e3d5] shadow-sm">
                <h3 class="text-sm font-bold text-slate-900 mb-4">⭐ Poin Reward</h3>
                <div class="text-center py-2">
                    <p class="text-4xl font-extrabold text-[#C6A443]">+1</p>
                    <p class="text-xs text-slate-600 font-bold mt-1">poin diberikan ke pelanggan</p>
                    <p class="text-xs text-slate-500 mt-0.5">setelah transaksi selesai</p>
                </div>
            </div>

            {{-- Aksi Cepat --}}
            <div class="glass rounded-2xl p-5 bg-white border border-[#e8e3d5] shadow-sm">
                <h3 class="text-sm font-bold text-slate-900 mb-4">⚡ Aksi Cepat</h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.customers.show', $transaction->user) }}" class="flex items-center gap-3 p-3 rounded-xl text-sm font-bold text-slate-700 bg-slate-50 hover:bg-slate-100 transition border border-slate-200">
                        👤 Lihat Profil Pelanggan
                    </a>
                    <a href="{{ route('admin.transactions.create') }}" class="flex items-center gap-3 p-3 rounded-xl text-sm font-bold text-slate-700 bg-slate-50 hover:bg-slate-100 transition border border-slate-200">
                        ➕ Buat Transaksi Baru
                    </a>
                    <a href="{{ route('admin.transactions.index') }}" class="flex items-center gap-3 p-3 rounded-xl text-sm font-bold text-slate-700 bg-slate-50 hover:bg-slate-100 transition border border-slate-200">
                        📋 Semua Transaksi
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-admin-app>
