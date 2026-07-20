<x-admin-app>
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold font-playfair text-white">Profil Pelanggan</h1>
            <p class="text-sm text-gray-400">Detail data pelanggan, histori transaksi, dan status tier CRM</p>
        </div>
        <a href="{{ route('admin.customers.index') }}" class="px-4 py-2 bg-dark-800 text-gray-300 rounded-lg hover:bg-dark-700 transition">
            ← Kembali ke Daftar
        </a>
    </div>

    <div class="grid lg:grid-cols-3 gap-6 mb-8">
        {{-- Kartu Info Pelanggan --}}
        <div class="glass p-6 rounded-2xl" style="border-color:rgba(255,255,255,0.05);">
            <div class="flex flex-col items-center text-center">
                <div class="w-20 h-20 rounded-full flex items-center justify-center text-3xl font-bold mb-4 shadow-lg"
                     style="background:linear-gradient(135deg,#f59e0b,#d97706); color:white;">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <h2 class="text-xl font-bold text-white">{{ $user->name }}</h2>
                <p class="text-sm text-gray-400">{{ $user->email }}</p>
                <div class="mt-4 px-4 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wider
                            {{ $user->is_active ? 'bg-green-900/40 text-green-400 border border-green-700/50' : 'bg-red-900/40 text-red-400 border border-red-700/50' }}">
                    {{ $user->is_active ? 'Akun Aktif' : 'Akun Non-aktif' }}
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-800 space-y-4">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">No. WhatsApp / HP</p>
                    <p class="text-sm text-white">{{ $user->profile->phone ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Alamat Lengkap</p>
                    <p class="text-sm text-white">{{ $user->profile->address ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Bergabung Sejak</p>
                    <p class="text-sm text-white">{{ $user->created_at->isoFormat('D MMMM Y') }}</p>
                </div>
            </div>
        </div>

        {{-- Kartu Status CRM & Tier --}}
        @php
            $tier = $user->customerReward->tier ?? 'bronze';
            $tierStyles = [
                'bronze'   => ['icon'=>'🥉', 'bg'=>'rgba(180,83,9,0.1)', 'color'=>'#b45309'],
                'silver'   => ['icon'=>'🥈', 'bg'=>'rgba(156,163,175,0.1)', 'color'=>'#9ca3af'],
                'gold'     => ['icon'=>'🥇', 'bg'=>'rgba(245,158,11,0.1)', 'color'=>'#f59e0b'],
                'platinum' => ['icon'=>'💎', 'bg'=>'rgba(96,165,250,0.1)', 'color'=>'#60a5fa'],
            ];
            $tStyle = $tierStyles[$tier];
        @endphp
        <div class="glass p-6 rounded-2xl flex flex-col justify-between" style="border-color:rgba(255,255,255,0.05);">
            <div>
                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-6">Status Loyalty (CRM)</h3>
                <div class="flex items-center gap-4 p-4 rounded-xl" style="background:{{ $tStyle['bg'] }}; border:1px solid {{ $tStyle['color'] }}40;">
                    <span class="text-4xl">{{ $tStyle['icon'] }}</span>
                    <div>
                        <p class="text-xs text-gray-400">Tier Saat Ini</p>
                        <p class="text-xl font-bold uppercase" style="color:{{ $tStyle['color'] }}">{{ $tier }}</p>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mt-6">
                <div class="bg-dark-800 p-4 rounded-xl border border-dark-700">
                    <p class="text-xs text-gray-500 mb-1">Total Poin</p>
                    <p class="text-lg font-bold text-yellow-400">{{ $user->customerReward->current_points ?? 0 }}</p>
                </div>
                <div class="bg-dark-800 p-4 rounded-xl border border-dark-700">
                    <p class="text-xs text-gray-500 mb-1">Total Transaksi</p>
                    <p class="text-lg font-bold text-white">{{ $user->transactions->where('status','completed')->count() }}</p>
                </div>
                <div class="bg-dark-800 p-4 rounded-xl border border-dark-700 col-span-2">
                    <p class="text-xs text-gray-500 mb-1">Lifetime Spending (Rp)</p>
                    <p class="text-lg font-bold text-white">Rp {{ number_format($user->customerReward->lifetime_spending ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        {{-- Ringkasan Aktivitas --}}
        <div class="glass p-6 rounded-2xl" style="border-color:rgba(255,255,255,0.05);">
            <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-6">Aktivitas Reservasi</h3>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center bg-dark-800 p-3 rounded-lg">
                    <div class="flex items-center gap-3">
                        <span class="text-yellow-400">⏳</span>
                        <span class="text-sm text-gray-300">Menunggu (Pending)</span>
                    </div>
                    <span class="font-bold text-white">{{ $user->reservations->where('status','pending')->count() }}</span>
                </div>
                <div class="flex justify-between items-center bg-dark-800 p-3 rounded-lg">
                    <div class="flex items-center gap-3">
                        <span class="text-blue-400">✓</span>
                        <span class="text-sm text-gray-300">Dikonfirmasi (Aktif)</span>
                    </div>
                    <span class="font-bold text-white">{{ $user->reservations->where('status','confirmed')->count() }}</span>
                </div>
                <div class="flex justify-between items-center bg-dark-800 p-3 rounded-lg">
                    <div class="flex items-center gap-3">
                        <span class="text-green-400">🏁</span>
                        <span class="text-sm text-gray-300">Selesai Berhasil</span>
                    </div>
                    <span class="font-bold text-white">{{ $user->reservations->where('status','completed')->count() }}</span>
                </div>
                <div class="flex justify-between items-center bg-dark-800 p-3 rounded-lg">
                    <div class="flex items-center gap-3">
                        <span class="text-red-400">❌</span>
                        <span class="text-sm text-gray-300">Dibatalkan</span>
                    </div>
                    <span class="font-bold text-white">{{ $user->reservations->where('status','cancelled')->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs untuk Histori --}}
    <div x-data="{ tab: 'transactions' }" class="glass rounded-2xl overflow-hidden" style="border-color:rgba(255,255,255,0.05);">
        <div class="flex border-b border-gray-800 bg-black/20">
            <button @click="tab = 'transactions'" :class="tab === 'transactions' ? 'border-b-2 border-yellow-500 text-yellow-400 bg-white/5' : 'text-gray-400 hover:bg-white/5'" class="px-6 py-4 text-sm font-semibold transition">
                Histori Transaksi
            </button>
            <button @click="tab = 'reservations'" :class="tab === 'reservations' ? 'border-b-2 border-yellow-500 text-yellow-400 bg-white/5' : 'text-gray-400 hover:bg-white/5'" class="px-6 py-4 text-sm font-semibold transition">
                Histori Reservasi
            </button>
        </div>

        {{-- Tab Transaksi --}}
        <div x-show="tab === 'transactions'" class="p-6">
            @if($user->transactions->isEmpty())
                <p class="text-center text-gray-500 py-8">Belum ada histori transaksi.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-xs text-gray-500 uppercase tracking-wider border-b border-gray-800">
                                <th class="py-3 px-4">Tanggal</th>
                                <th class="py-3 px-4">Kode Transaksi</th>
                                <th class="py-3 px-4">Tipe</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Total (Rp)</th>
                                <th class="py-3 px-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-gray-300">
                            @foreach($user->transactions->sortByDesc('created_at') as $tx)
                            <tr class="border-b border-gray-800/50 hover:bg-white/5">
                                <td class="py-3 px-4">{{ $tx->created_at->isoFormat('D MMM Y, H:mm') }}</td>
                                <td class="py-3 px-4 font-mono text-xs">{{ $tx->transaction_code }}</td>
                                <td class="py-3 px-4 uppercase">{{ $tx->type }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-1 rounded text-xs
                                        {{ $tx->status == 'completed' ? 'bg-green-900/40 text-green-400' : ($tx->status == 'cancelled' ? 'bg-red-900/40 text-red-400' : 'bg-yellow-900/40 text-yellow-400') }}">
                                        {{ $tx->status }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right font-semibold {{ $tx->type == 'buyback' ? 'text-green-400' : 'text-yellow-400' }}">
                                    {{ $tx->type == 'buyback' ? '+' : '' }}{{ number_format($tx->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4">
                                    <a href="{{ route('admin.transactions.show', $tx) }}" class="text-blue-400 hover:underline">Detail</a>
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
                <p class="text-center text-gray-500 py-8">Belum ada histori reservasi.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-xs text-gray-500 uppercase tracking-wider border-b border-gray-800">
                                <th class="py-3 px-4">Kode Reservasi</th>
                                <th class="py-3 px-4">Tgl Kunjungan</th>
                                <th class="py-3 px-4">Produk</th>
                                <th class="py-3 px-4">Qty</th>
                                <th class="py-3 px-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-gray-300">
                            @foreach($user->reservations->sortByDesc('created_at') as $res)
                            <tr class="border-b border-gray-800/50 hover:bg-white/5">
                                <td class="py-3 px-4 font-mono text-xs">{{ $res->reservation_code }}</td>
                                <td class="py-3 px-4">{{ \Carbon\Carbon::parse($res->preferred_date)->isoFormat('D MMM Y') }} ({{ \Carbon\Carbon::parse($res->preferred_time)->format('H:i') }})</td>
                                <td class="py-3 px-4">{{ $res->product->name ?? '-' }}</td>
                                <td class="py-3 px-4">{{ $res->quantity }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-1 rounded text-xs
                                        {{ $res->status == 'completed' ? 'bg-green-900/40 text-green-400' : ($res->status == 'cancelled' ? 'bg-red-900/40 text-red-400' : ($res->status == 'confirmed' ? 'bg-blue-900/40 text-blue-400' : 'bg-yellow-900/40 text-yellow-400')) }}">
                                        {{ $res->status }}
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
