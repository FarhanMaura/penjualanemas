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
        <a href="{{ route('admin.installments.index') }}" class="px-4 py-2 rounded-xl text-sm font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition shadow-sm">← Kembali</a>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 glass rounded-3xl overflow-hidden bg-white border border-[#e8e3d5] shadow-lg">
            <div class="p-6 bg-[#F4EDD9]/60 border-b border-[#e8e3d5]">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-2xl font-bold font-playfair text-slate-900">{{ $product->name ?? 'Cicilan Emas' }}</h2>
                        <p class="text-sm text-slate-600 font-semibold mt-1">{{ $product->gold_purity ?? '' }} • {{ number_format($product->weight_gram ?? 0, 3) }} gram</p>
                    </div>
                    <span class="px-3.5 py-1.5 rounded-full text-xs font-extrabold border
                        {{ $installmentPlan->status === 'active' ? 'bg-blue-100 text-blue-900 border-blue-300' : ($installmentPlan->status === 'completed' ? 'bg-emerald-100 text-emerald-900 border-emerald-300' : 'bg-slate-100 text-slate-800 border-slate-300') }}">
                        {{ ucfirst($installmentPlan->status) }}
                    </span>
                </div>
            </div>

            <div class="p-6 space-y-6">
                {{-- Progress --}}
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm font-bold text-slate-700">Progress Pembayaran</span>
                        <span class="text-sm font-extrabold text-[#085C54]">{{ $paid }} / {{ $total }} bulan ({{ $pct }}%)</span>
                    </div>
                    <div class="h-4 rounded-full overflow-hidden bg-slate-100 border border-slate-200">
                        <div class="h-full rounded-full transition-all" style="width:{{ $pct }}%; background:linear-gradient(90deg,#085C54,#C6A443);"></div>
                    </div>
                    <p class="text-xs text-slate-500 font-semibold mt-1.5">Sisa {{ $installmentPlan->remainingMonths() }} bulan lagi</p>
                </div>

                {{-- Detail Keuangan --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach([
                        ['Down Payment (DP)', 'Rp '.number_format($installmentPlan->down_payment, 0, ',', '.')],
                        ['Angsuran / Bulan', 'Rp '.number_format($installmentPlan->monthly_amount, 0, ',', '.')],
                        ['Total Cicilan', 'Rp '.number_format($installmentPlan->total_installment, 0, ',', '.')],
                        ['Tenor', $installmentPlan->tenure_months.' Bulan'],
                        ['Mulai', $installmentPlan->start_date?->isoFormat('D MMM Y') ?? '-'],
                        ['Selesai', $installmentPlan->end_date?->isoFormat('D MMM Y') ?? '-'],
                    ] as [$label, $val])
                    <div class="glass p-4 rounded-xl bg-white border border-[#e8e3d5] shadow-sm">
                        <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">{{ $label }}</p>
                        <p class="font-extrabold text-slate-900 text-base">{{ $val }}</p>
                    </div>
                    @endforeach
                </div>

                {{-- Riwayat Pembayaran --}}
                <div>
                    <h3 class="font-bold text-slate-900 mb-4 text-base">Riwayat Pembayaran Angsuran</h3>
                    @if($installmentPlan->payments->isEmpty())
                    <p class="text-slate-500 text-sm py-6 text-center bg-slate-50 rounded-2xl border border-slate-200">Belum ada pembayaran tercatat.</p>
                    @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead>
                                <tr class="text-xs text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                                    <th class="py-2.5 px-3">Bulan ke-</th>
                                    <th class="py-2.5 px-3">Jatuh Tempo</th>
                                    <th class="py-2.5 px-3">Tgl Bayar</th>
                                    <th class="py-2.5 px-3 text-right">Jumlah</th>
                                    <th class="py-2.5 px-3 text-center">Status</th>
                                    <th class="py-2.5 px-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($installmentPlan->payments->sortBy('installment_number') as $p)
                                <tr>
                                    <td class="py-3.5 px-3 font-bold text-slate-900">{{ $p->installment_number ?? '-' }}</td>
                                    <td class="py-3.5 px-3 text-slate-600 font-medium text-xs">{{ $p->due_date?->isoFormat('D MMM Y') ?? '-' }}</td>
                                    <td class="py-3.5 px-3 text-slate-700 font-medium">{{ $p->paid_date?->isoFormat('D MMM Y') ?? '-' }}</td>
                                    <td class="py-3.5 px-3 text-right font-extrabold text-slate-900">Rp {{ number_format($p->amount_due, 0, ',', '.') }}</td>
                                    <td class="py-3.5 px-3 text-center">
                                        <span class="text-xs px-2.5 py-0.5 rounded-full font-bold border {{ $p->status === 'paid' ? 'bg-emerald-100 text-emerald-900 border-emerald-300' : 'bg-amber-100 text-amber-900 border-amber-300' }}">
                                            {{ $p->status === 'paid' ? 'Lunas' : 'Belum Lunas' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-3 text-center">
                                        @if($p->status !== 'paid' && $installmentPlan->status === 'active')
                                        <button onclick="openPaymentModal({{ $p->id }}, {{ $p->installment_number }}, {{ $p->amount_due }})"
                                                class="px-3 py-1.5 rounded-xl text-xs bg-[#085C54] hover:bg-[#063e39] text-white font-extrabold transition shadow-sm">
                                            Catat Bayar
                                        </button>
                                        @else
                                        <span class="text-xs text-slate-400 font-semibold">-</span>
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
            <div class="glass rounded-2xl p-5 bg-white border border-[#e8e3d5] shadow-sm">
                <h3 class="text-sm font-bold text-slate-900 mb-4">👤 Info Pelanggan</h3>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-base font-extrabold text-[#042623] gold-gradient border border-[#C6A443] shadow-md">
                        {{ strtoupper(substr($installmentPlan->transaction->user->name ?? 'U', 0, 2)) }}
                    </div>
                    <div>
                        <p class="font-bold text-slate-900 text-base">{{ $installmentPlan->transaction->user->name ?? '-' }}</p>
                        <p class="text-xs text-slate-500 font-medium">{{ $installmentPlan->transaction->user->email ?? '-' }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.customers.show', $installmentPlan->transaction->user) }}" class="block w-full text-center text-xs font-bold py-2 rounded-xl bg-slate-100 text-slate-800 hover:bg-slate-200 transition border border-slate-300">
                    Lihat Profil Pelanggan →
                </a>
            </div>

            <div class="glass rounded-2xl p-5 bg-white border border-[#e8e3d5] shadow-sm">
                <h3 class="text-sm font-bold text-slate-900 mb-4">⚡ Aksi Cepat</h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.installments.index') }}" class="flex items-center gap-3 p-3 rounded-xl text-sm font-bold text-slate-700 bg-slate-50 hover:bg-slate-100 transition border border-slate-200">
                        📅 Semua Cicilan
                    </a>
                    <a href="{{ route('admin.transactions.show', $installmentPlan->transaction) }}" class="flex items-center gap-3 p-3 rounded-xl text-sm font-bold text-slate-700 bg-slate-50 hover:bg-slate-100 transition border border-slate-200">
                        🧾 Transaksi Asal
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment Recording Modal --}}
    <div id="paymentModal" class="fixed inset-0 z-50 flex items-center justify-center hidden" style="background:rgba(4,38,35,0.6); backdrop-filter:blur(4px);">
        <div class="w-full max-w-md p-6 rounded-3xl bg-white border border-[#e8e3d5] shadow-2xl">
            <div class="flex justify-between items-center mb-4 pb-3 border-b border-slate-200">
                <h3 class="text-base font-bold text-slate-900">📝 Catat Pembayaran Angsuran</h3>
                <button onclick="closePaymentModal()" class="text-slate-400 hover:text-slate-700 text-xl font-bold">&times;</button>
            </div>
            <form id="paymentForm" method="POST" action="">
                @csrf
                <input type="hidden" name="payment_id" id="modal_payment_id">
                
                <p class="text-xs text-slate-600 font-medium mb-4">Mencatat pembayaran untuk angsuran <strong id="modal_installment_number" class="text-slate-900 font-bold"></strong>.</p>

                <div class="mb-4">
                    <label class="input-label">Jumlah Bayar (Rp) <span class="text-red-600">*</span></label>
                    <input type="number" name="amount_paid" id="modal_amount_paid" class="input-field font-extrabold text-slate-900" required>
                </div>

                <div class="mb-4">
                    <label class="input-label">Metode Pembayaran <span class="text-red-600">*</span></label>
                    <select name="payment_method" class="input-field cursor-pointer font-semibold" required>
                        <option value="cash" class="text-slate-900">Tunai (Cash)</option>
                        <option value="transfer" class="text-slate-900">Transfer Bank</option>
                        <option value="debit" class="text-slate-900">Kartu Debit</option>
                        <option value="credit" class="text-slate-900">Kartu Kredit</option>
                    </select>
                </div>

                <div class="mb-6">
                    <label class="input-label">Catatan</label>
                    <textarea name="notes" rows="2" class="input-field text-sm" placeholder="Opsional..."></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closePaymentModal()" class="flex-1 py-2.5 rounded-xl text-sm font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl text-sm font-extrabold text-[#042623] gold-gradient border border-[#C6A443] shadow-md transition hover:scale-105">
                        Simpan Pembayaran
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

