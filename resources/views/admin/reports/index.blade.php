<x-admin-app>
    <x-slot name="pageTitle">Laporan & Analitik</x-slot>
    <x-slot name="breadcrumb">Ringkasan kinerja toko dan analitik bisnis</x-slot>

    {{-- Filter Periode --}}
    <div class="flex gap-2 mb-8">
        @foreach(['week'=>'7 Hari', 'month'=>'Bulan Ini', 'quarter'=>'Kuartal Ini', 'year'=>'Tahun Ini'] as $val => $label)
        <a href="{{ route('admin.reports.index', ['period'=>$val]) }}"
           class="px-4 py-2 rounded-xl text-sm font-medium transition {{ $period === $val ? 'text-white' : 'glass text-gray-400 hover:text-white hover:bg-white/10' }}"
           style="{{ $period === $val ? 'background:linear-gradient(135deg,#f59e0b,#d97706);' : '' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-5 mb-8">
        <div class="glass rounded-2xl p-5" style="background:rgba(245,158,11,0.08);">
            <div class="flex justify-between items-start mb-3">
                <p class="text-xs text-gray-400 uppercase tracking-wider">Total Omset</p>
                <span class="text-xl">💰</span>
            </div>
            <p class="text-2xl font-bold text-yellow-400">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 mt-1">dari {{ $totalTrx }} transaksi</p>
        </div>
        <div class="glass rounded-2xl p-5" style="background:rgba(16,185,129,0.06);">
            <div class="flex justify-between items-start mb-3">
                <p class="text-xs text-gray-400 uppercase tracking-wider">Transaksi Pembelian</p>
                <span class="text-xl">🛒</span>
            </div>
            <p class="text-2xl font-bold text-green-400">{{ $purchaseCount }}</p>
            <p class="text-xs text-gray-500 mt-1">pelanggan beli emas</p>
        </div>
        <div class="glass rounded-2xl p-5" style="background:rgba(96,165,250,0.06);">
            <div class="flex justify-between items-start mb-3">
                <p class="text-xs text-gray-400 uppercase tracking-wider">Reservasi Masuk</p>
                <span class="text-xl">📋</span>
            </div>
            <p class="text-2xl font-bold text-blue-400">{{ $reservationStats['total'] }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $reservationStats['confirmed'] }} dikonfirmasi</p>
        </div>
        <div class="glass rounded-2xl p-5" style="background:rgba(196,181,253,0.06);">
            <div class="flex justify-between items-start mb-3">
                <p class="text-xs text-gray-400 uppercase tracking-wider">Pelanggan Baru</p>
                <span class="text-xl">👤</span>
            </div>
            <p class="text-2xl font-bold text-purple-400">{{ $newCustomers }}</p>
            <p class="text-xs text-gray-500 mt-1">dari total {{ $totalCustomers }} member</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Grafik Revenue 7 Hari --}}
        <div class="lg:col-span-2 glass rounded-2xl p-6">
            <h3 class="font-semibold text-yellow-400 mb-6">📊 Revenue 7 Hari Terakhir</h3>
            @php
                $chartMax = max(array_values($chartData)) ?: 1;
            @endphp
            <div class="flex items-end gap-2 h-40">
                @foreach($chartData as $date => $revenue)
                @php
                    $heightPct = $chartMax > 0 ? round(($revenue / $chartMax) * 100) : 0;
                    $isToday = $date === now()->toDateString();
                @endphp
                <div class="flex-1 flex flex-col items-center gap-1">
                    <p class="text-xs text-yellow-400 font-semibold" style="font-size:0.6rem;">
                        {{ $revenue > 0 ? 'Rp '.number_format($revenue/1000000, 1).'jt' : '' }}
                    </p>
                    <div class="w-full rounded-t-lg transition-all" style="height:{{ max($heightPct, 2) }}%; min-height:4px; background:{{ $isToday ? 'linear-gradient(180deg,#f59e0b,#d97706)' : 'rgba(245,158,11,0.3)' }};"></div>
                    <p class="text-xs text-gray-500" style="font-size:0.6rem;">{{ \Carbon\Carbon::parse($date)->isoFormat('D/M') }}</p>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Stats Ringkasan --}}
        <div class="space-y-4">
            {{-- Harga Emas Hari Ini --}}
            <div class="glass rounded-2xl p-5" style="background:rgba(245,158,11,0.05);">
                <h3 class="text-sm font-semibold text-yellow-400 mb-3">💰 Harga Emas Hari Ini</h3>
                @if($latestGoldPrice)
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Harga Jual</span>
                        <span class="text-yellow-400 font-bold">Rp {{ number_format($latestGoldPrice->sell_price_per_gram, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Harga Beli</span>
                        <span class="text-green-400 font-bold">Rp {{ number_format($latestGoldPrice->buy_price_per_gram, 0, ',', '.') }}</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Diperbarui: {{ $latestGoldPrice->price_date->isoFormat('D MMM Y') }}</p>
                </div>
                @else
                <p class="text-gray-500 text-sm">Belum ada data harga emas.</p>
                @endif
            </div>

            {{-- Reservasi Status --}}
            <div class="glass rounded-2xl p-5">
                <h3 class="text-sm font-semibold text-gray-300 mb-4">📋 Status Reservasi</h3>
                @foreach([
                    ['Total', $reservationStats['total'], '#9ca3af'],
                    ['Dikonfirmasi', $reservationStats['confirmed'], '#4ade80'],
                    ['Dibatalkan', $reservationStats['cancelled'], '#f87171'],
                ] as [$label, $val, $color])
                <div class="flex justify-between items-center py-2" style="border-bottom:1px solid rgba(255,255,255,0.04);">
                    <span class="text-sm text-gray-400">{{ $label }}</span>
                    <span class="font-bold text-sm" style="color:{{ $color }}">{{ $val }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Transaksi Terbaru --}}
    <div class="glass rounded-2xl mt-6 overflow-hidden">
        <div class="px-6 py-4 flex justify-between items-center" style="border-bottom:1px solid rgba(255,255,255,0.06);">
            <h3 class="font-semibold text-yellow-400">✅ Transaksi Terbaru</h3>
            <a href="{{ route('admin.transactions.index') }}" class="text-xs text-gray-400 hover:text-white">Lihat Semua →</a>
        </div>
        @if($recentTransactions->isEmpty())
        <div class="py-10 text-center text-gray-500 text-sm">Belum ada transaksi.</div>
        @else
        <table class="data-table w-full">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Pelanggan</th>
                    <th>Tipe</th>
                    <th class="text-right">Jumlah</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentTransactions as $t)
                @php
                    $typeLabel = ['purchase'=>'Pembelian','buyback'=>'Buyback','installment'=>'Cicilan','pawn'=>'Gadai'][$t->type] ?? $t->type;
                    $typeColor = ['purchase'=>'badge-green','buyback'=>'badge-blue','installment'=>'badge-yellow','pawn'=>'badge-gray'][$t->type] ?? 'badge-gray';
                @endphp
                <tr>
                    <td class="font-mono text-xs text-gray-400">{{ $t->transaction_code }}</td>
                    <td class="font-medium text-white">{{ $t->user->name ?? '-' }}</td>
                    <td><span class="badge {{ $typeColor }}">{{ $typeLabel }}</span></td>
                    <td class="text-right font-semibold text-yellow-400">Rp {{ number_format($t->total_amount, 0, ',', '.') }}</td>
                    <td class="text-gray-500 text-xs">{{ $t->created_at->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</x-admin-app>
