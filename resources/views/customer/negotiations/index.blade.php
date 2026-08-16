<x-customer-app>
    <x-slot name="pageTitle">Tawar Harga Saya</x-slot>
    <x-slot name="breadcrumb">Daftar pengajuan penawaran harga produk yang Anda ajukan</x-slot>

    @if(session('success'))
    <div class="rounded-xl p-4 mb-6 bg-emerald-50 border border-emerald-300 text-emerald-900 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-3">
            <span class="text-xl">✅</span>
            <p class="text-sm font-semibold">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="rounded-xl p-4 mb-6 bg-red-50 border border-red-300 text-red-900 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-3">
            <span class="text-xl">⚠️</span>
            <p class="text-sm font-semibold">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    {{-- Action Bar --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        {{-- Status Filter Tabs --}}
        <div class="flex flex-wrap gap-2">
            @php $currentStatus = request('status'); @endphp
            <a href="{{ route('customer.negotiations.index') }}"
               class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all {{ !$currentStatus ? 'bg-[#085C54] text-[#E3D193] shadow-md border border-[#063e39]' : 'bg-white text-slate-700 hover:bg-[#F4EDD9] border border-slate-200 shadow-sm' }}">
                Semua Status
            </a>
            <a href="{{ route('customer.negotiations.index', ['status' => 'pending']) }}"
               class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all {{ $currentStatus === 'pending' ? 'bg-amber-500 text-slate-950 shadow-md border border-amber-600' : 'bg-white text-slate-700 hover:bg-[#F4EDD9] border border-slate-200 shadow-sm' }}">
                ⏳ Menunggu (Pending)
            </a>
            <a href="{{ route('customer.negotiations.index', ['status' => 'approved']) }}"
               class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all {{ $currentStatus === 'approved' ? 'bg-emerald-600 text-white shadow-md border border-emerald-700' : 'bg-white text-slate-700 hover:bg-[#F4EDD9] border border-slate-200 shadow-sm' }}">
                ✅ Disetujui
            </a>
            <a href="{{ route('customer.negotiations.index', ['status' => 'rejected']) }}"
               class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all {{ $currentStatus === 'rejected' ? 'bg-red-600 text-white shadow-md border border-red-700' : 'bg-white text-slate-700 hover:bg-[#F4EDD9] border border-slate-200 shadow-sm' }}">
                ❌ Ditolak
            </a>
        </div>

        <a href="{{ route('customer.negotiations.create') }}"
           class="px-5 py-2.5 rounded-xl text-sm font-extrabold text-[#042623] shadow-lg hover:brightness-110 transition flex items-center gap-2 gold-gradient border border-[#C6A443]">
            <span>🤝</span> Ajukan Tawar Harga
        </a>
    </div>

    {{-- Negotiations Table / List --}}
    @if($negotiations->isEmpty())
    <div class="glass rounded-2xl p-12 text-center bg-white border border-[#e8e3d5]">
        <span class="text-5xl">💬</span>
        <h3 class="text-lg font-bold text-slate-900 mt-4">Belum Ada Pengajuan Tawar Harga</h3>
        <p class="text-sm text-slate-600 mt-1">Anda dapat menawar harga produk pilihan Anda sebelum melakukan reservasi.</p>
        <a href="{{ route('customer.negotiations.create') }}"
           class="inline-block mt-5 px-6 py-2.5 rounded-xl text-sm font-extrabold text-[#042623] gold-gradient border border-[#C6A443] shadow-md">
            Ajukan Tawar Harga Sekarang
        </a>
    </div>
    @else
    <div class="glass rounded-2xl overflow-hidden shadow-md bg-white border border-[#e8e3d5]">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-800 min-w-[800px]">
                <thead class="text-xs uppercase bg-[#F4EDD9]/60 text-slate-700 font-bold border-b border-[#e8e3d5]">
                    <tr>
                        <th class="px-4 py-3.5 whitespace-nowrap">Kode Tawar</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Produk</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Harga Normal</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Harga Penawaran Anda</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Harga Kesepakatan Admin</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Status</th>
                        <th class="px-4 py-3.5 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($negotiations as $item)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-4 py-3.5 font-mono font-bold text-[#085C54] whitespace-nowrap">
                            {{ $item->negotiation_code }}
                            <span class="block text-xs text-slate-500 font-sans font-normal">{{ $item->created_at->format('d M Y H:i') }}</span>
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            <div class="font-bold text-slate-900">{{ $item->product->name ?? 'Produk Dihapus' }}</div>
                            <div class="text-xs font-medium text-slate-500">{{ number_format($item->product->weight_gram ?? 0, 3) }} gram • Qty: {{ $item->quantity }}</div>
                        </td>
                        <td class="px-4 py-3.5 text-slate-600 font-medium whitespace-nowrap">
                            Rp {{ number_format($item->original_price, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3.5 font-bold text-slate-900 whitespace-nowrap">
                            Rp {{ number_format($item->offered_price, 0, ',', '.') }}
                            @php
                                $diff = $item->original_price - $item->offered_price;
                            @endphp
                            @if($diff > 0)
                            <span class="block text-xs font-bold text-emerald-700">Hemat Rp {{ number_format($diff, 0, ',', '.') }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap font-extrabold">
                            @if($item->agreed_price)
                                <span class="text-[#C6A443]">Rp {{ number_format($item->agreed_price, 0, ',', '.') }}</span>
                            @else
                                <span class="text-slate-400 font-normal italic">- Belum disetujui -</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            @php
                                $statusBadge = [
                                    'pending'  => 'bg-amber-100 text-amber-900 border-amber-300',
                                    'approved' => 'bg-emerald-100 text-emerald-900 border-emerald-300',
                                    'rejected' => 'bg-red-100 text-red-900 border-red-300',
                                    'used'     => 'bg-blue-100 text-blue-900 border-blue-300',
                                ][$item->status] ?? 'bg-slate-100 text-slate-800 border-slate-300';

                                $statusLabel = [
                                    'pending'  => '⏳ Menunggu',
                                    'approved' => '✅ Disetujui',
                                    'rejected' => '❌ Ditolak',
                                    'used'     => '🛒 Sudah Dipakai',
                                ][$item->status] ?? ucfirst($item->status);
                            @endphp
                            <span class="inline-block px-2.5 py-1 rounded-full text-xs font-bold border {{ $statusBadge }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-right whitespace-nowrap">
                            @if(in_array($item->status, ['approved', 'used']))
                                <a href="{{ route('customer.reservations.create', ['negotiation_id' => $item->id]) }}"
                                   class="inline-block px-3.5 py-1.5 rounded-xl text-xs font-bold text-slate-950 gold-gradient border border-[#C6A443] shadow hover:brightness-110 transition">
                                    🚀 Reservasi Beli →
                                </a>
                            @else
                                <span class="text-xs text-slate-400">-</span>
                            @endif
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
</x-customer-app>
