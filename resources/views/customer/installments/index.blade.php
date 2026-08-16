<x-customer-app>
    <x-slot name="pageTitle">Cicilan Saya</x-slot>
    <x-slot name="breadcrumb">Monitor progress pembayaran cicilan emas Anda</x-slot>

    {{-- Summary Cards --}}
    <div class="grid md:grid-cols-2 gap-6 mb-8">
        <div class="glass rounded-2xl p-6 bg-blue-50/70 border border-blue-200">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-100 border border-blue-300 flex items-center justify-center text-2xl shrink-0">📅</div>
                <div>
                    <p class="text-xs font-bold text-blue-900 uppercase tracking-wider">Cicilan Aktif</p>
                    <p class="text-2xl font-extrabold text-blue-700 mt-0.5">{{ $summary['active'] }}</p>
                </div>
            </div>
        </div>
        <div class="glass rounded-2xl p-6 bg-emerald-50/70 border border-emerald-200">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 border border-emerald-300 flex items-center justify-center text-2xl shrink-0">✅</div>
                <div>
                    <p class="text-xs font-bold text-emerald-900 uppercase tracking-wider">Cicilan Lunas</p>
                    <p class="text-2xl font-extrabold text-emerald-700 mt-0.5">{{ $summary['completed'] }}</p>
                </div>
            </div>
        </div>
    </div>

    @if($installments->isEmpty())
    <div class="glass rounded-3xl p-12 text-center bg-white border border-[#e8e3d5]">
        <span class="text-6xl">📭</span>
        <p class="text-slate-800 text-lg mt-4 font-bold">Belum Ada Data Cicilan</p>
        <p class="text-slate-600 text-sm mt-2">Data cicilan akan muncul setelah Admin mencatat transaksi cicilan untuk Anda.</p>
        <a href="{{ route('customer.reservations.create') }}" class="mt-6 inline-block font-bold text-[#085C54] hover:underline">
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
                'active'    => ['label'=>'Aktif', 'class'=>'bg-blue-100 text-blue-800 border-blue-300'],
                'completed' => ['label'=>'Lunas', 'class'=>'bg-emerald-100 text-emerald-800 border-emerald-300'],
                'overdue'   => ['label'=>'Telat', 'class'=>'bg-red-100 text-red-800 border-red-300'],
                'cancelled' => ['label'=>'Batal', 'class'=>'bg-slate-100 text-slate-800 border-slate-300'],
            ][$plan->status] ?? ['label'=>$plan->status, 'class'=>'bg-slate-100 text-slate-800 border-slate-300'];
            $product = $plan->transaction->items->first()->product ?? null;
        @endphp

        <div class="glass rounded-2xl p-6 bg-white border border-[#e8e3d5] shadow-sm hover:border-[#085C54]/40 transition">
            <div class="flex flex-col md:flex-row justify-between gap-5">
                {{-- Info Produk --}}
                <div class="flex items-start gap-4 flex-1">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl shrink-0 bg-blue-100 border border-blue-200">📅</div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <h3 class="font-bold text-slate-900 text-base">{{ $product->name ?? 'Produk Cicilan' }}</h3>
                            <span class="text-xs px-2.5 py-0.5 rounded-full font-bold uppercase border tracking-wider {{ $statusStyles['class'] }}">
                                {{ $statusStyles['label'] }}
                            </span>
                        </div>
                        <p class="text-xs font-medium text-slate-600">Mulai: <span class="font-bold text-slate-800">{{ $plan->start_date?->isoFormat('D MMM Y') }}</span> • Selesai: <span class="font-bold text-slate-800">{{ $plan->end_date?->isoFormat('D MMM Y') }}</span></p>
                        <p class="text-xs font-semibold text-slate-700 mt-1">
                            DP: <span class="font-bold text-[#042623]">Rp {{ number_format($plan->down_payment, 0, ',', '.') }}</span> •
                            Angsuran: <span class="font-bold text-[#085C54]">Rp {{ number_format($plan->monthly_amount, 0, ',', '.') }}/bulan</span>
                        </p>
                    </div>
                </div>

                {{-- Progress --}}
                <div class="md:w-64">
                    <div class="flex justify-between text-xs font-bold mb-1.5">
                        <span class="text-slate-600">Progress Pembayaran</span>
                        <span class="text-blue-700">{{ $paid }} / {{ $total }} bulan</span>
                    </div>
                    <div class="h-3 rounded-full overflow-hidden bg-slate-100 border border-slate-200">
                        <div class="h-full rounded-full transition-all bg-gradient-to-r from-blue-500 to-blue-600" style="width:{{ $pct }}%;"></div>
                    </div>
                    <div class="flex justify-between mt-1.5 text-xs font-semibold text-slate-500">
                        <span>{{ $pct }}% lunas</span>
                        <span>Sisa {{ $plan->remainingMonths() }} bulan</span>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <a href="{{ route('customer.installments.show', $plan) }}"
                           class="text-xs px-4 py-2 rounded-xl font-bold text-white shadow-md bg-gradient-to-r from-blue-600 to-blue-700 hover:brightness-110 transition">
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
</x-customer-app>
