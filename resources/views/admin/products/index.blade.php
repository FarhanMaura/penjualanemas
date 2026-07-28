<x-admin-app>
    <x-slot name="pageTitle">Manajemen Produk</x-slot>
    <x-slot name="breadcrumb">Kelola katalog produk emas Sinar Baru II</x-slot>

    @if(session('success'))
    <div class="mb-6 p-4 rounded-xl text-sm text-green-400" style="background:rgba(34,197,94,0.1); border:1px solid rgba(34,197,94,0.25);">
        ✅ {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-6 p-4 rounded-xl text-sm text-red-400" style="background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.25);">
        ❌ {{ session('error') }}
    </div>
    @endif

    {{-- Filter & Search --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <div class="glass flex items-center gap-2 px-3 py-2 rounded-xl text-sm">
                <span class="text-gray-400 whitespace-nowrap">Kategori:</span>
                <select name="category" onchange="this.form.submit()" class="bg-transparent text-white text-sm focus:outline-none cursor-pointer">
                    <option value="">Semua</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="glass flex items-center gap-2 px-3 py-2 rounded-xl text-sm">
                <span class="text-gray-400">Status:</span>
                <select name="status" onchange="this.form.submit()" class="bg-transparent text-white text-sm focus:outline-none cursor-pointer">
                    <option value="">Semua</option>
                    <option value="available" {{ request('status')=='available' ? 'selected':'' }}>Tersedia</option>
                    <option value="empty"     {{ request('status')=='empty' ? 'selected':'' }}>Stok Habis</option>
                </select>
            </div>
            <div class="glass flex items-center gap-2 px-3 py-2 rounded-xl">
                <span class="text-gray-400 text-sm">🔍</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau SKU..."
                       class="bg-transparent text-sm text-white placeholder-gray-500 focus:outline-none w-40">
            </div>
        </form>
        <a href="{{ route('admin.products.create') }}"
           class="px-4 py-2 rounded-xl text-sm font-bold text-white whitespace-nowrap"
           style="background:linear-gradient(135deg,#f59e0b,#d97706);">+ Tambah Produk</a>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @foreach([
            ['Total Produk', $stats['total'], 'text-white'],
            ['Kategori Aktif', $stats['categories'], 'text-yellow-400'],
            ['Tersedia', $stats['available'], 'text-green-400'],
            ['Stok Habis', $stats['empty'], 'text-red-400'],
        ] as [$label, $val, $color])
        <div class="glass rounded-2xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">{{ $label }}</p>
            <p class="text-3xl font-bold {{ $color }}">{{ $val }}</p>
        </div>
        @endforeach
    </div>

    {{-- Product List --}}
    <div class="glass rounded-2xl overflow-hidden" style="border-color:rgba(255,255,255,0.06);">
        <div class="flex items-center justify-between px-6 py-4" style="border-bottom:1px solid rgba(245,158,11,0.1);">
            <h3 class="font-semibold text-yellow-400">📦 Daftar Produk</h3>
            <span class="text-xs text-gray-500">{{ $products->total() }} produk ditemukan</span>
        </div>

        @if($products->isEmpty())
        <div class="text-center py-16">
            <p class="text-4xl mb-3">📦</p>
            <p class="text-gray-400 mb-4">Belum ada produk</p>
            <a href="{{ route('admin.products.create') }}"
               class="inline-block px-6 py-2 rounded-xl text-white text-sm font-bold"
               style="background:linear-gradient(135deg,#f59e0b,#d97706);">+ Tambah Produk Pertama</a>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left" style="min-width:700px;">
                <thead>
                    <tr class="text-xs text-gray-500 uppercase tracking-wider" style="background:rgba(0,0,0,0.3); border-bottom:1px solid rgba(255,255,255,0.08);">
                        <th class="py-3 px-4 w-12">Foto</th>
                        <th class="py-3 px-4">Produk</th>
                        <th class="py-3 px-4 whitespace-nowrap">Karat</th>
                        <th class="py-3 px-4 whitespace-nowrap">Berat</th>
                        <th class="py-3 px-4 whitespace-nowrap">Harga Jual</th>
                        <th class="py-3 px-4 text-center whitespace-nowrap">Stok</th>
                        <th class="py-3 px-4 text-center whitespace-nowrap">Status</th>
                        <th class="py-3 px-4 text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $p)
                    <tr style="border-bottom:1px solid rgba(255,255,255,0.04);" class="hover:bg-white/5 transition">
                        <td class="py-3 px-4">
                            @if($p->thumbnail_url)
                            <img src="{{ $p->thumbnail_url }}" class="w-10 h-10 rounded-lg object-cover">
                            @else
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center text-xl"
                                 style="background:rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.2);">
                                {{ $p->category->icon ?? '📦' }}
                            </div>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <p class="font-semibold text-white">{{ $p->name }}</p>
                            <p class="text-xs text-gray-500 font-mono mt-0.5">{{ $p->sku }}</p>
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            <p class="text-yellow-400 font-semibold">{{ $p->gold_purity }}</p>
                            <p class="text-xs text-gray-500">{{ $p->category->name }}</p>
                        </td>
                        <td class="py-3 px-4 text-gray-300 whitespace-nowrap">{{ $p->weight_gram }}g</td>
                        <td class="py-3 px-4 font-semibold text-white whitespace-nowrap">
                            Rp {{ number_format($p->base_price, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-4 text-center font-bold {{ $p->stock == 0 ? 'text-red-400' : 'text-gray-300' }}">
                            {{ $p->stock }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium whitespace-nowrap
                                {{ $p->is_available && $p->stock > 0 ? 'bg-green-900/40 text-green-400' : ($p->stock == 0 ? 'bg-red-900/40 text-red-400' : 'bg-gray-900/40 text-gray-400') }}">
                                {{ $p->is_available && $p->stock > 0 ? 'Aktif' : ($p->stock == 0 ? 'Habis' : 'Hidden') }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex items-center justify-center gap-2 whitespace-nowrap">
                                <a href="{{ route('admin.products.edit', $p) }}"
                                   class="px-3 py-1 rounded-lg text-xs font-medium"
                                   style="background:rgba(245,158,11,0.15); color:#fbbf24; border:1px solid rgba(245,158,11,0.3);">Edit</a>
                                <form method="POST" action="{{ route('admin.products.destroy', $p) }}" onsubmit="return confirm('Hapus produk {{ $p->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-1 rounded-lg text-xs font-medium"
                                            style="background:rgba(239,68,68,0.15); color:#f87171; border:1px solid rgba(239,68,68,0.3);">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4">{{ $products->links() }}</div>
        @endif
    </div>

    <x-slot name="scripts">
        @vite('resources/js/admin/products.js')
    </x-slot>
</x-admin-app>
