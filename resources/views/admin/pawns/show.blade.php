<x-admin-app>
    <x-slot name="pageTitle">Detail Gadai & Angsuran</x-slot>
    <x-slot name="breadcrumb">Rincian gadai, skema cicilan, dan tebusan #{{ $pawn->pawn_code }}</x-slot>

    @php
        $daysLeft  = now()->diffInDays($pawn->due_date, false);
        $isExpired = $daysLeft < 0 && $pawn->status === 'active';
        $plan = $pawn->transaction?->installmentPlan;
        $paidMonths = $plan ? $plan->paidCount() : 0;
        $totalMonths = $plan ? $plan->tenure_months : 1;
        $unpaidAmount = $plan ? $plan->payments()->where('status', '!=', 'paid')->sum('amount_due') : $pawn->loan_amount;
        $unpaidPayments = $plan ? $plan->payments()->where('status', '!=', 'paid')->get() : collect();
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
    @if($errors->any())
    <div class="mb-6 p-4 rounded-xl text-sm font-bold text-red-900 bg-red-50 border border-red-300 shadow-sm">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <div></div>
        <a href="{{ route('admin.pawns.index') }}" class="px-4 py-2 rounded-xl text-sm font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition shadow-sm">← Kembali ke Daftar Gadai</a>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Card Utama Gadai --}}
            <div class="glass rounded-3xl overflow-hidden bg-white border border-[#e8e3d5] shadow-lg">
                <div class="p-6 bg-[#F4EDD9]/60 border-b border-[#e8e3d5]">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-mono text-xs font-bold text-[#085C54]">{{ $pawn->pawn_code }}</p>
                            <h2 class="text-2xl font-bold font-playfair text-slate-900 mt-1">{{ $pawn->gold_description }}</h2>
                            <p class="text-sm text-slate-600 font-semibold mt-1">{{ $pawn->gold_purity }} • {{ number_format($pawn->weight_gram, 2) }} gram</p>
                        </div>
                        <span class="px-3.5 py-1.5 rounded-full text-xs font-extrabold border
                            {{ $pawn->status === 'active' ? 'bg-amber-100 text-amber-900 border-amber-300' : ($pawn->status === 'redeemed' ? 'bg-emerald-100 text-emerald-900 border-emerald-300' : 'bg-red-100 text-red-900 border-red-300') }}">
                            {{ $pawn->status === 'redeemed' ? '🎉 Lunas Ditebus' : ($pawn->status === 'active' ? '⚡ Aktif' : ucfirst($pawn->status)) }}
                        </span>
                    </div>
                </div>

                @if($isExpired)
                <div class="mx-6 mt-6 p-4 rounded-xl bg-red-50 border border-red-300">
                    <p class="text-red-900 font-bold text-sm">⚠️ Gadai ini telah melewati jatuh tempo {{ abs($daysLeft) }} hari!</p>
                </div>
                @endif

                <div class="p-6 grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div class="glass p-4 rounded-xl bg-white border border-[#e8e3d5] shadow-sm">
                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mb-1">Nilai Taksiran</p>
                        <p class="font-extrabold text-slate-900 text-base">Rp {{ number_format($pawn->appraised_value, 0, ',', '.') }}</p>
                    </div>
                    <div class="glass p-4 rounded-xl bg-amber-50/70 border border-amber-200 shadow-sm">
                        <p class="text-[10px] text-amber-900 font-bold uppercase tracking-wider mb-1">Total Pinjaman</p>
                        <p class="font-extrabold text-[#C6A443] text-base">Rp {{ number_format($pawn->loan_amount, 0, ',', '.') }}</p>
                    </div>
                    <div class="glass p-4 rounded-xl bg-emerald-50/70 border border-emerald-200 shadow-sm">
                        <p class="text-[10px] text-emerald-900 font-bold uppercase tracking-wider mb-1">Sisa Belum Ditebus</p>
                        <p class="font-extrabold text-emerald-800 text-base">Rp {{ number_format($unpaidAmount, 0, ',', '.') }}</p>
                    </div>
                    <div class="glass p-4 rounded-xl bg-white border border-[#e8e3d5] shadow-sm">
                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mb-1">Durasi / Tempo</p>
                        <p class="font-extrabold text-slate-900 text-base">{{ $plan ? $plan->tenure_months . ' Bulan' : '-' }}</p>
                    </div>
                    <div class="glass p-4 rounded-xl bg-white border border-[#e8e3d5] shadow-sm">
                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mb-1">Angsuran / Bulan</p>
                        <p class="font-extrabold text-[#085C54] text-base">Rp {{ number_format($plan ? $plan->monthly_amount : 0, 0, ',', '.') }}</p>
                    </div>
                    <div class="glass p-4 rounded-xl bg-white border border-[#e8e3d5] shadow-sm">
                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mb-1">Jatuh Tempo</p>
                        <p class="font-extrabold text-slate-900 text-sm">{{ $pawn->due_date?->isoFormat('D MMM Y') ?? '-' }}</p>
                    </div>
                </div>

                @if($pawn->status === 'redeemed')
                <div class="mx-6 mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-950">
                    <p class="font-extrabold text-base flex items-center gap-2"><span>🎉</span> <span>Gadai Sudah Lunas Ditebus!</span></p>
                    <p class="text-xs font-semibold text-emerald-800 mt-1">
                        Tanggal Tebus: <strong>{{ $pawn->redemption_date?->isoFormat('D MMMM Y') }}</strong> •
                        Nominal Tebus: <strong>Rp {{ number_format($pawn->redemption_amount, 0, ',', '.') }}</strong>
                    </p>
                </div>
                @endif
            </div>

            {{-- Skema Cicilan & Jadwal Angsuran Gadai --}}
            @if($plan)
            <div class="glass rounded-3xl overflow-hidden bg-white border border-[#e8e3d5] shadow-lg">
                <div class="p-6 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                            <span>📅</span> Skema Angsuran Cicilan Gadai ({{ $plan->tenure_months }} Bulan)
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Pantau progres pembayaran angsuran berkala atau pelunasan tebus langsung pelanggan</p>
                    </div>
                    <span class="text-xs font-extrabold px-3 py-1 rounded-full bg-emerald-100 text-emerald-900 border border-emerald-300">
                        {{ $paidMonths }} / {{ $totalMonths }} Angsuran Lunas
                    </span>
                </div>

                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-800">
                            <thead>
                                <tr class="text-xs text-slate-600 font-bold uppercase tracking-wider border-b border-slate-200">
                                    <th class="py-3 px-3">Bulan Ke-</th>
                                    <th class="py-3 px-3">Jatuh Tempo</th>
                                    <th class="py-3 px-3 text-right">Nominal Angsuran</th>
                                    <th class="py-3 px-3 text-center">Status</th>
                                    <th class="py-3 px-3">Tanggal Bayar</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($plan->payments->sortBy('installment_number') as $pmt)
                                <tr class="hover:bg-slate-50">
                                    <td class="py-3.5 px-3 font-bold text-slate-900">
                                        Angsuran Bulan #{{ $pmt->installment_number }}
                                    </td>
                                    <td class="py-3.5 px-3 text-xs font-medium text-slate-600">
                                        {{ $pmt->due_date?->isoFormat('D MMM Y') }}
                                    </td>
                                    <td class="py-3.5 px-3 text-right font-extrabold text-slate-900">
                                        Rp {{ number_format($pmt->amount_due, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-3 text-center">
                                        @if($pmt->status === 'paid')
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">✓ Lunas</span>
                                        @elseif($pmt->status === 'waiting_verification')
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800 border border-amber-300 animate-pulse">Menunggu Verifikasi</span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-300">Belum Bayar</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-3 text-xs text-slate-500 font-medium">
                                        {{ $pmt->paid_at ? \Carbon\Carbon::parse($pmt->paid_at)->isoFormat('D MMM Y, HH:mm') : '-' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Antrean Verifikasi Pembayaran Pelanggan --}}
            @if($plan->installmentTransactions->isNotEmpty())
            <div class="glass rounded-3xl overflow-hidden bg-white border border-[#e8e3d5] shadow-lg">
                <div class="p-6 bg-slate-50 border-b border-slate-200">
                    <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                        <span>💳</span> Riwayat Pembayaran & Antrean Verifikasi
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Bukti transfer dan pembayaran yang dikirimkan oleh pelanggan untuk gadai ini</p>
                </div>
                <div class="p-6 space-y-4">
                    @foreach($plan->installmentTransactions as $trx)
                    <div class="p-4 rounded-2xl border {{ in_array($trx->status, ['pending', 'waiting_verification']) ? 'bg-amber-50/50 border-amber-300 ring-2 ring-amber-200' : ($trx->status === 'verified' ? 'bg-emerald-50/40 border-emerald-200' : 'bg-red-50/40 border-red-200') }} flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-bold text-[#085C54]">{{ $trx->transaction_code }}</span>
                                <span class="text-xs px-2 py-0.5 rounded-full font-bold uppercase border {{ in_array($trx->status, ['pending', 'waiting_verification']) ? 'bg-amber-100 text-amber-900 border-amber-300' : ($trx->status === 'verified' ? 'bg-emerald-100 text-emerald-900 border-emerald-300' : 'bg-red-100 text-red-900 border-red-300') }}">
                                    {{ in_array($trx->status, ['pending', 'waiting_verification']) ? 'Menunggu Konfirmasi' : ucfirst($trx->status) }}
                                </span>
                            </div>
                            <p class="text-base font-extrabold text-slate-900 mt-1">
                                Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                <span class="text-xs text-slate-500 font-normal">({{ strtoupper($trx->payment_method) }})</span>
                            </p>
                            <p class="text-xs text-slate-600 mt-0.5">
                                Pengirim: <strong>{{ $trx->sender_name ?? '-' }}</strong> ({{ $trx->sender_bank ?? '-' }}) • {{ $trx->created_at->isoFormat('D MMM Y, HH:mm') }}
                            </p>
                            @if($trx->notes)
                            <p class="text-xs text-slate-500 italic mt-1 bg-white/70 px-2 py-1 rounded-md border border-slate-200">Catatan: {{ $trx->notes }}</p>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            @if($trx->proof_image)
                            <a href="{{ asset('storage/' . $trx->proof_image) }}" target="_blank" class="px-3 py-2 rounded-xl text-xs font-bold text-blue-800 bg-blue-50 border border-blue-300 hover:bg-blue-100 transition flex items-center gap-1">
                                <span>🔍</span> Bukti Transfer
                            </a>
                            @endif

                            @if(in_array($trx->status, ['pending', 'waiting_verification']))
                            <form method="POST" action="{{ route('admin.pawns.verify-payment', [$pawn, $trx]) }}" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui verifikasi pembayaran ini?');">
                                @csrf
                                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-extrabold text-[#042623] gold-gradient border border-[#C6A443] shadow-md hover:brightness-110 transition">
                                    ✓ Setujui
                                </button>
                            </form>

                            <button type="button" onclick="openRejectModal({{ $trx->id }})" class="px-3 py-2 rounded-xl text-xs font-bold text-red-700 bg-red-50 border border-red-300 hover:bg-red-100 transition">
                                ✕ Tolak
                            </button>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            @endif
        </div>

        {{-- Sidebar Pelanggan & Aksi --}}
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
                <h3 class="text-sm font-bold text-slate-900 mb-4">⚡ Aksi Kasir Toko</h3>
                <div class="space-y-2">
                    @if($pawn->status === 'active')
                    <button onclick="openRedeemModal({{ $unpaidAmount }})" class="w-full text-center text-xs py-3 rounded-xl text-[#042623] font-black transition hover:scale-105 mb-2 gold-gradient border border-[#C6A443] shadow-lg">
                        🔓 Tebus Gadai Langsung (Kasir Toko)
                    </button>
                    <p class="text-[11px] text-slate-500 text-center mb-3">Gunakan tombol di atas jika pelanggan melunasi tebusan langsung di kasir toko.</p>
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

    {{-- Pawn Redemption Modal (Fix validation bug) --}}
    <div id="redeemModal" class="fixed inset-0 z-50 flex items-center justify-center hidden" style="background:rgba(4,38,35,0.6); backdrop-filter:blur(4px);">
        <div class="w-full max-w-md p-6 rounded-3xl bg-white border border-[#e8e3d5] shadow-2xl">
            <div class="flex justify-between items-center mb-4 pb-3 border-b border-slate-200">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2"><span>🔓</span> Tebus Gadai Langsung</h3>
                <button onclick="closeRedeemModal()" class="text-slate-400 hover:text-slate-700 text-xl font-bold">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.pawns.redeem', $pawn) }}" onsubmit="unformatModalInput()">
                @csrf
                <p class="text-xs text-slate-600 font-medium mb-4 leading-relaxed">
                    Pastikan pelanggan telah melunasi seluruh kewajiban pinjaman gadai di kasir toko. Aksi ini akan menandai gadai sebagai <strong>Lunas Ditebus</strong> dan menyelesaikan seluruh angsuran terkait.
                </p>

                <div class="mb-4">
                    <label class="input-label">Jumlah Tebusan / Pelunasan (Rp) <span class="text-red-600">*</span></label>
                    <input type="text" inputmode="numeric" name="redemption_amount" id="modal_redemption_amount" class="input-field format-rupiah font-extrabold text-slate-900" required>
                </div>

                <div class="mb-6">
                    <label class="input-label">Catatan Tebusan</label>
                    <textarea name="notes" rows="2" class="input-field text-sm" placeholder="cth: Ditebus lunas tunai di toko oleh pemilik"></textarea>
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

    {{-- Reject Payment Modal --}}
    <div id="rejectModal" class="fixed inset-0 z-50 flex items-center justify-center hidden" style="background:rgba(4,38,35,0.6); backdrop-filter:blur(4px);">
        <div class="w-full max-w-md p-6 rounded-3xl bg-white border border-[#e8e3d5] shadow-2xl">
            <div class="flex justify-between items-center mb-4 pb-3 border-b border-slate-200">
                <h3 class="text-base font-bold text-slate-900">✕ Tolak Bukti Pembayaran</h3>
                <button onclick="closeRejectModal()" class="text-slate-400 hover:text-slate-700 text-xl font-bold">&times;</button>
            </div>
            <form method="POST" id="rejectForm" action="">
                @csrf
                <div class="mb-4">
                    <label class="input-label">Alasan Penolakan <span class="text-red-600">*</span></label>
                    <textarea name="rejection_reason" rows="3" class="input-field text-sm" placeholder="cth: Bukti transfer buram / nominal tidak sesuai mutasi rekening" required></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeRejectModal()" class="flex-1 py-2.5 rounded-xl text-sm font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl text-sm font-bold text-white bg-red-600 hover:bg-red-700 transition">
                        Tolak Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRedeemModal(amount) {
            const input = document.getElementById('modal_redemption_amount');
            input.value = window.formatRupiah ? window.formatRupiah(amount) : amount;
            document.getElementById('redeemModal').classList.remove('hidden');
        }

        function closeRedeemModal() {
            document.getElementById('redeemModal').classList.add('hidden');
        }

        function unformatModalInput() {
            const input = document.getElementById('modal_redemption_amount');
            if (input) {
                input.value = input.value.replace(/[^0-9]/g, '');
            }
        }

        function openRejectModal(trxId) {
            const form = document.getElementById('rejectForm');
            form.action = `/admin/pawns/{{ $pawn->id }}/reject-payment/${trxId}`;
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }
    </script>
</x-admin-app>
