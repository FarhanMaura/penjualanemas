<x-admin-app>
    <x-slot name="pageTitle">Manajemen Cicilan</x-slot>
    <x-slot name="breadcrumb">Kelola dan monitor semua cicilan emas pelanggan</x-slot>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-5 mb-8">
        <div class="glass rounded-2xl p-5 bg-blue-50/70 border border-blue-200 shadow-sm">
            <div class="flex justify-between items-start mb-2">
                <p class="text-xs font-bold text-blue-900 uppercase tracking-wider">Aktif</p>
                <span class="text-xl">📅</span>
            </div>
            <p class="text-2xl font-extrabold text-blue-700">{{ $stats['active'] }}</p>
        </div>
        <div class="glass rounded-2xl p-5 bg-emerald-50/70 border border-emerald-200 shadow-sm">
            <div class="flex justify-between items-start mb-2">
                <p class="text-xs font-bold text-emerald-900 uppercase tracking-wider">Lunas</p>
                <span class="text-xl">✅</span>
            </div>
            <p class="text-2xl font-extrabold text-emerald-700">{{ $stats['completed'] }}</p>
        </div>
        <div class="glass rounded-2xl p-5 bg-red-50/70 border border-red-200 shadow-sm">
            <div class="flex justify-between items-start mb-2">
                <p class="text-xs font-bold text-red-900 uppercase tracking-wider">Menunggak</p>
                <span class="text-xl">⚠️</span>
            </div>
            <p class="text-2xl font-extrabold text-red-700">{{ $stats['overdue'] }}</p>
        </div>
        <div class="glass rounded-2xl p-5 bg-amber-50/70 border border-amber-200 shadow-sm">
            <div class="flex justify-between items-start mb-2">
                <p class="text-xs font-bold text-amber-900 uppercase tracking-wider">Total Pendapatan</p>
                <span class="text-xl">💰</span>
            </div>
            <p class="text-2xl font-extrabold text-[#C6A443]">Rp {{ number_format($stats['revenue'], 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="glass rounded-2xl p-4 mb-6 flex gap-4 bg-white border border-[#e8e3d5] shadow-sm">
        <form method="GET" action="{{ route('admin.installments.index') }}" class="flex gap-3">
            <select name="status" onchange="this.form.submit()" class="rounded-xl px-4 py-2 text-sm text-slate-900 font-bold outline-none bg-white border border-[#cbd5e1] cursor-pointer">
                <option value="" class="text-slate-900">Semua Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }} class="text-slate-900">Aktif</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }} class="text-slate-900">Lunas</option>
                <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }} class="text-slate-900">Menunggak</option>
            </select>
        </form>
    </div>

    {{-- Tabel Cicilan --}}
    @if($installments->isEmpty())
    <div class="glass rounded-3xl p-12 text-center bg-white border border-[#e8e3d5] shadow-md">
        <span class="text-6xl">📭</span>
        <p class="text-slate-700 font-bold text-lg mt-4">Belum ada data cicilan</p>
    </div>
    @else
    <div class="glass rounded-2xl overflow-hidden bg-white border border-[#e8e3d5] shadow-md">
        <table class="w-full text-left text-slate-800">
            <thead>
                <tr class="text-xs text-slate-700 font-bold uppercase tracking-wider bg-[#F4EDD9]/60 border-b border-[#e8e3d5]">
                    <th class="py-4 px-5">Pelanggan / Produk</th>
                    <th class="py-4 px-5 text-center">Tenor</th>
                    <th class="py-4 px-5 text-center">Progress</th>
                    <th class="py-4 px-5 text-right">Angsuran/Bln</th>
                    <th class="py-4 px-5 text-center">Status</th>
                    <th class="py-4 px-5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($installments as $plan)
                @php
                    $paid  = $plan->paidCount();
                    $total = $plan->tenure_months;
                    $pct   = $total > 0 ? round(($paid / $total) * 100) : 0;
                    $statusCls = [
                        'active'    => 'bg-blue-100 text-blue-900 border-blue-300',
                        'completed' => 'bg-emerald-100 text-emerald-900 border-emerald-300',
                        'overdue'   => 'bg-red-100 text-red-900 border-red-300',
                        'cancelled' => 'bg-slate-100 text-slate-800 border-slate-300',
                    ][$plan->status] ?? 'bg-slate-100 text-slate-800 border-slate-300';
                    $product = $plan->transaction->items->first()->product ?? null;
                @endphp
                <tr class="text-sm hover:bg-slate-50 transition">
                    <td class="py-4 px-5">
                        <p class="font-bold text-slate-900">{{ $plan->transaction->user->name ?? '-' }}</p>
                        <p class="text-xs font-semibold text-[#085C54] mt-0.5">{{ $product->name ?? 'Produk cicilan' }}</p>
                        <p class="text-xs font-medium text-slate-500">DP: Rp {{ number_format($plan->down_payment, 0, ',', '.') }}</p>
                    </td>
                    <td class="py-4 px-5 text-center font-bold text-slate-800">{{ $plan->tenure_months }} bulan</td>
                    <td class="py-4 px-5">
                        <div class="flex items-center gap-2">
                            <div class="flex-1 h-2.5 rounded-full overflow-hidden bg-slate-100 border border-slate-200">
                                <div class="h-full rounded-full bg-gradient-to-r from-blue-500 to-blue-600" style="width:{{ $pct }}%;"></div>
                            </div>
                            <span class="text-xs font-bold text-blue-800 shrink-0">{{ $paid }}/{{ $total }}</span>
                        </div>
                    </td>
                    <td class="py-4 px-5 text-right font-extrabold text-[#C6A443]">
                        Rp {{ number_format($plan->monthly_amount, 0, ',', '.') }}
                    </td>
                    <td class="py-4 px-5 text-center">
                        <span class="text-xs px-2.5 py-0.5 rounded-full font-bold border {{ $statusCls }}">{{ ucfirst($plan->status) }}</span>
                    </td>
                    <td class="py-4 px-5 text-right">
                        <a href="{{ route('admin.installments.show', $plan) }}" class="btn-edit text-xs">Detail</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 bg-slate-50 border-t border-slate-100">{{ $installments->links() }}</div>
    </div>
    @endif
</x-admin-app>
