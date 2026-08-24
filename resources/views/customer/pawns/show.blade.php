<x-customer-app>
    <x-slot name="pageTitle">Detail Gadai</x-slot>
    <x-slot name="breadcrumb">Informasi lengkap gadai emas #{{ $pawn->pawn_code }}</x-slot>

    @php
        $daysLeft  = now()->diffInDays($pawn->due_date, false);
        $isExpired = $daysLeft < 0;
    @endphp

    <div class="max-w-3xl mx-auto">
        <a href="{{ route('customer.pawns.index') }}" class="text-xs font-bold text-[#085C54] hover:underline mb-6 inline-flex items-center gap-1">
            ← Kembali ke Daftar Gadai
        </a>

        <div class="glass rounded-3xl overflow-hidden bg-white border border-[#e8e3d5] shadow-lg {{ $isExpired && $pawn->status=='active' ? 'ring-2 ring-red-500' : '' }}">
            <div class="p-6 bg-[#F4EDD9]/60 border-b border-[#e8e3d5]">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-mono text-xs font-bold text-[#085C54]">{{ $pawn->pawn_code }}</p>
                        <h2 class="text-2xl font-bold font-playfair text-slate-900 mt-1">{{ $pawn->gold_description }}</h2>
                        <p class="text-sm text-slate-600 font-semibold mt-1">{{ $pawn->gold_purity }} • {{ number_format($pawn->weight_gram, 2) }} gram</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-extrabold border
                        {{ $pawn->status === 'active' ? 'bg-amber-100 text-amber-900 border-amber-300' : ($pawn->status === 'redeemed' ? 'bg-emerald-100 text-emerald-900 border-emerald-300' : 'bg-red-100 text-red-900 border-red-300') }}">
                        {{ ucfirst($pawn->status) }}
                    </span>
                </div>
            </div>

            <div class="p-6 space-y-6">
                {{-- Alert jika melewati jatuh tempo --}}
                @if($isExpired && $pawn->status === 'active')
                <div class="p-4 rounded-xl bg-red-50 border border-red-300">
                    <p class="text-red-900 font-bold text-sm">⚠️ Gadai ini telah melewati jatuh tempo {{ abs($daysLeft) }} hari!</p>
                    <p class="text-red-800 text-xs font-medium mt-1">Segera hubungi toko untuk mencegah penyitaan barang gadai Anda.</p>
                </div>
                @elseif($pawn->status === 'active' && $daysLeft <= 7)
                <div class="p-4 rounded-xl bg-amber-50 border border-amber-300">
                    <p class="text-amber-900 font-bold text-sm">⏰ Jatuh tempo dalam {{ $daysLeft }} hari!</p>
                    <p class="text-amber-800 text-xs font-medium mt-1">Segera bayar atau hubungi toko untuk perpanjangan.</p>
                </div>
                @endif

                {{-- Detail Gadai --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="glass p-4 rounded-xl bg-white border border-[#e8e3d5]">
                        <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Nilai Taksiran</p>
                        <p class="font-extrabold text-slate-900 text-base">Rp {{ number_format($pawn->appraised_value, 0, ',', '.') }}</p>
                    </div>
                    <div class="glass p-4 rounded-xl bg-amber-50/60 border border-amber-200">
                        <p class="text-xs text-amber-900 font-bold uppercase tracking-wider mb-1">Jumlah Pinjaman</p>
                        <p class="font-extrabold text-[#C6A443] text-base">Rp {{ number_format($pawn->loan_amount, 0, ',', '.') }}</p>
                    </div>
                    <div class="glass p-4 rounded-xl bg-white border border-[#e8e3d5]">
                        <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Bunga per Bulan</p>
                        <p class="font-extrabold text-slate-900 text-base">{{ $pawn->interest_rate }}%</p>
                    </div>
                    <div class="glass p-4 rounded-xl bg-red-50/60 border border-red-200">
                        <p class="text-xs text-red-900 font-bold uppercase tracking-wider mb-1">Estimasi Tebus Sekarang</p>
                        <p class="font-extrabold text-red-700 text-base">Rp {{ number_format($pawn->calculateRedemptionAmount(), 0, ',', '.') }}</p>
                    </div>
                    <div class="glass p-4 rounded-xl bg-white border border-[#e8e3d5]">
                        <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Tanggal Mulai</p>
                        <p class="font-extrabold text-slate-900 text-sm">{{ $pawn->start_date?->isoFormat('D MMM Y') }}</p>
                    </div>
                    <div class="glass p-4 rounded-xl bg-white border border-[#e8e3d5] {{ $isExpired ? 'bg-red-50' : '' }}">
                        <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Jatuh Tempo</p>
                        <p class="font-extrabold text-sm {{ $isExpired ? 'text-red-700' : 'text-slate-900' }}">{{ $pawn->due_date?->isoFormat('D MMM Y') }}</p>
                    </div>
                </div>

                @if($pawn->notes)
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Catatan</p>
                    <p class="text-sm text-slate-800 font-medium">{{ $pawn->notes }}</p>
                </div>
                @endif

                @if($pawn->status === 'redeemed')
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-300">
                    <p class="text-emerald-900 font-bold text-sm">✅ Gadai telah ditebus</p>
                    <p class="text-xs text-emerald-800 font-semibold mt-1">
                        Tanggal tebus: {{ $pawn->redemption_date?->isoFormat('D MMM Y') }} •
                        Jumlah tebus: Rp {{ number_format($pawn->redemption_amount, 0, ',', '.') }}
                    </p>
                </div>
                @endif

                <div class="p-5 rounded-2xl bg-[#F4EDD9]/40 border border-[#e8e3d5]">
                    <p class="font-bold text-slate-900 mb-2 text-sm">Cara Penebusan Emas:</p>
                    <ol class="list-decimal list-inside space-y-1 text-xs text-slate-700 font-medium">
                        <li>Datang langsung ke Toko Emas Sinar Baru II pada jam operasional (08.00 - 17.00 WIB)</li>
                        <li>Bawa kartu identitas (KTP) dan tunjukkan kode gadai <strong class="font-mono text-[#085C54]">{{ $pawn->pawn_code }}</strong> ini</li>
                        <li>Bayar jumlah tebus (pokok pinjaman + bunga berjalan)</li>
                        <li>Emas Anda akan diserahkan kembali dalam kondisi utuh dan aman</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</x-customer-app>

