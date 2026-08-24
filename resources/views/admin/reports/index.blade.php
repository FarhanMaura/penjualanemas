<x-admin-app>
    <x-slot name="pageTitle">Laporan & Analitik</x-slot>
    <x-slot name="breadcrumb">Ringkasan kinerja toko dan analitik bisnis Toko Emas Sinar Baru II</x-slot>

    {{-- Filter Periode --}}
    <div class="flex flex-wrap gap-2 mb-8">
        @foreach(['week'=>'7 Hari Terakhir', 'month'=>'Bulan Ini', 'quarter'=>'Kuartal Ini', 'year'=>'Tahun Ini'] as $val => $label)
        <a href="{{ route('admin.reports.index', ['period'=>$val]) }}"
           class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition shadow-sm {{ $period === $val ? 'gold-gradient text-[#042623] border border-[#C6A443]' : 'bg-white text-slate-700 hover:bg-slate-100 border border-[#e8e3d5]' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-5 mb-8">
        <div class="glass rounded-2xl p-5 bg-white border border-[#e8e3d5] shadow-sm">
            <div class="flex justify-between items-start mb-3">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Penjualan Toko</p>
                <span class="text-xl">💰</span>
            </div>
            <p class="text-2xl font-extrabold text-[#C6A443]">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-500 font-semibold mt-1">dari {{ $totalTrx }} total transaksi</p>
        </div>
        <div class="glass rounded-2xl p-5 bg-emerald-50/60 border border-emerald-200 shadow-sm">
            <div class="flex justify-between items-start mb-3">
                <p class="text-xs font-bold text-emerald-900 uppercase tracking-wider">Transaksi Buyback</p>
                <span class="text-xl">🛒</span>
            </div>
            <p class="text-2xl font-extrabold text-[#085C54]">{{ $buybackCount }} Transaksi</p>
            <p class="text-xs text-emerald-800 font-semibold mt-1">Rp {{ number_format($buybackTotal ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="glass rounded-2xl p-5 bg-blue-50/60 border border-blue-200 shadow-sm">
            <div class="flex justify-between items-start mb-3">
                <p class="text-xs font-bold text-blue-900 uppercase tracking-wider">Reservasi Masuk</p>
                <span class="text-xl">📋</span>
            </div>
            <p class="text-2xl font-extrabold text-blue-900">{{ $reservationStats['total'] }}</p>
            <p class="text-xs text-blue-800 font-semibold mt-1">{{ $reservationStats['confirmed'] }} dikonfirmasi</p>
        </div>
        <div class="glass rounded-2xl p-5 bg-purple-50/60 border border-purple-200 shadow-sm">
            <div class="flex justify-between items-start mb-3">
                <p class="text-xs font-bold text-purple-900 uppercase tracking-wider">Pelanggan Baru</p>
                <span class="text-xl">👤</span>
            </div>
            <p class="text-2xl font-extrabold text-purple-900">{{ $newCustomers }}</p>
            <p class="text-xs text-purple-800 font-semibold mt-1">dari total {{ $totalCustomers }} member</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Grafik Revenue 7 Hari --}}
        <div class="lg:col-span-2 glass rounded-2xl p-6 bg-white border border-[#e8e3d5] shadow-md">
            <h3 class="font-bold text-slate-900 font-playfair mb-6 text-base">📊 Omset 7 Hari Terakhir</h3>
            @php
                $chartMax = max(array_values($chartData)) ?: 1;
            @endphp
            <div class="flex items-end gap-3 h-44 pt-4 border-b border-slate-100">
                @foreach($chartData as $date => $revenue)
                @php
                    $heightPct = $chartMax > 0 ? round(($revenue / $chartMax) * 100) : 0;
                    $isToday = $date === now()->toDateString();
                @endphp
                <div class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end">
                    <p class="text-[10px] text-slate-700 font-bold whitespace-nowrap">
                        {{ $revenue > 0 ? 'Rp '.number_format($revenue/1000000, 1).'jt' : '-' }}
                    </p>
                    <div class="w-full rounded-t-lg transition-all shadow-sm" style="height:{{ max($heightPct, 4) }}%; min-height:6px; background:{{ $isToday ? 'linear-gradient(180deg,#C6A443,#085C54)' : '#e2f2f0' }}; border:1px solid {{ $isToday ? '#085C54' : '#c9e8e4' }};"></div>
                    <p class="text-[10px] text-slate-600 font-semibold">{{ \Carbon\Carbon::parse($date)->isoFormat('D/M') }}</p>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Stats Ringkasan --}}
        <div class="space-y-4">
            {{-- Harga Emas Hari Ini --}}
            <div class="glass rounded-2xl p-5 bg-white border border-[#e8e3d5] shadow-md">
                <h3 class="text-sm font-bold text-slate-900 mb-3">💰 Harga Emas Referensi</h3>
                @if($latestGoldPrice)
                <div class="space-y-2">
                    <div class="flex justify-between text-sm py-1 border-b border-slate-100">
                        <span class="text-slate-600 font-semibold">Harga Jual Toko</span>
                        <span class="text-[#C6A443] font-extrabold">Rp {{ number_format($latestGoldPrice->sell_price_per_gram, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm py-1 border-b border-slate-100">
                        <span class="text-slate-600 font-semibold">Harga Beli (Buyback)</span>
                        <span class="text-[#085C54] font-extrabold">Rp {{ number_format($latestGoldPrice->buy_price_per_gram, 0, ',', '.') }}</span>
                    </div>
                    <p class="text-xs text-slate-500 font-medium mt-2">Diperbarui: {{ $latestGoldPrice->price_date->isoFormat('D MMM Y') }}</p>
                </div>
                @else
                <p class="text-slate-500 text-sm">Belum ada data harga emas.</p>
                @endif
            </div>

            {{-- Reservasi Status --}}
            <div class="glass rounded-2xl p-5 bg-white border border-[#e8e3d5] shadow-md">
                <h3 class="text-sm font-bold text-slate-900 mb-4">📋 Status Reservasi Periode Ini</h3>
                @foreach([
                    ['Total Masuk', $reservationStats['total'], 'text-slate-900'],
                    ['Dikonfirmasi', $reservationStats['confirmed'], 'text-emerald-700'],
                    ['Dibatalkan', $reservationStats['cancelled'], 'text-red-700'],
                ] as [$label, $val, $textColor])
                <div class="flex justify-between items-center py-2 border-b border-slate-100 last:border-0">
                    <span class="text-sm text-slate-600 font-medium">{{ $label }}</span>
                    <span class="font-extrabold text-sm {{ $textColor }}">{{ $val }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Transaksi Terbaru --}}
    <div class="glass rounded-2xl mt-6 overflow-hidden bg-white border border-[#e8e3d5] shadow-md">
        <div class="px-6 py-4 flex justify-between items-center bg-[#F4EDD9]/60 border-b border-[#e8e3d5]">
            <h3 class="font-bold text-[#042623] font-playfair">🧾 Transaksi Selesai Terbaru</h3>
            <a href="{{ route('admin.transactions.index') }}" class="text-xs text-[#085C54] font-bold hover:underline">Lihat Semua →</a>
        </div>
        @if($recentTransactions->isEmpty())
        <div class="py-10 text-center text-slate-500 text-sm">Belum ada transaksi.</div>
        @else
        <div class="overflow-x-auto">
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
                <tbody class="divide-y divide-slate-100">
                    @foreach($recentTransactions as $t)
                    @php
                        $typeLabel = ['purchase'=>'Pembelian','buyback'=>'Buyback','installment'=>'Cicilan','pawn'=>'Gadai'][$t->type] ?? $t->type;
                        $typeColor = ['purchase'=>'badge-green','buyback'=>'badge-blue','installment'=>'badge-yellow','pawn'=>'badge-gray'][$t->type] ?? 'badge-gray';
                    @endphp
                    <tr class="hover:bg-slate-50 transition">
                        <td class="font-mono text-xs font-bold text-[#085C54]">{{ $t->transaction_code }}</td>
                        <td class="font-bold text-slate-900">{{ $t->user->name ?? '-' }}</td>
                        <td><span class="badge {{ $typeColor }}">{{ $typeLabel }}</span></td>
                        <td class="text-right font-extrabold text-slate-900">Rp {{ number_format($t->total_amount, 0, ',', '.') }}</td>
                        <td class="text-slate-500 text-xs font-medium">{{ $t->created_at->isoFormat('D MMM Y, HH:mm') }} WIB</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</x-admin-app>

