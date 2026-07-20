<x-admin-app>
    <x-slot name="pageTitle">Detail Gadai</x-slot>
    <x-slot name="breadcrumb">Rincian transaksi gadai #{{ $pawn->pawn_code }}</x-slot>

    @php
        $daysLeft  = now()->diffInDays($pawn->due_date, false);
        $isExpired = $daysLeft < 0 && $pawn->status === 'active';
    @endphp

    @if(session('success'))
    <div class="mb-6 p-4 rounded-xl text-sm text-green-400" style="background:rgba(34,197,94,0.1); border:1px solid rgba(34,197,94,0.25);">
        ✅ {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-6 p-4 rounded-xl text-sm text-red-400" style="background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.25);">
        ❌ {{ session('error') }}
    </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <div></div>
        <a href="{{ route('admin.pawns.index') }}" class="px-4 py-2 rounded-lg text-sm text-gray-300 glass hover:bg-white/10 transition">← Kembali</a>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 glass rounded-3xl overflow-hidden">
            <div class="p-6" style="background:linear-gradient(135deg,rgba(245,158,11,0.08),rgba(0,0,0,0.4)); border-bottom:1px solid rgba(255,255,255,0.08);">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-mono text-sm text-gray-400">{{ $pawn->pawn_code }}</p>
                        <h2 class="text-2xl font-bold font-playfair text-white mt-1">{{ $pawn->gold_description }}</h2>
                        <p class="text-sm text-gray-400">{{ $pawn->gold_purity }} • {{ number_format($pawn->weight_gram, 2) }} gram</p>
                    </div>
                    <span class="px-3 py-1.5 rounded-full text-xs font-semibold
                        {{ $pawn->status === 'active' ? 'bg-yellow-900/50 text-yellow-400 border border-yellow-700/50' : ($pawn->status === 'redeemed' ? 'bg-green-900/50 text-green-400' : 'bg-red-900/50 text-red-400') }}">
                        {{ ucfirst($pawn->status) }}
                    </span>
                </div>
            </div>

            @if($isExpired)
            <div class="mx-6 mt-6 p-4 rounded-xl" style="background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.3);">
                <p class="text-red-400 font-semibold">⚠️ Gadai ini telah melewati jatuh tempo {{ abs($daysLeft) }} hari!</p>
            </div>
            @endif

            <div class="p-6 grid grid-cols-2 gap-4">
                @foreach([
                    ['Nilai Taksiran', 'Rp '.number_format($pawn->appraised_value, 0, ',', '.')],
                    ['Jumlah Pinjaman', 'Rp '.number_format($pawn->loan_amount, 0, ',', '.')],
                    ['Bunga/Bulan', $pawn->interest_rate.'%'],
                    ['Estimasi Tebus Skrng', 'Rp '.number_format($pawn->calculateRedemptionAmount(), 0, ',', '.')],
                    ['Tanggal Mulai', $pawn->start_date?->isoFormat('D MMM Y') ?? '-'],
                    ['Jatuh Tempo', $pawn->due_date?->isoFormat('D MMM Y') ?? '-'],
                ] as [$label, $val])
                <div class="glass p-4 rounded-xl">
                    <p class="text-xs text-gray-500 mb-1">{{ $label }}</p>
                    <p class="font-bold text-white">{{ $val }}</p>
                </div>
                @endforeach
            </div>

            @if($pawn->status === 'redeemed')
            <div class="mx-6 mb-6 p-4 rounded-xl" style="background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.3);">
                <p class="text-green-400 font-semibold text-sm">✅ Sudah Ditebus</p>
                <p class="text-xs text-gray-400 mt-1">
                    Tgl: {{ $pawn->redemption_date?->isoFormat('D MMM Y') }} •
                    Jumlah: Rp {{ number_format($pawn->redemption_amount, 0, ',', '.') }}
                </p>
            </div>
            @endif

            @if($pawn->notes)
            <div class="mx-6 mb-6 p-4 rounded-xl" style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08);">
                <p class="text-xs text-gray-500 mb-1">Catatan Admin:</p>
                <p class="text-sm text-gray-300">{{ $pawn->notes }}</p>
            </div>
            @endif
        </div>

        {{-- Sidebar Pelanggan --}}
        <div class="space-y-5">
            <div class="glass rounded-2xl p-5">
                <h3 class="text-sm font-semibold text-yellow-400 mb-4">👤 Info Pelanggan</h3>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-lg font-bold"
                         style="background:linear-gradient(135deg,#f59e0b,#d97706);">
                        {{ strtoupper(substr($pawn->transaction->user->name ?? 'U', 0, 2)) }}
                    </div>
                    <div>
                        <p class="font-bold text-white">{{ $pawn->transaction->user->name ?? '-' }}</p>
                        <p class="text-xs text-gray-400">{{ $pawn->transaction->user->email ?? '-' }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.customers.show', $pawn->transaction->user) }}" class="block w-full text-center text-xs py-2 rounded-lg glass text-gray-300 hover:text-white transition">
                    Lihat Profil Pelanggan →
                </a>
            </div>

            <div class="glass rounded-2xl p-5">
                <h3 class="text-sm font-semibold text-gray-400 mb-4">⚡ Aksi Cepat</h3>
                <div class="space-y-2">
                    @if($pawn->status === 'active')
                    <button onclick="openRedeemModal({{ $pawn->calculateRedemptionAmount() }})" class="w-full text-center text-xs py-2.5 rounded-lg text-black font-semibold transition hover:scale-105 mb-2" style="background:linear-gradient(135deg,#f59e0b,#d97706);">
                        🔓 Tebus Gadai
                    </button>
                    @endif
                    <a href="{{ route('admin.pawns.index') }}" class="flex items-center gap-3 p-3 rounded-xl text-sm text-gray-300 hover:bg-white/5 transition">
                        🏦 Semua Gadai
                    </a>
                    <a href="{{ route('admin.customers.index') }}" class="flex items-center gap-3 p-3 rounded-xl text-sm text-gray-300 hover:bg-white/5 transition">
                        👥 Daftar Pelanggan
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Pawn Redemption Modal --}}
    <div id="redeemModal" class="fixed inset-0 z-50 flex items-center justify-center hidden" style="background:rgba(0,0,0,0.8); backdrop-filter:blur(4px);">
        <div class="glass w-full max-w-md p-6 rounded-3xl" style="border:1px solid rgba(255,255,255,0.15);">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-yellow-400">🔓 Tebus Gadai</h3>
                <button onclick="closeRedeemModal()" class="text-gray-400 hover:text-white text-lg">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.pawns.redeem', $pawn) }}">
                @csrf
                
                <p class="text-xs text-gray-400 mb-4">Pastikan pelanggan telah melakukan pembayaran pokok pinjaman beserta bunga berjalan.</p>

                <div class="mb-4">
                    <label class="block text-xs text-gray-400 mb-1">Jumlah Tebusan (Rp) *</label>
                    <input type="number" name="redemption_amount" id="modal_redemption_amount" class="w-full bg-black/40 text-white rounded-xl p-3 text-sm border border-white/10 focus:outline-none focus:border-yellow-400" required>
                </div>

                <div class="mb-6">
                    <label class="block text-xs text-gray-400 mb-1">Catatan Tebusan</label>
                    <textarea name="notes" rows="2" class="w-full bg-black/40 text-white rounded-xl p-3 text-sm border border-white/10 focus:outline-none focus:border-yellow-400" placeholder="Catatan opsional..."></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeRedeemModal()" class="flex-1 py-2.5 rounded-xl text-sm font-semibold glass text-gray-400 hover:text-white transition">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-black transition hover:scale-105" style="background:linear-gradient(135deg,#f59e0b,#d97706);">
                        Konfirmasi Tebus
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRedeemModal(amount) {
            document.getElementById('modal_redemption_amount').value = amount;
            document.getElementById('redeemModal').classList.remove('hidden');
        }

        function closeRedeemModal() {
            document.getElementById('redeemModal').classList.add('hidden');
        }
    </script>
</x-admin-app>
