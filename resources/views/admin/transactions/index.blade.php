<x-admin-app>
<x-slot name="pageTitle">Manajemen Transaksi</x-slot>

@if(session('success'))
<div class="flash-success" data-flash>✅ {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="flash-error" data-flash>❌ {{ session('error') }}</div>
@endif

<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <form method="GET" class="flex items-center gap-3">
        <div class="glass flex items-center gap-2 px-3 py-2 rounded-xl text-sm">
            <span class="text-gray-400">Tipe:</span>
            <select name="type" onchange="this.form.submit()" class="bg-transparent text-white text-sm focus:outline-none cursor-pointer">
                <option value="">Semua</option>
                @foreach(['purchase'=>'Beli','buyback'=>'Jual Kembali','installment'=>'Cicilan','pawn'=>'Gadai'] as $val=>$lbl)
                <option value="{{ $val }}" {{ request('type')==$val ? 'selected':'' }}>{{ $lbl }}</option>
                @endforeach
            </select>
        </div>
        <div class="glass flex items-center gap-2 px-3 py-2 rounded-xl">
            <span class="text-gray-400 text-sm">🔍</span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode / pelanggan..." class="bg-transparent text-sm text-white placeholder-gray-500 focus:outline-none w-48">
        </div>
    </form>
    <a href="{{ route('admin.transactions.create') }}" class="btn-orange">+ Catat Transaksi</a>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="kpi-card"><p class="kpi-label">Total Transaksi</p><p class="kpi-value text-white">{{ $stats['total'] }}</p></div>
    <div class="kpi-card"><p class="kpi-label">Pembelian</p><p class="kpi-value text-yellow-400">{{ $stats['purchase'] }}</p></div>
    <div class="kpi-card"><p class="kpi-label">Cicilan Aktif</p><p class="kpi-value text-blue-400">{{ $stats['installment'] }}</p></div>
    <div class="kpi-card"><p class="kpi-label">Gadai Berjalan</p><p class="kpi-value text-red-400">{{ $stats['pawn'] }}</p></div>
</div>

<div class="glass rounded-2xl overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4" style="border-bottom:1px solid rgba(245,158,11,0.1);">
        <h3 class="font-semibold text-yellow-400">🧾 Riwayat Transaksi</h3>
        <span class="text-xs text-gray-500">{{ $transactions->total() }} transaksi</span>
    </div>
    @if($transactions->isEmpty())
    <div class="text-center py-16"><p class="text-gray-500">Belum ada transaksi.</p></div>
    @else
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Pelanggan</th>
                    <th>Tipe</th>
                    <th>Total</th>
                    <th>Pembayaran</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $t)
                <tr>
                    <td class="font-mono text-xs text-gray-400">{{ $t->transaction_code }}</td>
                    <td class="text-white font-medium">{{ $t->user->name }}</td>
                    <td>
                        <span class="badge {{
                            match($t->type) {
                                'purchase'    => 'badge-green',
                                'buyback'     => 'badge-blue',
                                'installment' => 'badge-yellow',
                                'pawn'        => 'badge-red',
                            }
                        }}">{{
                            ['purchase'=>'Beli','buyback'=>'Jual','installment'=>'Cicilan','pawn'=>'Gadai'][$t->type]
                        }}</span>
                    </td>
                    <td class="text-white font-semibold">Rp {{ number_format($t->total_amount,0,',','.') }}</td>
                    <td class="text-gray-400">{{ $t->payment_method ?? '-' }}</td>
                    <td>
                        <span class="badge {{
                            match($t->status) {
                                'completed'  => 'badge-green',
                                'in_progress'=> 'badge-yellow',
                                'cancelled'  => 'badge-red',
                                default      => 'badge-gray',
                            }
                        }}">{{ ucfirst($t->status) }}</span>
                    </td>
                    <td>
                        <a href="{{ route('admin.transactions.show', $t) }}" class="btn-edit">Detail</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4">{{ $transactions->links() }}</div>
    @endif
</div>
</x-admin-app>
