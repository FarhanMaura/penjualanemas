<x-customer-app>
    <x-slot name="pageTitle">Gadai Saya</x-slot>
    <x-slot name="breadcrumb">Status dan rincian gadai emas Anda di Sinar Baru II</x-slot>

    {{-- Summary Cards --}}
    <div class="grid md:grid-cols-3 gap-6 mb-8">
        <div class="glass rounded-2xl p-6" style="background:rgba(245,158,11,0.05); border-color:rgba(245,158,11,0.2);">
            <div class="flex items-center gap-4">
                <span class="text-3xl">🏦</span>
                <div>
                    <p class="text-xs text-gray-400">Gadai Aktif</p>
                    <p class="text-2xl font-bold text-yellow-400">{{ $summary['active'] }}</p>
                </div>
            </div>
        </div>
        <div class="glass rounded-2xl p-6" style="background:rgba(16,185,129,0.05); border-color:rgba(16,185,129,0.2);">
            <div class="flex items-center gap-4">
                <span class="text-3xl">✅</span>
                <div>
                    <p class="text-xs text-gray-400">Sudah Ditebus</p>
                    <p class="text-2xl font-bold text-green-400">{{ $summary['redeemed'] }}</p>
                </div>
            </div>
        </div>
        <div class="glass rounded-2xl p-6" style="background:rgba(239,68,68,0.05); border-color:rgba(239,68,68,0.2);">
            <div class="flex items-center gap-4">
                <span class="text-3xl">💰</span>
                <div>
                    <p class="text-xs text-gray-400">Total Pinjaman Aktif</p>
                    <p class="text-xl font-bold text-red-400">Rp {{ number_format($summary['total_loan'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    @if($pawns->isEmpty())
    <div class="glass rounded-3xl p-12 text-center">
        <span class="text-6xl">📭</span>
        <p class="text-gray-400 text-lg mt-4 font-semibold">Belum Ada Data Gadai</p>
        <p class="text-gray-500 text-sm mt-2">Data gadai akan muncul setelah Admin mencatat transaksi gadai untuk Anda.</p>
        <a href="{{ route('customer.reservations.create') }}" class="mt-6 inline-block text-yellow-400 hover:underline">
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
                'active'   => ['label'=>'Aktif', 'class'=>'bg-yellow-900/40 text-yellow-400 border-yellow-700/50'],
                'redeemed' => ['label'=>'Ditebus', 'class'=>'bg-green-900/40 text-green-400 border-green-700/50'],
                'forfeited'=> ['label'=>'Hangus', 'class'=>'bg-red-900/40 text-red-400 border-red-700/50'],
            ][$pawn->status] ?? ['label'=>$pawn->status, 'class'=>'bg-gray-900/40 text-gray-400'];
        @endphp

        <div class="glass rounded-2xl overflow-hidden {{ $isExpired ? 'ring-2 ring-red-500/30' : ($isWarning ? 'ring-2 ring-yellow-500/30' : '') }}"
             style="border-color:rgba(255,255,255,0.06);">

            <div class="p-5" style="background:rgba(0,0,0,0.2); border-bottom:1px solid rgba(255,255,255,0.06);">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-mono text-xs text-gray-400">{{ $pawn->pawn_code }}</p>
                        <h3 class="font-bold text-white text-lg mt-1">{{ $pawn->gold_description }}</h3>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full border {{ $statusStyles['class'] }}">{{ $statusStyles['label'] }}</span>
                </div>
            </div>

            <div class="p-5 space-y-3">
                <div class="grid grid-cols-2 gap-3">
                    <div class="glass p-3 rounded-xl">
                        <p class="text-xs text-gray-500 mb-1">Nilai Emas</p>
                        <p class="font-bold text-white text-sm">Rp {{ number_format($pawn->appraised_value, 0, ',', '.') }}</p>
                    </div>
                    <div class="glass p-3 rounded-xl" style="background:rgba(245,158,11,0.05);">
                        <p class="text-xs text-gray-500 mb-1">Jumlah Pinjaman</p>
                        <p class="font-bold text-yellow-400 text-sm">Rp {{ number_format($pawn->loan_amount, 0, ',', '.') }}</p>
                    </div>
                    <div class="glass p-3 rounded-xl">
                        <p class="text-xs text-gray-500 mb-1">Bunga/Bulan</p>
                        <p class="font-bold text-white text-sm">{{ $pawn->interest_rate }}%</p>
                    </div>
                    <div class="glass p-3 rounded-xl {{ $isExpired ? 'bg-red-900/20' : ($isWarning ? 'bg-yellow-900/20' : '') }}">
                        <p class="text-xs text-gray-500 mb-1">Jatuh Tempo</p>
                        <p class="font-bold text-sm {{ $isExpired ? 'text-red-400' : ($isWarning ? 'text-yellow-400' : 'text-white') }}">
                            {{ $dueDate?->isoFormat('D MMM Y') }}
                        </p>
                    </div>
                </div>

                @if($pawn->status === 'active')
                <div class="p-3 rounded-xl {{ $isExpired ? 'bg-red-900/20 border border-red-700/30' : ($isWarning ? 'bg-yellow-900/20 border border-yellow-700/30' : 'glass') }}">
                    @if($isExpired)
                        <p class="text-xs text-red-400 font-semibold">⚠️ Gadai ini sudah jatuh tempo! Segera hubungi toko.</p>
                    @elseif($isWarning)
                        <p class="text-xs text-yellow-400 font-semibold">⏰ Sisa {{ $daysLeft }} hari lagi! Jatuh tempo segera.</p>
                    @else
                        <p class="text-xs text-gray-400">Sisa waktu: <span class="text-white font-semibold">{{ $daysLeft }} hari</span></p>
                    @endif
                </div>
                @endif

                @if($pawn->status === 'active')
                <div class="flex gap-2 pt-1">
                    <a href="{{ route('customer.pawns.show', $pawn) }}"
                       class="flex-1 text-center text-xs font-semibold py-2 rounded-lg text-white transition"
                       style="background:linear-gradient(135deg,#f59e0b,#d97706);">
                        Lihat Detail & Tebus
                    </a>
                </div>
                @else
                <a href="{{ route('customer.pawns.show', $pawn) }}"
                   class="block w-full text-center text-xs text-gray-400 py-2 rounded-lg glass hover:bg-white/10 transition">
                    Lihat Detail
                </a>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    {{ $pawns->links() }}
    @endif

    {{-- Info Box --}}
    <div class="glass rounded-2xl p-6 mt-8" style="background:rgba(245,158,11,0.04); border-color:rgba(245,158,11,0.15);">
        <h3 class="font-semibold text-yellow-400 mb-3">ℹ️ Tentang Gadai Emas</h3>
        <div class="grid md:grid-cols-3 gap-4 text-sm text-gray-400">
            <div>
                <p class="font-semibold text-white mb-1">📌 Penebusan</p>
                <p>Datang ke toko sebelum jatuh tempo dengan membawa bukti gadai. Bayar pokok + bunga berjalan.</p>
            </div>
            <div>
                <p class="font-semibold text-white mb-1">⏰ Perpanjangan</p>
                <p>Gadai dapat diperpanjang sebelum jatuh tempo. Hubungi toko untuk negosiasi perpanjangan.</p>
            </div>
            <div>
                <p class="font-semibold text-white mb-1">📞 Kontak Toko</p>
                <p>Hubungi kami di jam operasional toko (08.00–17.00) untuk informasi lebih lanjut.</p>
            </div>
        </div>
    </div>
</x-customer-app>
