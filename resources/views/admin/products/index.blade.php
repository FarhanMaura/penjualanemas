<x-admin-app>
    <x-slot name="pageTitle">Manajemen Produk</x-slot>
    <x-slot name="breadcrumb">Kelola katalog produk emas Sinar Baru II</x-slot>

    @if(session('success'))
    <div class="mb-6 p-4 rounded-xl text-sm font-semibold text-emerald-900 bg-emerald-50 border border-emerald-300 shadow-sm">
        ✅ {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-6 p-4 rounded-xl text-sm font-semibold text-red-900 bg-red-50 border border-red-300 shadow-sm">
        ❌ {{ session('error') }}
    </div>
    @endif

    {{-- Filter & Search --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <div class="glass flex items-center gap-2 px-3 py-2 rounded-xl text-sm bg-white border border-[#e8e3d5]">
                <span class="text-slate-600 font-bold whitespace-nowrap">Kategori:</span>
                <select name="category" onchange="this.form.submit()" class="bg-transparent text-slate-900 font-bold text-sm focus:outline-none cursor-pointer">
                    <option value="" class="text-slate-900">Semua</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }} class="text-slate-900">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="glass flex items-center gap-2 px-3 py-2 rounded-xl text-sm bg-white border border-[#e8e3d5]">
                <span class="text-slate-600 font-bold">Status:</span>
                <select name="status" onchange="this.form.submit()" class="bg-transparent text-slate-900 font-bold text-sm focus:outline-none cursor-pointer">
                    <option value="" class="text-slate-900">Semua</option>
                    <option value="available" {{ request('status')=='available' ? 'selected':'' }} class="text-slate-900">Tersedia</option>
                    <option value="empty"     {{ request('status')=='empty' ? 'selected':'' }} class="text-slate-900">Stok Habis</option>
                </select>
            </div>
            <div class="glass flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-[#e8e3d5]">
                <span class="text-slate-500 text-sm">🔍</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau SKU..."
                       class="bg-transparent text-sm text-slate-900 font-semibold placeholder-slate-400 focus:outline-none w-40">
                <button type="submit" class="text-xs font-bold text-[#085C54] hover:underline">Cari</button>
            </div>
        </form>
        <a href="{{ route('admin.products.create') }}"
           class="px-4 py-2.5 rounded-xl text-xs font-extrabold text-[#042623] gold-gradient border border-[#C6A443] shadow-md hover:brightness-110 transition whitespace-nowrap">+ Tambah Produk Baru</a>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="glass rounded-2xl p-5 bg-white border border-[#e8e3d5] shadow-sm">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Total Produk</p>
            <p class="text-3xl font-extrabold text-[#042623]">{{ $stats['total'] }}</p>
        </div>
        <div class="glass rounded-2xl p-5 bg-amber-50/60 border border-amber-200 shadow-sm">
            <p class="text-xs font-bold text-amber-900 uppercase tracking-wider mb-2">Kategori Aktif</p>
            <p class="text-3xl font-extrabold text-[#C6A443]">{{ $stats['categories'] }}</p>
        </div>
        <div class="glass rounded-2xl p-5 bg-emerald-50/60 border border-emerald-200 shadow-sm">
            <p class="text-xs font-bold text-emerald-900 uppercase tracking-wider mb-2">Tersedia</p>
            <p class="text-3xl font-extrabold text-[#085C54]">{{ $stats['available'] }}</p>
        </div>
        <div class="glass rounded-2xl p-5 bg-red-50/60 border border-red-200 shadow-sm">
            <p class="text-xs font-bold text-red-900 uppercase tracking-wider mb-2">Stok Habis</p>
            <p class="text-3xl font-extrabold text-red-700">{{ $stats['empty'] }}</p>
        </div>
    </div>

    {{-- Product List --}}
    <div class="glass rounded-2xl overflow-hidden bg-white border border-[#e8e3d5] shadow-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#e8e3d5] bg-[#F4EDD9]/60">
            <h3 class="font-bold text-[#042623] font-playfair">📦 Daftar Produk</h3>
            <span class="text-xs text-slate-600 font-bold">{{ $products->total() }} produk ditemukan</span>
        </div>

        @if($products->isEmpty())
        <div class="text-center py-16">
            <p class="text-4xl mb-3">📦</p>
            <p class="text-slate-600 font-medium mb-4">Belum ada produk</p>
            <a href="{{ route('admin.products.create') }}"
               class="inline-block px-6 py-2.5 rounded-xl text-xs font-extrabold text-[#042623] gold-gradient border border-[#C6A443] shadow-md">+ Tambah Produk Pertama</a>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-800" style="min-width:700px;">
                <thead>
                    <tr class="text-xs text-slate-700 uppercase tracking-wider font-bold bg-[#F4EDD9]/40 border-b border-[#e8e3d5]">
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
                <tbody class="divide-y divide-slate-100">
                    @foreach($products as $p)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3 px-4">
                            @if($p->thumbnail_url)
                            <img src="{{ $p->thumbnail_url }}" class="w-10 h-10 rounded-lg object-cover border border-slate-200 shadow-sm">
                            @else
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center text-xl bg-amber-50 border border-amber-200">
                                🪙
                            </div>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <span class="font-bold text-slate-900 block">{{ $p->name }}</span>
                            <span class="text-xs font-mono font-semibold text-slate-500">SKU: {{ $p->sku }} • {{ $p->category->name ?? '-' }}</span>
                        </td>
                        <td class="py-3 px-4 font-bold text-[#085C54] whitespace-nowrap">{{ $p->gold_purity }}</td>
                        <td class="py-3 px-4 font-semibold text-slate-700 whitespace-nowrap">{{ number_format($p->weight_gram, 3) }} gram</td>
                        <td class="py-3 px-4 font-extrabold text-[#C6A443] whitespace-nowrap">Rp {{ number_format($p->base_price, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-center font-bold text-slate-900 whitespace-nowrap">{{ $p->stock }} pcs</td>
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            @if($p->is_available && $p->stock > 0)
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-900 border border-emerald-300">Tersedia</span>
                            @elseif($p->stock == 0)
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-900 border border-red-300">Habis</span>
                            @else
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-800 border border-slate-300">Sembunyi</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.products.edit', $p) }}" class="btn-edit text-xs">Edit</a>
                                <form action="{{ route('admin.products.destroy', $p) }}" method="POST" onsubmit="return confirm('Hapus produk ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger text-xs">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">{{ $products->links() }}</div>
        @endif
    </div>
</x-admin-app>
