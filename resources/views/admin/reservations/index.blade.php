<x-admin-app>
    <x-slot name="pageTitle">Manajemen Reservasi</x-slot>
    <x-slot name="breadcrumb">Kelola permintaan reservasi pelanggan</x-slot>

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

    {{-- KPI Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="glass rounded-2xl p-5 bg-white border border-[#e8e3d5] shadow-sm">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Hari Ini</p>
            <p class="text-3xl font-extrabold text-[#042623]">{{ $stats['today'] }}</p>
        </div>
        <div class="glass rounded-2xl p-5 bg-amber-50/60 border border-amber-200 shadow-sm">
            <p class="text-xs font-bold text-amber-900 uppercase tracking-wider mb-2">Menunggu</p>
            <p class="text-3xl font-extrabold text-[#C6A443]">{{ $stats['pending'] }}</p>
        </div>
        <div class="glass rounded-2xl p-5 bg-emerald-50/60 border border-emerald-200 shadow-sm">
            <p class="text-xs font-bold text-emerald-900 uppercase tracking-wider mb-2">Dikonfirmasi</p>
            <p class="text-3xl font-extrabold text-[#085C54]">{{ $stats['confirmed'] }}</p>
        </div>
        <div class="glass rounded-2xl p-5 bg-red-50/60 border border-red-200 shadow-sm">
            <p class="text-xs font-bold text-red-900 uppercase tracking-wider mb-2">Dibatalkan</p>
            <p class="text-3xl font-extrabold text-red-700">{{ $stats['cancelled'] }}</p>
        </div>
    </div>

    {{-- Filter & Search --}}
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <form method="GET" action="{{ route('admin.reservations.index') }}" class="flex flex-wrap gap-3">
            <div class="glass flex items-center gap-2 px-3 py-2 rounded-xl text-sm bg-white border border-[#e8e3d5]">
                <span class="text-slate-600 font-bold">Status:</span>
                <select name="status" onchange="this.form.submit()" class="bg-transparent text-slate-900 font-bold text-sm focus:outline-none cursor-pointer">
                    <option value="" class="text-slate-900">Semua</option>
                    @foreach(['pending'=>'Pending','confirmed'=>'Dikonfirmasi','cancelled'=>'Dibatalkan','completed'=>'Selesai'] as $val => $lbl)
                    <option value="{{ $val }}" {{ request('status')==$val ? 'selected':'' }} class="text-slate-900">{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div class="glass flex items-center gap-2 px-3 py-2 rounded-xl text-sm bg-white border border-[#e8e3d5]">
                <span class="text-slate-600 font-bold">Tipe:</span>
                <select name="type" onchange="this.form.submit()" class="bg-transparent text-slate-900 font-bold text-sm focus:outline-none cursor-pointer">
                    <option value="" class="text-slate-900">Semua</option>
                    @foreach(['purchase'=>'Pembelian (Tunai)','installment'=>'Cicilan Emas','pawn'=>'Gadai Emas'] as $val => $lbl)
                    <option value="{{ $val }}" {{ request('type')==$val ? 'selected':'' }} class="text-slate-900">{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div class="glass flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-[#e8e3d5]">
                <span class="text-slate-500 text-sm">🔍</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pelanggan..."
                       class="bg-transparent text-sm text-slate-900 font-semibold placeholder-slate-400 focus:outline-none w-44">
                <button type="submit" class="text-xs font-bold text-[#085C54] hover:underline">Cari</button>
            </div>
        </form>
    </div>

    {{-- Reservation Table --}}
    <div class="glass rounded-2xl overflow-hidden bg-white border border-[#e8e3d5] shadow-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#e8e3d5] bg-[#F4EDD9]/60">
            <h3 class="font-bold text-[#042623] font-playfair">📋 Daftar Reservasi</h3>
            <span class="text-xs text-slate-600 font-bold">{{ $reservations->total() }} reservasi</span>
        </div>

        @if($reservations->isEmpty())
        <div class="text-center py-16">
            <p class="text-5xl mb-3">📭</p>
            <p class="text-slate-600 font-medium">Belum ada reservasi.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-800" style="min-width:750px;">
                <thead>
                    <tr class="text-xs text-slate-700 uppercase tracking-wider font-bold bg-[#F4EDD9]/40 border-b border-[#e8e3d5]">
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
                <tbody class="divide-y divide-slate-100">
                    @foreach($reservations as $r)
                    @php
                        $statusStyle = match($r->status) {
                            'pending'   => 'bg-amber-100 text-amber-900 border-amber-300',
                            'confirmed' => 'bg-emerald-100 text-emerald-900 border-emerald-300',
                            'completed' => 'bg-blue-100 text-blue-900 border-blue-300',
                            default     => 'bg-red-100 text-red-900 border-red-300',
                        };
                        $statusLabel = match($r->status) {
                            'pending'   => 'Pending',
                            'confirmed' => 'Dikonfirmasi',
                            'completed' => 'Selesai',
                            default     => 'Batal / Expired',
                        };
                    @endphp
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3.5 px-4">
                            <span class="font-bold text-slate-900 block">{{ $r->user->name ?? 'User Hapus' }}</span>
                            <span class="text-xs text-slate-500 font-medium">{{ $r->user->email ?? '-' }}</span>
                        </td>
                        <td class="py-3.5 px-4 font-semibold text-slate-800">
                            {{
                                match($r->type) {
                                    'purchase'    => 'Pembelian',
                                    'installment' => 'Cicilan',
                                    'pawn'        => 'Gadai',
                                    default       => ucfirst($r->type ?? 'Pembelian')
                                }
                            }}
                        </td>
                        <td class="py-3.5 px-4 font-bold text-[#085C54]">
                            @if(($r->type ?? 'purchase') === 'pawn')
                                📦 {{ $r->pawn_gold_description ?? 'Gadai Emas' }} ({{ $r->pawn_gold_purity }}, {{ number_format($r->pawn_weight_gram, 2) }}g)
                            @else
                                💍 {{ $r->product->name ?? 'Produk Dihapus' }} (Qty: {{ $r->quantity }})
                            @endif
                        </td>
                        <td class="py-3.5 px-4 font-mono text-xs font-bold text-slate-600 whitespace-nowrap">{{ $r->reservation_code }}</td>
                        <td class="py-3.5 px-4 whitespace-nowrap text-xs font-semibold text-slate-700">
                            {{ \Carbon\Carbon::parse($r->preferred_date)->isoFormat('D MMM Y') }}
                            <span class="block text-slate-500 font-normal">{{ \Carbon\Carbon::parse($r->preferred_time)->format('H:i') }} WIB</span>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap text-xs text-slate-500 font-medium">{{ $r->created_at->isoFormat('D MMM Y, HH:mm') }}</td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $statusStyle }}">{{ $statusLabel }}</span>
                        </td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <a href="{{ route('admin.reservations.show', $r) }}" class="btn-edit text-xs">Detail & Aksi</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">{{ $reservations->links() }}</div>
        @endif
    </div>
</x-admin-app>
