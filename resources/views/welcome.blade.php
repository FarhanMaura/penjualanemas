<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Emas Sinar Baru II — Investasi Emas Lebih Mudah & Terpercaya</title>
    <meta name="description" content="Toko emas terpercaya sejak 1995. Temukan perhiasan emas pilihan, pantau harga real-time, simulasikan cicilan, dan nikmati program loyalitas eksklusif.">

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/css/landing.css', 'resources/js/landing.js'])
</head>
<body class="text-slate-800 font-sans antialiased bg-[#F4EDD9]">

    {{-- NAVBAR (EmasKITA Emerald & Gold Style) --}}
    <nav class="fixed w-full z-50 public-nav">
        <div class="max-w-7xl mx-auto px-6 py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('logo.jpg') }}" alt="Logo Toko Emas Sinar Baru II" class="w-10 h-10 rounded-xl object-cover border border-[#C6A443] shadow-md">
                <div>
                    <p class="font-playfair font-bold text-[#E3D193] leading-none tracking-wide text-base">Sinar Baru II</p>
                    <p class="text-[11px] text-[#C6A443] font-semibold mt-0.5">Teluk Lubuk Muara Enim</p>
                    <p class="text-[10px] text-emerald-200/70 mt-0.5 font-medium">Toko Emas Terpercaya Sejak 1995</p>
                </div>
            </div>
            <div class="hidden md:flex items-center gap-8 text-sm text-emerald-50 font-medium">
                <a href="#" class="hover:text-[#E3D193] transition">Beranda</a>
                <a href="#produk" class="hover:text-[#E3D193] transition">Katalog</a>
                <a href="#harga" class="hover:text-[#E3D193] transition">Harga Emas</a>
                <a href="#layanan" class="hover:text-[#E3D193] transition">Layanan</a>
                <a href="#loyalitas" class="hover:text-[#E3D193] transition">Rewards</a>
            </div>
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('customer.dashboard') }}"
                       class="px-5 py-2.5 text-xs font-bold gold-gradient rounded-xl transition hover:opacity-90 shadow-md">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="px-4 py-2 text-xs text-[#E3D193] font-bold border border-[#C6A443]/60 rounded-xl bg-[#063e39]/60 hover:bg-[#063e39] transition shadow-sm">
                        Masuk
                    </a>
                    @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                       class="px-4 py-2 text-xs font-bold gold-gradient rounded-xl transition hover:opacity-90 shadow-md">
                        Daftar
                    </a>
                    @endif
                @endauth
            </div>
        </div>
    </nav>

    {{-- HERO (Clean Pearl & Emerald Gold Accent) --}}
    <section class="hero-bg min-h-screen flex items-center pt-28 pb-16 relative">
        <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-12 items-center relative z-10">
            <div>
                <span class="text-xs uppercase tracking-widest text-[#085C54] font-bold bg-[#F4EDD9] px-4 py-1.5 rounded-full border border-[#E3D193] shadow-sm inline-flex items-center gap-1.5">
                    <span>✨</span> Terpercaya Sejak 1995
                </span>
                <h1 class="font-playfair text-4xl md:text-6xl font-bold mt-5 leading-tight text-[#042623]">
                    Investasi Emas<br><span class="text-[#085C54]">Lebih Mudah,</span><br><span class="text-[#C6A443]">Lebih Cerdas</span>
                </h1>
                <p class="mt-6 text-slate-600 text-base leading-relaxed font-normal">
                    Temukan koleksi perhiasan emas murni pilihan, pantau harga pasaran real-time, simulasikan cicilan, dan nikmati program loyalitas eksklusif hanya untuk member Sinar Baru II.
                </p>
                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="#produk" class="px-7 py-3.5 bg-[#085C54] text-white hover:text-[#E3D193] rounded-xl font-bold shadow-xl hover:bg-[#063e39] transition hover:scale-105 inline-flex items-center gap-2 border border-[#063e39]">
                        <span>📦</span> Lihat Katalog Emas
                    </a>
                    <a href="#harga" class="px-7 py-3.5 bg-white text-[#085C54] font-bold border border-[#C6A443] rounded-xl hover:bg-[#F4EDD9] transition hover:scale-105 shadow-md">
                        💰 Harga Emas Hari Ini
                    </a>
                </div>
                <div class="mt-12 flex gap-10 border-t border-slate-200 pt-8">
                    <div>
                        <p class="text-3xl font-extrabold text-[#085C54] font-playfair">2.5K+</p>
                        <p class="text-xs text-slate-500 font-semibold mt-0.5">Pelanggan Aktif</p>
                    </div>
                    <div>
                        <p class="text-3xl font-extrabold text-[#085C54] font-playfair">500+</p>
                        <p class="text-xs text-slate-500 font-semibold mt-0.5">Produk Tersedia</p>
                    </div>
                    <div>
                        <p class="text-3xl font-extrabold text-[#C6A443] font-playfair">30 Th</p>
                        <p class="text-xs text-slate-500 font-semibold mt-0.5">Berpengalaman</p>
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan Hero: Visual Logo Showcase & Harga Emas --}}
            <div class="flex flex-col gap-6">
                {{-- Hero Banner Image (Logo) --}}
                <div class="bg-white rounded-3xl p-3 shadow-xl relative overflow-hidden border border-[#e8e3d5] group">
                    <div class="relative overflow-hidden rounded-2xl">
                        <img src="{{ asset('logo.jpg') }}" alt="Toko Emas Sinar Baru II"
                             class="w-full h-64 md:h-72 object-cover rounded-2xl shadow-md transition-transform duration-700 group-hover:scale-105">
                        <div class="absolute inset-0 bg-gradient-to-t from-[#042623]/90 via-[#042623]/30 to-transparent"></div>
                        <div class="absolute bottom-4 left-5 right-5 flex justify-between items-end">
                            <div>
                                <span class="text-[11px] font-bold text-[#E3D193] px-3 py-1 rounded-full bg-[#085C54]/90 border border-[#C6A443]/40 shadow-md">✨ Toko Emas Sinar Baru II</span>
                                <h3 class="text-lg md:text-xl font-bold text-white mt-2 font-playfair">Perhiasan & Investasi Emas Berkualitas</h3>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Harga Emas Card --}}
                <div id="harga" class="bg-white rounded-3xl p-6 shadow-xl border border-[#e8e3d5] relative overflow-hidden">
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">📈</span>
                            <h3 class="font-playfair text-lg font-bold text-[#042623]">Harga Emas Hari Ini</h3>
                        </div>
                        <span class="text-xs text-[#085C54] bg-[#F4EDD9] border border-[#E3D193] px-3 py-1 rounded-lg font-bold">{{ now()->isoFormat('D MMM Y') }}</span>
                    </div>
                    @if($goldPrice)
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="bg-emerald-50/90 rounded-2xl p-4 border border-emerald-200 shadow-sm">
                            <p class="text-xs text-emerald-800 font-semibold mb-1 flex items-center gap-1">
                                <span>🟢</span> Harga Beli (Toko Beli)
                            </p>
                            <p class="text-xl font-extrabold text-[#085C54]">Rp {{ number_format($goldPrice->buy_price_per_gram, 0, ',', '.') }}</p>
                            <p class="text-[11px] text-emerald-700 mt-0.5 font-semibold">per gram • 24K</p>
                        </div>
                        <div class="bg-[#F4EDD9] rounded-2xl p-4 border border-[#E3D193] shadow-sm">
                            <p class="text-xs text-[#866a20] font-semibold mb-1 flex items-center gap-1">
                                <span>🟡</span> Harga Jual (Toko Jual)
                            </p>
                            <p class="text-xl font-extrabold text-[#C6A443]">Rp {{ number_format($goldPrice->sell_price_per_gram, 0, ',', '.') }}</p>
                            <p class="text-[11px] text-[#866a20] mt-0.5 font-semibold">per gram • 24K</p>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 font-medium text-center">📡 Sumber: {{ $goldPrice->source }}</p>
                    @else
                    <div class="text-center py-6">
                        <p class="text-slate-600 font-medium text-sm">Harga sedang diperbarui...</p>
                        <p class="text-slate-500 text-xs mt-1">Hubungi toko untuk harga terkini</p>
                    </div>
                    @endif
                    <p class="text-[11px] text-slate-400 mt-3 text-center font-medium">Harga dapat berubah sewaktu-waktu • Datang ke toko untuk transaksi</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Wave Divider: Hero → Produk --}}
    <div class="relative overflow-hidden" style="height:64px; background:#F4EDD9; margin-top:-1px;">
        <svg viewBox="0 0 1440 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="absolute bottom-0 w-full" preserveAspectRatio="none">
            <path d="M0,32 C240,64 480,0 720,32 C960,64 1200,0 1440,32 L1440,64 L0,64 Z" fill="rgba(198,164,67,0.18)"/>
            <path d="M0,48 C360,16 720,64 1080,32 C1260,20 1380,48 1440,40 L1440,64 L0,64 Z" fill="rgba(8,92,84,0.10)"/>
        </svg>
        <div class="absolute inset-x-0 bottom-0 h-px" style="background: linear-gradient(90deg, transparent 0%, #C6A443 30%, #085C54 50%, #C6A443 70%, transparent 100%); opacity:0.5;"></div>
    </div>

    {{-- PRODUK UNGGULAN --}}
    <section id="produk" class="py-20 section-produk-bg relative">
        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="text-center mb-12">
                <p class="text-[#085C54] text-xs font-bold uppercase tracking-widest bg-[#F4EDD9] inline-block px-3.5 py-1.5 rounded-full border border-[#E3D193] shadow-sm">Koleksi Pilihan</p>
                <h2 class="font-playfair text-3xl md:text-4xl font-bold mt-3 text-[#042623]">Pilihan Emas <span class="text-[#C6A443]">Unggulan</span></h2>
                <div class="hr-goldline"></div>
                <p class="text-slate-600 text-sm mt-2 font-medium">Emas murni 24 karat dalam berbagai gramatur & model elegan • Klik produk untuk detail</p>

                {{-- Filter Kategori --}}
                <div class="flex flex-wrap justify-center items-center gap-2.5 mt-8">
                    <button onclick="filterCategory('all', this)"
                            data-default="true"
                            class="category-filter-btn px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all bg-[#085C54] text-[#E3D193] shadow-lg hover:scale-105 border border-[#063e39]">
                        📦 Semua Produk
                    </button>
                    @if(isset($categories))
                        @foreach($categories as $cat)
                        <button onclick="filterCategory('{{ $cat->slug }}', this)"
                                class="category-filter-btn px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all bg-white text-slate-700 hover:text-[#085C54] hover:bg-[#F4EDD9] border border-slate-200 shadow-sm hover:scale-105">
                            {{ $cat->icon ?? '✨' }} {{ $cat->name }}
                        </button>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-5 gap-6" id="product-grid">
                @foreach($products as $product)
                @php
                    $icons = ['🪙','🥇','⭐','✨','💫'];
                    $icon  = $icons[$loop->index % count($icons)];
                    $harga = $goldPrice ? number_format($goldPrice->sell_price_per_gram * $product->weight_gram, 0, ',', '.') : number_format($product->base_price, 0, ',', '.');
                @endphp
                <div onclick="openProductModal({{ $product->id }}, event)"
                     data-product-id="{{ $product->id }}"
                     data-category-slug="{{ $product->category->slug ?? '' }}"
                     data-name="{{ $product->name }}"
                     data-weight="{{ number_format($product->weight_gram, 3) }} gram"
                     data-purity="{{ $product->gold_purity }}"
                     data-price="Rp {{ $harga }}"
                     data-stock="{{ $product->stock }} pcs"
                     data-desc="{{ $product->description }}"
                     data-slug="{{ $product->slug }}"
                     data-image="{{ $product->thumbnail_url ?? '' }}"
                     class="landing-product-card p-0 group bg-white">
                    <div class="h-40 flex items-center justify-center flex-col gap-2 relative overflow-hidden bg-slate-50 border-b border-slate-100">
                        @if($product->thumbnail_url)
                        <img src="{{ $product->thumbnail_url }}" class="h-32 w-32 object-contain group-hover:scale-110 transition-transform duration-500">
                        @else
                        <span class="text-5xl group-hover:scale-110 transition-transform duration-300">{{ $icon }}</span>
                        @endif
                        @if(!$product->is_available)
                        <span class="absolute top-2 right-2 text-[10px] bg-red-100 text-red-700 font-bold px-2 py-0.5 rounded-full border border-red-300">Habis</span>
                        @endif
                    </div>
                    <div class="p-4">
                        <span class="text-[10px] font-bold text-[#866a20] bg-[#F4EDD9] border border-[#E3D193] px-2.5 py-0.5 rounded-full">{{ $product->gold_purity }}</span>
                        <h3 class="font-bold text-[#042623] mt-2 text-sm group-hover:text-[#085C54] transition line-clamp-1">{{ $product->name }}</h3>
                        <p class="text-xs text-slate-500 mt-1 font-medium">{{ number_format($product->weight_gram, 3) }} gram</p>
                        <p class="text-sm font-extrabold text-[#C6A443] mt-2">Rp {{ $harga }}</p>
                        @if($product->category)
                        <button onclick="event.stopPropagation(); filterCategory('{{ $product->category->slug }}', null)"
                                class="mt-3 w-full text-center text-[11px] font-semibold px-2 py-1.5 rounded-lg text-[#085C54] bg-[#e2f2f0] hover:bg-[#c9e8e4] transition border border-[#085C54]/20">
                            📂 Semua {{ $product->category->name }} →
                        </button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Empty State jika filter kosong --}}
            <div id="product-empty-state" class="hidden text-center py-16 bg-white rounded-3xl mt-6 border border-slate-200">
                <span class="text-5xl">🔍</span>
                <p class="text-slate-700 font-medium text-base mt-3">Tidak ada produk dalam kategori ini.</p>
                <button onclick="filterCategory('all', document.querySelector('.category-filter-btn:last-child'))"
                        class="mt-4 px-5 py-2.5 bg-[#085C54] text-[#E3D193] text-xs font-bold rounded-xl shadow-lg hover:scale-105 transition">
                    Tampilkan Semua Produk
                </button>
            </div>

            @auth
            <div class="text-center mt-12">
                <a href="{{ route('customer.catalog.index') }}" class="px-8 py-3.5 bg-white text-[#085C54] border border-[#085C54] font-bold rounded-xl hover:bg-[#F4EDD9] transition shadow-md hover:scale-105 inline-block">
                    Lihat Semua di Dashboard Member →
                </a>
            </div>
            @endauth
        </div>
    </section>

    {{-- Wave Divider: Produk → Layanan --}}
    <div class="relative overflow-hidden" style="height:72px; background:#F4EDD9; margin-top:-1px;">
        <svg viewBox="0 0 1440 72" fill="none" xmlns="http://www.w3.org/2000/svg" class="absolute top-0 w-full" preserveAspectRatio="none">
            <path d="M0,0 L1440,0 L1440,24 C1200,56 960,8 720,36 C480,64 240,16 0,48 Z" fill="rgba(8,92,84,0.12)"/>
            <path d="M0,0 L1440,0 L1440,12 C1080,44 720,4 360,36 C240,48 120,20 0,36 Z" fill="rgba(198,164,67,0.14)"/>
        </svg>
        <div class="absolute inset-x-0 top-0 h-px" style="background: linear-gradient(90deg, transparent 0%, #085C54 25%, #C6A443 50%, #085C54 75%, transparent 100%); opacity:0.45;"></div>
        <div class="absolute inset-x-0 bottom-0 h-px" style="background: linear-gradient(90deg, transparent 0%, #C6A443 30%, #085C54 50%, #C6A443 70%, transparent 100%); opacity:0.4;"></div>
    </div>

    {{-- MODAL PRODUK --}}
    <div id="modal" class="fixed inset-0 z-50 hidden items-center justify-center modal-bg" onclick="closeModal(event)">
        <div class="bg-white rounded-3xl p-0 max-w-md w-full mx-4 overflow-hidden shadow-2xl border border-slate-200" onclick="event.stopPropagation()">
            <div id="modal-header" class="h-48 flex items-center justify-center relative overflow-hidden bg-slate-50 border-b border-slate-200">
                <span id="modal-icon" class="text-7xl">🪙</span>
                <img id="modal-img" src="" class="hidden h-36 w-36 object-contain" />
                <button onclick="closeModal()"
                    class="absolute top-4 right-4 text-slate-500 hover:text-slate-800 text-xl bg-white/80 w-8 h-8 rounded-full flex items-center justify-center z-10 border border-slate-200 shadow-sm">×</button>
            </div>
            <div class="p-6">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <span id="modal-badge" class="text-xs font-semibold text-[#866a20] bg-[#F4EDD9] border border-[#E3D193] px-2.5 py-0.5 rounded-full"></span>
                        <h3 id="modal-name" class="font-playfair text-xl font-bold mt-2 text-[#042623]"></h3>
                    </div>
                    <span id="modal-status" class="text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300 px-3 py-1 rounded-full">Tersedia</span>
                </div>
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="bg-[#F4EDD9] rounded-xl p-3 border border-slate-200">
                        <p class="text-xs text-slate-500 font-medium">Berat</p>
                        <p id="modal-berat" class="font-bold text-[#085C54]"></p>
                    </div>
                    <div class="bg-[#F4EDD9] rounded-xl p-3 border border-slate-200">
                        <p class="text-xs text-slate-500 font-medium">Kadar</p>
                        <p id="modal-kadar" class="font-bold text-[#085C54]"></p>
                    </div>
                    <div class="bg-[#F4EDD9] rounded-xl p-3 border border-slate-200">
                        <p class="text-xs text-slate-500 font-medium">Harga Jual</p>
                        <p id="modal-harga" class="font-bold text-[#C6A443]"></p>
                    </div>
                    <div class="bg-[#F4EDD9] rounded-xl p-3 border border-slate-200">
                        <p class="text-xs text-slate-500 font-medium">Stok</p>
                        <p id="modal-stok" class="font-bold text-[#042623]"></p>
                    </div>
                </div>
                <p id="modal-desc" class="text-xs text-slate-600 mb-5 leading-relaxed font-normal"></p>
                <div class="flex gap-3" id="modal-actions">
                    @auth
                        <a id="modal-reserve-btn" href="{{ route('customer.reservations.create') }}"
                           class="flex-1 py-3 gold-gradient text-center rounded-xl font-bold text-sm hover:opacity-90 transition shadow-lg">
                            Reservasi Beli
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="flex-1 py-3 gold-gradient text-center rounded-xl font-bold text-sm hover:opacity-90 transition shadow-lg">
                            Login untuk Reservasi
                        </a>
                    @endauth
                    <button onclick="closeModal()"
                        class="px-4 py-3 bg-slate-100 rounded-xl text-sm font-semibold text-slate-700 border border-slate-200 hover:bg-slate-200 transition">Tutup</button>
                </div>
                <p class="text-xs text-slate-500 text-center mt-3 font-medium">* Transaksi fisik dilakukan langsung di toko</p>
            </div>
        </div>
    </div>

    {{-- LAYANAN O2O (EmasKITA Khaki Section #F4EDD9) --}}
    <section id="layanan" class="py-20 section-layanan-bg relative">
        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="text-center mb-14">
                <p class="text-[#085C54] text-xs font-bold uppercase tracking-widest bg-white inline-block px-3.5 py-1.5 rounded-full border border-[#E3D193] shadow-sm">Layanan Kami</p>
                <h2 class="font-playfair text-3xl md:text-4xl font-bold mt-3 text-[#042623]">Semua Kebutuhan Emas <span class="text-[#085C54]">Anda</span></h2>
                <div class="hr-goldline"></div>
                <p class="text-slate-700 text-sm mt-2 font-medium">Reservasi & informasi lengkap secara online — transaksi aman langsung di toko (O2O)</p>
            </div>
            <div class="grid md:grid-cols-4 gap-6">
                @foreach([
                    ['icon'=>'🛒','title'=>'Beli Emas','desc'=>'Reservasi perhiasan & logam mulia online, ambil & bayar langsung di toko.'],
                    ['icon'=>'💰','title'=>'Jual Emas','desc'=>'Cek estimasi harga buyback otomatis secara real-time, verifikasi di toko.'],
                    ['icon'=>'📅','title'=>'Cicilan Emas','desc'=>'Simulasi cicilan bunga ringan online dengan tenor fleksibel 3–24 bulan.'],
                    ['icon'=>'🏦','title'=>'Gadai Emas','desc'=>'Estimasi nilai pinjaman cepat & transparan, pantau status gadai berkala.'],
                ] as $s)
                <div class="bg-white rounded-3xl p-7 text-center border border-[#e8e3d5] hover:border-[#C6A443] hover:scale-105 transition-all shadow-md group">
                    <div class="text-4xl mb-4 group-hover:scale-110 transition-transform duration-300">{{ $s['icon'] }}</div>
                    <h3 class="font-bold text-[#085C54] text-lg group-hover:text-[#C6A443] transition">{{ $s['title'] }}</h3>
                    <p class="text-xs text-slate-600 mt-2.5 leading-relaxed">{{ $s['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Wave Divider: Layanan → Loyalty --}}
    <div class="relative overflow-hidden" style="height:64px; background:#F4EDD9; margin-top:-1px;">
        <svg viewBox="0 0 1440 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="absolute bottom-0 w-full" preserveAspectRatio="none">
            <path d="M0,20 C360,60 720,0 1080,40 C1260,56 1380,24 1440,32 L1440,64 L0,64 Z" fill="rgba(198,164,67,0.22)"/>
            <path d="M0,40 C480,8 960,56 1440,20 L1440,64 L0,64 Z" fill="rgba(8,92,84,0.09)"/>
        </svg>
        <div class="absolute inset-x-0 top-0 h-[1.5px]" style="background: linear-gradient(90deg, transparent 0%, #C6A443 20%, #E3D193 50%, #C6A443 80%, transparent 100%); opacity:0.6;"></div>
    </div>

    {{-- LOYALTY / REWARDS --}}
    <section id="loyalitas" class="py-20 section-loyalitas-bg relative">
        <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-12 items-center relative z-10">
            <div>
                <p class="text-[#085C54] text-xs font-bold uppercase tracking-widest bg-[#F4EDD9] inline-block px-3.5 py-1.5 rounded-full border border-[#E3D193] shadow-sm">Program Eksklusif</p>
                <h2 class="font-playfair text-3xl md:text-4xl font-bold mt-3 text-[#042623]">Sinar Baru <span class="text-[#C6A443]">Rewards</span></h2>
                <div class="w-16 h-1 bg-[#C6A443] mt-3 rounded-full"></div>
                <p class="text-slate-600 mt-4 text-sm leading-relaxed font-normal">
                    Setiap transaksi di toko memberikan Anda poin loyalty. Kumpulkan poin dan nikmati reward istimewa di setiap tingkatan membership!
                </p>
                <div class="mt-8 space-y-3.5">
                    <div class="flex items-center gap-3.5 bg-white rounded-2xl p-4 border border-slate-200 shadow-sm">
                        <span class="text-2xl">🥉</span>
                        <div>
                            <p class="font-bold text-sm text-[#042623]">Bronze Member</p>
                            <p class="text-xs text-slate-500">0–4 transaksi • Akses katalog & info harga harian</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3.5 bg-white rounded-2xl p-4 border border-slate-200 shadow-sm">
                        <span class="text-2xl">🥈</span>
                        <div>
                            <p class="font-bold text-sm text-[#042623]">Silver Member</p>
                            <p class="text-xs text-slate-500">5–9 transaksi • Prioritas reservasi stok produk</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3.5 rounded-2xl p-4 border-2 border-[#C6A443] bg-[#F4EDD9]/60 shadow-md">
                        <span class="text-2xl">🥇</span>
                        <div>
                            <p class="font-bold text-sm text-[#085C54]">Gold Member — Ke-10 Transaksi!</p>
                            <p class="text-xs text-[#866a20] font-semibold">🎁 Diskon Khusus Member + Cuci Emas GRATIS</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3.5 bg-white rounded-2xl p-4 border border-slate-200 shadow-sm">
                        <span class="text-2xl">💎</span>
                        <div>
                            <p class="font-bold text-sm text-[#042623]">Diamond VIP</p>
                            <p class="text-xs text-slate-500">20+ transaksi • Layanan VIP & penawaran eksklusif</p>
                        </div>
                    </div>
                </div>
                @guest
                <a href="{{ route('register') }}"
                   class="mt-8 inline-block px-8 py-3.5 bg-[#085C54] text-white hover:text-[#E3D193] rounded-xl font-bold hover:scale-105 transition shadow-xl border border-[#063e39]">
                    Daftar & Mulai Kumpulkan Poin →
                </a>
                @endguest
            </div>

            {{-- Cara Kerja --}}
            <div class="bg-white rounded-3xl p-8 shadow-xl border border-[#e8e3d5]">
                <p class="text-[#042623] font-playfair text-2xl font-bold mb-6 text-center">Cara Kerja Sistem O2O</p>
                <div class="space-y-5">
                    @foreach([
                        ['1','Daftar & Buat Akun','Daftar gratis untuk mengakses fitur reservasi, cicilan, dan loyalty.'],
                        ['2','Pilih & Reservasi Online','Pilih produk emas dari katalog dan amankan unit Anda secara online.'],
                        ['3','Selesaikan di Toko','Kunjungi Toko Emas Sinar Baru II untuk verifikasi & transaksi fisik.'],
                    ] as [$num, $title, $desc])
                    <div class="flex items-start gap-4">
                        <div class="w-9 h-9 gold-gradient rounded-full flex items-center justify-center text-sm font-extrabold shrink-0 shadow-md">{{ $num }}</div>
                        <div>
                            <p class="font-bold text-sm text-[#042623]">{{ $title }}</p>
                            <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ $desc }}</p>
                        </div>
                    </div>
                    @endforeach
                    <div class="flex items-start gap-4 pt-2 border-t border-slate-200">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold shrink-0 bg-[#F4EDD9] text-[#866a20] border border-[#E3D193] shadow-sm">🎁</div>
                        <div>
                            <p class="font-bold text-sm text-[#085C54]">Reward Transaksi ke-10</p>
                            <p class="text-xs text-slate-500 mt-1 leading-relaxed">Nikmati benefit loyalitas otomatis saat Anda bertransaksi ke-10 di toko kami!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- FOOTER (EmasKITA Deep Forest Green #042623) --}}
    <footer class="footer-bg text-white py-16">
        <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-3 gap-10">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 gold-gradient rounded-xl flex items-center justify-center font-bold text-base shadow-lg">SB</div>
                    <p class="font-playfair text-[#E3D193] font-bold text-xl">Sinar Baru II</p>
                </div>
                <p class="text-xs text-emerald-100/70 leading-relaxed font-normal">Toko emas terpercaya yang melayani jual, beli, cicil, dan gadai emas dengan sepenuh hati sejak 1995.</p>
            </div>
            <div>
                <p class="text-sm font-bold text-[#E3D193] mb-3 tracking-wide">Jam Operasional Toko</p>
                <p class="text-xs text-emerald-100/80 font-normal">Senin – Sabtu: 08.00 – 17.00 WIB</p>
                <p class="text-xs text-emerald-100/80 font-normal mt-1.5">Minggu: 08.00 – 13.00 WIB</p>
            </div>
            <div>
                <p class="text-sm font-bold text-[#E3D193] mb-3 tracking-wide">Lokasi & Kontak</p>
                <p class="text-xs text-emerald-100/80 font-normal">📍 Teluk Lubuk, Kec. Belimbing, Kab. Muara Enim, Sumatera Selatan</p>
                <p class="text-xs text-emerald-100/80 font-normal mt-1.5">📱 WhatsApp / Telp Toko</p>
            </div>
        </div>
        <div class="text-center mt-12 pt-8 border-t border-emerald-800/40 text-xs text-emerald-200/50">© {{ date('Y') }} Toko Emas Sinar Baru II. All rights reserved.</div>
    </footer>

</body>
</html>
