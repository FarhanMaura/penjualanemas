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
        <div class="glass flex items-center gap-2 px-3 py-2 rounded-xl text-sm bg-white border border-[#e8e3d5]">
            <span class="text-slate-600 font-bold">Tipe:</span>
            <select name="type" onchange="this.form.submit()" class="bg-transparent text-slate-900 font-bold text-sm focus:outline-none cursor-pointer">
                <option value="" class="text-slate-900">Semua</option>
                @foreach(['purchase'=>'Beli','buyback'=>'Jual Kembali','installment'=>'Cicilan','pawn'=>'Gadai'] as $val=>$lbl)
                <option value="{{ $val }}" {{ request('type')==$val ? 'selected':'' }} class="text-slate-900">{{ $lbl }}</option>
                @endforeach
            </select>
        </div>
        <div class="glass flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-[#e8e3d5]">
            <span class="text-slate-500 text-sm">🔍</span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode / pelanggan..." class="bg-transparent text-sm text-slate-900 font-semibold placeholder-slate-400 focus:outline-none w-48">
        </div>
    </form>
    <a href="{{ route('admin.transactions.create') }}" class="btn-orange">+ Catat Transaksi</a>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="kpi-card border border-[#e8e3d5] shadow-sm bg-white"><p class="kpi-label">Total Transaksi</p><p class="kpi-value text-[#042623]">{{ $stats['total'] }}</p></div>
    <div class="kpi-card border border-[#e8e3d5] shadow-sm bg-white"><p class="kpi-label">Pembelian</p><p class="kpi-value text-[#C6A443]">{{ $stats['purchase'] }}</p></div>
    <div class="kpi-card border border-[#e8e3d5] shadow-sm bg-white"><p class="kpi-label">Cicilan Aktif</p><p class="kpi-value text-blue-700">{{ $stats['installment'] }}</p></div>
    <div class="kpi-card border border-[#e8e3d5] shadow-sm bg-white"><p class="kpi-label">Gadai Berjalan</p><p class="kpi-value text-purple-700">{{ $stats['pawn'] }}</p></div>
</div>

<div class="glass rounded-2xl overflow-hidden bg-white border border-[#e8e3d5] shadow-md">
    <div class="flex items-center justify-between px-6 py-4 border-b border-[#e8e3d5] bg-[#F4EDD9]/60">
        <h3 class="font-bold text-[#042623] font-playfair">🧾 Riwayat Transaksi</h3>
        <span class="text-xs text-slate-600 font-bold">{{ $transactions->total() }} transaksi</span>
    </div>
    @if($transactions->isEmpty())
    <div class="text-center py-16"><p class="text-slate-600 font-medium">Belum ada transaksi.</p></div>
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
                    <td class="font-mono text-xs font-bold text-[#085C54]">{{ $t->transaction_code }}</td>
                    <td class="text-slate-900 font-bold">{{ $t->user->name }}</td>
                    <td>
                        <span class="badge {{
                            match($t->type) {
                                'purchase'    => 'badge-yellow',
                                'buyback'     => 'badge-green',
                                'installment' => 'badge-blue',
                                'pawn'        => 'badge-red',
                            }
                        }}">{{
                            ['purchase'=>'Beli','buyback'=>'Jual','installment'=>'Cicilan','pawn'=>'Gadai'][$t->type]
                        }}</span>
                    </td>
                    <td class="text-slate-900 font-extrabold">Rp {{ number_format($t->total_amount,0,',','.') }}</td>
                    <td class="text-slate-600 font-semibold uppercase">{{ $t->payment_method ?? '-' }}</td>
                    <td>
                        <span class="badge {{
                            match($t->status) {
                                'completed'  => 'badge-green',
                                'in_progress'=> 'badge-blue',
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
