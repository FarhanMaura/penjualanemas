<x-admin-app>
<x-slot name="pageTitle">Dashboard Admin</x-slot>

{{-- Flash Messages --}}
@if(session('success'))
<div class="flash-success" data-flash>✅ {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="flash-error" data-flash>❌ {{ session('error') }}</div>
@endif

{{-- KPI Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">
    <div class="kpi-card">
        <div class="flex justify-between items-start mb-3">
            <p class="kpi-label">Total Pelanggan</p>
            <span class="text-2xl">👥</span>
        </div>
        <p class="kpi-value text-white">{{ number_format($stats['customers']) }}</p>
        <p class="kpi-sub text-green-400">Pelanggan terdaftar</p>
    </div>
    <div class="kpi-card">
        <div class="flex justify-between items-start mb-3">
            <p class="kpi-label">Reservasi Hari Ini</p>
            <span class="text-2xl">📋</span>
        </div>
        <p class="kpi-value text-yellow-400">{{ $stats['reservations_today'] }}</p>
        <p class="kpi-sub text-yellow-400">⏳ {{ $stats['pending_reservations'] }} menunggu konfirmasi</p>
    </div>
    <div class="kpi-card">
        <div class="flex justify-between items-start mb-3">
            <p class="kpi-label">Transaksi Bulan Ini</p>
            <span class="text-2xl">✅</span>
        </div>
        <p class="kpi-value text-white">{{ $stats['transactions_month'] }}</p>
        <p class="kpi-sub text-green-400">Total transaksi</p>
    </div>
    <div class="kpi-card">
        <div class="flex justify-between items-start mb-3">
            <p class="kpi-label">Gadai Aktif</p>
            <span class="text-2xl">🏦</span>
        </div>
        <p class="kpi-value text-red-400">{{ $stats['pawn_active'] }}</p>
        <p class="kpi-sub text-red-400">Gadai berjalan</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6 mb-8">
    {{-- Harga Emas Hari Ini --}}
    <div class="glass rounded-2xl p-5">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-semibold text-yellow-400 text-sm">💰 Harga Emas Aktif</h3>
            <a href="{{ route('admin.gold-prices.index') }}" class="btn-gold text-xs">Update</a>
        </div>
        @if($goldPrice)
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="rounded-xl p-3 text-center" style="background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.2);">
                <p class="text-xs text-green-400 mb-1">24K — Harga Beli</p>
                <p class="font-bold text-white text-base">Rp {{ number_format($goldPrice->buy_price_per_gram,0,',','.') }}</p>
                <p class="text-xs text-gray-500">/gram</p>
            </div>
            <div class="rounded-xl p-3 text-center" style="background:rgba(245,158,11,0.08); border:1px solid rgba(245,158,11,0.2);">
                <p class="text-xs text-yellow-400 mb-1">24K — Harga Jual</p>
                <p class="font-bold text-yellow-400 text-base">Rp {{ number_format($goldPrice->sell_price_per_gram,0,',','.') }}</p>
                <p class="text-xs text-gray-500">/gram</p>
            </div>
        </div>
        <p class="text-xs text-gray-500 mt-3">📅 {{ \Carbon\Carbon::parse($goldPrice->price_date)->isoFormat('D MMMM Y') }} • {{ $goldPrice->source }}</p>
        @else
        <p class="text-xs text-gray-500 text-center py-4">Harga belum diinput hari ini</p>
        @endif
    </div>

    {{-- Distribusi Tier CRM --}}
    <div class="glass rounded-2xl p-5">
        <h3 class="font-semibold text-yellow-400 text-sm mb-4">📊 Distribusi Tier Pelanggan</h3>
        @php $totalCustomers = max(1, array_sum($tierDistribution)); @endphp
        <div class="space-y-3">
            @foreach([
                ['🥉','Bronze','bronze','#b45309'],
                ['🥈','Silver','silver','#9ca3af'],
                ['🥇','Gold','gold','#f59e0b'],
                ['💎','Platinum','platinum','#60a5fa'],
            ] as [$icon,$label,$key,$color])
            <div>
                <div class="flex justify-between text-xs mb-1">
                    <span class="text-gray-400">{{ $icon }} {{ $label }}</span>
                    <span class="text-gray-300">{{ $tierDistribution[$key] }} pelanggan</span>
                </div>
                <div class="h-2 rounded-full" style="background:rgba(255,255,255,0.05);">
                    <div class="h-2 rounded-full" style="width:{{ round($tierDistribution[$key]/$totalCustomers*100) }}%; background:{{ $color }};"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Quick Links --}}
    <div class="glass rounded-2xl p-5">
        <h3 class="font-semibold text-yellow-400 text-sm mb-4">⚡ Akses Cepat</h3>
        <div class="space-y-2">
            <a href="{{ route('admin.reservations.index') }}" class="flex items-center justify-between p-3 rounded-xl hover:bg-white/5 transition" style="border:1px solid rgba(255,255,255,0.06);">
                <span class="text-sm text-gray-300">📋 Reservasi Pending</span>
                @if($stats['pending_reservations'] > 0)
                <span class="badge badge-yellow">{{ $stats['pending_reservations'] }}</span>
                @endif
            </a>
            <a href="{{ route('admin.products.create') }}" class="flex items-center p-3 rounded-xl hover:bg-white/5 transition" style="border:1px solid rgba(255,255,255,0.06);">
                <span class="text-sm text-gray-300">📦 Tambah Produk</span>
            </a>
            <a href="{{ route('admin.transactions.create') }}" class="flex items-center p-3 rounded-xl hover:bg-white/5 transition" style="border:1px solid rgba(255,255,255,0.06);">
                <span class="text-sm text-gray-300">✅ Catat Transaksi</span>
            </a>
            <a href="{{ route('admin.gold-prices.index') }}" class="flex items-center p-3 rounded-xl hover:bg-white/5 transition" style="border:1px solid rgba(255,255,255,0.06);">
                <span class="text-sm text-gray-300">💰 Update Harga Emas</span>
            </a>
        </div>
    </div>
</div>

{{-- Reservasi Terbaru --}}
<div class="glass rounded-2xl p-6">
    <div class="flex justify-between items-center mb-5">
        <h3 class="font-semibold text-yellow-400">📋 Reservasi Terbaru — Perlu Dikonfirmasi</h3>
        <a href="{{ route('admin.reservations.index') }}" class="text-xs text-yellow-400 hover:underline">Lihat Semua →</a>
    </div>
    @if($recentReservations->isEmpty())
    <div class="text-center py-10">
        <p class="text-gray-500 text-sm">Belum ada reservasi pending.</p>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Pelanggan</th>
                    <th>Produk</th>
                    <th>Tgl Kunjungan</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentReservations as $r)
                <tr>
                    <td class="font-medium text-white">{{ $r->user->name }}</td>
                    <td class="text-gray-400">{{ $r->product->name ?? ($r->pawn_gold_description ?? 'Gadai Emas') }}</td>
                    <td class="text-gray-300">{{ \Carbon\Carbon::parse($r->preferred_date)->isoFormat('D MMM Y') }}</td>
                    <td class="text-gray-500">{{ $r->created_at->diffForHumans() }}</td>
                    <td>
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('admin.reservations.confirm', $r) }}">
                                @csrf
                                <button type="submit" class="btn-confirm">✓ Konfirmasi</button>
                            </form>
                            <a href="{{ route('admin.reservations.show', $r) }}" class="btn-edit">Detail</a>
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
