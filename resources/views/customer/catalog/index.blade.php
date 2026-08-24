<x-customer-app>
    <x-slot name="pageTitle">Katalog Produk Emas</x-slot>
    <x-slot name="breadcrumb">Temukan koleksi perhiasan emas murni 24K terbaik Toko Sinar Baru II</x-slot>

    {{-- Harga Emas Aktif --}}
    @if($goldPrice)
    <div class="glass rounded-2xl p-4 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white border border-[#e8e3d5] shadow-sm">
        <div class="flex items-center gap-4">
            <span class="text-2xl">💰</span>
            <div>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Harga Emas Hari Ini ({{ now()->isoFormat('D MMM Y') }})</p>
                <p class="text-sm text-slate-900 font-extrabold mt-0.5">
                    Beli: <span class="text-emerald-700">Rp {{ number_format($goldPrice->buy_price_per_gram, 0, ',', '.') }}</span>/gram
                    &nbsp;•&nbsp;
                    Jual: <span class="text-[#C6A443]">Rp {{ number_format($goldPrice->sell_price_per_gram, 0, ',', '.') }}</span>/gram
                </p>
            </div>
        </div>
        <span class="text-xs text-slate-500 font-semibold self-start sm:self-center">📡 Sumber: {{ $goldPrice->source }}</span>
    </div>
    @endif

    {{-- Category Pills Filter --}}
    <div class="flex flex-wrap items-center gap-2 mb-4">
        @php
            $currentCat = request('category');
        @endphp
        <a href="{{ route('customer.catalog.index', array_merge(request()->query(), ['category' => 'all'])) }}"
           class="px-4 py-2 rounded-xl text-xs sm:text-sm transition-all font-bold {{ !$currentCat || $currentCat === 'all' ? 'gold-gradient text-[#042623] shadow-md border border-[#C6A443]' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
            📦 Semua Produk
        </a>
        @foreach($categories as $cat)
        <a href="{{ route('customer.catalog.index', array_merge(request()->query(), ['category' => $cat->slug])) }}"
           class="px-4 py-2 rounded-xl text-xs sm:text-sm transition-all font-bold {{ $currentCat === $cat->slug ? 'gold-gradient text-[#042623] shadow-md border border-[#C6A443]' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
            {{ $cat->icon ?? '✨' }} {{ $cat->name }}
        </a>
        @endforeach
    </div>

    {{-- Filter & Search --}}
    <form method="GET" action="{{ route('customer.catalog.index') }}" class="flex gap-3 mb-6">
        @if(request('category'))
        <input type="hidden" name="category" value="{{ request('category') }}">
        @endif
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari kalung, gelang, cincin emas..."
               class="flex-1 input-field">
        <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-extrabold text-[#042623] gold-gradient border border-[#C6A443] shadow-md hover:brightness-110 transition">
            🔍 Cari
        </button>
        @if(request('search') || (request('category') && request('category') !== 'all'))
        <a href="{{ route('customer.catalog.index') }}" class="px-4 py-2.5 rounded-xl text-sm font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition flex items-center">
            Reset
        </a>
        @endif
    </form>

    {{-- Produk Grid --}}
    @if($products->isEmpty())
    <div class="text-center py-20 bg-white rounded-3xl border border-[#e8e3d5] shadow-sm">
        <span class="text-6xl">🔍</span>
        <p class="text-slate-900 text-lg font-bold mt-4">Produk tidak ditemukan</p>
        <p class="text-slate-600 text-sm mt-1">Coba kata kunci lain atau pilih kategori lain.</p>
        <a href="{{ route('customer.catalog.index') }}" class="mt-4 inline-block font-bold text-[#085C54] hover:underline text-sm">Lihat Semua Produk →</a>
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
        <div class="glass rounded-2xl overflow-hidden hover:scale-105 transition-all group bg-white border border-[#e8e3d5] shadow-sm flex flex-col justify-between"
             style="transition: all 0.3s ease;">
            <div>
                <div class="h-36 flex items-center justify-center flex-col gap-2 relative bg-[#F4EDD9]/40 border-b border-[#e8e3d5]">
                    @if($product->thumbnail_url)
                    <img src="{{ $product->thumbnail_url }}" class="h-28 w-28 object-contain group-hover:scale-110 transition-transform duration-300">
                    @else
                    <span class="text-4xl group-hover:scale-110 transition-transform">{{ $icon }}</span>
                    @endif
                    @if($product->stock <= 0)
                    <span class="text-xs px-2.5 py-0.5 rounded-full font-bold bg-red-100 text-red-800 border border-red-200 absolute top-2 right-2">Stok Habis</span>
                    @endif
                </div>
                <div class="p-4">
                    <span class="text-[10px] px-2.5 py-0.5 rounded-full font-extrabold uppercase bg-amber-100 text-amber-900 border border-amber-300">
                        {{ $product->gold_purity }} Murni
                    </span>
                    <h3 class="font-bold mt-2 text-sm text-slate-900 line-clamp-2">{{ $product->name }}</h3>
                    <p class="text-xs font-semibold text-slate-500 mt-1">{{ number_format($product->weight_gram, 3) }} gram</p>
                    <div class="mt-3 pt-3 border-t border-slate-100">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Harga Jual</p>
                        <p class="text-base font-extrabold text-[#C6A443]">Rp {{ number_format($harga, 0, ',', '.') }}</p>
                        @if($goldPrice)
                        <p class="text-[11px] text-emerald-800 font-semibold mt-0.5">Buyback: Rp {{ number_format($goldPrice->buy_price_per_gram * $product->weight_gram, 0, ',', '.') }}</p>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="p-4 pt-0">
                <div class="flex justify-between items-center text-xs text-slate-500 font-medium mb-2">
                    <span>Stok: <strong class="text-slate-800">{{ $product->stock }}</strong></span>
                </div>
                @if($product->is_available && $product->stock > 0)
                <div class="grid grid-cols-2 gap-1.5">
                    <a href="{{ route('customer.negotiations.create', ['product_id' => $product->id]) }}"
                       class="text-center text-[11px] font-bold px-2 py-2 rounded-xl text-slate-800 bg-slate-100 hover:bg-slate-200 transition border border-slate-300 shadow-sm">
                        🤝 Tawar
                    </a>
                    <a href="{{ route('customer.reservations.create', ['product_id' => $product->id]) }}"
                       class="text-center text-[11px] font-extrabold px-2 py-2 rounded-xl text-[#042623] gold-gradient border border-[#C6A443] shadow-sm hover:brightness-110 transition">
                        Reservasi →
                    </a>
                </div>
                @else
                <span class="text-xs font-semibold text-slate-400 block text-center py-1">Tidak tersedia</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    <div class="flex justify-center">
        {{ $products->links() }}
    </div>
    @endif

    {{-- Info O2O --}}
    <div class="glass rounded-2xl p-6 mt-8 bg-white border border-[#e8e3d5] shadow-sm">
        <h3 class="font-bold text-slate-900 font-playfair mb-4 text-center text-base">📋 Cara Berbelanja di Toko Emas Sinar Baru II</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-center">
            @foreach([['1','Pilih & Reservasi','Pilih produk perhiasan emas idaman Anda, lalu klik Reservasi untuk memilih jadwal kunjungan.'],['2','Tunggu Konfirmasi','Admin akan mengonfirmasi jadwal & menyiapkan produk dalam 1×24 jam.'],['3','Datang ke Toko','Kunjungi toko kami di Teluk Lubuk untuk cek fisik emas, timbang transparan, & selesaikan pembayaran.']] as [$num,$title,$desc])
            <div class="flex flex-col items-center gap-3">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-extrabold text-[#042623] text-sm gold-gradient border border-[#C6A443] shadow-md">{{ $num }}</div>
                <p class="font-bold text-sm text-slate-900">{{ $title }}</p>
                <p class="text-xs text-slate-600 leading-relaxed">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</x-customer-app>

