<x-customer-app>
    <x-slot name="pageTitle">Gadai Saya</x-slot>
    <x-slot name="breadcrumb">Status dan rincian gadai emas Anda di Sinar Baru II</x-slot>

    {{-- Summary Cards --}}
    <div class="grid md:grid-cols-3 gap-6 mb-8">
        <div class="glass rounded-2xl p-6 bg-amber-50/70 border border-amber-200">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-100 border border-amber-300 flex items-center justify-center text-2xl shrink-0">🏦</div>
                <div>
                    <p class="text-xs font-bold text-amber-900 uppercase tracking-wider">Gadai Aktif</p>
                    <p class="text-2xl font-extrabold text-[#C6A443] mt-0.5">{{ $summary['active'] }}</p>
                </div>
            </div>
        </div>
        <div class="glass rounded-2xl p-6 bg-emerald-50/70 border border-emerald-200">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 border border-emerald-300 flex items-center justify-center text-2xl shrink-0">✅</div>
                <div>
                    <p class="text-xs font-bold text-emerald-900 uppercase tracking-wider">Sudah Ditebus</p>
                    <p class="text-2xl font-extrabold text-emerald-700 mt-0.5">{{ $summary['redeemed'] }}</p>
                </div>
            </div>
        </div>
        <div class="glass rounded-2xl p-6 bg-red-50/70 border border-red-200">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-red-100 border border-red-300 flex items-center justify-center text-2xl shrink-0">💰</div>
                <div>
                    <p class="text-xs font-bold text-red-900 uppercase tracking-wider">Total Pinjaman Aktif</p>
                    <p class="text-xl font-extrabold text-red-700 mt-0.5">Rp {{ number_format($summary['total_loan'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    @if($pawns->isEmpty())
    <div class="glass rounded-3xl p-12 text-center bg-white border border-[#e8e3d5]">
        <span class="text-6xl">📭</span>
        <p class="text-slate-800 text-lg mt-4 font-bold">Belum Ada Data Gadai</p>
        <p class="text-slate-600 text-sm mt-2">Data gadai akan muncul setelah Admin mencatat transaksi gadai untuk Anda.</p>
        <a href="{{ route('customer.reservations.create') }}" class="mt-6 inline-block font-bold text-[#085C54] hover:underline">
            Buat Reservasi untuk Gadai →
        </a>
    </div>
    @else
    <div class="grid md:grid-cols-2 gap-6 mb-8">
        @foreach($pawns as $pawn)
        @php
            $dueDate    = $pawn->due_date;
            $daysLeft   = now()->diffInDays($dueDate, false);
            $isExpired  = $daysLeft < 0;
            $isWarning  = !$isExpired && $daysLeft <= 7;

            $statusStyles = [
                'active'   => ['label'=>'Aktif', 'class'=>'bg-amber-100 text-amber-800 border-amber-300'],
                'redeemed' => ['label'=>'Ditebus', 'class'=>'bg-emerald-100 text-emerald-800 border-emerald-300'],
                'forfeited'=> ['label'=>'Hangus', 'class'=>'bg-red-100 text-red-800 border-red-300'],
            ][$pawn->status] ?? ['label'=>$pawn->status, 'class'=>'bg-slate-100 text-slate-800 border-slate-300'];
        @endphp

        <div class="glass rounded-2xl overflow-hidden bg-white border border-[#e8e3d5] shadow-sm {{ $isExpired ? 'ring-2 ring-red-500/50' : ($isWarning ? 'ring-2 ring-amber-500/50' : '') }}">

            <div class="p-5 bg-slate-50 border-b border-slate-100">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-mono text-xs font-bold text-slate-500">{{ $pawn->pawn_code }}</p>
                        <h3 class="font-bold text-slate-900 text-lg mt-1">{{ $pawn->gold_description }}</h3>
                    </div>
                    <span class="text-xs px-2.5 py-0.5 rounded-full font-bold uppercase border tracking-wider {{ $statusStyles['class'] }}">{{ $statusStyles['label'] }}</span>
                </div>
            </div>

            <div class="p-5 space-y-3">
                <div class="grid grid-cols-2 gap-3">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                        <p class="text-xs font-bold text-slate-500 mb-1 uppercase tracking-wider">Nilai Taksiran</p>
                        <p class="font-extrabold text-slate-900 text-sm">Rp {{ number_format($pawn->appraised_value, 0, ',', '.') }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-amber-50 border border-amber-200">
                        <p class="text-xs font-bold text-amber-900 mb-1 uppercase tracking-wider">Jumlah Pinjaman</p>
                        <p class="font-extrabold text-[#C6A443] text-sm">Rp {{ number_format($pawn->loan_amount, 0, ',', '.') }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                        <p class="text-xs font-bold text-slate-500 mb-1 uppercase tracking-wider">Bunga / Bulan</p>
                        <p class="font-extrabold text-slate-900 text-sm">{{ $pawn->interest_rate }}%</p>
                    </div>
                    <div class="p-3 rounded-xl border {{ $isExpired ? 'bg-red-50 border-red-200' : ($isWarning ? 'bg-amber-50 border-amber-200' : 'bg-slate-50 border-slate-200') }}">
                        <p class="text-xs font-bold text-slate-500 mb-1 uppercase tracking-wider">Jatuh Tempo</p>
                        <p class="font-extrabold text-sm {{ $isExpired ? 'text-red-700' : ($isWarning ? 'text-amber-800' : 'text-slate-900') }}">
                            {{ $dueDate?->isoFormat('D MMM Y') }}
                        </p>
                    </div>
                </div>

                @if($pawn->status === 'active')
                <div class="p-3 rounded-xl border {{ $isExpired ? 'bg-red-50 border-red-300' : ($isWarning ? 'bg-amber-50 border-amber-300' : 'bg-slate-50 border-slate-200') }}">
                    @if($isExpired)
                        <p class="text-xs text-red-800 font-bold">⚠️ Gadai ini sudah jatuh tempo! Segera hubungi toko.</p>
                    @elseif($isWarning)
                        <p class="text-xs text-amber-900 font-bold">⏳ Sisa {{ $daysLeft }} hari lagi menuju jatuh tempo.</p>
                    @else
                        <p class="text-xs text-slate-700 font-semibold">Sisa waktu: <span class="font-bold text-[#085C54]">{{ $daysLeft }} hari</span></p>
                    @endif
                </div>
                @endif
            </div>

            <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-between items-center">
                <span class="text-xs text-slate-500 font-semibold">Estimasi Tebus Sekarang:</span>
                <span class="text-sm font-extrabold text-[#085C54]">Rp {{ number_format($pawn->calculateRedemptionAmount(), 0, ',', '.') }}</span>
            </div>
        </div>
        @endforeach
    </div>
    {{ $pawns->links() }}
    @endif
</x-customer-app>
