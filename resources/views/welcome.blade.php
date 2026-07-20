<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Emas Sinar Baru II — Investasi Emas Lebih Mudah</title>
    <meta name="description" content="Toko emas terpercaya sejak 1995. Temukan perhiasan emas pilihan, pantau harga real-time, simulasikan cicilan, dan nikmati program loyalitas eksklusif.">

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/css/landing.css', 'resources/js/landing.js'])
</head>
<body>

    {{-- NAVBAR --}}
    <nav class="fixed w-full z-50 glass">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 orange-gradient rounded-full flex items-center justify-center font-bold text-white text-lg">SB</div>
                <div>
                    <p class="font-playfair font-bold text-primary-400 leading-none">Sinar Baru II</p>
                    <p class="text-xs text-gray-400">Toko Emas Terpercaya</p>
                </div>
            </div>
            <div class="hidden md:flex items-center gap-8 text-sm text-gray-300">
                <a href="#" class="hover:text-primary-400 transition">Beranda</a>
                <a href="#produk" class="hover:text-primary-400 transition">Katalog</a>
                <a href="#harga" class="hover:text-primary-400 transition">Harga Emas</a>
                <a href="#layanan" class="hover:text-primary-400 transition">Layanan</a>
                <a href="#loyalitas" class="hover:text-primary-400 transition">Rewards</a>
            </div>
            <div class="flex gap-3">
                @auth
                    <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('customer.dashboard') }}"
                       class="px-4 py-2 text-sm orange-gradient text-white rounded-lg font-medium hover:opacity-90 transition">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="px-4 py-2 text-sm text-primary-400 border border-primary-600 rounded-lg hover:bg-primary-900\/40 transition">
                        Masuk
                    </a>
                    @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                       class="px-4 py-2 text-sm orange-gradient text-white rounded-lg font-medium hover:opacity-90 transition">
                        Daftar
                    </a>
                    @endif
                @endauth
            </div>
        </div>
    </nav>

    {{-- HERO --}}
    <section class="hero-bg min-h-screen flex items-center pt-20">
        <div class="max-w-7xl mx-auto px-6 py-20 grid md:grid-cols-2 gap-12 items-center">
            <div>
                <span class="text-xs uppercase tracking-widest text-primary-400 font-medium">✦ Terpercaya Sejak 1995</span>
                <h1 class="font-playfair text-5xl md:text-6xl font-bold mt-4 leading-tight">
                    Investasi Emas<br><span class="text-primary-400">Lebih Mudah,</span><br>Lebih Cerdas
                </h1>
                <p class="mt-6 text-gray-400 leading-relaxed">
                    Temukan perhiasan emas pilihan, pantau harga pasaran real-time, simulasikan cicilan, dan nikmati program loyalitas eksklusif hanya untuk member Sinar Baru II.
                </p>
                <div class="mt-8 flex gap-4">
                    <a href="#produk" class="px-6 py-3 orange-gradient rounded-xl font-semibold glow hover:opacity-90 transition">
                        Lihat Katalog
                    </a>
                    <a href="#harga" class="px-6 py-3 glass rounded-xl text-primary-300 hover:bg-white/10 transition">
                        Harga Emas Hari Ini
                    </a>
                </div>
                <div class="mt-10 flex gap-8">
                    <div>
                        <p class="text-2xl font-bold text-accent-400">2.5K+</p>
                        <p class="text-xs text-gray-500">Pelanggan Aktif</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-accent-400">500+</p>
                        <p class="text-xs text-gray-500">Produk Tersedia</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-accent-400">30 Th</p>
                        <p class="text-xs text-gray-500">Berpengalaman</p>
                    </div>
                </div>
            </div>

            {{-- Harga Emas Card --}}
            <div id="harga" class="glass rounded-2xl p-6 glow">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-playfair text-lg text-primary-300">Harga Emas Hari Ini</h3>
                    <span class="text-xs text-gray-400 bg-dark-700 px-2 py-1 rounded">{{ now()->isoFormat('D MMM Y') }}</span>
                </div>
                @if($goldPrice)
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="bg-green-900\/30 rounded-xl p-4 border border-green-700\/30">
                        <p class="text-xs text-green-400 mb-1">Harga Beli (Toko Beli)</p>
                        <p class="text-xl font-bold">Rp {{ number_format($goldPrice->buy_price_per_gram, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-400">per gram • 24K</p>
                    </div>
                    <div class="bg-orange-900\/30 rounded-xl p-4 border border-orange-700\/30">
                        <p class="text-xs text-primary-400 mb-1">Harga Jual (Toko Jual)</p>
                        <p class="text-xl font-bold">Rp {{ number_format($goldPrice->sell_price_per_gram, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-400">per gram • 24K</p>
                    </div>
                </div>
                <p class="text-xs text-gray-500 text-center">📡 Sumber: {{ $goldPrice->source }}</p>
                @else
                <div class="text-center py-6">
                    <p class="text-gray-400 text-sm">Harga sedang diperbarui...</p>
                    <p class="text-gray-600 text-xs mt-1">Hubungi toko untuk harga terkini</p>
                </div>
                @endif
                <p class="text-xs text-gray-500 mt-4 text-center">Harga dapat berubah sewaktu-waktu • Datang ke toko untuk transaksi</p>
            </div>
        </div>
    </section>

    {{-- PRODUK UNGGULAN --}}
    <section id="produk" class="py-20 bg-dark-800">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-12">
                <p class="text-primary-400 text-sm uppercase tracking-widest">Koleksi Pilihan</p>
                <h2 class="font-playfair text-3xl font-bold mt-2">Pilihan Emas Kami</h2>
                <p class="text-gray-400 text-sm mt-2">Emas murni 24 karat dalam berbagai ukuran • Klik untuk melihat harga</p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-6" id="product-grid">
                @foreach($products as $product)
                @php
                    $icons = ['🪙','🥇','⭐','✨','💫'];
                    $icon  = $icons[$loop->index % count($icons)];
                    $harga = $goldPrice ? number_format($goldPrice->sell_price_per_gram * $product->weight_gram, 0, ',', '.') : number_format($product->base_price, 0, ',', '.');
                @endphp
                <div onclick="openProductModal({{ $product->id }})"
                     data-product-id="{{ $product->id }}"
                     data-name="{{ $product->name }}"
                     data-weight="{{ number_format($product->weight_gram, 3) }} gram"
                     data-purity="{{ $product->gold_purity }}"
                     data-price="Rp {{ $harga }}"
                     data-stock="{{ $product->stock }} pcs"
                     data-desc="{{ $product->description }}"
                     data-slug="{{ $product->slug }}"
                     data-image="{{ $product->thumbnail ? asset($product->thumbnail) : '' }}"
                     class="glass rounded-2xl overflow-hidden hover:scale-105 hover:border-primary-500 transition-all cursor-pointer group"
                     style="transition: all 0.3s ease;">
                    <div class="h-36 flex items-center justify-center flex-col gap-2" style="background: linear-gradient(135deg, rgba(124,45,18,0.5), rgba(194,65,12,0.2));">
                        @if($product->thumbnail)
                        <img src="{{ asset($product->thumbnail) }}" class="h-28 w-28 object-contain group-hover:scale-110 transition-transform duration-300">
                        @else
                        <span class="text-4xl group-hover:scale-110" style="transition: transform 0.2s;">{{ $icon }}</span>
                        @endif
                        @if(!$product->is_available)
                        <span class="text-xs bg-red-900/60 text-red-400 px-2 py-0.5 rounded-full">Habis</span>
                        @endif
                    </div>
                    <div class="p-4">
                        <span class="text-xs text-primary-400 bg-primary-900\/50 px-2 py-0.5 rounded-full">{{ $product->gold_purity }}</span>
                        <h3 class="font-semibold mt-2 text-sm">{{ $product->name }}</h3>
                        <p class="text-xs text-gray-400 mt-1">{{ number_format($product->weight_gram, 3) }} gram</p>
                        <p class="text-sm font-bold text-accent-400 mt-2">Rp {{ $harga }}</p>
                        <p class="text-xs text-primary-400 mt-1 font-medium">Klik untuk detail →</p>
                    </div>
                </div>
                @endforeach
            </div>
            @auth
            <div class="text-center mt-8">
                <a href="{{ route('customer.catalog.index') }}" class="px-8 py-3 glass border border-primary-600 text-primary-400 rounded-xl hover:bg-primary-900\/30 transition">
                    Lihat Semua di Dashboard →
                </a>
            </div>
            @endauth
        </div>
    </section>

    {{-- MODAL PRODUK --}}
    <div id="modal" class="fixed inset-0 z-50 hidden items-center justify-center modal-bg" onclick="closeModal(event)">
        <div class="glass rounded-3xl p-0 max-w-md w-full mx-4 overflow-hidden glow" onclick="event.stopPropagation()">
            <div id="modal-header" class="h-48 flex items-center justify-center text-7xl relative"
                 style="background: linear-gradient(135deg, rgba(124,45,18,0.6), #1c1008);">
                <span id="modal-icon">🪙</span>
                <button onclick="closeModal()"
                    class="absolute top-4 right-4 text-gray-400 hover:text-white text-2xl bg-dark-800 w-8 h-8 rounded-full flex items-center justify-center"
                    style="background:rgba(28,16,8,0.8);">×</button>
            </div>
            <div class="p-6">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <span id="modal-badge" class="text-xs text-primary-400 bg-primary-900\/50 px-2 py-0.5 rounded-full"></span>
                        <h3 id="modal-name" class="font-playfair text-xl font-bold mt-2"></h3>
                    </div>
                    <span id="modal-status" class="text-xs bg-green-900\/50 text-green-400 px-3 py-1 rounded-full">Tersedia</span>
                </div>
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="glass rounded-xl p-3">
                        <p class="text-xs text-gray-400">Berat</p>
                        <p id="modal-berat" class="font-bold text-accent-400"></p>
                    </div>
                    <div class="glass rounded-xl p-3">
                        <p class="text-xs text-gray-400">Kadar</p>
                        <p id="modal-kadar" class="font-bold text-accent-400"></p>
                    </div>
                    <div class="glass rounded-xl p-3">
                        <p class="text-xs text-gray-400">Harga Jual</p>
                        <p id="modal-harga" class="font-bold text-white"></p>
                    </div>
                    <div class="glass rounded-xl p-3">
                        <p class="text-xs text-gray-400">Stok</p>
                        <p id="modal-stok" class="font-bold text-white"></p>
                    </div>
                </div>
                <p id="modal-desc" class="text-xs text-gray-400 mb-5 leading-relaxed"></p>
                <div class="flex gap-3" id="modal-actions">
                    @auth
                        <a id="modal-reserve-btn" href="{{ route('customer.reservations.create') }}"
                           class="flex-1 py-3 orange-gradient text-center rounded-xl font-semibold text-sm hover:opacity-90 transition">
                            Reservasi Beli
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="flex-1 py-3 orange-gradient text-center rounded-xl font-semibold text-sm hover:opacity-90 transition">
                            Login untuk Reservasi
                        </a>
                    @endauth
                    <button onclick="closeModal()"
                        class="px-4 py-3 glass rounded-xl text-sm text-gray-300 hover:bg-white/10 transition">Tutup</button>
                </div>
                <p class="text-xs text-gray-500 text-center mt-3">* Transaksi dilakukan langsung di toko</p>
            </div>
        </div>
    </div>

    {{-- LAYANAN O2O --}}
    <section id="layanan" class="py-20 bg-dark-900">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-12">
                <p class="text-primary-400 text-sm uppercase tracking-widest">Layanan Kami</p>
                <h2 class="font-playfair text-3xl font-bold mt-2">Semua Kebutuhan Emas Anda</h2>
                <p class="text-gray-400 text-sm mt-2">Reservasi & informasi online — transaksi tetap di toko (O2O)</p>
            </div>
            <div class="grid md:grid-cols-4 gap-6">
                @foreach([
                    ['icon'=>'🛒','title'=>'Beli Emas','desc'=>'Reservasi online, transaksi di toko. Pilih produk dari katalog.'],
                    ['icon'=>'💰','title'=>'Jual Emas','desc'=>'Isi form online, estimasi harga otomatis, verifikasi di toko.'],
                    ['icon'=>'📅','title'=>'Cicilan Emas','desc'=>'Simulasi cicilan online, tenor 3–24 bulan, akad di toko.'],
                    ['icon'=>'🏦','title'=>'Gadai Emas','desc'=>'Estimasi pinjaman online, pantau status gadai kapan saja.'],
                ] as $s)
                <div class="glass rounded-2xl p-6 text-center hover:border-primary-500 hover:scale-105 transition-all" style="transition: all 0.3s ease;">
                    <div class="text-4xl mb-4">{{ $s['icon'] }}</div>
                    <h3 class="font-semibold text-primary-300">{{ $s['title'] }}</h3>
                    <p class="text-xs text-gray-400 mt-2">{{ $s['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- LOYALTY / REWARDS --}}
    <section id="loyalitas" class="py-20" style="background: linear-gradient(135deg, #2d1a0a, #3d2412);">
        <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-12 items-center">
            <div>
                <p class="text-primary-400 text-sm uppercase tracking-widest">Program Eksklusif</p>
                <h2 class="font-playfair text-3xl font-bold mt-2">Sinar Baru <span class="text-accent-400">Rewards</span></h2>
                <p class="text-gray-400 mt-4">
                    Setiap transaksi di toko memberi Anda poin. Capai 10 transaksi dan dapatkan hadiah istimewa!
                </p>
                <div class="mt-6 space-y-3">
                    <div class="flex items-center gap-3 glass rounded-xl p-3">
                        <span class="text-2xl">🥉</span>
                        <div>
                            <p class="font-semibold text-sm">Bronze</p>
                            <p class="text-xs text-gray-400">0–4 transaksi • Akses dasar</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 glass rounded-xl p-3">
                        <span class="text-2xl">🥈</span>
                        <div>
                            <p class="font-semibold text-sm">Silver</p>
                            <p class="text-xs text-gray-400">5–9 transaksi • Prioritas reservasi</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 rounded-xl p-3 border border-accent-500 bg-primary-900\/20">
                        <span class="text-2xl">🥇</span>
                        <div>
                            <p class="font-semibold text-sm text-accent-300">Gold — Ke-10 Transaksi!</p>
                            <p class="text-xs text-accent-500">🎁 Diskon Khusus + Cuci Emas GRATIS</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 glass rounded-xl p-3">
                        <span class="text-2xl">💎</span>
                        <div>
                            <p class="font-semibold text-sm">Diamond</p>
                            <p class="text-xs text-gray-400">20+ transaksi • VIP service & diskon terbesar</p>
                        </div>
                    </div>
                </div>
                @guest
                <a href="{{ route('register') }}"
                   class="mt-6 inline-block px-6 py-3 orange-gradient rounded-xl font-semibold hover:opacity-90 transition glow">
                    Daftar & Mulai Kumpulkan Poin →
                </a>
                @endguest
            </div>

            {{-- Cara Kerja --}}
            <div class="glass rounded-3xl p-8 glow">
                <p class="text-primary-300 font-playfair text-xl font-bold mb-6 text-center">Cara Kerja Sistem</p>
                <div class="space-y-4">
                    @foreach([
                        ['1','Daftar & Login','Buat akun member gratis untuk akses semua fitur'],
                        ['2','Pilih & Reservasi Online','Lihat produk, harga, simulasi cicilan, buat reservasi'],
                        ['3','Datang ke Toko','Selesaikan transaksi langsung di Toko Emas Sinar Baru II'],
                    ] as [$num, $title, $desc])
                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 orange-gradient rounded-full flex items-center justify-center text-sm font-bold shrink-0">{{ $num }}</div>
                        <div>
                            <p class="font-semibold text-sm">{{ $title }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $desc }}</p>
                        </div>
                    </div>
                    @endforeach
                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold shrink-0" style="background:#f59e0b;">🎁</div>
                        <div>
                            <p class="font-semibold text-sm text-accent-300">Kumpulkan Poin Reward</p>
                            <p class="text-xs text-gray-400 mt-1">Ke-10 transaksi: diskon + cuci emas gratis!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="bg-dark-900 border-t border-dark-700 py-12">
        <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-3 gap-8">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 orange-gradient rounded-full flex items-center justify-center font-bold">SB</div>
                    <p class="font-playfair text-primary-400 font-bold">Sinar Baru II</p>
                </div>
                <p class="text-xs text-gray-500">Toko emas terpercaya yang melayani dengan sepenuh hati sejak 1995.</p>
            </div>
            <div>
                <p class="text-sm font-semibold text-primary-400 mb-3">Jam Operasional</p>
                <p class="text-xs text-gray-400">Senin – Sabtu: 08.00 – 17.00</p>
                <p class="text-xs text-gray-400">Minggu: 08.00 – 13.00</p>
            </div>
            <div>
                <p class="text-sm font-semibold text-primary-400 mb-3">Kontak</p>
                <p class="text-xs text-gray-400">📍 Jl. Contoh No. 123, Kota Anda</p>
                <p class="text-xs text-gray-400 mt-1">📱 +62 812-3456-7890</p>
            </div>
        </div>
        <div class="text-center mt-8 text-xs text-gray-600">© {{ date('Y') }} Toko Emas Sinar Baru II. All rights reserved.</div>
    </footer>

</body>
</html>
