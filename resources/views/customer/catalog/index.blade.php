<x-customer-app>
    <x-slot name="pageTitle">Katalog Produk Emas</x-slot>
    <x-slot name="breadcrumb">Temukan emas terbaik untuk investasi Anda</x-slot>

    {{-- Harga Emas Aktif --}}
    @if($goldPrice)
    <div class="glass rounded-2xl p-4 mb-6 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <span class="text-2xl">💰</span>
            <div>
                <p class="text-xs text-gray-400">Harga Emas Hari Ini ({{ now()->isoFormat('D MMM Y') }})</p>
                <p class="text-sm text-white font-semibold">
                    Beli: <span class="text-green-400">Rp {{ number_format($goldPrice->buy_price_per_gram, 0, ',', '.') }}</span>/gram
                    &nbsp;•&nbsp;
                    Jual: <span class="text-yellow-400">Rp {{ number_format($goldPrice->sell_price_per_gram, 0, ',', '.') }}</span>/gram
                </p>
            </div>
        </div>
        <span class="text-xs text-gray-500">📡 {{ $goldPrice->source }}</span>
    </div>
    @endif

    {{-- Filter & Search --}}
    <form method="GET" action="{{ route('customer.catalog.index') }}" class="flex gap-3 mb-6">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari produk emas..."
               class="flex-1 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-500 outline-none"
               style="background:rgba(255,255,255,0.04); border:1px solid rgba(245,158,11,0.15);">
        <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white"
                style="background:linear-gradient(135deg,#f59e0b,#d97706,#92400e);">
            🔍 Cari
        </button>
        @if(request('search'))
        <a href="{{ route('customer.catalog.index') }}" class="px-4 py-2.5 rounded-xl text-sm text-gray-400 glass hover:bg-white/10 transition">
            Reset
        </a>
        @endif
    </form>

    {{-- Produk Grid --}}
    @if($products->isEmpty())
    <div class="text-center py-20">
        <span class="text-6xl">🔍</span>
        <p class="text-gray-400 text-lg mt-4">Produk tidak ditemukan</p>
        <a href="{{ route('customer.catalog.index') }}" class="mt-4 inline-block text-yellow-400 hover:underline text-sm">Lihat Semua →</a>
    </div>
    @else
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-5 mb-8">
        @php $icons = ['🪙','🥇','⭐','✨','💫']; @endphp
        @foreach($products as $product)
        @php
            $icon  = $icons[$loop->index % count($icons)];
            $harga = $goldPrice
                ? round($goldPrice->sell_price_per_gram * $product->weight_gram, -3)
                : $product->base_price;
        @endphp
        <div class="glass rounded-2xl overflow-hidden hover:scale-105 transition-all group"
             style="transition: all 0.3s ease;">
            <div class="h-36 flex items-center justify-center flex-col gap-2"
                 style="background:linear-gradient(135deg, rgba(124,45,18,0.5), rgba(194,65,12,0.2));">
                @if($product->thumbnail)
                <img src="{{ asset($product->thumbnail) }}" class="h-28 w-28 object-contain group-hover:scale-110 transition-transform duration-300">
                @else
                <span class="text-4xl group-hover:scale-110 transition-transform">{{ $icon }}</span>
                @endif
                @if($product->stock <= 0)
                <span class="text-xs px-2 py-0.5 rounded-full" style="background:rgba(239,68,68,0.2); color:#f87171;">Stok Habis</span>
                @endif
            </div>
            <div class="p-4">
                <span class="text-xs px-2 py-0.5 rounded-full" style="background:rgba(245,158,11,0.1); color:#f59e0b;">
                    {{ $product->gold_purity }} Murni
                </span>
                <h3 class="font-semibold mt-2 text-sm text-white">{{ $product->name }}</h3>
                <p class="text-xs text-gray-400 mt-1">{{ number_format($product->weight_gram, 3) }} gram</p>
                <div class="mt-3 pt-3" style="border-top:1px solid rgba(245,158,11,0.1);">
                    <p class="text-xs text-gray-500">Harga Jual</p>
                    <p class="text-base font-bold text-yellow-400">Rp {{ number_format($harga, 0, ',', '.') }}</p>
                    @if($goldPrice)
                    <p class="text-xs text-gray-600 mt-0.5">Buyback: Rp {{ number_format($goldPrice->buy_price_per_gram * $product->weight_gram, 0, ',', '.') }}</p>
                    @endif
                </div>
                <div class="mt-3 flex justify-between items-center">
                    <span class="text-xs text-gray-500">Stok: {{ $product->stock }}</span>
                    @if($product->is_available && $product->stock > 0)
                    <a href="{{ route('customer.reservations.create', ['product_id' => $product->id]) }}"
                       class="text-xs font-semibold px-3 py-1.5 rounded-lg text-white transition"
                       style="background:linear-gradient(135deg,#f59e0b,#d97706);">
                        Reservasi →
                    </a>
                    @else
                    <span class="text-xs text-gray-600">Tidak tersedia</span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    {{ $products->links() }}
    @endif

    {{-- Info O2O --}}
    <div class="glass rounded-2xl p-6 mt-8">
        <h3 class="font-semibold text-yellow-400 mb-4 text-center">📋 Cara Berbelanja di Sinar Baru II</h3>
        <div class="grid grid-cols-3 gap-6 text-center">
            @foreach([['1','Pilih & Reservasi','Pilih produk, klik Reservasi untuk mendaftarkan minat Anda.'],['2','Tunggu Konfirmasi','Admin akan konfirmasi reservasi dalam 1×24 jam.'],['3','Datang ke Toko','Selesaikan transaksi langsung di toko dengan referensi reservasi.']] as [$num,$title,$desc])
            <div class="flex flex-col items-center gap-3">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-white text-sm"
                     style="background:linear-gradient(135deg,#f59e0b,#d97706,#92400e);">{{ $num }}</div>
                <p class="font-semibold text-sm">{{ $title }}</p>
                <p class="text-xs text-gray-400">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</x-customer-app>
