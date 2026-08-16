<x-admin-app>
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold font-playfair text-[#042623]">Profil Pelanggan</h1>
            <p class="text-sm text-slate-600 font-medium">Detail data pelanggan, histori transaksi, dan status tier CRM</p>
        </div>
        <a href="{{ route('admin.customers.index') }}" class="px-4 py-2 bg-white text-slate-700 border border-[#e8e3d5] font-bold text-xs rounded-xl hover:bg-[#F4EDD9] transition shadow-sm">
            ← Kembali ke Daftar
        </a>
    </div>

    <div class="grid lg:grid-cols-3 gap-6 mb-8">
        {{-- Kartu Info Pelanggan --}}
        <div class="glass p-6 rounded-2xl bg-white border border-[#e8e3d5] shadow-md">
            <div class="flex flex-col items-center text-center">
                <div class="w-20 h-20 rounded-full flex items-center justify-center text-3xl font-bold mb-4 shadow-md gold-gradient border border-[#C6A443]">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <h2 class="text-xl font-bold text-slate-900">{{ $user->name }}</h2>
                <p class="text-sm font-semibold text-slate-500">{{ $user->email }}</p>
                <div class="mt-4 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider
                            {{ $user->is_active ? 'bg-emerald-100 text-emerald-900 border border-emerald-300' : 'bg-red-100 text-red-900 border border-red-300' }}">
                    {{ $user->is_active ? 'Akun Aktif' : 'Akun Non-aktif' }}
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100 space-y-4">
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">No. WhatsApp / HP</p>
                    <p class="text-sm font-bold text-slate-900">{{ $user->profile?->phone ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Alamat Lengkap</p>
                    <p class="text-sm font-semibold text-slate-800">{{ $user->profile?->address ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Bergabung Sejak</p>
                    <p class="text-sm font-semibold text-slate-800">{{ $user->created_at->isoFormat('D MMMM Y') }}</p>
                </div>
            </div>
        </div>

        {{-- Kartu Status CRM & Tier --}}
        @php
            $tier = $user->customerReward->tier ?? 'bronze';
            $tierStyles = [
                'bronze'   => ['icon'=>'🥉', 'bg'=>'#fef3c7', 'color'=>'#b45309', 'border'=>'#fde68a'],
                'silver'   => ['icon'=>'🥈', 'bg'=>'#f1f5f9', 'color'=>'#334155', 'border'=>'#cbd5e1'],
                'gold'     => ['icon'=>'🥇', 'bg'=>'#fef3c7', 'color'=>'#866a20', 'border'=>'#fde68a'],
                'platinum' => ['icon'=>'💎', 'bg'=>'#e0f2fe', 'color'=>'#0284c7', 'border'=>'#bae6fd'],
            ];
            $tStyle = $tierStyles[$tier];
        @endphp
        <div class="glass p-6 rounded-2xl flex flex-col justify-between bg-white border border-[#e8e3d5] shadow-md">
            <div>
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-6">Status Loyalty (CRM)</h3>
                <div class="flex items-center gap-4 p-4 rounded-xl border shadow-sm" style="background:{{ $tStyle['bg'] }}; border-color:{{ $tStyle['border'] }};">
                    <span class="text-4xl">{{ $tStyle['icon'] }}</span>
                    <div>
                        <p class="text-xs font-bold text-slate-600">Tier Saat Ini</p>
                        <p class="text-xl font-extrabold uppercase" style="color:{{ $tStyle['color'] }}">{{ $tier }} Member</p>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mt-6">
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Total Poin</p>
                    <p class="text-lg font-extrabold text-[#C6A443]">⭐ {{ number_format($user->customerReward->current_points ?? 0) }}</p>
                </div>
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Total Transaksi</p>
                    <p class="text-lg font-extrabold text-[#042623]">{{ $user->transactions->where('status','completed')->count() }}</p>
                </div>
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 col-span-2">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Lifetime Spending (Rp)</p>
                    <p class="text-lg font-extrabold text-[#085C54]">Rp {{ number_format($user->customerReward->lifetime_spending ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        {{-- Ringkasan Aktivitas --}}
        <div class="glass p-6 rounded-2xl bg-white border border-[#e8e3d5] shadow-md">
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-6">Aktivitas Reservasi</h3>
            
            <div class="space-y-3">
                <div class="flex justify-between items-center bg-amber-50/70 border border-amber-200 p-3 rounded-xl">
                    <div class="flex items-center gap-3">
                        <span class="text-amber-800">⏳</span>
                        <span class="text-xs font-bold text-amber-900">Menunggu (Pending)</span>
                    </div>
                    <span class="font-extrabold text-amber-900 text-sm">{{ $user->reservations->where('status','pending')->count() }}</span>
                </div>
                <div class="flex justify-between items-center bg-blue-50/70 border border-blue-200 p-3 rounded-xl">
                    <div class="flex items-center gap-3">
                        <span class="text-blue-800">✓</span>
                        <span class="text-xs font-bold text-blue-900">Dikonfirmasi (Aktif)</span>
                    </div>
                    <span class="font-extrabold text-blue-900 text-sm">{{ $user->reservations->where('status','confirmed')->count() }}</span>
                </div>
                <div class="flex justify-between items-center bg-emerald-50/70 border border-emerald-200 p-3 rounded-xl">
                    <div class="flex items-center gap-3">
                        <span class="text-emerald-800">🏁</span>
                        <span class="text-xs font-bold text-emerald-900">Selesai Berhasil</span>
                    </div>
                    <span class="font-extrabold text-emerald-900 text-sm">{{ $user->reservations->where('status','completed')->count() }}</span>
                </div>
                <div class="flex justify-between items-center bg-red-50/70 border border-red-200 p-3 rounded-xl">
                    <div class="flex items-center gap-3">
                        <span class="text-red-800">❌</span>
                        <span class="text-xs font-bold text-red-900">Dibatalkan</span>
                    </div>
                    <span class="font-extrabold text-red-900 text-sm">{{ $user->reservations->where('status','cancelled')->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs untuk Histori --}}
    <div x-data="{ tab: 'transactions' }" class="glass rounded-2xl overflow-hidden bg-white border border-[#e8e3d5] shadow-md">
        <div class="flex border-b border-slate-200 bg-[#F4EDD9]/40">
            <button @click="tab = 'transactions'" :class="tab === 'transactions' ? 'border-b-2 border-[#085C54] text-[#085C54] font-bold bg-white' : 'text-slate-600 font-semibold hover:bg-white/50'" class="px-6 py-4 text-sm transition">
                Histori Transaksi
            </button>
            <button @click="tab = 'reservations'" :class="tab === 'reservations' ? 'border-b-2 border-[#085C54] text-[#085C54] font-bold bg-white' : 'text-slate-600 font-semibold hover:bg-white/50'" class="px-6 py-4 text-sm transition">
                Histori Reservasi
            </button>
        </div>

        {{-- Tab Transaksi --}}
        <div x-show="tab === 'transactions'" class="p-6">
            @if($user->transactions->isEmpty())
                <p class="text-center text-slate-500 py-8 font-medium">Belum ada histori transaksi.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[650px]">
                        <thead>
                            <tr class="text-xs text-slate-700 font-bold uppercase tracking-wider border-b border-slate-200 bg-slate-50">
                                <th class="py-3 px-4">Tanggal</th>
                                <th class="py-3 px-4">Kode Transaksi</th>
                                <th class="py-3 px-4">Tipe</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Total (Rp)</th>
                                <th class="py-3 px-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-slate-800 divide-y divide-slate-100">
                            @foreach($user->transactions->sortByDesc('created_at') as $tx)
                            <tr class="hover:bg-slate-50">
                                <td class="py-3 px-4 font-semibold text-slate-600 text-xs">{{ $tx->created_at->isoFormat('D MMM Y, HH:mm') }}</td>
                                <td class="py-3 px-4 font-mono text-xs font-bold text-[#085C54]">{{ $tx->transaction_code }}</td>
                                <td class="py-3 px-4 font-bold uppercase text-xs text-slate-800">{{ $tx->type }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border
                                        {{ $tx->status == 'completed' ? 'bg-emerald-100 text-emerald-900 border-emerald-300' : ($tx->status == 'cancelled' ? 'bg-red-100 text-red-900 border-red-300' : 'bg-amber-100 text-amber-900 border-amber-300') }}">
                                        {{ ucfirst($tx->status) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right font-extrabold {{ $tx->type == 'buyback' ? 'text-[#085C54]' : 'text-slate-900' }}">
                                    {{ $tx->type == 'buyback' ? '+' : '' }} Rp {{ number_format($tx->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4">
                                    <a href="{{ route('admin.transactions.show', $tx) }}" class="btn-edit text-xs">Detail</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Tab Reservasi --}}
        <div x-show="tab === 'reservations'" class="p-6" style="display: none;">
            @if($user->reservations->isEmpty())
                <p class="text-center text-slate-500 py-8 font-medium">Belum ada histori reservasi.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[650px]">
                        <thead>
                            <tr class="text-xs text-slate-700 font-bold uppercase tracking-wider border-b border-slate-200 bg-slate-50">
                                <th class="py-3 px-4">Kode Reservasi</th>
                                <th class="py-3 px-4">Tgl Kunjungan</th>
                                <th class="py-3 px-4">Produk</th>
                                <th class="py-3 px-4">Qty</th>
                                <th class="py-3 px-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-slate-800 divide-y divide-slate-100">
                            @foreach($user->reservations->sortByDesc('created_at') as $res)
                            <tr class="hover:bg-slate-50">
                                <td class="py-3 px-4 font-mono text-xs font-bold text-[#085C54]">{{ $res->reservation_code }}</td>
                                <td class="py-3 px-4 text-xs font-semibold text-slate-700">{{ \Carbon\Carbon::parse($res->preferred_date)->isoFormat('D MMM Y') }} ({{ \Carbon\Carbon::parse($res->preferred_time)->format('H:i') }})</td>
                                <td class="py-3 px-4 font-bold text-slate-900">{{ $res->product->name ?? ($res->pawn_gold_description ?? '-') }}</td>
                                <td class="py-3 px-4 font-bold text-slate-800">{{ $res->quantity }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border
                                        {{ $res->status == 'completed' ? 'bg-emerald-100 text-emerald-900 border-emerald-300' : ($res->status == 'cancelled' ? 'bg-red-100 text-red-900 border-red-300' : ($res->status == 'confirmed' ? 'bg-blue-100 text-blue-900 border-blue-300' : 'bg-amber-100 text-amber-900 border-amber-300')) }}">
                                        {{ ucfirst($res->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-admin-app>
