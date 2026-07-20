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
        <a href="{{ route('customer.installments.index') }}" class="text-sm text-gray-400 hover:text-white mb-6 inline-block">← Kembali</a>

        <div class="glass rounded-3xl overflow-hidden">
            <div class="p-6" style="background:linear-gradient(135deg,rgba(59,130,246,0.1),rgba(0,0,0,0.5)); border-bottom:1px solid rgba(255,255,255,0.08);">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-xl font-bold font-playfair text-white">{{ $product->name ?? 'Cicilan Emas' }}</h2>
                        <p class="text-sm text-gray-400 mt-1">{{ $product->gold_purity ?? '' }} • {{ number_format($product->weight_gram ?? 0, 3) }} gram</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                        {{ $installmentPlan->status === 'active' ? 'bg-blue-900/50 text-blue-400' : ($installmentPlan->status === 'completed' ? 'bg-green-900/50 text-green-400' : 'bg-gray-900/50 text-gray-400') }}">
                        {{ ucfirst($installmentPlan->status) }}
                    </span>
                </div>
            </div>

            <div class="p-6 space-y-6">
                {{-- Progress Bar Besar --}}
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm text-gray-400">Progress Pembayaran</span>
                        <span class="text-sm font-bold text-blue-400">{{ $paid }} / {{ $total }} bulan ({{ $pct }}%)</span>
                    </div>
                    <div class="h-4 rounded-full overflow-hidden" style="background:rgba(255,255,255,0.05);">
                        <div class="h-full rounded-full" style="width:{{ $pct }}%; background:linear-gradient(90deg,#3b82f6,#60a5fa);"></div>
                    </div>
                </div>

                {{-- Detail Keuangan --}}
                <div class="grid grid-cols-2 gap-4">
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
                    <p class="text-gray-500 text-sm text-center py-4">Belum ada pembayaran tercatat.</p>
                    @else
                    <div class="space-y-2">
                        @foreach($installmentPlan->payments->sortBy('installment_number') as $payment)
                        <div class="flex justify-between items-center p-3 rounded-xl" style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06);">
                            <div>
                                <p class="text-sm text-white">Bulan ke-{{ $payment->installment_number ?? '-' }}</p>
                                <p class="text-xs text-gray-500">{{ $payment->paid_date?->isoFormat('D MMM Y') ?? '-' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-green-400">Rp {{ number_format($payment->amount_due, 0, ',', '.') }}</p>
                                <span class="text-xs {{ $payment->status === 'paid' ? 'text-green-400' : 'text-yellow-400' }}">{{ ucfirst($payment->status) }}</span>
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
