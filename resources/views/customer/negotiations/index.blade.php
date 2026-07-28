<x-customer-app>
    <x-slot name="pageTitle">Tawar Harga Saya</x-slot>
    <x-slot name="breadcrumb">Daftar pengajuan penawaran harga produk yang Anda ajukan</x-slot>

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

    {{-- Action Bar --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        {{-- Status Filter Tabs --}}
        <div class="flex flex-wrap gap-2">
            @php $currentStatus = request('status'); @endphp
            <a href="{{ route('customer.negotiations.index') }}"
               class="px-4 py-2 rounded-xl text-xs sm:text-sm font-medium transition-all {{ !$currentStatus ? 'bg-amber-500 text-gray-950 font-bold shadow-lg' : 'glass text-gray-300 hover:bg-white/10' }}">
                Semua Status
            </a>
            <a href="{{ route('customer.negotiations.index', ['status' => 'pending']) }}"
               class="px-4 py-2 rounded-xl text-xs sm:text-sm font-medium transition-all {{ $currentStatus === 'pending' ? 'bg-amber-500 text-gray-950 font-bold shadow-lg' : 'glass text-gray-300 hover:bg-white/10' }}">
                ⏳ Menunggu (Pending)
            </a>
            <a href="{{ route('customer.negotiations.index', ['status' => 'approved']) }}"
               class="px-4 py-2 rounded-xl text-xs sm:text-sm font-medium transition-all {{ $currentStatus === 'approved' ? 'bg-green-500 text-gray-950 font-bold shadow-lg' : 'glass text-gray-300 hover:bg-white/10' }}">
                ✅ Disetujui
            </a>
            <a href="{{ route('customer.negotiations.index', ['status' => 'rejected']) }}"
               class="px-4 py-2 rounded-xl text-xs sm:text-sm font-medium transition-all {{ $currentStatus === 'rejected' ? 'bg-red-500 text-white font-bold shadow-lg' : 'glass text-gray-300 hover:bg-white/10' }}">
                ❌ Ditolak
            </a>
        </div>

        <a href="{{ route('customer.negotiations.create') }}"
           class="px-5 py-2.5 rounded-xl text-sm font-bold text-gray-950 shadow-lg hover:brightness-110 transition flex items-center gap-2"
           style="background:linear-gradient(135deg,#f59e0b,#d97706);">
            <span>🤝</span> Ajukan Tawar Harga
        </a>
    </div>

    {{-- Negotiations Table / List --}}
    @if($negotiations->isEmpty())
    <div class="glass rounded-2xl p-12 text-center">
        <span class="text-5xl">💬</span>
        <h3 class="text-lg font-semibold text-white mt-4">Belum Ada Pengajuan Tawar Harga</h3>
        <p class="text-sm text-gray-400 mt-1">Anda dapat menawar harga produk pilihan Anda sebelum melakukan reservasi.</p>
        <a href="{{ route('customer.negotiations.create') }}"
           class="inline-block mt-5 px-6 py-2.5 rounded-xl text-sm font-bold text-gray-950"
           style="background:linear-gradient(135deg,#f59e0b,#d97706);">
            Ajukan Tawar Harga Sekarang
        </a>
    </div>
    @else
    <div class="glass rounded-2xl overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-300 min-w-[800px]">
                <thead class="text-xs uppercase bg-white/5 text-gray-400 border-b border-amber-500/10">
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
                <tbody class="divide-y divide-white/5">
                    @foreach($negotiations as $item)
                    <tr class="hover:bg-white/5 transition">
                        <td class="px-4 py-3.5 font-mono font-semibold text-amber-400 whitespace-nowrap">
                            {{ $item->negotiation_code }}
                            <span class="block text-xs text-gray-500 font-sans font-normal">{{ $item->created_at->format('d M Y H:i') }}</span>
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            <div class="font-semibold text-white">{{ $item->product->name ?? 'Produk Dihapus' }}</div>
                            <div class="text-xs text-gray-400">{{ number_format($item->product->weight_gram ?? 0, 3) }} gram • Qty: {{ $item->quantity }}</div>
                        </td>
                        <td class="px-4 py-3.5 text-gray-400 whitespace-nowrap">
                            Rp {{ number_format($item->original_price, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3.5 font-semibold text-white whitespace-nowrap">
                            Rp {{ number_format($item->offered_price, 0, ',', '.') }}
                            @php
                                $diff = $item->original_price - $item->offered_price;
                            @endphp
                            @if($diff > 0)
                            <span class="block text-xs text-green-400">Hemat Rp {{ number_format($diff, 0, ',', '.') }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 font-semibold whitespace-nowrap">
                            @if($item->agreed_price)
                                <span class="text-amber-400 text-base">Rp {{ number_format($item->agreed_price, 0, ',', '.') }}</span>
                            @else
                                <span class="text-gray-500 text-xs italic">- Belum ditentukan -</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            @if($item->status === 'pending')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                    ⏳ Menunggu Konfirmasi
                                </span>
                            @elseif($item->status === 'approved')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-green-500/10 text-green-400 border border-green-500/20">
                                    ✅ Disetujui
                                </span>
                            @elseif($item->status === 'rejected')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-red-500/10 text-red-400 border border-red-500/20">
                                    ❌ Ditolak
                                </span>
                            @elseif($item->status === 'used')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-gray-500/10 text-gray-400 border border-gray-500/20">
                                    🏷️ Sudah Digunakan
                                </span>
                            @endif

                            @if($item->admin_notes)
                            <p class="text-xs text-gray-400 mt-1 italic">Note Admin: "{{ $item->admin_notes }}"</p>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-right whitespace-nowrap">
                            @if($item->status === 'approved')
                                <a href="{{ route('customer.reservations.create', ['negotiation_id' => $item->id]) }}"
                                   class="inline-block whitespace-nowrap px-3.5 py-1.5 rounded-lg text-xs font-bold text-gray-950 bg-green-400 hover:bg-green-300 transition shadow">
                                    Lanjut Reservasi →
                                </a>
                            @else
                                <span class="text-xs text-gray-500">-</span>
                            @endif
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
</x-customer-app>
