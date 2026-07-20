<x-customer-app>
    <x-slot name="pageTitle">Detail Gadai</x-slot>
    <x-slot name="breadcrumb">Informasi lengkap gadai emas #{{ $pawn->pawn_code }}</x-slot>

    @php
        $daysLeft  = now()->diffInDays($pawn->due_date, false);
        $isExpired = $daysLeft < 0;
    @endphp

    <div class="max-w-3xl mx-auto">
        <a href="{{ route('customer.pawns.index') }}" class="text-sm text-gray-400 hover:text-white mb-6 inline-block">← Kembali</a>

        <div class="glass rounded-3xl overflow-hidden {{ $isExpired && $pawn->status=='active' ? 'ring-2 ring-red-500/40' : '' }}">
            <div class="p-6" style="background:linear-gradient(135deg,rgba(245,158,11,0.1),rgba(0,0,0,0.5)); border-bottom:1px solid rgba(255,255,255,0.08);">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-mono text-sm text-gray-400">{{ $pawn->pawn_code }}</p>
                        <h2 class="text-2xl font-bold font-playfair text-white mt-1">{{ $pawn->gold_description }}</h2>
                        <p class="text-sm text-gray-400 mt-1">{{ $pawn->gold_purity }} • {{ number_format($pawn->weight_gram, 2) }} gram</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                        {{ $pawn->status === 'active' ? 'bg-yellow-900/50 text-yellow-400' : ($pawn->status === 'redeemed' ? 'bg-green-900/50 text-green-400' : 'bg-red-900/50 text-red-400') }}">
                        {{ ucfirst($pawn->status) }}
                    </span>
                </div>
            </div>

            <div class="p-6 space-y-6">
                {{-- Alert jika melewati jatuh tempo --}}
                @if($isExpired && $pawn->status === 'active')
                <div class="p-4 rounded-xl" style="background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.3);">
                    <p class="text-red-400 font-semibold text-sm">⚠️ Gadai ini telah melewati jatuh tempo {{ abs($daysLeft) }} hari!</p>
                    <p class="text-red-400/70 text-xs mt-1">Segera hubungi toko untuk mencegah penyitaan barang gadai Anda.</p>
                </div>
                @elseif($pawn->status === 'active' && $daysLeft <= 7)
                <div class="p-4 rounded-xl" style="background:rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.3);">
                    <p class="text-yellow-400 font-semibold text-sm">⏰ Jatuh tempo dalam {{ $daysLeft }} hari!</p>
                    <p class="text-yellow-400/70 text-xs mt-1">Segera bayar atau hubungi toko untuk perpanjangan.</p>
                </div>
                @endif

                {{-- Detail Gadai --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="glass p-4 rounded-xl">
                        <p class="text-xs text-gray-500 mb-1">Nilai Taksiran</p>
                        <p class="font-bold text-white">Rp {{ number_format($pawn->appraised_value, 0, ',', '.') }}</p>
                    </div>
                    <div class="glass p-4 rounded-xl" style="background:rgba(245,158,11,0.05);">
                        <p class="text-xs text-gray-500 mb-1">Jumlah Pinjaman</p>
                        <p class="font-bold text-yellow-400">Rp {{ number_format($pawn->loan_amount, 0, ',', '.') }}</p>
                    </div>
                    <div class="glass p-4 rounded-xl">
                        <p class="text-xs text-gray-500 mb-1">Bunga per Bulan</p>
                        <p class="font-bold text-white">{{ $pawn->interest_rate }}%</p>
                    </div>
                    <div class="glass p-4 rounded-xl">
                        <p class="text-xs text-gray-500 mb-1">Estimasi Tebus Sekarang</p>
                        <p class="font-bold text-red-400">Rp {{ number_format($pawn->calculateRedemptionAmount(), 0, ',', '.') }}</p>
                    </div>
                    <div class="glass p-4 rounded-xl">
                        <p class="text-xs text-gray-500 mb-1">Tanggal Mulai</p>
                        <p class="font-bold text-white">{{ $pawn->start_date?->isoFormat('D MMM Y') }}</p>
                    </div>
                    <div class="glass p-4 rounded-xl {{ $isExpired ? 'bg-red-900/10' : '' }}">
                        <p class="text-xs text-gray-500 mb-1">Jatuh Tempo</p>
                        <p class="font-bold {{ $isExpired ? 'text-red-400' : 'text-white' }}">{{ $pawn->due_date?->isoFormat('D MMM Y') }}</p>
                    </div>
                </div>

                @if($pawn->notes)
                <div class="p-4 rounded-xl" style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08);">
                    <p class="text-xs text-gray-500 mb-1">Catatan</p>
                    <p class="text-sm text-gray-300">{{ $pawn->notes }}</p>
                </div>
                @endif

                @if($pawn->status === 'redeemed')
                <div class="p-4 rounded-xl" style="background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.3);">
                    <p class="text-green-400 font-semibold text-sm">✅ Gadai telah ditebus</p>
                    <p class="text-xs text-gray-400 mt-1">
                        Tanggal tebus: {{ $pawn->redemption_date?->isoFormat('D MMM Y') }} •
                        Jumlah tebus: Rp {{ number_format($pawn->redemption_amount, 0, ',', '.') }}
                    </p>
                </div>
                @endif

                <div class="p-4 rounded-xl text-sm text-gray-400" style="background:rgba(255,255,255,0.02); border:1px solid rgba(255,255,255,0.06);">
                    <p class="font-semibold text-gray-300 mb-2">Cara Penebusan:</p>
                    <ol class="list-decimal list-inside space-y-1 text-xs">
                        <li>Datang ke toko Sinar Baru II pada jam operasional (08.00 - 17.00)</li>
                        <li>Bawa kartu identitas dan nomor kode gadai ini</li>
                        <li>Bayar jumlah tebus (pokok + bunga berjalan)</li>
                        <li>Emas Anda akan diserahkan kembali</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</x-customer-app>
