<x-admin-app>
    <x-slot name="pageTitle">Pengajuan Tawar Harga</x-slot>

    @if(session('success'))
    <div class="glass rounded-xl p-4 mb-6 border-green-500/30 text-green-400 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="text-xl">✅</span>
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="glass rounded-xl p-4 mb-6 border-red-500/30 text-red-400 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="text-xl">⚠️</span>
            <p class="text-sm font-medium">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    {{-- Stats Widgets --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="glass rounded-2xl p-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl gold-gradient flex items-center justify-center text-xl">📊</div>
            <div>
                <p class="text-xs text-gray-400">Total Pengajuan</p>
                <p class="text-xl font-bold text-white">{{ number_format($stats['total']) }}</p>
            </div>
        </div>
        <div class="glass rounded-2xl p-4 flex items-center gap-4 border-amber-500/30">
            <div class="w-12 h-12 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-xl">⏳</div>
            <div>
                <p class="text-xs text-gray-400">Menunggu Konfirmasi</p>
                <p class="text-xl font-bold text-amber-400">{{ number_format($stats['pending']) }}</p>
            </div>
        </div>
        <div class="glass rounded-2xl p-4 flex items-center gap-4 border-green-500/30">
            <div class="w-12 h-12 rounded-xl bg-green-500/20 text-green-400 flex items-center justify-center text-xl">✅</div>
            <div>
                <p class="text-xs text-gray-400">Disetujui</p>
                <p class="text-xl font-bold text-green-400">{{ number_format($stats['approved']) }}</p>
            </div>
        </div>
        <div class="glass rounded-2xl p-4 flex items-center gap-4 border-red-500/30">
            <div class="w-12 h-12 rounded-xl bg-red-500/20 text-red-400 flex items-center justify-center text-xl">❌</div>
            <div>
                <p class="text-xs text-gray-400">Ditolak</p>
                <p class="text-xl font-bold text-red-400">{{ number_format($stats['rejected']) }}</p>
            </div>
        </div>
    </div>

    {{-- Filter & Search --}}
    <form method="GET" action="{{ route('admin.negotiations.index') }}" class="flex flex-col sm:flex-row gap-3 mb-6">
        <div class="flex flex-wrap gap-2">
            @php $currentStatus = request('status'); @endphp
            <a href="{{ route('admin.negotiations.index') }}"
               class="px-4 py-2 rounded-xl text-xs sm:text-sm transition-all {{ !$currentStatus ? 'bg-amber-500 text-gray-950 font-bold shadow-lg' : 'glass text-gray-300 hover:bg-white/10' }}">
                Semua Status
            </a>
            <a href="{{ route('admin.negotiations.index', ['status' => 'pending']) }}"
               class="px-4 py-2 rounded-xl text-xs sm:text-sm transition-all {{ $currentStatus === 'pending' ? 'bg-amber-500 text-gray-950 font-bold shadow-lg' : 'glass text-gray-300 hover:bg-white/10' }}">
                ⏳ Menunggu ({{ $stats['pending'] }})
            </a>
            <a href="{{ route('admin.negotiations.index', ['status' => 'approved']) }}"
               class="px-4 py-2 rounded-xl text-xs sm:text-sm transition-all {{ $currentStatus === 'approved' ? 'bg-green-500 text-gray-950 font-bold shadow-lg' : 'glass text-gray-300 hover:bg-white/10' }}">
                ✅ Disetujui
            </a>
            <a href="{{ route('admin.negotiations.index', ['status' => 'rejected']) }}"
               class="px-4 py-2 rounded-xl text-xs sm:text-sm transition-all {{ $currentStatus === 'rejected' ? 'bg-red-500 text-white font-bold shadow-lg' : 'glass text-gray-300 hover:bg-white/10' }}">
                ❌ Ditolak
            </a>
        </div>
        <div class="flex-1 flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari kode, nama pelanggan, atau produk..."
                   class="w-full rounded-xl px-4 py-2 text-sm text-white placeholder-gray-500 outline-none"
                   style="background:rgba(255,255,255,0.04); border:1px solid rgba(245,158,11,0.15);">
            <button type="submit" class="px-5 py-2 rounded-xl text-sm font-semibold text-white"
                    style="background:linear-gradient(135deg,#f59e0b,#d97706,#92400e);">
                🔍
            </button>
        </div>
    </form>

    {{-- Negotiations Table --}}
    @if($negotiations->isEmpty())
    <div class="glass rounded-2xl p-12 text-center">
        <span class="text-5xl">💬</span>
        <h3 class="text-lg font-semibold text-white mt-4">Belum Ada Pengajuan Penawaran</h3>
        <p class="text-sm text-gray-400 mt-1">Tidak ada data penawaran harga yang sesuai dengan filter.</p>
    </div>
    @else
    <div class="glass rounded-2xl overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-300 min-w-[800px]">
                <thead class="text-xs uppercase bg-white/5 text-gray-400 border-b border-amber-500/10">
                    <tr>
                        <th class="px-6 py-4">Kode Tawar</th>
                        <th class="px-6 py-4">Pelanggan</th>
                        <th class="px-6 py-4">Produk</th>
                        <th class="px-6 py-4">Harga Original</th>
                        <th class="px-6 py-4">Harga Penawaran</th>
                        <th class="px-6 py-4">Status / Disetujui</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($negotiations as $item)
                    <tr class="hover:bg-white/5 transition">
                        <td class="px-6 py-4 font-mono font-semibold text-amber-400">
                            {{ $item->negotiation_code }}
                            <span class="block text-xs text-gray-500 font-sans font-normal">{{ $item->created_at->format('d M Y H:i') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-white">{{ $item->user->name ?? 'User Unknown' }}</div>
                            <div class="text-xs text-gray-400">{{ $item->user->email ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-white">{{ $item->product->name ?? 'Produk Dihapus' }}</div>
                            <div class="text-xs text-gray-400">{{ number_format($item->product->weight_gram ?? 0, 3) }}g • Qty: {{ $item->quantity }}</div>
                        </td>
                        <td class="px-6 py-4 text-gray-400">
                            Rp {{ number_format($item->original_price, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 font-semibold text-amber-300">
                            Rp {{ number_format($item->offered_price, 0, ',', '.') }}
                            @php $selisih = $item->original_price - $item->offered_price; @endphp
                            @if($selisih > 0)
                            <span class="block text-xs text-green-400">Nawar -Rp {{ number_format($selisih, 0, ',', '.') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($item->status === 'pending')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                    ⏳ Pending
                                </span>
                            @elseif($item->status === 'approved')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-green-500/10 text-green-400 border border-green-500/20">
                                    ✅ Rp {{ number_format($item->agreed_price, 0, ',', '.') }}
                                </span>
                            @elseif($item->status === 'rejected')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-red-500/10 text-red-400 border border-red-500/20">
                                    ❌ Ditolak
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.negotiations.show', $item) }}"
                               class="px-3.5 py-1.5 rounded-lg text-xs font-bold text-gray-950 bg-amber-400 hover:bg-amber-300 transition shadow">
                                Process / Detail →
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-white/5">
            {{ $negotiations->links() }}
        </div>
    </div>
    @endif
</x-admin-app>
