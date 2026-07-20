<x-admin-app>
    <x-slot name="pageTitle">Manajemen Reservasi</x-slot>
    <x-slot name="breadcrumb">Kelola permintaan reservasi pelanggan</x-slot>

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

    {{-- KPI Stats --}}
    <div class="grid grid-cols-4 gap-4 mb-6">
        @foreach([
            ['Hari Ini', $stats['today'], 'text-white'],
            ['Menunggu', $stats['pending'], 'text-yellow-400'],
            ['Dikonfirmasi', $stats['confirmed'], 'text-green-400'],
            ['Dibatalkan', $stats['cancelled'], 'text-red-400'],
        ] as [$label, $val, $color])
        <div class="glass rounded-2xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">{{ $label }}</p>
            <p class="text-3xl font-bold {{ $color }}">{{ $val }}</p>
        </div>
        @endforeach
    </div>

    {{-- Filter & Search --}}
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <form method="GET" action="{{ route('admin.reservations.index') }}" class="flex flex-wrap gap-3">
            <div class="glass flex items-center gap-2 px-3 py-2 rounded-xl text-sm">
                <span class="text-gray-400">Status:</span>
                <select name="status" onchange="this.form.submit()" class="bg-transparent text-white text-sm focus:outline-none cursor-pointer">
                    <option value="">Semua</option>
                    @foreach(['pending'=>'Pending','confirmed'=>'Dikonfirmasi','cancelled'=>'Dibatalkan','completed'=>'Selesai'] as $val => $lbl)
                    <option value="{{ $val }}" {{ request('status')==$val ? 'selected':'' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div class="glass flex items-center gap-2 px-3 py-2 rounded-xl text-sm">
                <span class="text-gray-400">Tipe:</span>
                <select name="type" onchange="this.form.submit()" class="bg-transparent text-white text-sm focus:outline-none cursor-pointer">
                    <option value="">Semua</option>
                    @foreach(['purchase'=>'Pembelian (Tunai)','installment'=>'Cicilan Emas','pawn'=>'Gadai Emas'] as $val => $lbl)
                    <option value="{{ $val }}" {{ request('type')==$val ? 'selected':'' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div class="glass flex items-center gap-2 px-3 py-2 rounded-xl">
                <span class="text-gray-400 text-sm">🔍</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pelanggan..."
                       class="bg-transparent text-sm text-white placeholder-gray-500 focus:outline-none w-44">
                <button type="submit" class="text-gray-400 hover:text-white text-xs">Cari</button>
            </div>
        </form>
    </div>

    {{-- Reservation Table --}}
    <div class="glass rounded-2xl overflow-hidden" style="border-color:rgba(255,255,255,0.06);">
        <div class="flex items-center justify-between px-6 py-4" style="border-bottom:1px solid rgba(245,158,11,0.1);">
            <h3 class="font-semibold text-yellow-400">📋 Daftar Reservasi</h3>
            <span class="text-xs text-gray-500">{{ $reservations->total() }} reservasi</span>
        </div>

        @if($reservations->isEmpty())
        <div class="text-center py-16">
            <p class="text-5xl mb-3">📭</p>
            <p class="text-gray-500">Belum ada reservasi.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left" style="min-width:750px;">
                <thead>
                    <tr class="text-xs text-gray-500 uppercase tracking-wider" style="background:rgba(0,0,0,0.3); border-bottom:1px solid rgba(255,255,255,0.08);">
                        <th class="py-3 px-4">Pelanggan</th>
                        <th class="py-3 px-4">Tipe</th>
                        <th class="py-3 px-4">Produk / Keterangan</th>
                        <th class="py-3 px-4 whitespace-nowrap">Kode</th>
                        <th class="py-3 px-4 whitespace-nowrap">Tgl Kunjungan</th>
                        <th class="py-3 px-4 whitespace-nowrap">Dibuat</th>
                        <th class="py-3 px-4 text-center whitespace-nowrap">Status</th>
                        <th class="py-3 px-4 text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reservations as $r)
                    @php
                        $statusStyle = match($r->status) {
                            'pending'   => 'background:rgba(245,158,11,0.15); color:#fbbf24;',
                            'confirmed' => 'background:rgba(34,197,94,0.15); color:#4ade80;',
                            'completed' => 'background:rgba(96,165,250,0.15); color:#60a5fa;',
                            default     => 'background:rgba(239,68,68,0.15); color:#f87171;',
                        };
                        $statusLabel = match($r->status) {
                            'pending'   => 'Pending',
                            'confirmed' => 'Dikonfirmasi',
                            'completed' => 'Selesai',
                            default     => 'Dibatalkan',
                        };
                    @endphp
                    <tr style="border-bottom:1px solid rgba(255,255,255,0.04);" class="hover:bg-white/5 transition">
                        <td class="py-3 px-4">
                            <p class="font-medium text-white">{{ $r->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $r->user->email }}</p>
                        </td>
                        <td class="py-3 px-4 text-xs font-semibold text-yellow-400">
                            {{
                                match($r->type) {
                                    'purchase'    => 'Pembelian (Tunai)',
                                    'installment' => 'Cicilan Emas',
                                    'pawn'        => 'Gadai Emas',
                                    default       => ucfirst($r->type ?? 'Pembelian')
                                }
                            }}
                        </td>
                        <td class="py-3 px-4 text-gray-300 max-w-[150px]">
                            <p class="truncate">{{ $r->product->name ?? ($r->pawn_gold_description ?? 'Gadai Emas') }}</p>
                        </td>
                        <td class="py-3 px-4 text-gray-500 font-mono text-xs whitespace-nowrap">{{ $r->reservation_code }}</td>
                        <td class="py-3 px-4 text-gray-300 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($r->preferred_date)->isoFormat('D MMM Y') }}
                        </td>
                        <td class="py-3 px-4 text-gray-500 whitespace-nowrap text-xs">{{ $r->created_at->diffForHumans() }}</td>
                        <td class="py-3 px-4 text-center">
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium whitespace-nowrap"
                                  style="{{ $statusStyle }}">{{ $statusLabel }}</span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex items-center justify-center gap-1.5 whitespace-nowrap">
                                @if($r->status === 'pending')
                                <form method="POST" action="{{ route('admin.reservations.confirm', $r) }}">
                                    @csrf
                                    <button type="submit"
                                            class="px-2.5 py-1 rounded-lg text-xs font-medium"
                                            style="background:rgba(34,197,94,0.15); color:#4ade80; border:1px solid rgba(34,197,94,0.3);">✓ Konfirmasi</button>
                                </form>
                                <form method="POST" action="{{ route('admin.reservations.reject', $r) }}"
                                      onsubmit="return confirm('Tolak reservasi {{ $r->user->name }}?')">
                                    @csrf
                                    <button type="submit"
                                            class="px-2.5 py-1 rounded-lg text-xs font-medium"
                                            style="background:rgba(239,68,68,0.15); color:#f87171; border:1px solid rgba(239,68,68,0.3);">✗ Tolak</button>
                                </form>
                                @elseif($r->status === 'confirmed')
                                <a href="{{ route('admin.transactions.create', ['reservation_id' => $r->id]) }}"
                                   class="px-2.5 py-1 rounded-lg text-xs font-medium whitespace-nowrap"
                                   style="background:rgba(245,158,11,0.15); color:#fbbf24; border:1px solid rgba(245,158,11,0.3);">Input Trx</a>
                                @endif
                                <a href="{{ route('admin.reservations.show', $r) }}"
                                   class="px-2.5 py-1 rounded-lg text-xs font-medium"
                                   style="background:rgba(255,255,255,0.05); color:#9ca3af; border:1px solid rgba(255,255,255,0.1);">Detail</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4">{{ $reservations->links() }}</div>
        @endif
    </div>
</x-admin-app>
