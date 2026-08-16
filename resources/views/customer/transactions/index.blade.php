<x-customer-app>
    <x-slot name="pageTitle">Riwayat Transaksi</x-slot>
    <x-slot name="breadcrumb">Daftar transaksi emas yang telah Anda lakukan di Sinar Baru II</x-slot>

    {{-- Summary Cards --}}
    <div class="grid md:grid-cols-3 gap-6 mb-8">
        <div class="glass rounded-2xl p-6 bg-white border border-[#e8e3d5]">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-2xl shrink-0">🧾</div>
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Transaksi</p>
                    <p class="text-2xl font-extrabold text-[#042623] mt-0.5">{{ $summary['total'] }}</p>
                </div>
            </div>
        </div>
        <div class="glass rounded-2xl p-6 bg-amber-50/60 border border-amber-200">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-100 border border-amber-300 flex items-center justify-center text-2xl shrink-0">🛒</div>
                <div>
                    <p class="text-xs font-bold text-amber-900 uppercase tracking-wider">Total Pembelian (Toko)</p>
                    <p class="text-xl font-extrabold text-[#C6A443] mt-0.5">Rp {{ number_format($summary['purchase'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="glass rounded-2xl p-6 bg-emerald-50/60 border border-emerald-200">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 border border-emerald-300 flex items-center justify-center text-2xl shrink-0">💰</div>
                <div>
                    <p class="text-xs font-bold text-emerald-900 uppercase tracking-wider">Total Penjualan (Toko Beli)</p>
                    <p class="text-xl font-extrabold text-[#085C54] mt-0.5">Rp {{ number_format($summary['income'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="glass rounded-2xl p-4 mb-6 bg-white border border-[#e8e3d5]">
        <form method="GET" action="{{ route('customer.transactions.index') }}" class="flex items-center gap-4">
            <select name="type" onchange="this.form.submit()" class="rounded-xl px-4 py-2 text-sm text-slate-900 font-semibold bg-[#F4EDD9]/60 border border-[#e8e3d5] outline-none focus:ring-2 focus:ring-[#085C54]">
                <option value="" class="text-slate-900">Semua Jenis Transaksi</option>
                <option value="purchase" {{ request('type') == 'purchase' ? 'selected' : '' }} class="text-slate-900">Pembelian (Beli Emas)</option>
                <option value="buyback" {{ request('type') == 'buyback' ? 'selected' : '' }} class="text-slate-900">Penjualan (Jual Emas)</option>
                <option value="installment" {{ request('type') == 'installment' ? 'selected' : '' }} class="text-slate-900">Cicilan</option>
                <option value="pawn" {{ request('type') == 'pawn' ? 'selected' : '' }} class="text-slate-900">Gadai</option>
            </select>
            @if(request('type'))
            <a href="{{ route('customer.transactions.index') }}" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 transition">Reset Filter</a>
            @endif
        </form>
    </div>

    {{-- Daftar Transaksi --}}
    @if($transactions->isEmpty())
    <div class="glass rounded-3xl p-12 text-center mt-6 bg-white border border-[#e8e3d5]">
        <span class="text-6xl">📭</span>
        <p class="text-slate-800 text-lg mt-4 font-bold">Belum Ada Transaksi</p>
        <p class="text-slate-600 text-sm mt-2">Riwayat transaksi Anda akan muncul di sini setelah Anda berbelanja di toko.</p>
        <a href="{{ route('customer.catalog.index') }}" class="mt-6 inline-block font-bold text-[#085C54] hover:underline">Lihat Katalog Produk →</a>
    </div>
    @else
    <div class="space-y-4 mb-8">
        @foreach($transactions as $tx)
        @php
            $typeInfo = [
                'purchase'    => ['icon'=>'🛒', 'label'=>'Beli Emas', 'badgeBg'=>'#fef3c7', 'badgeText'=>'#92400e', 'badgeBorder'=>'#fde68a'],
                'buyback'     => ['icon'=>'💰', 'label'=>'Jual Emas', 'badgeBg'=>'#d1fae5', 'badgeText'=>'#065f46', 'badgeBorder'=>'#a7f3d0'],
                'installment' => ['icon'=>'📅', 'label'=>'Cicilan',   'badgeBg'=>'#dbeafe', 'badgeText'=>'#1e40af', 'badgeBorder'=>'#bfdbfe'],
                'pawn'        => ['icon'=>'🏦', 'label'=>'Gadai',     'badgeBg'=>'#f3e8ff', 'badgeText'=>'#6b21a8', 'badgeBorder'=>'#e9d5ff'],
            ][$tx->type] ?? ['icon'=>'📄', 'label'=>'Transaksi', 'badgeBg'=>'#f1f5f9', 'badgeText'=>'#334155', 'badgeBorder'=>'#e2e8f0'];

            $statusInfo = [
                'pending'     => ['label'=>'Pending',   'class'=>'bg-amber-100 text-amber-800 border-amber-300'],
                'in_progress' => ['label'=>'Berjalan',  'class'=>'bg-blue-100 text-blue-800 border-blue-300'],
                'completed'   => ['label'=>'Selesai',   'class'=>'bg-emerald-100 text-emerald-800 border-emerald-300'],
                'cancelled'   => ['label'=>'Batal',     'class'=>'bg-red-100 text-red-800 border-red-300'],
            ][$tx->status] ?? ['label'=>$tx->status, 'class'=>'bg-slate-100 text-slate-800 border-slate-300'];
        @endphp

        <div class="glass rounded-2xl p-5 bg-white border border-[#e8e3d5] hover:border-[#085C54]/40 transition-all flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl shrink-0 border"
                     style="background:{{ $typeInfo['badgeBg'] }}; border-color:{{ $typeInfo['badgeBorder'] }};">
                    {{ $typeInfo['icon'] }}
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <h3 class="font-bold text-slate-900 text-base">{{ $typeInfo['label'] }}</h3>
                        <span class="text-[10px] px-2.5 py-0.5 rounded-full font-bold uppercase border tracking-wider {{ $statusInfo['class'] }}">{{ $statusInfo['label'] }}</span>
                    </div>
                    <p class="text-xs font-semibold text-slate-500">
                        <span class="font-mono text-slate-700 font-bold">{{ $tx->transaction_code }}</span> • 
                        {{ $tx->created_at->isoFormat('D MMM Y, HH:mm') }} WIB
                    </p>
                    <p class="text-sm font-semibold text-slate-800 mt-1">
                        @if($tx->items->count() > 0)
                            {{ $tx->items->first()->product_name ?? ($tx->items->first()->product->name ?? 'Produk') }} 
                            @if($tx->items->count() > 1) <span class="text-slate-500 font-normal">(+{{ $tx->items->count() - 1 }} item lainnya)</span> @endif
                        @else
                            <span class="text-slate-600 font-normal">Transaksi Tanpa Rincian Produk</span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="flex md:flex-col items-center md:items-end justify-between md:justify-center w-full md:w-auto mt-4 md:mt-0 pt-4 md:pt-0 border-t border-slate-100 md:border-0">
                <div class="text-left md:text-right">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Transaksi</p>
                    <p class="text-xl font-extrabold {{ $tx->type == 'buyback' ? 'text-[#085C54]' : 'text-[#042623]' }}">
                        {{ $tx->type == 'buyback' ? '+' : '' }} Rp {{ number_format($tx->total_amount, 0, ',', '.') }}
                    </p>
                </div>
                <a href="{{ route('customer.transactions.show', $tx) }}" class="text-xs font-bold px-4 py-2 rounded-xl text-[#085C54] bg-[#e2f2f0] hover:bg-[#c9e8e4] transition whitespace-nowrap mt-2 border border-[#085C54]/20 shadow-sm">
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
