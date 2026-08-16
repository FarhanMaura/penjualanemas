<x-admin-app>
    <x-slot name="pageTitle">Pengajuan Tawar Harga</x-slot>

    @if(session('success'))
    <div class="rounded-xl p-4 mb-6 bg-emerald-50 border border-emerald-300 text-emerald-900 shadow-sm flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="text-xl">✅</span>
            <p class="text-sm font-bold">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="rounded-xl p-4 mb-6 bg-red-50 border border-red-300 text-red-900 shadow-sm flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="text-xl">⚠️</span>
            <p class="text-sm font-bold">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    {{-- Stats Widgets --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="glass rounded-2xl p-4 flex items-center gap-4 bg-white border border-[#e8e3d5] shadow-sm">
            <div class="w-12 h-12 rounded-xl gold-gradient flex items-center justify-center text-xl shadow-sm border border-[#C6A443]">📊</div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Pengajuan</p>
                <p class="text-xl font-extrabold text-[#042623]">{{ number_format($stats['total']) }}</p>
            </div>
        </div>
        <div class="glass rounded-2xl p-4 flex items-center gap-4 bg-amber-50/60 border border-amber-200 shadow-sm">
            <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-900 flex items-center justify-center text-xl border border-amber-300">⏳</div>
            <div>
                <p class="text-xs font-bold text-amber-900 uppercase tracking-wider">Menunggu Konfirmasi</p>
                <p class="text-xl font-extrabold text-[#C6A443]">{{ number_format($stats['pending']) }}</p>
            </div>
        </div>
        <div class="glass rounded-2xl p-4 flex items-center gap-4 bg-emerald-50/60 border border-emerald-200 shadow-sm">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-900 flex items-center justify-center text-xl border border-emerald-300">✅</div>
            <div>
                <p class="text-xs font-bold text-emerald-900 uppercase tracking-wider">Disetujui</p>
                <p class="text-xl font-extrabold text-[#085C54]">{{ number_format($stats['approved']) }}</p>
            </div>
        </div>
        <div class="glass rounded-2xl p-4 flex items-center gap-4 bg-red-50/60 border border-red-200 shadow-sm">
            <div class="w-12 h-12 rounded-xl bg-red-100 text-red-900 flex items-center justify-center text-xl border border-red-300">❌</div>
            <div>
                <p class="text-xs font-bold text-red-900 uppercase tracking-wider">Ditolak</p>
                <p class="text-xl font-extrabold text-red-700">{{ number_format($stats['rejected']) }}</p>
            </div>
        </div>
    </div>

    {{-- Filter & Search --}}
    <form method="GET" action="{{ route('admin.negotiations.index') }}" class="flex flex-col sm:flex-row gap-3 mb-6">
        <div class="flex flex-wrap gap-2">
            @php $currentStatus = request('status'); @endphp
            <a href="{{ route('admin.negotiations.index') }}"
               class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all {{ !$currentStatus ? 'bg-[#085C54] text-[#E3D193] shadow-md border border-[#063e39]' : 'bg-white text-slate-700 hover:bg-[#F4EDD9] border border-slate-200 shadow-sm' }}">
                Semua Status
            </a>
            <a href="{{ route('admin.negotiations.index', ['status' => 'pending']) }}"
               class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all {{ $currentStatus === 'pending' ? 'bg-amber-500 text-slate-950 shadow-md border border-amber-600' : 'bg-white text-slate-700 hover:bg-[#F4EDD9] border border-slate-200 shadow-sm' }}">
                ⏳ Menunggu ({{ $stats['pending'] }})
            </a>
            <a href="{{ route('admin.negotiations.index', ['status' => 'approved']) }}"
               class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all {{ $currentStatus === 'approved' ? 'bg-emerald-600 text-white shadow-md border border-emerald-700' : 'bg-white text-slate-700 hover:bg-[#F4EDD9] border border-slate-200 shadow-sm' }}">
                ✅ Disetujui
            </a>
            <a href="{{ route('admin.negotiations.index', ['status' => 'rejected']) }}"
               class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all {{ $currentStatus === 'rejected' ? 'bg-red-600 text-white shadow-md border border-red-700' : 'bg-white text-slate-700 hover:bg-[#F4EDD9] border border-slate-200 shadow-sm' }}">
                ❌ Ditolak
            </a>
        </div>
        <div class="flex-1 flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari kode, nama pelanggan, atau produk..."
                   class="w-full rounded-xl px-4 py-2 text-sm text-slate-900 font-semibold placeholder-slate-400 outline-none bg-white border border-[#e8e3d5] shadow-sm">
            <button type="submit" class="px-5 py-2 rounded-xl text-sm font-bold text-[#042623] gold-gradient border border-[#C6A443] shadow-md hover:brightness-110">
                🔍
            </button>
        </div>
    </form>

    {{-- Negotiations Table --}}
    @if($negotiations->isEmpty())
    <div class="glass rounded-2xl p-12 text-center bg-white border border-[#e8e3d5] shadow-md">
        <span class="text-5xl">💬</span>
        <h3 class="text-lg font-bold text-slate-900 mt-4">Belum Ada Pengajuan Penawaran</h3>
        <p class="text-sm text-slate-600 mt-1">Tidak ada data penawaran harga yang sesuai dengan filter.</p>
    </div>
    @else
    <div class="glass rounded-2xl overflow-hidden bg-white border border-[#e8e3d5] shadow-md">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-800 min-w-[800px]">
                <thead class="text-xs uppercase bg-[#F4EDD9]/60 text-slate-700 font-bold border-b border-[#e8e3d5]">
                    <tr>
                        <th class="px-6 py-4">Kode Tawar</th>
                        <th class="px-6 py-4">Pelanggan</th>
                        <th class="px-6 py-4">Produk</th>
                        <th class="px-6 py-4">Harga Normal</th>
                        <th class="px-6 py-4">Penawaran Pelanggan</th>
                        <th class="px-6 py-4">Kesepakatan Admin</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($negotiations as $item)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4 font-mono font-bold text-[#085C54]">
                            {{ $item->negotiation_code }}
                            <span class="block text-xs text-slate-500 font-sans font-normal">{{ $item->created_at->format('d M Y H:i') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-900">{{ $item->user->name ?? 'User Hapus' }}</div>
                            <div class="text-xs text-slate-500 font-medium">{{ $item->user->profile?->phone ?? $item->user->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-900">{{ $item->product->name ?? 'Produk Dihapus' }}</div>
                            <div class="text-xs text-slate-500 font-semibold">{{ number_format($item->product->weight_gram ?? 0, 3) }} gram • Qty: {{ $item->quantity }}</div>
                        </td>
                        <td class="px-6 py-4 text-slate-600 font-semibold whitespace-nowrap">
                            Rp {{ number_format($item->original_price, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 font-bold text-slate-900 whitespace-nowrap">
                            Rp {{ number_format($item->offered_price, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 font-extrabold whitespace-nowrap">
                            @if($item->agreed_price)
                                <span class="text-[#C6A443]">Rp {{ number_format($item->agreed_price, 0, ',', '.') }}</span>
                            @else
                                <span class="text-slate-400 font-normal italic">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusBadge = [
                                    'pending'  => 'bg-amber-100 text-amber-900 border-amber-300',
                                    'approved' => 'bg-emerald-100 text-emerald-900 border-emerald-300',
                                    'rejected' => 'bg-red-100 text-red-900 border-red-300',
                                    'used'     => 'bg-blue-100 text-blue-900 border-blue-300',
                                ][$item->status] ?? 'bg-slate-100 text-slate-800 border-slate-300';
                            @endphp
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $statusBadge }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <a href="{{ route('admin.negotiations.show', $item->id) }}"
                               class="btn-edit text-xs">
                                Respon →
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 bg-slate-50 border-t border-slate-100">
            {{ $negotiations->links() }}
        </div>
    </div>
    @endif
</x-admin-app>
