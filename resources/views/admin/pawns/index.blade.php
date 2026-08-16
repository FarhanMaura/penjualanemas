<x-admin-app>
    <x-slot name="pageTitle">Manajemen Gadai</x-slot>
    <x-slot name="breadcrumb">Kelola dan monitor semua gadai emas pelanggan</x-slot>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-5 mb-8">
        <div class="glass rounded-2xl p-5 bg-amber-50/70 border border-amber-200 shadow-sm">
            <div class="flex justify-between items-start mb-2">
                <p class="text-xs font-bold text-amber-900 uppercase tracking-wider">Gadai Aktif</p>
                <span class="text-xl">🏦</span>
            </div>
            <p class="text-2xl font-extrabold text-[#C6A443]">{{ $stats['active'] }}</p>
        </div>
        <div class="glass rounded-2xl p-5 bg-emerald-50/70 border border-emerald-200 shadow-sm">
            <div class="flex justify-between items-start mb-2">
                <p class="text-xs font-bold text-emerald-900 uppercase tracking-wider">Sudah Ditebus</p>
                <span class="text-xl">✅</span>
            </div>
            <p class="text-2xl font-extrabold text-emerald-700">{{ $stats['redeemed'] }}</p>
        </div>
        <div class="glass rounded-2xl p-5 bg-red-50/70 border border-red-200 shadow-sm">
            <div class="flex justify-between items-start mb-2">
                <p class="text-xs font-bold text-red-900 uppercase tracking-wider">Melewati JT</p>
                <span class="text-xl">⚠️</span>
            </div>
            <p class="text-2xl font-extrabold text-red-700">{{ $stats['overdue'] }}</p>
        </div>
        <div class="glass rounded-2xl p-5 bg-blue-50/70 border border-blue-200 shadow-sm">
            <div class="flex justify-between items-start mb-2">
                <p class="text-xs font-bold text-blue-900 uppercase tracking-wider">Total Pinjaman Aktif</p>
                <span class="text-xl">💰</span>
            </div>
            <p class="text-xl font-extrabold text-blue-700">Rp {{ number_format($stats['total_loans'], 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="glass rounded-2xl p-4 mb-6 bg-white border border-[#e8e3d5] shadow-sm">
        <form method="GET" action="{{ route('admin.pawns.index') }}" class="flex gap-3">
            <select name="status" onchange="this.form.submit()" class="rounded-xl px-4 py-2 text-sm text-slate-900 font-bold outline-none bg-white border border-[#cbd5e1] cursor-pointer">
                <option value="" class="text-slate-900">Semua Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }} class="text-slate-900">Aktif</option>
                <option value="redeemed" {{ request('status') == 'redeemed' ? 'selected' : '' }} class="text-slate-900">Ditebus</option>
                <option value="forfeited" {{ request('status') == 'forfeited' ? 'selected' : '' }} class="text-slate-900">Hangus</option>
            </select>
        </form>
    </div>

    {{-- Tabel Gadai --}}
    @if($pawns->isEmpty())
    <div class="glass rounded-3xl p-12 text-center bg-white border border-[#e8e3d5] shadow-md">
        <span class="text-6xl">📭</span>
        <p class="text-slate-700 font-bold text-lg mt-4">Belum ada data gadai</p>
    </div>
    @else
    <div class="glass rounded-2xl overflow-hidden bg-white border border-[#e8e3d5] shadow-md">
        <table class="w-full text-left text-slate-800">
            <thead>
                <tr class="text-xs text-slate-700 font-bold uppercase tracking-wider bg-[#F4EDD9]/60 border-b border-[#e8e3d5]">
                    <th class="py-4 px-5">Kode / Pelanggan</th>
                    <th class="py-4 px-5">Deskripsi Emas</th>
                    <th class="py-4 px-5 text-right">Pinjaman</th>
                    <th class="py-4 px-5 text-right">Bunga</th>
                    <th class="py-4 px-5 text-center">Jatuh Tempo</th>
                    <th class="py-4 px-5 text-center">Status</th>
                    <th class="py-4 px-5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($pawns as $pawn)
                @php
                    $daysLeft  = now()->diffInDays($pawn->due_date, false);
                    $isExpired = $daysLeft < 0;
                    $isWarning = !$isExpired && $daysLeft <= 7;
                    $statusCls = [
                        'active'    => 'bg-amber-100 text-amber-900 border-amber-300',
                        'redeemed'  => 'bg-emerald-100 text-emerald-900 border-emerald-300',
                        'forfeited' => 'bg-red-100 text-red-900 border-red-300',
                    ][$pawn->status] ?? 'bg-slate-100 text-slate-800 border-slate-300';
                @endphp
                <tr class="text-sm hover:bg-slate-50 transition {{ $isExpired && $pawn->status=='active' ? 'bg-red-50/50' : '' }}">
                    <td class="py-4 px-5">
                        <p class="font-mono text-xs font-bold text-[#085C54]">{{ $pawn->pawn_code }}</p>
                        <p class="font-bold text-slate-900 mt-0.5">{{ $pawn->transaction->user->name ?? '-' }}</p>
                    </td>
                    <td class="py-4 px-5">
                        <p class="text-slate-900 font-bold text-sm">{{ $pawn->gold_description }}</p>
                        <p class="text-xs font-semibold text-slate-500">{{ $pawn->gold_purity }} • {{ number_format($pawn->weight_gram, 2) }}g</p>
                    </td>
                    <td class="py-4 px-5 text-right font-extrabold text-[#C6A443]">
                        Rp {{ number_format($pawn->loan_amount, 0, ',', '.') }}
                    </td>
                    <td class="py-4 px-5 text-right font-bold text-slate-700">{{ $pawn->interest_rate }}%/bln</td>
                    <td class="py-4 px-5 text-center">
                        <p class="text-sm font-bold {{ $isExpired ? 'text-red-700 font-extrabold' : ($isWarning ? 'text-amber-800' : 'text-slate-800') }}">
                            {{ $pawn->due_date?->isoFormat('D MMM Y') }}
                        </p>
                        @if($pawn->status === 'active')
                        <p class="text-xs mt-0.5 font-semibold {{ $isExpired ? 'text-red-700' : 'text-slate-500' }}">
                            {{ $isExpired ? abs($daysLeft).' hari lewat' : $daysLeft.' hari lagi' }}
                        </p>
                        @endif
                    </td>
                    <td class="py-4 px-5 text-center">
                        <span class="text-xs px-2.5 py-0.5 rounded-full font-bold border {{ $statusCls }}">{{ ucfirst($pawn->status) }}</span>
                    </td>
                    <td class="py-4 px-5 text-right">
                        <a href="{{ route('admin.pawns.show', $pawn) }}" class="btn-edit text-xs">Detail</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 bg-slate-50 border-t border-slate-100">{{ $pawns->links() }}</div>
    </div>
    @endif
</x-admin-app>
