<x-admin-app>
    <x-slot name="pageTitle">Detail Cicilan</x-slot>
    <x-slot name="breadcrumb">Rincian rencana cicilan pelanggan</x-slot>

    @php
        $paid    = $installmentPlan->paidCount();
        $total   = $installmentPlan->tenure_months;
        $pct     = $total > 0 ? round(($paid / $total) * 100) : 0;
        $product = $installmentPlan->transaction->items->first()->product ?? null;
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
        <a href="{{ route('admin.installments.index') }}" class="px-4 py-2 rounded-lg text-sm text-gray-300 glass hover:bg-white/10 transition">← Kembali</a>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 glass rounded-3xl overflow-hidden">
            <div class="p-6" style="background:linear-gradient(135deg,rgba(59,130,246,0.08),rgba(0,0,0,0.4)); border-bottom:1px solid rgba(255,255,255,0.08);">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-2xl font-bold font-playfair text-white">{{ $product->name ?? 'Cicilan Emas' }}</h2>
                        <p class="text-sm text-gray-400 mt-1">{{ $product->gold_purity ?? '' }} • {{ number_format($product->weight_gram ?? 0, 3) }} gram</p>
                    </div>
                    <span class="px-3 py-1.5 rounded-full text-xs font-semibold
                        {{ $installmentPlan->status === 'active' ? 'bg-blue-900/50 text-blue-400 border border-blue-700/50' : ($installmentPlan->status === 'completed' ? 'bg-green-900/50 text-green-400' : 'bg-gray-900/50 text-gray-400') }}">
                        {{ ucfirst($installmentPlan->status) }}
                    </span>
                </div>
            </div>

            <div class="p-6 space-y-6">
                {{-- Progress --}}
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm text-gray-400">Progress Pembayaran</span>
                        <span class="text-sm font-bold text-blue-400">{{ $paid }} / {{ $total }} bulan ({{ $pct }}%)</span>
                    </div>
                    <div class="h-4 rounded-full overflow-hidden" style="background:rgba(255,255,255,0.05);">
                        <div class="h-full rounded-full" style="width:{{ $pct }}%; background:linear-gradient(90deg,#3b82f6,#60a5fa);"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Sisa {{ $installmentPlan->remainingMonths() }} bulan lagi</p>
                </div>

                {{-- Detail Keuangan --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach([
                        ['Down Payment', 'Rp '.number_format($installmentPlan->down_payment, 0, ',', '.')],
                        ['Angsuran/Bulan', 'Rp '.number_format($installmentPlan->monthly_amount, 0, ',', '.')],
                        ['Total Cicilan', 'Rp '.number_format($installmentPlan->total_installment, 0, ',', '.')],
                        ['Tenor', $installmentPlan->tenure_months.' Bulan'],
                        ['Mulai', $installmentPlan->start_date?->isoFormat('D MMM Y') ?? '-'],
                        ['Selesai', $installmentPlan->end_date?->isoFormat('D MMM Y') ?? '-'],
                    ] as [$label, $val])
                    <div class="glass p-4 rounded-xl">
                        <p class="text-xs text-gray-500 mb-1">{{ $label }}</p>
                        <p class="font-bold text-white">{{ $val }}</p>
                    </div>
                    @endforeach
                </div>

                {{-- Riwayat Pembayaran --}}
                <div>
                    <h3 class="font-semibold text-white mb-4">Riwayat Pembayaran</h3>
                    @if($installmentPlan->payments->isEmpty())
                    <p class="text-gray-500 text-sm py-4 text-center">Belum ada pembayaran tercatat.</p>
                    @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead>
                                <tr class="text-xs text-gray-500" style="border-bottom:1px solid rgba(255,255,255,0.08);">
                                    <th class="py-2 px-3">Bulan ke-</th>
                                    <th class="py-2 px-3">Jatuh Tempo</th>
                                    <th class="py-2 px-3">Tgl Bayar</th>
                                    <th class="py-2 px-3 text-right">Jumlah</th>
                                    <th class="py-2 px-3 text-center">Status</th>
                                    <th class="py-2 px-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($installmentPlan->payments->sortBy('installment_number') as $p)
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.04);">
                                    <td class="py-3 px-3 text-gray-300">{{ $p->installment_number ?? '-' }}</td>
                                    <td class="py-3 px-3 text-gray-400 text-xs">{{ $p->due_date?->isoFormat('D MMM Y') ?? '-' }}</td>
                                    <td class="py-3 px-3 text-gray-300">{{ $p->paid_date?->isoFormat('D MMM Y') ?? '-' }}</td>
                                    <td class="py-3 px-3 text-right font-semibold text-green-400">Rp {{ number_format($p->amount_due, 0, ',', '.') }}</td>
                                    <td class="py-3 px-3 text-center">
                                        <span class="text-xs px-2 py-0.5 rounded-full {{ $p->status === 'paid' ? 'bg-green-900/40 text-green-400' : 'bg-yellow-900/40 text-yellow-400' }}">
                                            {{ ucfirst($p->status) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        @if($p->status !== 'paid' && $installmentPlan->status === 'active')
                                        <button onclick="openPaymentModal({{ $p->id }}, {{ $p->installment_number }}, {{ $p->amount_due }})"
                                                class="px-2 py-1 rounded text-xs bg-yellow-600 hover:bg-yellow-500 text-white font-medium transition">
                                            Catat Bayar
                                        </button>
                                        @else
                                        <span class="text-xs text-gray-500">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar Pelanggan --}}
        <div class="space-y-5">
            <div class="glass rounded-2xl p-5">
                <h3 class="text-sm font-semibold text-yellow-400 mb-4">👤 Info Pelanggan</h3>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-lg font-bold"
                         style="background:linear-gradient(135deg,#f59e0b,#d97706);">
                        {{ strtoupper(substr($installmentPlan->transaction->user->name ?? 'U', 0, 2)) }}
                    </div>
                    <div>
                        <p class="font-bold text-white">{{ $installmentPlan->transaction->user->name ?? '-' }}</p>
                        <p class="text-xs text-gray-400">{{ $installmentPlan->transaction->user->email ?? '-' }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.customers.show', $installmentPlan->transaction->user) }}" class="block w-full text-center text-xs py-2 rounded-lg glass text-gray-300 hover:text-white transition">
                    Lihat Profil Pelanggan →
                </a>
            </div>

            <div class="glass rounded-2xl p-5">
                <h3 class="text-sm font-semibold text-gray-400 mb-4">⚡ Aksi Cepat</h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.installments.index') }}" class="flex items-center gap-3 p-3 rounded-xl text-sm text-gray-300 hover:bg-white/5 transition">
                        📅 Semua Cicilan
                    </a>
                    <a href="{{ route('admin.transactions.show', $installmentPlan->transaction) }}" class="flex items-center gap-3 p-3 rounded-xl text-sm text-gray-300 hover:bg-white/5 transition">
                        🧾 Transaksi Asal
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment Recording Modal --}}
    <div id="paymentModal" class="fixed inset-0 z-50 flex items-center justify-center hidden" style="background:rgba(0,0,0,0.8); backdrop-filter:blur(4px);">
        <div class="glass w-full max-w-md p-6 rounded-3xl" style="border:1px solid rgba(255,255,255,0.15);">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-yellow-400">📝 Catat Pembayaran Angsuran</h3>
                <button onclick="closePaymentModal()" class="text-gray-400 hover:text-white text-lg">&times;</button>
            </div>
            <form id="paymentForm" method="POST" action="">
                @csrf
                <input type="hidden" name="payment_id" id="modal_payment_id">
                
                <p class="text-xs text-gray-400 mb-4">Mencatat pembayaran untuk angsuran <span id="modal_installment_number" class="text-white font-bold"></span>.</p>

                <div class="mb-4">
                    <label class="block text-xs text-gray-400 mb-1">Jumlah Bayar (Rp) *</label>
                    <input type="number" name="amount_paid" id="modal_amount_paid" class="w-full bg-black/40 text-white rounded-xl p-3 text-sm border border-white/10 focus:outline-none focus:border-yellow-400" required>
                </div>

                <div class="mb-4">
                    <label class="block text-xs text-gray-400 mb-1">Metode Pembayaran *</label>
                    <select name="payment_method" class="w-full bg-black/40 text-white rounded-xl p-3 text-sm border border-white/10 focus:outline-none focus:border-yellow-400" required>
                        <option value="cash" class="text-black">Tunai (Cash)</option>
                        <option value="transfer" class="text-black">Transfer Bank</option>
                        <option value="debit" class="text-black">Kartu Debit</option>
                        <option value="credit" class="text-black">Kartu Kredit</option>
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-xs text-gray-400 mb-1">Catatan</label>
                    <textarea name="notes" rows="2" class="w-full bg-black/40 text-white rounded-xl p-3 text-sm border border-white/10 focus:outline-none focus:border-yellow-400" placeholder="Opsional..."></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closePaymentModal()" class="flex-1 py-2.5 rounded-xl text-sm font-semibold glass text-gray-400 hover:text-white transition">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-black transition hover:scale-105" style="background:linear-gradient(135deg,#f59e0b,#d97706);">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openPaymentModal(id, number, amount) {
            const modal = document.getElementById('paymentModal');
            const form = document.getElementById('paymentForm');
            const url = `{{ route('admin.installments.payments.pay', [$installmentPlan->id, ':paymentId']) }}`.replace(':paymentId', id);
            
            form.action = url;
            document.getElementById('modal_payment_id').value = id;
            document.getElementById('modal_installment_number').textContent = 'Bulan ke-' + number;
            document.getElementById('modal_amount_paid').value = amount;

            modal.classList.remove('hidden');
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
        }
    </script>
</x-admin-app>
