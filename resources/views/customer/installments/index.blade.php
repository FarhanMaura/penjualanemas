<x-customer-app>
    <x-slot name="pageTitle">Cicilan Saya</x-slot>
    <x-slot name="breadcrumb">Monitor progress pembayaran cicilan emas Anda</x-slot>

    {{-- Summary Cards --}}
    <div class="grid md:grid-cols-2 gap-6 mb-8">
        <div class="glass rounded-2xl p-6" style="background:rgba(59,130,246,0.05); border-color:rgba(59,130,246,0.2);">
            <div class="flex items-center gap-4">
                <span class="text-3xl">📅</span>
                <div>
                    <p class="text-xs text-gray-400">Cicilan Aktif</p>
                    <p class="text-2xl font-bold text-blue-400">{{ $summary['active'] }}</p>
                </div>
            </div>
        </div>
        <div class="glass rounded-2xl p-6" style="background:rgba(16,185,129,0.05); border-color:rgba(16,185,129,0.2);">
            <div class="flex items-center gap-4">
                <span class="text-3xl">✅</span>
                <div>
                    <p class="text-xs text-gray-400">Cicilan Lunas</p>
                    <p class="text-2xl font-bold text-green-400">{{ $summary['completed'] }}</p>
                </div>
            </div>
        </div>
    </div>

    @if($installments->isEmpty())
    <div class="glass rounded-3xl p-12 text-center">
        <span class="text-6xl">📭</span>
        <p class="text-gray-400 text-lg mt-4 font-semibold">Belum Ada Data Cicilan</p>
        <p class="text-gray-500 text-sm mt-2">Data cicilan akan muncul setelah Admin mencatat transaksi cicilan untuk Anda.</p>
        <a href="{{ route('customer.reservations.create') }}" class="mt-6 inline-block text-yellow-400 hover:underline">
            Buat Reservasi →
        </a>
    </div>
    @else
    <div class="space-y-5">
        @foreach($installments as $plan)
        @php
            $paid    = $plan->paidCount();
            $total   = $plan->tenure_months;
            $pct     = $total > 0 ? round(($paid / $total) * 100) : 0;
            $statusStyles = [
                'active'    => ['label'=>'Aktif', 'class'=>'bg-blue-900/40 text-blue-400 border-blue-700/50'],
                'completed' => ['label'=>'Lunas', 'class'=>'bg-green-900/40 text-green-400 border-green-700/50'],
                'overdue'   => ['label'=>'Telat', 'class'=>'bg-red-900/40 text-red-400 border-red-700/50'],
                'cancelled' => ['label'=>'Batal', 'class'=>'bg-gray-900/40 text-gray-400 border-gray-700/50'],
            ][$plan->status] ?? ['label'=>$plan->status, 'class'=>'bg-gray-900/40 text-gray-400'];
            $product = $plan->transaction->items->first()->product ?? null;
        @endphp

        <div class="glass rounded-2xl p-6 hover:bg-white/5 transition" style="border-color:rgba(255,255,255,0.06);">
            <div class="flex flex-col md:flex-row justify-between gap-5">
                {{-- Info Produk --}}
                <div class="flex items-start gap-4 flex-1">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl shrink-0"
                         style="background:rgba(59,130,246,0.15);">📅</div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <h3 class="font-bold text-white">{{ $product->name ?? 'Produk Cicilan' }}</h3>
                            <span class="text-xs px-2 py-0.5 rounded-full border {{ $statusStyles['class'] }}">
                                {{ $statusStyles['label'] }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-400">Mulai: {{ $plan->start_date?->isoFormat('D MMM Y') }} • Selesai: {{ $plan->end_date?->isoFormat('D MMM Y') }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            DP: Rp {{ number_format($plan->down_payment, 0, ',', '.') }} •
                            Angsuran: Rp {{ number_format($plan->monthly_amount, 0, ',', '.') }}/bulan
                        </p>
                    </div>
                </div>

                {{-- Progress --}}
                <div class="md:w-64">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-400">Progress</span>
                        <span class="text-blue-400 font-semibold">{{ $paid }} / {{ $total }} bulan</span>
                    </div>
                    <div class="h-2.5 rounded-full overflow-hidden" style="background:rgba(255,255,255,0.05);">
                        <div class="h-full rounded-full transition-all" style="width:{{ $pct }}%; background:linear-gradient(90deg,#3b82f6,#60a5fa);"></div>
                    </div>
                    <div class="flex justify-between mt-1.5 text-xs text-gray-500">
                        <span>{{ $pct }}% lunas</span>
                        <span>Sisa {{ $plan->remainingMonths() }} bulan</span>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <a href="{{ route('customer.installments.show', $plan) }}"
                           class="text-xs px-4 py-2 rounded-lg font-semibold text-white"
                           style="background:linear-gradient(135deg,#3b82f6,#2563eb);">
                            Lihat Detail →
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    {{ $installments->links() }}
    @endif

    {{-- Info Box --}}
    <div class="glass rounded-2xl p-6 mt-8" style="background:rgba(59,130,246,0.04); border-color:rgba(59,130,246,0.15);">
        <h3 class="font-semibold text-blue-400 mb-3">ℹ️ Tentang Cicilan Emas</h3>
        <p class="text-sm text-gray-400">Pembayaran angsuran dilakukan langsung di toko setiap bulan. Pastikan membayar tepat waktu untuk menghindari denda. Hubungi toko jika ada pertanyaan mengenai jadwal cicilan Anda.</p>
    </div>
</x-customer-app>
