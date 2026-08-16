<x-admin-app>
<x-slot name="pageTitle">Dashboard Admin</x-slot>

{{-- Flash Messages --}}
@if(session('success'))
<div class="flash-success mb-6 px-4 py-3 rounded-xl text-sm font-semibold bg-emerald-50 border border-emerald-300 text-emerald-900 shadow-sm" data-flash>✅ {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="flash-error mb-6 px-4 py-3 rounded-xl text-sm font-semibold bg-red-50 border border-red-300 text-red-900 shadow-sm" data-flash>❌ {{ session('error') }}</div>
@endif

{{-- KPI Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">
    <div class="kpi-card border border-[#e8e3d5] shadow-md bg-white">
        <div class="flex justify-between items-start mb-3">
            <p class="kpi-label text-slate-500 font-bold">Total Pelanggan</p>
            <span class="text-2xl">👥</span>
        </div>
        <p class="kpi-value text-[#042623] font-black font-playfair">{{ number_format($stats['customers']) }}</p>
        <p class="kpi-sub text-[#085C54] font-semibold mt-1">Pelanggan terdaftar</p>
    </div>
    <div class="kpi-card border border-[#e8e3d5] shadow-md bg-white">
        <div class="flex justify-between items-start mb-3">
            <p class="kpi-label text-slate-500 font-bold">Reservasi Hari Ini</p>
            <span class="text-2xl">📋</span>
        </div>
        <p class="kpi-value text-[#085C54] font-black font-playfair">{{ $stats['reservations_today'] }}</p>
        <p class="kpi-sub text-[#866a20] font-bold mt-1">⏳ {{ $stats['pending_reservations'] }} menunggu konfirmasi</p>
    </div>
    <div class="kpi-card border border-[#e8e3d5] shadow-md bg-white">
        <div class="flex justify-between items-start mb-3">
            <p class="kpi-label text-slate-500 font-bold">Transaksi Bulan Ini</p>
            <span class="text-2xl">✅</span>
        </div>
        <p class="kpi-value text-[#042623] font-black font-playfair">{{ $stats['transactions_month'] }}</p>
        <p class="kpi-sub text-[#085C54] font-semibold mt-1">Total transaksi</p>
    </div>
    <div class="kpi-card border border-[#e8e3d5] shadow-md bg-white">
        <div class="flex justify-between items-start mb-3">
            <p class="kpi-label text-slate-500 font-bold">Gadai Aktif</p>
            <span class="text-2xl">🏦</span>
        </div>
        <p class="kpi-value text-[#042623] font-black font-playfair">{{ $stats['pawn_active'] }}</p>
        <p class="kpi-sub text-[#866a20] font-semibold mt-1">Gadai berjalan</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6 mb-8">
    {{-- Harga Emas Hari Ini --}}
    <div class="glass rounded-2xl p-5 border border-[#e8e3d5] shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-[#042623] font-playfair text-base">💰 Harga Emas Aktif</h3>
            <a href="{{ route('admin.gold-prices.index') }}" class="btn-gold text-xs shadow-sm">Update</a>
        </div>
        @if($goldPrice)
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="rounded-xl p-3 text-center bg-emerald-50 border border-emerald-200 shadow-sm">
                <p class="text-xs text-emerald-800 font-semibold mb-1">24K — Harga Beli</p>
                <p class="font-black text-[#085C54] text-base font-playfair">Rp {{ number_format($goldPrice->buy_price_per_gram,0,',','.') }}</p>
                <p class="text-xs text-emerald-700 font-medium">/gram</p>
            </div>
            <div class="rounded-xl p-3 text-center bg-[#F4EDD9] border border-[#E3D193] shadow-sm">
                <p class="text-xs text-[#866a20] font-semibold mb-1">24K — Harga Jual</p>
                <p class="font-black text-[#C6A443] text-base font-playfair">Rp {{ number_format($goldPrice->sell_price_per_gram,0,',','.') }}</p>
                <p class="text-xs text-[#866a20] font-medium">/gram</p>
            </div>
        </div>
        <p class="text-xs text-slate-500 mt-3 font-medium">📅 {{ \Carbon\Carbon::parse($goldPrice->price_date)->isoFormat('D MMMM Y') }} • {{ $goldPrice->source }}</p>
        @else
        <p class="text-xs text-slate-500 text-center py-4 font-medium">Harga belum diinput hari ini</p>
        @endif
    </div>

    {{-- Distribusi Tier CRM --}}
    <div class="glass rounded-2xl p-5 border border-[#e8e3d5] shadow-md">
        <h3 class="font-bold text-[#042623] font-playfair text-base mb-4">📊 Distribusi Tier Pelanggan</h3>
        @php $totalCustomers = max(1, array_sum($tierDistribution)); @endphp
        <div class="space-y-3">
            @foreach([
                ['🥉','Bronze','bronze','#085C54'],
                ['🥈','Silver','silver','#64748b'],
                ['🥇','Gold','gold','#C6A443'],
                ['💎','Platinum','platinum','#0284c7'],
            ] as [$icon,$label,$key,$color])
            <div>
                <div class="flex justify-between text-xs mb-1">
                    <span class="text-slate-600 font-medium">{{ $icon }} {{ $label }}</span>
                    <span class="text-[#042623] font-bold">{{ $tierDistribution[$key] }} pelanggan</span>
                </div>
                <div class="h-2 rounded-full bg-slate-100 border border-slate-200">
                    <div class="h-full rounded-full" style="width:{{ round($tierDistribution[$key]/$totalCustomers*100) }}%; background:{{ $color }};"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Quick Links --}}
    <div class="glass rounded-2xl p-5 border border-[#e8e3d5] shadow-md">
        <h3 class="font-bold text-[#042623] font-playfair text-base mb-4">⚡ Akses Cepat</h3>
        <div class="space-y-2">
            <a href="{{ route('admin.reservations.index') }}" class="flex items-center justify-between p-3 rounded-xl bg-[#F4EDD9] hover:bg-white transition border border-slate-200 shadow-sm hover:border-[#085C54]">
                <span class="text-sm font-semibold text-[#042623]">📋 Reservasi Pending</span>
                @if($stats['pending_reservations'] > 0)
                <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-[#F4EDD9] text-[#866a20] border border-[#E3D193]">{{ $stats['pending_reservations'] }}</span>
                @endif
            </a>
            <a href="{{ route('admin.products.create') }}" class="flex items-center p-3 rounded-xl bg-[#F4EDD9] hover:bg-white transition border border-slate-200 shadow-sm hover:border-[#085C54]">
                <span class="text-sm font-semibold text-[#042623]">📦 Tambah Produk</span>
            </a>
            <a href="{{ route('admin.transactions.create') }}" class="flex items-center p-3 rounded-xl bg-[#F4EDD9] hover:bg-white transition border border-slate-200 shadow-sm hover:border-[#085C54]">
                <span class="text-sm font-semibold text-[#042623]">✅ Catat Transaksi</span>
            </a>
            <a href="{{ route('admin.gold-prices.index') }}" class="flex items-center p-3 rounded-xl bg-[#F4EDD9] hover:bg-white transition border border-slate-200 shadow-sm hover:border-[#085C54]">
                <span class="text-sm font-semibold text-[#042623]">💰 Update Harga Emas</span>
            </a>
        </div>
    </div>
</div>

{{-- Reservasi Terbaru --}}
<div class="glass rounded-2xl p-6 border border-[#e8e3d5] shadow-md">
    <div class="flex justify-between items-center mb-5">
        <h3 class="font-bold text-[#042623] font-playfair text-base">📋 Reservasi Terbaru — Perlu Dikonfirmasi</h3>
        <a href="{{ route('admin.reservations.index') }}" class="text-xs text-[#085C54] font-bold hover:underline">Lihat Semua →</a>
    </div>
    @if($recentReservations->isEmpty())
    <div class="text-center py-10">
        <p class="text-slate-500 text-sm font-medium">Belum ada reservasi pending.</p>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="data-table w-full">
            <thead>
                <tr class="border-b border-slate-200 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                    <th class="pb-3">Pelanggan</th>
                    <th class="pb-3">Produk</th>
                    <th class="pb-3">Tgl Kunjungan</th>
                    <th class="pb-3">Dibuat</th>
                    <th class="pb-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($recentReservations as $r)
                <tr>
                    <td class="py-3.5 font-bold text-[#042623]">{{ $r->user->name }}</td>
                    <td class="py-3.5 text-slate-700 font-medium">{{ $r->product->name ?? ($r->pawn_gold_description ?? 'Gadai Emas') }}</td>
                    <td class="py-3.5 text-slate-600">{{ \Carbon\Carbon::parse($r->preferred_date)->isoFormat('D MMM Y') }}</td>
                    <td class="py-3.5 text-slate-400 text-xs">{{ $r->created_at->diffForHumans() }}</td>
                    <td class="py-3.5">
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('admin.reservations.confirm', $r) }}">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-[#e2f2f0] text-[#085C54] border border-[#085C54]/30 hover:bg-[#085C54] hover:text-white transition">✓ Konfirmasi</button>
                            </form>
                            <a href="{{ route('admin.reservations.show', $r) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-[#F4EDD9] text-[#866a20] border border-[#E3D193] hover:bg-[#ebdcb0] transition">Detail</a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

</x-admin-app>
