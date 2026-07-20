<x-admin-app>
    <x-slot name="styles">
        <style>
            @media print {
                /* Sembunyikan sidebar, topbar, tombol print, dan sidebar info */
                aside#sidebar,
                main > div.flex.justify-between.items-center.mb-8,
                .px-4.py-2.rounded-lg.text-sm.text-gray-300.glass,
                .px-8.py-4.flex.justify-end,
                .space-y-5,
                .mb-6 {
                    display: none !important;
                }

                /* Atur area main untuk full print */
                main {
                    margin-left: 0 !important;
                    padding: 0 !important;
                    background: #ffffff !important;
                    color: #000000 !important;
                }

                /* Desain Invoice Bersih Minimalis */
                .glass {
                    border: 1px solid #cccccc !important;
                    background: #ffffff !important;
                    box-shadow: none !important;
                    color: #000000 !important;
                    border-radius: 0.5rem !important;
                }

                .lg\:col-span-2 {
                    width: 100% !important;
                    max-width: 100% !important;
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

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold font-playfair text-white">Detail Transaksi</h1>
            <p class="text-sm text-gray-400">Invoice #{{ $transaction->transaction_code }}</p>
        </div>
        <a href="{{ route('admin.transactions.index') }}" class="px-4 py-2 rounded-lg text-sm text-gray-300 glass hover:bg-white/10 transition">
            ← Kembali
        </a>
    </div>

    @php
        $typeMap = [
            'purchase'    => ['label'=>'Pembelian Emas', 'icon'=>'🛒', 'color'=>'#f59e0b'],
            'buyback'     => ['label'=>'Penjualan (Buyback)', 'icon'=>'💰', 'color'=>'#34d399'],
            'installment' => ['label'=>'Cicilan', 'icon'=>'📅', 'color'=>'#60a5fa'],
            'pawn'        => ['label'=>'Gadai', 'icon'=>'🏦', 'color'=>'#c084fc'],
        ];
        $statusMap = [
            'pending'     => ['label'=>'Pending', 'class'=>'bg-yellow-900/40 text-yellow-400 border-yellow-700/50'],
            'in_progress' => ['label'=>'Berjalan', 'class'=>'bg-blue-900/40 text-blue-400 border-blue-700/50'],
            'completed'   => ['label'=>'Selesai', 'class'=>'bg-green-900/40 text-green-400 border-green-700/50'],
            'cancelled'   => ['label'=>'Dibatalkan', 'class'=>'bg-red-900/40 text-red-400 border-red-700/50'],
        ];
        $type   = $typeMap[$transaction->type]   ?? ['label'=>$transaction->type, 'icon'=>'📄', 'color'=>'#9ca3af'];
        $status = $statusMap[$transaction->status] ?? ['label'=>$transaction->status, 'class'=>'bg-gray-900/40 text-gray-400'];
    @endphp

    <div class="grid lg:grid-cols-3 gap-6">

        {{-- Invoice Panel (Main) --}}
        <div class="lg:col-span-2 glass rounded-3xl overflow-hidden" style="border-color:rgba(255,255,255,0.08);">

            {{-- Header Invoice --}}
            <div class="px-8 py-6 flex justify-between items-start" style="background:linear-gradient(135deg,rgba(245,158,11,0.08),rgba(0,0,0,0.4)); border-bottom:1px solid rgba(255,255,255,0.08);">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-2xl">{{ $type['icon'] }}</span>
                        <span class="font-bold" style="color:{{ $type['color'] }}">{{ $type['label'] }}</span>
                    </div>
                    <p class="font-mono text-sm text-gray-400">{{ $transaction->transaction_code }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $transaction->created_at->isoFormat('D MMMM Y, H:mm') }} WIB</p>
                </div>
                <span class="px-3 py-1.5 rounded-full text-xs font-semibold border {{ $status['class'] }}">
                    {{ $status['label'] }}
                </span>
            </div>

            {{-- Info Pelanggan & Toko --}}
            <div class="px-8 py-6 grid grid-cols-2 gap-6" style="border-bottom:1px solid rgba(255,255,255,0.06);">
                <div>
                    <p class="text-xs uppercase tracking-widest text-gray-500 mb-2">Pelanggan</p>
                    <p class="font-bold text-white">{{ $transaction->user->name }}</p>
                    <p class="text-sm text-gray-400">{{ $transaction->user->email }}</p>
                    <p class="text-sm text-gray-400">{{ $transaction->user->profile->phone ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-widest text-gray-500 mb-2">Diproses Oleh</p>
                    <p class="font-bold text-white">
                        {{ $transaction->processedBy->name ?? 'Admin' }}
                    </p>
                    <p class="text-sm text-gray-400 mt-2">Metode Bayar</p>
                    <p class="text-sm font-semibold text-white uppercase">{{ $transaction->payment_method }}</p>
                    <p class="text-sm text-gray-400 mt-1">Tgl Bayar: {{ $transaction->payment_date ? \Carbon\Carbon::parse($transaction->payment_date)->isoFormat('D MMM Y') : '-' }}</p>
                </div>
            </div>

            {{-- Tabel Item --}}
            <div class="px-8 py-6">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-xs text-gray-500 uppercase tracking-wider" style="border-bottom:1px solid rgba(255,255,255,0.08);">
                            <th class="py-3">Produk</th>
                            <th class="py-3 text-center">Harga/Unit</th>
                            <th class="py-3 text-center">Qty</th>
                            <th class="py-3 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaction->items as $item)
                        <tr style="border-bottom:1px solid rgba(255,255,255,0.04);">
                            <td class="py-4">
                                <p class="font-semibold text-white text-sm">{{ $item->product_name }}</p>
                                <p class="text-xs text-gray-500">{{ $item->gold_purity ?? '' }} • {{ number_format($item->weight_gram ?? 0, 3) }} gram</p>
                            </td>
                            <td class="py-4 text-center text-sm text-gray-300">
                                Rp {{ number_format($item->price_per_unit, 0, ',', '.') }}
                            </td>
                            <td class="py-4 text-center text-sm text-gray-300">
                                {{ $item->quantity }}
                            </td>
                            <td class="py-4 text-right text-sm font-bold text-yellow-400">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Total Breakdown --}}
                <div class="mt-6 flex justify-end">
                    <div class="w-72 space-y-2">
                        <div class="flex justify-between text-sm text-gray-400">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($transaction->subtotal ?? $transaction->total_amount, 0, ',', '.') }}</span>
                        </div>
                        @if(($transaction->admin_fee ?? 0) > 0)
                        <div class="flex justify-between text-sm text-gray-400">
                            <span>Biaya Admin</span>
                            <span>Rp {{ number_format($transaction->admin_fee, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        @if(($transaction->discount ?? 0) > 0)
                        <div class="flex justify-between text-sm text-green-400">
                            <span>Diskon</span>
                            <span>- Rp {{ number_format($transaction->discount, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between text-lg font-bold pt-3" style="border-top:1px solid rgba(255,255,255,0.1);">
                            <span class="text-white">Total Akhir</span>
                            <span style="color:{{ $type['color'] }}">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                @if($transaction->notes)
                <div class="mt-6 p-4 rounded-xl text-sm" style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08);">
                    <p class="text-xs text-gray-500 mb-1">Catatan:</p>
                    <p class="text-gray-300">{{ $transaction->notes }}</p>
                </div>
                @endif
            </div>

            {{-- Footer Print --}}
            <div class="px-8 py-4 flex justify-end" style="background:rgba(0,0,0,0.2); border-top:1px solid rgba(255,255,255,0.06);">
                <button onclick="window.print()" class="px-6 py-2 rounded-xl text-sm font-bold text-white transition hover:scale-105"
                        style="background:linear-gradient(135deg,#f59e0b,#d97706);">
                    🖨️ Cetak Invoice
                </button>
            </div>
        </div>

        {{-- Sidebar Info --}}
        <div class="space-y-5">

            {{-- Reservasi Terkait --}}
            @if($transaction->reservation)
            <div class="glass rounded-2xl p-5" style="border-color:rgba(255,255,255,0.06);">
                <h3 class="text-sm font-semibold text-yellow-400 mb-4">📋 Reservasi Terkait</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Kode</span>
                        <span class="font-mono text-xs text-gray-300">{{ $transaction->reservation->reservation_code }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Tgl Kunjungan</span>
                        <span class="text-gray-300">{{ \Carbon\Carbon::parse($transaction->reservation->preferred_date)->isoFormat('D MMM Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Status Reservasi</span>
                        <span class="text-green-400 font-semibold">{{ $transaction->reservation->status }}</span>
                    </div>
                </div>
            </div>
            @endif

            {{-- Info Reward --}}
            <div class="glass rounded-2xl p-5" style="border-color:rgba(255,255,255,0.06);">
                <h3 class="text-sm font-semibold text-yellow-400 mb-4">⭐ Poin Reward</h3>
                <div class="text-center py-2">
                    <p class="text-4xl font-bold text-yellow-400">+1</p>
                    <p class="text-xs text-gray-500 mt-1">poin diberikan ke pelanggan</p>
                    <p class="text-xs text-gray-600 mt-1">setelah transaksi selesai</p>
                </div>
            </div>

            {{-- Aksi Cepat --}}
            <div class="glass rounded-2xl p-5" style="border-color:rgba(255,255,255,0.06);">
                <h3 class="text-sm font-semibold text-gray-400 mb-4">⚡ Aksi Cepat</h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.customers.show', $transaction->user) }}" class="flex items-center gap-3 p-3 rounded-xl text-sm text-gray-300 hover:bg-white/5 transition">
                        👤 Lihat Profil Pelanggan
                    </a>
                    <a href="{{ route('admin.transactions.create') }}" class="flex items-center gap-3 p-3 rounded-xl text-sm text-gray-300 hover:bg-white/5 transition">
                        ➕ Buat Transaksi Baru
                    </a>
                    <a href="{{ route('admin.transactions.index') }}" class="flex items-center gap-3 p-3 rounded-xl text-sm text-gray-300 hover:bg-white/5 transition">
                        📋 Semua Transaksi
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-admin-app>
