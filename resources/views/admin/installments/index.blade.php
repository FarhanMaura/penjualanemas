<x-admin-app>
    <x-slot name="pageTitle">Manajemen Cicilan</x-slot>
    <x-slot name="breadcrumb">Kelola dan monitor semua cicilan emas pelanggan</x-slot>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-5 mb-8">
        @foreach([
            ['Aktif', $stats['active'], '📅', 'text-blue-400', 'rgba(59,130,246,0.1)', 'rgba(59,130,246,0.2)'],
            ['Lunas', $stats['completed'], '✅', 'text-green-400', 'rgba(16,185,129,0.1)', 'rgba(16,185,129,0.2)'],
            ['Menunggak', $stats['overdue'], '⚠️', 'text-red-400', 'rgba(239,68,68,0.1)', 'rgba(239,68,68,0.2)'],
            ['Total Pendapatan', 'Rp '.number_format($stats['revenue'], 0, ',', '.'), '💰', 'text-yellow-400', 'rgba(245,158,11,0.1)', 'rgba(245,158,11,0.2)'],
        ] as [$label, $val, $icon, $color, $bg, $border])
        <div class="glass rounded-2xl p-5" style="background:{{ $bg }}; border-color:{{ $border }};">
            <div class="flex justify-between items-start mb-2">
                <p class="text-xs text-gray-400 uppercase tracking-wider">{{ $label }}</p>
                <span class="text-xl">{{ $icon }}</span>
            </div>
            <p class="text-2xl font-bold {{ $color }}">{{ $val }}</p>
        </div>
        @endforeach
    </div>

    {{-- Filter --}}
    <div class="glass rounded-2xl p-4 mb-6 flex gap-4">
        <form method="GET" action="{{ route('admin.installments.index') }}" class="flex gap-3">
            <select name="status" onchange="this.form.submit()" class="rounded-xl px-4 py-2 text-sm text-white outline-none"
                    style="background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1);">
                <option value="" class="text-gray-900">Semua Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }} class="text-gray-900">Aktif</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }} class="text-gray-900">Lunas</option>
                <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }} class="text-gray-900">Menunggak</option>
            </select>
        </form>
    </div>

    {{-- Tabel Cicilan --}}
    @if($installments->isEmpty())
    <div class="glass rounded-3xl p-12 text-center">
        <span class="text-6xl">📭</span>
        <p class="text-gray-400 text-lg mt-4">Belum ada data cicilan</p>
    </div>
    @else
    <div class="glass rounded-2xl overflow-hidden" style="border-color:rgba(255,255,255,0.06);">
        <table class="w-full text-left">
            <thead>
                <tr class="text-xs text-gray-500 uppercase tracking-wider" style="background:rgba(0,0,0,0.3); border-bottom:1px solid rgba(255,255,255,0.08);">
                    <th class="py-4 px-5">Pelanggan / Produk</th>
                    <th class="py-4 px-5 text-center">Tenor</th>
                    <th class="py-4 px-5 text-center">Progress</th>
                    <th class="py-4 px-5 text-right">Angsuran/Bln</th>
                    <th class="py-4 px-5 text-center">Status</th>
                    <th class="py-4 px-5">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($installments as $plan)
                @php
                    $paid  = $plan->paidCount();
                    $total = $plan->tenure_months;
                    $pct   = $total > 0 ? round(($paid / $total) * 100) : 0;
                    $statusCls = [
                        'active'    => 'bg-blue-900/40 text-blue-400',
                        'completed' => 'bg-green-900/40 text-green-400',
                        'overdue'   => 'bg-red-900/40 text-red-400',
                        'cancelled' => 'bg-gray-900/40 text-gray-400',
                    ][$plan->status] ?? 'bg-gray-900/40 text-gray-400';
                    $product = $plan->transaction->items->first()->product ?? null;
                @endphp
                <tr class="text-sm border-b border-gray-800/50 hover:bg-white/5 transition">
                    <td class="py-4 px-5">
                        <p class="font-semibold text-white">{{ $plan->transaction->user->name ?? '-' }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $product->name ?? 'Produk cicilan' }}</p>
                        <p class="text-xs text-gray-500">DP: Rp {{ number_format($plan->down_payment, 0, ',', '.') }}</p>
                    </td>
                    <td class="py-4 px-5 text-center text-gray-300">{{ $plan->tenure_months }} bulan</td>
                    <td class="py-4 px-5">
                        <div class="flex items-center gap-2">
                            <div class="flex-1 h-2 rounded-full overflow-hidden" style="background:rgba(255,255,255,0.05);">
                                <div class="h-full rounded-full" style="width:{{ $pct }}%; background:linear-gradient(90deg,#3b82f6,#60a5fa);"></div>
                            </div>
                            <span class="text-xs text-gray-400 shrink-0">{{ $paid }}/{{ $total }}</span>
                        </div>
                    </td>
                    <td class="py-4 px-5 text-right font-semibold text-yellow-400">
                        Rp {{ number_format($plan->monthly_amount, 0, ',', '.') }}
                    </td>
                    <td class="py-4 px-5 text-center">
                        <span class="text-xs px-2 py-1 rounded-full {{ $statusCls }}">{{ ucfirst($plan->status) }}</span>
                    </td>
                    <td class="py-4 px-5">
                        <a href="{{ route('admin.installments.show', $plan) }}" class="text-blue-400 hover:underline text-xs">Detail</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $installments->links() }}</div>
    @endif
</x-admin-app>
