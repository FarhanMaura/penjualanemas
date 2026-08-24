<x-admin-app>
    <x-slot name="pageTitle">Detail Gadai</x-slot>
    <x-slot name="breadcrumb">Rincian transaksi gadai #{{ $pawn->pawn_code }}</x-slot>

    @php
        $daysLeft  = now()->diffInDays($pawn->due_date, false);
        $isExpired = $daysLeft < 0 && $pawn->status === 'active';
    @endphp

    @if(session('success'))
    <div class="mb-6 p-4 rounded-xl text-sm font-bold text-emerald-900 bg-emerald-50 border border-emerald-300 shadow-sm">
        ✅ {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-6 p-4 rounded-xl text-sm font-bold text-red-900 bg-red-50 border border-red-300 shadow-sm">
        ❌ {{ session('error') }}
    </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <div></div>
        <a href="{{ route('admin.pawns.index') }}" class="px-4 py-2 rounded-xl text-sm font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition shadow-sm">← Kembali</a>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 glass rounded-3xl overflow-hidden bg-white border border-[#e8e3d5] shadow-lg">
            <div class="p-6 bg-[#F4EDD9]/60 border-b border-[#e8e3d5]">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-mono text-xs font-bold text-[#085C54]">{{ $pawn->pawn_code }}</p>
                        <h2 class="text-2xl font-bold font-playfair text-slate-900 mt-1">{{ $pawn->gold_description }}</h2>
                        <p class="text-sm text-slate-600 font-semibold mt-1">{{ $pawn->gold_purity }} • {{ number_format($pawn->weight_gram, 2) }} gram</p>
                    </div>
                    <span class="px-3.5 py-1.5 rounded-full text-xs font-extrabold border
                        {{ $pawn->status === 'active' ? 'bg-amber-100 text-amber-900 border-amber-300' : ($pawn->status === 'redeemed' ? 'bg-emerald-100 text-emerald-900 border-emerald-300' : 'bg-red-100 text-red-900 border-red-300') }}">
                        {{ ucfirst($pawn->status) }}
                    </span>
                </div>
            </div>

            @if($isExpired)
            <div class="mx-6 mt-6 p-4 rounded-xl bg-red-50 border border-red-300">
                <p class="text-red-900 font-bold text-sm">⚠️ Gadai ini telah melewati jatuh tempo {{ abs($daysLeft) }} hari!</p>
            </div>
            @endif

            <div class="p-6 grid grid-cols-2 gap-4">
                @foreach([
                    ['Nilai Taksiran', 'Rp '.number_format($pawn->appraised_value, 0, ',', '.')],
                    ['Jumlah Pinjaman', 'Rp '.number_format($pawn->loan_amount, 0, ',', '.')],
                    ['Bunga / Bulan', $pawn->interest_rate.'%'],
                    ['Estimasi Tebus Sekarang', 'Rp '.number_format($pawn->calculateRedemptionAmount(), 0, ',', '.')],
                    ['Tanggal Mulai', $pawn->start_date?->isoFormat('D MMM Y') ?? '-'],
                    ['Jatuh Tempo', $pawn->due_date?->isoFormat('D MMM Y') ?? '-'],
                ] as [$label, $val])
                <div class="glass p-4 rounded-xl bg-white border border-[#e8e3d5] shadow-sm">
                    <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">{{ $label }}</p>
                    <p class="font-extrabold text-slate-900 text-base">{{ $val }}</p>
                </div>
                @endforeach
            </div>

            @if($pawn->status === 'redeemed')
            <div class="mx-6 mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-300">
                <p class="text-emerald-900 font-bold text-sm">✅ Sudah Ditebus</p>
                <p class="text-xs text-emerald-800 font-semibold mt-1">
                    Tgl: {{ $pawn->redemption_date?->isoFormat('D MMM Y') }} •
                    Jumlah: Rp {{ number_format($pawn->redemption_amount, 0, ',', '.') }}
                </p>
            </div>
            @endif

            @if($pawn->notes)
            <div class="mx-6 mb-6 p-4 rounded-xl bg-slate-50 border border-slate-200">
                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Catatan Admin:</p>
                <p class="text-sm text-slate-800 font-medium">{{ $pawn->notes }}</p>
            </div>
            @endif
        </div>

        {{-- Sidebar Pelanggan --}}
        <div class="space-y-5">
            <div class="glass rounded-2xl p-5 bg-white border border-[#e8e3d5] shadow-sm">
                <h3 class="text-sm font-bold text-slate-900 mb-4">👤 Info Pelanggan</h3>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-base font-extrabold text-[#042623] gold-gradient border border-[#C6A443] shadow-md">
                        {{ strtoupper(substr($pawn->transaction->user->name ?? 'U', 0, 2)) }}
                    </div>
                    <div>
                        <p class="font-bold text-slate-900 text-base">{{ $pawn->transaction->user->name ?? '-' }}</p>
                        <p class="text-xs text-slate-500 font-medium">{{ $pawn->transaction->user->email ?? '-' }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.customers.show', $pawn->transaction->user) }}" class="block w-full text-center text-xs font-bold py-2 rounded-xl bg-slate-100 text-slate-800 hover:bg-slate-200 transition border border-slate-300">
                    Lihat Profil Pelanggan →
                </a>
            </div>

            <div class="glass rounded-2xl p-5 bg-white border border-[#e8e3d5] shadow-sm">
                <h3 class="text-sm font-bold text-slate-900 mb-4">⚡ Aksi Cepat</h3>
                <div class="space-y-2">
                    @if($pawn->status === 'active')
                    <button onclick="openRedeemModal({{ $pawn->calculateRedemptionAmount() }})" class="w-full text-center text-xs py-2.5 rounded-xl text-[#042623] font-extrabold transition hover:scale-105 mb-2 gold-gradient border border-[#C6A443] shadow-md">
                        🔓 Tebus Gadai
                    </button>
                    @endif
                    <a href="{{ route('admin.pawns.index') }}" class="flex items-center gap-3 p-3 rounded-xl text-sm font-bold text-slate-700 bg-slate-50 hover:bg-slate-100 transition border border-slate-200">
                        🏦 Semua Gadai
                    </a>
                    <a href="{{ route('admin.customers.index') }}" class="flex items-center gap-3 p-3 rounded-xl text-sm font-bold text-slate-700 bg-slate-50 hover:bg-slate-100 transition border border-slate-200">
                        👥 Daftar Pelanggan
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Pawn Redemption Modal --}}
    <div id="redeemModal" class="fixed inset-0 z-50 flex items-center justify-center hidden" style="background:rgba(4,38,35,0.6); backdrop-filter:blur(4px);">
        <div class="w-full max-w-md p-6 rounded-3xl bg-white border border-[#e8e3d5] shadow-2xl">
            <div class="flex justify-between items-center mb-4 pb-3 border-b border-slate-200">
                <h3 class="text-base font-bold text-slate-900">🔓 Tebus Gadai</h3>
                <button onclick="closeRedeemModal()" class="text-slate-400 hover:text-slate-700 text-xl font-bold">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.pawns.redeem', $pawn) }}">
                @csrf
                
                <p class="text-xs text-slate-600 font-medium mb-4">Pastikan pelanggan telah melakukan pembayaran pokok pinjaman beserta bunga berjalan.</p>

                <div class="mb-4">
                    <label class="input-label">Jumlah Tebusan (Rp) <span class="text-red-600">*</span></label>
                    <input type="number" name="redemption_amount" id="modal_redemption_amount" class="input-field font-extrabold text-slate-900" required>
                </div>

                <div class="mb-6">
                    <label class="input-label">Catatan Tebusan</label>
                    <textarea name="notes" rows="2" class="input-field text-sm" placeholder="Catatan opsional..."></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeRedeemModal()" class="flex-1 py-2.5 rounded-xl text-sm font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl text-sm font-extrabold text-[#042623] gold-gradient border border-[#C6A443] shadow-md transition hover:scale-105">
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

