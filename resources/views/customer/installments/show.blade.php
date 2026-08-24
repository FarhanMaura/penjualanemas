<x-customer-app>
    <x-slot name="pageTitle">Detail Cicilan</x-slot>
    <x-slot name="breadcrumb">Rincian dan jadwal pembayaran cicilan Anda</x-slot>

    @php
        $paid  = $installmentPlan->paidCount();
        $total = $installmentPlan->tenure_months;
        $pct   = $total > 0 ? round(($paid / $total) * 100) : 0;
        $product = $installmentPlan->transaction->items->first()->product ?? null;
    @endphp

    <div class="max-w-3xl mx-auto">
        <a href="{{ route('customer.installments.index') }}" class="text-xs font-bold text-[#085C54] hover:underline mb-6 inline-flex items-center gap-1">
            ← Kembali ke Daftar Cicilan
        </a>

        <div class="glass rounded-3xl overflow-hidden bg-white border border-[#e8e3d5] shadow-lg">
            <div class="p-6 bg-[#F4EDD9]/60 border-b border-[#e8e3d5]">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-xl font-bold font-playfair text-slate-900">{{ $product->name ?? 'Cicilan Emas' }}</h2>
                        <p class="text-sm text-slate-600 font-semibold mt-1">{{ $product->gold_purity ?? '' }} • {{ number_format($product->weight_gram ?? 0, 3) }} gram</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-extrabold border
                        {{ $installmentPlan->status === 'active' ? 'bg-blue-100 text-blue-900 border-blue-300' : ($installmentPlan->status === 'completed' ? 'bg-emerald-100 text-emerald-900 border-emerald-300' : 'bg-slate-100 text-slate-800 border-slate-300') }}">
                        {{ ucfirst($installmentPlan->status) }}
                    </span>
                </div>
            </div>

            <div class="p-6 space-y-6">
                {{-- Progress Bar Besar --}}
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm font-bold text-slate-700">Progress Pembayaran</span>
                        <span class="text-sm font-extrabold text-[#085C54]">{{ $paid }} / {{ $total }} bulan ({{ $pct }}%)</span>
                    </div>
                    <div class="h-4 rounded-full overflow-hidden bg-slate-100 border border-slate-200">
                        <div class="h-full rounded-full transition-all" style="width:{{ $pct }}%; background:linear-gradient(90deg,#085C54,#C6A443);"></div>
                    </div>
                </div>

                {{-- Detail Keuangan --}}
                <div class="grid grid-cols-2 gap-4">
                    @foreach([
                        ['Down Payment (DP)', 'Rp '.number_format($installmentPlan->down_payment, 0, ',', '.')],
                        ['Angsuran / Bulan', 'Rp '.number_format($installmentPlan->monthly_amount, 0, ',', '.')],
                        ['Total Pembayaran Cicilan', 'Rp '.number_format($installmentPlan->total_installment, 0, ',', '.')],
                        ['Jangka Waktu (Tenor)', $installmentPlan->tenure_months.' Bulan'],
                        ['Tanggal Mulai', $installmentPlan->start_date?->isoFormat('D MMM Y') ?? '-'],
                        ['Perkiraan Selesai', $installmentPlan->end_date?->isoFormat('D MMM Y') ?? '-'],
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
                    <p class="text-slate-500 text-sm text-center py-6 bg-slate-50 rounded-2xl border border-slate-200">Belum ada angsuran tercatat.</p>
                    @else
                    <div class="space-y-2">
                        @foreach($installmentPlan->payments->sortBy('installment_number') as $payment)
                        <div class="flex justify-between items-center p-3.5 rounded-xl bg-slate-50 border border-slate-200 shadow-sm">
                            <div>
                                <p class="text-sm font-bold text-slate-900">Bulan ke-{{ $payment->installment_number ?? '-' }}</p>
                                <p class="text-xs text-slate-500 font-medium mt-0.5">Tanggal: {{ $payment->paid_date?->isoFormat('D MMM Y') ?? '-' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-extrabold text-slate-900">Rp {{ number_format($payment->amount_due, 0, ',', '.') }}</p>
                                <span class="text-xs font-bold {{ $payment->status === 'paid' ? 'text-emerald-700' : 'text-amber-700' }}">
                                    {{ $payment->status === 'paid' ? '✅ Lunas' : '⏳ Belum Dibayar' }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-customer-app>

