<x-admin-app>
<x-slot name="pageTitle">Data Pelanggan</x-slot>

{{-- Search & Filter --}}
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <form method="GET" action="{{ route('admin.customers.index') }}" class="flex flex-wrap items-center gap-3">
        <div class="glass flex items-center gap-2 px-3 py-2 rounded-xl text-sm bg-white border border-[#e8e3d5]">
            <span class="text-slate-600 font-bold">Tier:</span>
            <select name="tier" onchange="this.form.submit()" class="bg-transparent text-slate-900 font-bold text-sm focus:outline-none cursor-pointer">
                <option value="" class="text-slate-900">Semua Tier</option>
                <option value="bronze" {{ request('tier')=='bronze' ? 'selected':'' }} class="text-slate-900">Bronze</option>
                <option value="silver" {{ request('tier')=='silver' ? 'selected':'' }} class="text-slate-900">Silver</option>
                <option value="gold" {{ request('tier')=='gold' ? 'selected':'' }} class="text-slate-900">Gold</option>
                <option value="platinum" {{ request('tier')=='platinum' ? 'selected':'' }} class="text-slate-900">Platinum</option>
            </select>
        </div>
        <div class="glass flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-[#e8e3d5]">
            <span class="text-slate-500 text-sm">🔍</span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..." class="bg-transparent text-sm text-slate-900 font-semibold placeholder-slate-400 focus:outline-none w-52">
            <button type="submit" class="text-xs font-bold text-[#085C54] hover:underline">Cari</button>
        </div>
    </form>
</div>

{{-- Tier Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['tier'=>'🥉 Bronze','count'=>$tierCounts['bronze'] ?? 0,'range'=>'0–4 transaksi','color'=>'#b45309','bg'=>'bg-amber-50/60','border'=>'border-amber-200'],
        ['tier'=>'🥈 Silver','count'=>$tierCounts['silver'] ?? 0,'range'=>'5–9 transaksi','color'=>'#334155','bg'=>'bg-slate-50/60','border'=>'border-slate-200'],
        ['tier'=>'🥇 Gold','count'=>$tierCounts['gold'] ?? 0,'range'=>'10–19 transaksi','color'=>'#866a20','bg'=>'bg-amber-50/60','border'=>'border-amber-300'],
        ['tier'=>'💎 Platinum','count'=>$tierCounts['platinum'] ?? 0,'range'=>'20+ transaksi','color'=>'#0284c7','bg'=>'bg-blue-50/60','border'=>'border-blue-200'],
    ] as $t)
    <div class="glass rounded-2xl p-4 border {{ $t['border'] }} {{ $t['bg'] }} shadow-sm">
        <p class="text-xs font-extrabold uppercase tracking-wider" style="color:{{ $t['color'] }}">{{ $t['tier'] }}</p>
        <p class="text-3xl font-extrabold text-[#042623] mt-1 font-playfair">{{ $t['count'] }}</p>
        <p class="text-xs text-slate-600 font-medium mt-0.5">{{ $t['range'] }}</p>
    </div>
    @endforeach
</div>

{{-- Customer Table --}}
<div class="glass rounded-2xl overflow-hidden bg-white border border-[#e8e3d5] shadow-md">
    <div class="flex items-center justify-between px-6 py-4 border-b border-[#e8e3d5] bg-[#F4EDD9]/60">
        <h3 class="font-bold text-[#042623] font-playfair">👥 Data Pelanggan Terdaftar</h3>
        <span class="text-xs text-slate-600 font-bold">{{ $customers->total() }} pelanggan</span>
    </div>
    @if($customers->isEmpty())
    <div class="text-center py-16">
        <p class="text-4xl mb-2">👥</p>
        <p class="text-slate-600 font-medium">Tidak ada data pelanggan ditemukan.</p>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[700px]">
            <thead>
                <tr class="text-xs text-slate-700 font-bold uppercase bg-[#F4EDD9]/40 border-b border-[#e8e3d5]">
                    <th class="text-left px-6 py-3.5">Pelanggan</th>
                    <th class="text-left px-6 py-3.5">No. HP</th>
                    <th class="text-left px-6 py-3.5">Tier</th>
                    <th class="text-left px-6 py-3.5">Poin Reward</th>
                    <th class="text-left px-6 py-3.5">Bergabung</th>
                    <th class="text-right px-6 py-3.5">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($customers as $c)
                @php
                    $tier = $c->customerReward->tier ?? 'bronze';
                    $tierBadge = [
                        'bronze'   => 'bg-amber-100 text-amber-900 border-amber-300',
                        'silver'   => 'bg-slate-100 text-slate-800 border-slate-300',
                        'gold'     => 'bg-amber-100 text-amber-950 border-amber-400 font-extrabold',
                        'platinum' => 'bg-blue-100 text-blue-900 border-blue-300 font-extrabold',
                    ][$tier] ?? 'bg-slate-100 text-slate-800 border-slate-300';
                @endphp
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 gold-gradient rounded-full flex items-center justify-center text-sm font-extrabold shadow-sm">
                                {{ strtoupper(substr($c->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-bold text-slate-900 text-sm">{{ $c->name }}</p>
                                <p class="text-xs text-slate-500 font-semibold">{{ $c->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-slate-700 font-semibold text-xs">{{ $c->profile?->phone ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border uppercase tracking-wider {{ $tierBadge }}">
                            {{ $tier }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-[#085C54] font-extrabold text-sm">
                        ⭐ {{ number_format($c->customerReward->current_points ?? 0) }} Poin
                    </td>
                    <td class="px-6 py-4 text-slate-500 font-semibold text-xs">{{ $c->created_at->isoFormat('D MMM Y') }}</td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.customers.show', $c) }}" class="btn-edit text-xs">Detail Profile</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
        {{ $customers->links() }}
    </div>
    @endif
</div>
</x-admin-app>
