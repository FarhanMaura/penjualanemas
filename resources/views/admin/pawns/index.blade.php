<x-admin-app>
    <x-slot name="pageTitle">Manajemen Gadai</x-slot>
    <x-slot name="breadcrumb">Kelola dan monitor semua gadai emas pelanggan</x-slot>

    {{-- Stats --}}
    <div class="grid grid-cols-4 gap-5 mb-8">
        @foreach([
            ['Gadai Aktif', $stats['active'], '🏦', 'text-yellow-400', 'rgba(245,158,11,0.1)', 'rgba(245,158,11,0.2)'],
            ['Sudah Ditebus', $stats['redeemed'], '✅', 'text-green-400', 'rgba(16,185,129,0.1)', 'rgba(16,185,129,0.2)'],
            ['Melewati JT', $stats['overdue'], '⚠️', 'text-red-400', 'rgba(239,68,68,0.1)', 'rgba(239,68,68,0.2)'],
            ['Total Pinjaman Aktif', 'Rp '.number_format($stats['total_loans'], 0, ',', '.'), '💰', 'text-blue-400', 'rgba(59,130,246,0.1)', 'rgba(59,130,246,0.2)'],
        ] as [$label, $val, $icon, $color, $bg, $border])
        <div class="glass rounded-2xl p-5" style="background:{{ $bg }}; border-color:{{ $border }};">
            <div class="flex justify-between items-start mb-2">
                <p class="text-xs text-gray-400 uppercase tracking-wider">{{ $label }}</p>
                <span class="text-xl">{{ $icon }}</span>
            </div>
            <p class="text-xl font-bold {{ $color }}">{{ $val }}</p>
        </div>
        @endforeach
    </div>

    {{-- Filter --}}
    <div class="glass rounded-2xl p-4 mb-6">
        <form method="GET" action="{{ route('admin.pawns.index') }}" class="flex gap-3">
            <select name="status" onchange="this.form.submit()" class="rounded-xl px-4 py-2 text-sm text-white outline-none"
                    style="background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1);">
                <option value="" class="text-gray-900">Semua Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }} class="text-gray-900">Aktif</option>
                <option value="redeemed" {{ request('status') == 'redeemed' ? 'selected' : '' }} class="text-gray-900">Ditebus</option>
                <option value="forfeited" {{ request('status') == 'forfeited' ? 'selected' : '' }} class="text-gray-900">Hangus</option>
            </select>
        </form>
    </div>

    {{-- Tabel Gadai --}}
    @if($pawns->isEmpty())
    <div class="glass rounded-3xl p-12 text-center">
        <span class="text-6xl">📭</span>
        <p class="text-gray-400 text-lg mt-4">Belum ada data gadai</p>
    </div>
    @else
    <div class="glass rounded-2xl overflow-hidden" style="border-color:rgba(255,255,255,0.06);">
        <table class="w-full text-left">
            <thead>
                <tr class="text-xs text-gray-500 uppercase tracking-wider" style="background:rgba(0,0,0,0.3); border-bottom:1px solid rgba(255,255,255,0.08);">
                    <th class="py-4 px-5">Kode / Pelanggan</th>
                    <th class="py-4 px-5">Deskripsi Emas</th>
                    <th class="py-4 px-5 text-right">Pinjaman</th>
                    <th class="py-4 px-5 text-right">Bunga</th>
                    <th class="py-4 px-5 text-center">Jatuh Tempo</th>
                    <th class="py-4 px-5 text-center">Status</th>
                    <th class="py-4 px-5">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pawns as $pawn)
                @php
                    $daysLeft  = now()->diffInDays($pawn->due_date, false);
                    $isExpired = $daysLeft < 0;
                    $isWarning = !$isExpired && $daysLeft <= 7;
                    $statusCls = [
                        'active'    => 'bg-yellow-900/40 text-yellow-400',
                        'redeemed'  => 'bg-green-900/40 text-green-400',
                        'forfeited' => 'bg-red-900/40 text-red-400',
                    ][$pawn->status] ?? 'bg-gray-900/40 text-gray-400';
                @endphp
                <tr class="text-sm border-b border-gray-800/50 hover:bg-white/5 transition {{ $isExpired && $pawn->status=='active' ? 'bg-red-900/5' : '' }}">
                    <td class="py-4 px-5">
                        <p class="font-mono text-xs text-gray-400">{{ $pawn->pawn_code }}</p>
                        <p class="font-semibold text-white mt-0.5">{{ $pawn->transaction->user->name ?? '-' }}</p>
                    </td>
                    <td class="py-4 px-5">
                        <p class="text-gray-200 text-sm">{{ $pawn->gold_description }}</p>
                        <p class="text-xs text-gray-500">{{ $pawn->gold_purity }} • {{ number_format($pawn->weight_gram, 2) }}g</p>
                    </td>
                    <td class="py-4 px-5 text-right font-semibold text-yellow-400">
                        Rp {{ number_format($pawn->loan_amount, 0, ',', '.') }}
                    </td>
                    <td class="py-4 px-5 text-right text-gray-300">{{ $pawn->interest_rate }}%/bln</td>
                    <td class="py-4 px-5 text-center">
                        <p class="text-sm {{ $isExpired ? 'text-red-400 font-bold' : ($isWarning ? 'text-yellow-400' : 'text-gray-300') }}">
                            {{ $pawn->due_date?->isoFormat('D MMM Y') }}
                        </p>
                        @if($pawn->status === 'active')
                        <p class="text-xs mt-0.5 {{ $isExpired ? 'text-red-400' : 'text-gray-500' }}">
                            {{ $isExpired ? abs($daysLeft).' hari lewat' : $daysLeft.' hari lagi' }}
                        </p>
                        @endif
                    </td>
                    <td class="py-4 px-5 text-center">
                        <span class="text-xs px-2 py-1 rounded-full {{ $statusCls }}">{{ ucfirst($pawn->status) }}</span>
                    </td>
                    <td class="py-4 px-5">
                        <a href="{{ route('admin.pawns.show', $pawn) }}" class="text-blue-400 hover:underline text-xs">Detail</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $pawns->links() }}</div>
    @endif
</x-admin-app>
