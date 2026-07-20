<x-customer-app>
    <x-slot name="pageTitle">Riwayat Transaksi</x-slot>
    <x-slot name="breadcrumb">Daftar transaksi emas yang telah Anda lakukan di Sinar Baru II</x-slot>

    {{-- Summary Cards --}}
    <div class="grid md:grid-cols-3 gap-6 mb-8">
        <div class="glass rounded-2xl p-6" style="border-color:rgba(255,255,255,0.05);">
            <div class="flex items-center gap-4 mb-2">
                <span class="text-3xl">🧾</span>
                <div>
                    <p class="text-xs text-gray-400">Total Transaksi</p>
                    <p class="text-2xl font-bold text-white">{{ $summary['total'] }}</p>
                </div>
            </div>
        </div>
        <div class="glass rounded-2xl p-6" style="background:rgba(245,158,11,0.05); border-color:rgba(245,158,11,0.2);">
            <div class="flex items-center gap-4 mb-2">
                <span class="text-3xl">🛒</span>
                <div>
                    <p class="text-xs text-gray-400">Total Pembelian (Toko)</p>
                    <p class="text-xl font-bold text-yellow-400">Rp {{ number_format($summary['purchase'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="glass rounded-2xl p-6" style="background:rgba(16,185,129,0.05); border-color:rgba(16,185,129,0.2);">
            <div class="flex items-center gap-4 mb-2">
                <span class="text-3xl">💰</span>
                <div>
                    <p class="text-xs text-gray-400">Total Penjualan (Toko Beli)</p>
                    <p class="text-xl font-bold text-green-400">Rp {{ number_format($summary['income'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="glass rounded-2xl p-4 mb-6">
        <form method="GET" action="{{ route('customer.transactions.index') }}" class="flex gap-4">
            <select name="type" onchange="this.form.submit()" class="rounded-xl px-4 py-2 text-sm text-white outline-none focus:ring-2"
                    style="background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); --tw-ring-color:#f59e0b;">
                <option value="" class="text-gray-900">Semua Jenis Transaksi</option>
                <option value="purchase" {{ request('type') == 'purchase' ? 'selected' : '' }} class="text-gray-900">Pembelian (Beli Emas)</option>
                <option value="buyback" {{ request('type') == 'buyback' ? 'selected' : '' }} class="text-gray-900">Penjualan (Jual Emas)</option>
                <option value="installment" {{ request('type') == 'installment' ? 'selected' : '' }} class="text-gray-900">Cicilan</option>
                <option value="pawn" {{ request('type') == 'pawn' ? 'selected' : '' }} class="text-gray-900">Gadai</option>
            </select>
            @if(request('type'))
            <a href="{{ route('customer.transactions.index') }}" class="px-4 py-2 rounded-xl text-sm text-gray-400 hover:text-white glass transition">Reset</a>
            @endif
        </form>
    </div>

    {{-- Daftar Transaksi --}}
    @if($transactions->isEmpty())
    <div class="glass rounded-3xl p-12 text-center mt-6">
        <span class="text-6xl">📭</span>
        <p class="text-gray-400 text-lg mt-4 font-semibold">Belum Ada Transaksi</p>
        <p class="text-gray-500 text-sm mt-2">Riwayat transaksi Anda akan muncul di sini setelah Anda berbelanja di toko.</p>
        <a href="{{ route('customer.catalog.index') }}" class="mt-6 inline-block text-yellow-400 hover:underline">Lihat Katalog Produk →</a>
    </div>
    @else
    <div class="space-y-4 mb-8">
        @foreach($transactions as $tx)
        @php
            $typeInfo = [
                'purchase'    => ['icon'=>'🛒', 'label'=>'Beli Emas', 'color'=>'text-yellow-400', 'bg'=>'rgba(245,158,11,0.1)'],
                'buyback'     => ['icon'=>'💰', 'label'=>'Jual Emas', 'color'=>'text-green-400', 'bg'=>'rgba(16,185,129,0.1)'],
                'installment' => ['icon'=>'📅', 'label'=>'Cicilan', 'color'=>'text-blue-400', 'bg'=>'rgba(59,130,246,0.1)'],
                'pawn'        => ['icon'=>'🏦', 'label'=>'Gadai', 'color'=>'text-purple-400', 'bg'=>'rgba(168,85,247,0.1)'],
            ][$tx->type] ?? ['icon'=>'📄', 'label'=>'Transaksi', 'color'=>'text-gray-400', 'bg'=>'rgba(156,163,175,0.1)'];

            $statusInfo = [
                'pending'     => ['label'=>'Pending', 'class'=>'text-yellow-400 bg-yellow-900/40'],
                'in_progress' => ['label'=>'Berjalan', 'class'=>'text-blue-400 bg-blue-900/40'],
                'completed'   => ['label'=>'Selesai', 'class'=>'text-green-400 bg-green-900/40'],
                'cancelled'   => ['label'=>'Batal', 'class'=>'text-red-400 bg-red-900/40'],
            ][$tx->status] ?? ['label'=>$tx->status, 'class'=>'text-gray-400 bg-gray-900/40'];
        @endphp

        <div class="glass rounded-2xl p-5 hover:bg-white/5 transition-all group flex flex-col md:flex-row md:items-center justify-between gap-4" style="border-color:rgba(255,255,255,0.05);">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl shrink-0" style="background:{{ $typeInfo['bg'] }};">
                    {{ $typeInfo['icon'] }}
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <h3 class="font-bold text-white {{ $typeInfo['color'] }}">{{ $typeInfo['label'] }}</h3>
                        <span class="text-[10px] px-2 py-0.5 rounded-full uppercase tracking-wider {{ $statusInfo['class'] }}">{{ $statusInfo['label'] }}</span>
                    </div>
                    <p class="text-xs text-gray-400">
                        {{ $tx->transaction_code }} • 
                        {{ $tx->created_at->isoFormat('D MMM Y, H:mm') }} WIB
                    </p>
                    <p class="text-sm text-gray-300 mt-1">
                        @if($tx->items->count() > 0)
                            {{ $tx->items->first()->product->name ?? 'Produk' }} 
                            @if($tx->items->count() > 1) dkk (+{{ $tx->items->count() - 1 }} item) @endif
                        @else
                            Transaksi tanpa rincian produk
                        @endif
                    </p>
                </div>
            </div>

            <div class="flex md:flex-col items-center md:items-end justify-between md:justify-center w-full md:w-auto mt-4 md:mt-0 pt-4 md:pt-0 border-t border-gray-800 md:border-0">
                <div class="text-left md:text-right">
                    <p class="text-xs text-gray-500">Total Transaksi</p>
                    <p class="text-lg font-bold {{ $tx->type == 'buyback' ? 'text-green-400' : 'text-white' }}">
                        {{ $tx->type == 'buyback' ? '+' : '' }} Rp {{ number_format($tx->total_amount, 0, ',', '.') }}
                    </p>
                </div>
                <a href="{{ route('customer.transactions.show', $tx) }}" class="text-xs px-4 py-2 rounded-lg glass text-gray-300 hover:text-white transition whitespace-nowrap mt-2">
                    Detail Invoice →
                </a>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="flex justify-center">
        {{ $transactions->links() }}
    </div>
    @endif
</x-customer-app>
