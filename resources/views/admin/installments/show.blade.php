<x-admin-app>
    <x-slot name="pageTitle">Detail Cicilan Pelanggan</x-slot>
    <x-slot name="breadcrumb">Kelola rencana cicilan, verifikasi bukti transfer, dan pencatatan pembayaran angsuran</x-slot>

    @php
        $paid    = $installmentPlan->paidCount();
        $total   = $installmentPlan->tenure_months;
        $pct     = $total > 0 ? round(($paid / $total) * 100) : 0;
        $product = $installmentPlan->transaction->items->first()->product ?? null;
        $pendingTrxs = $installmentPlan->installmentTransactions->where('status', 'waiting_verification');
    @endphp

    @if(session('success'))
    <div class="mb-6 p-4 rounded-xl text-sm font-bold text-emerald-900 bg-emerald-50 border border-emerald-300 shadow-sm flex items-center gap-2">
        <span>✅</span> <div>{{ session('success') }}</div>
    </div>
    @endif
    @if(session('error'))
    <div class="mb-6 p-4 rounded-xl text-sm font-bold text-red-900 bg-red-50 border border-red-300 shadow-sm flex items-center gap-2">
        <span>❌</span> <div>{{ session('error') }}</div>
    </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-2">
            @if($pendingTrxs->isNotEmpty())
            <span class="px-3 py-1 rounded-full text-xs font-black bg-amber-500 text-white shadow-sm animate-pulse">
                ⚡ {{ $pendingTrxs->count() }} Pembayaran Perlu Verifikasi
            </span>
            @endif
        </div>
        <a href="{{ route('admin.installments.index') }}" class="px-4 py-2 rounded-xl text-sm font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition shadow-sm">
            ← Kembali ke Daftar Cicilan
        </a>
    </div>

    {{-- SEKSI PERSETUJUAN & VERIFIKASI BUKTI BAYAR TRANSFER (PENDING VERIFICATION) --}}
    @if($pendingTrxs->isNotEmpty())
    <div class="mb-8 p-6 rounded-3xl bg-gradient-to-br from-amber-50 to-orange-50 border-2 border-amber-400 shadow-lg">
        <div class="flex items-center justify-between pb-4 mb-4 border-b border-amber-200">
            <div class="flex items-center gap-3">
                <span class="text-3xl">⏳</span>
                <div>
                    <h3 class="text-lg font-black text-amber-950 font-playfair">Persetujuan & Verifikasi Pembayaran Angsuran</h3>
                    <p class="text-xs text-amber-800">Pelanggan telah mengunggah bukti transfer. Silakan periksa mutasi rekening sebelum menyetujui.</p>
                </div>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-amber-200 text-amber-900 border border-amber-400">
                {{ $pendingTrxs->count() }} Permintaan Menunggu
            </span>
        </div>

        <div class="space-y-4">
            @foreach($pendingTrxs as $trx)
            <div class="p-5 rounded-2xl bg-white border border-amber-300 shadow-md">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-mono font-black text-slate-700 bg-slate-100 px-2 py-0.5 rounded">{{ $trx->transaction_code }}</span>
                            <span class="text-xs text-slate-500 font-semibold">• {{ $trx->created_at->isoFormat('D MMM Y, HH:mm') }} WIB</span>
                        </div>
                        <h4 class="text-base font-extrabold text-slate-900">
                            Pembayaran {{ $trx->formattedMonths() }}
                        </h4>
                        <p class="text-xs text-slate-600">
                            Metode: <strong class="uppercase text-slate-800">{{ $trx->payment_method }}</strong>
                            @if($trx->sender_bank) • Pengirim: <strong>{{ $trx->sender_bank }} ({{ $trx->sender_name ?? '-' }})</strong> @endif
                            @if($trx->notes) • Catatan: <em>"{{ $trx->notes }}"</em> @endif
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <div class="text-right pr-2">
                            <p class="text-[10px] text-slate-500 font-bold uppercase">Nominal Ditransfer</p>
                            <p class="text-xl font-black text-[#085C54]">Rp {{ number_format($trx->amount, 0, ',', '.') }}</p>
                        </div>

                        @if($trx->proof_image)
                        <button type="button"
                                onclick="openProofModal('{{ asset('storage/' . $trx->proof_image) }}', '{{ $trx->transaction_code }}')"
                                class="px-3.5 py-2 rounded-xl text-xs font-bold text-blue-800 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition flex items-center gap-1.5 shadow-sm">
                            <span>🖼️</span> <span>Lihat Bukti Transfer</span>
                        </button>
                        @endif

                        {{-- Tombol Setujui Verifikasi --}}
                        <form action="{{ route('admin.installments.transactions.verify', $trx) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin telah mengecek mutasi dan ingin menyetujui pembayaran cicilan ini?');">
                            @csrf
                            <button type="submit" class="px-4 py-2 rounded-xl text-xs font-extrabold text-white bg-emerald-600 hover:bg-emerald-700 transition shadow-md flex items-center gap-1.5">
                                <span>✅</span> <span>Setujui & Tandai Lunas</span>
                            </button>
                        </form>

                        {{-- Tombol Tolak Verifikasi --}}
                        <button type="button"
                                onclick="openRejectModal('{{ route('admin.installments.transactions.reject', $trx) }}', '{{ $trx->transaction_code }}', '{{ $trx->formattedMonths() }}')"
                                class="px-3.5 py-2 rounded-xl text-xs font-bold text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 transition flex items-center gap-1 shadow-sm">
                            <span>✕</span> <span>Tolak</span>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Main Detail Card --}}
            <div class="glass rounded-3xl overflow-hidden bg-white border border-[#e8e3d5] shadow-lg">
                <div class="p-6 bg-[#F4EDD9]/60 border-b border-[#e8e3d5]">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full bg-[#085C54]/10 text-[#085C54] uppercase">
                                {{ $installmentPlan->transaction->transaction_code }}
                            </span>
                            <h2 class="text-2xl font-bold font-playfair text-slate-900 mt-1">{{ $product->name ?? 'Cicilan Emas' }}</h2>
                            <p class="text-sm text-slate-600 font-semibold mt-0.5">{{ $product->gold_purity ?? '24K' }} • {{ number_format($product->weight_gram ?? 0, 3) }} gram</p>
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
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach([
                            ['Angsuran / Bulan', 'Rp '.number_format($installmentPlan->monthly_amount, 0, ',', '.')],
                            ['Total Nilai Emas', 'Rp '.number_format($installmentPlan->total_installment, 0, ',', '.')],
                            ['Tenor', $installmentPlan->tenure_months.' Bulan'],
                            ['Mulai', $installmentPlan->start_date?->isoFormat('D MMM Y') ?? '-'],
                            ['Selesai', $installmentPlan->end_date?->isoFormat('D MMM Y') ?? '-'],
                        ] as [$label, $val])
                        <div class="glass p-3.5 rounded-xl bg-white border border-[#e8e3d5] shadow-sm">
                            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mb-0.5">{{ $label }}</p>
                            <p class="font-extrabold text-slate-900 text-sm">{{ $val }}</p>
                        </div>
                        @endforeach
                    </div>

                    {{-- Status Reservasi Pengambilan Emas Fisik --}}
                    @if($installmentPlan->pickupReservation)
                    <div class="p-5 rounded-2xl bg-gradient-to-br from-amber-50 to-emerald-50 border-2 border-emerald-400 shadow-md">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-3 pb-3 border-b border-emerald-200">
                            <div>
                                <span class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-emerald-600 text-white shadow-sm">
                                    📦 Jadwal Pengambilan Emas Fisik
                                </span>
                                <h4 class="font-bold text-slate-900 text-base mt-2">Kode Reservasi: <span class="font-mono text-[#085C54]">{{ $installmentPlan->pickupReservation->reservation_code }}</span></h4>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-white text-emerald-900 border border-emerald-300">
                                {{ ucfirst($installmentPlan->pickupReservation->status) }}
                            </span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-slate-700">
                            <div>
                                <p class="text-slate-500 font-bold uppercase">Tanggal Kunjungan</p>
                                <p class="font-bold text-sm text-slate-900 mt-0.5">{{ \Carbon\Carbon::parse($installmentPlan->pickupReservation->preferred_date)->isoFormat('dddd, D MMMM Y') }}</p>
                            </div>
                            <div>
                                <p class="text-slate-500 font-bold uppercase">Jam Kunjungan</p>
                                <p class="font-bold text-sm text-slate-900 mt-0.5">{{ $installmentPlan->pickupReservation->preferred_time ?? '09:00 - 17:00' }} WIB</p>
                            </div>
                            @if($installmentPlan->pickupReservation->notes)
                            <div class="sm:col-span-2">
                                <p class="text-slate-500 font-bold uppercase">Catatan Pelanggan</p>
                                <p class="font-semibold text-slate-800 italic">"{{ $installmentPlan->pickupReservation->notes }}"</p>
                            </div>
                            @endif
                        </div>

                        @if(in_array($installmentPlan->pickupReservation->status, ['pending', 'confirmed']))
                        <div class="mt-4 pt-3 border-t border-emerald-200 flex justify-end">
                            <form action="{{ route('admin.reservations.complete', $installmentPlan->pickupReservation) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin serah terima perhiasan emas fisik telah selesai dilakukan?');">
                                @csrf
                                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-extrabold text-[#042623] gold-gradient border border-[#C6A443] shadow-md hover:brightness-110 transition flex items-center gap-1.5">
                                    <span>🎉</span> <span>Tandai Selesai (Serah Terima Emas Fisik)</span>
                                </button>
                            </form>
                        </div>
                        @elseif($installmentPlan->pickupReservation->status === 'completed')
                        <div class="mt-3 text-xs font-bold text-emerald-800 flex items-center gap-1">
                            <span>✅</span> <span>Perhiasan emas fisik telah selesai diserahkan ke pelanggan.</span>
                        </div>
                        @endif
                    </div>
                    @endif

                    {{-- Jadwal Angsuran Per Bulan --}}
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                                <span>📅</span> Jadwal Angsuran Bulanan
                            </h3>
                            @if($unpaidPayments->isNotEmpty() && $installmentPlan->status === 'active')
                            <button type="button"
                                    onclick="openBatchPaymentModal()"
                                    class="px-3 py-1.5 rounded-xl text-xs font-extrabold text-[#042623] gold-gradient border border-[#C6A443] hover:brightness-110 transition shadow-sm">
                                ➕ Catat Bayar Sekaligus (Kasir)
                            </button>
                            @endif
                        </div>

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
                                    <tr class="{{ $p->isWaitingVerification() ? 'bg-amber-50/50' : '' }}">
                                        <td class="py-3.5 px-3 font-bold text-slate-900">Bulan ke-{{ $p->installment_number ?? '-' }}</td>
                                        <td class="py-3.5 px-3 text-slate-600 font-medium text-xs">{{ $p->due_date?->isoFormat('D MMM Y') ?? '-' }}</td>
                                        <td class="py-3.5 px-3 text-slate-700 font-medium text-xs">{{ $p->paid_date?->isoFormat('D MMM Y') ?? '-' }}</td>
                                        <td class="py-3.5 px-3 text-right font-extrabold text-slate-900">Rp {{ number_format($p->amount_due, 0, ',', '.') }}</td>
                                        <td class="py-3.5 px-3 text-center">
                                            @if($p->isPaid())
                                            <span class="text-xs px-2.5 py-0.5 rounded-full font-bold bg-emerald-100 text-emerald-900 border border-emerald-300">
                                                Lunas
                                            </span>
                                            @elseif($p->isWaitingVerification())
                                            <span class="text-xs px-2.5 py-0.5 rounded-full font-bold bg-amber-100 text-amber-900 border border-amber-300 animate-pulse">
                                                Menunggu Verifikasi
                                            </span>
                                            @else
                                            <span class="text-xs px-2.5 py-0.5 rounded-full font-bold bg-slate-100 text-slate-800 border border-slate-300">
                                                Belum Lunas
                                            </span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-3 text-center">
                                            @if($p->status !== 'paid' && $installmentPlan->status === 'active')
                                            <button onclick="openPaymentModal({{ $p->id }}, {{ $p->installment_number }}, {{ $p->amount_due }})"
                                                    class="px-2.5 py-1 rounded-lg text-xs bg-[#085C54] hover:bg-[#063e39] text-white font-bold transition shadow-sm">
                                                Catat
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

            {{-- HISTORI SELURUH TRANSAKSI PEMBAYARAN CICILAN --}}
            <div class="glass rounded-3xl p-6 bg-white border border-[#e8e3d5] shadow-lg">
                <div class="flex justify-between items-center mb-4 pb-2 border-b border-slate-200">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                            <span>🧾</span> Histori Transaksi Pembayaran
                        </h3>
                        <p class="text-xs text-slate-500">Semua setoran angsuran baik via transfer pelanggan maupun langsung di kasir</p>
                    </div>
                    <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-0.5 rounded-full border border-slate-200">
                        {{ $installmentPlan->installmentTransactions->count() }} Data
                    </span>
                </div>

                @if($installmentPlan->installmentTransactions->isEmpty())
                <p class="text-slate-400 text-xs text-center py-6">Belum ada transaksi pembayaran cicilan yang tercatat.</p>
                @else
                <div class="space-y-3">
                    @foreach($installmentPlan->installmentTransactions as $trx)
                    <div class="p-4 rounded-2xl border text-xs transition shadow-sm
                        {{ $trx->isVerified() ? 'bg-emerald-50/40 border-emerald-200' : ($trx->isRejected() ? 'bg-red-50/40 border-red-200' : 'bg-amber-50/40 border-amber-200') }}">
                        <div class="flex justify-between items-start pb-2 border-b border-slate-200">
                            <div>
                                <span class="font-mono font-extrabold text-slate-800">{{ $trx->transaction_code }}</span>
                                <p class="font-bold text-slate-900 text-sm mt-0.5">{{ $trx->formattedMonths() }}</p>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider border shadow-sm
                                {{ $trx->isVerified() ? 'bg-emerald-100 text-emerald-900 border-emerald-300' : ($trx->isRejected() ? 'bg-red-100 text-red-900 border-red-300' : 'bg-amber-100 text-amber-900 border-amber-300') }}">
                                {{ $trx->isVerified() ? 'Terverifikasi' : ($trx->isRejected() ? 'Ditolak' : 'Menunggu') }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 py-2">
                            <div>
                                <span class="text-slate-500 text-[10px] uppercase">Waktu</span>
                                <p class="font-semibold text-slate-900">{{ $trx->created_at->isoFormat('D MMM Y, HH:mm') }}</p>
                            </div>
                            <div>
                                <span class="text-slate-500 text-[10px] uppercase">Nominal</span>
                                <p class="font-black text-[#085C54]">Rp {{ number_format($trx->amount, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <span class="text-slate-500 text-[10px] uppercase">Metode</span>
                                <p class="font-semibold text-slate-900 uppercase">{{ $trx->payment_method }}</p>
                            </div>
                            <div>
                                <span class="text-slate-500 text-[10px] uppercase">Bukti</span>
                                @if($trx->proof_image)
                                <button type="button"
                                        onclick="openProofModal('{{ asset('storage/' . $trx->proof_image) }}', '{{ $trx->transaction_code }}')"
                                        class="text-blue-700 font-bold hover:underline block">
                                    Lihat Foto
                                </button>
                                @else
                                <span class="text-slate-400 italic">Kasir Toko</span>
                                @endif
                            </div>
                        </div>

                        @if($trx->isRejected() && $trx->rejection_reason)
                        <div class="mt-1 p-2 rounded-lg bg-red-100 text-red-900">
                            <strong>Alasan Tolak:</strong> {{ $trx->rejection_reason }}
                        </div>
                        @endif

                        @if($trx->isVerified())
                        <div class="pt-2 border-t border-slate-200 text-slate-500 text-[11px] flex justify-between">
                            <span>Diverifikasi oleh: <strong>{{ $trx->verifier->name ?? 'Admin' }}</strong></span>
                            <span>{{ $trx->verified_at?->isoFormat('D MMM Y, HH:mm') }}</span>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- Sidebar Pelanggan & Aksi --}}
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

    {{-- MODAL CATAT PEMBAYARAN ANGSURAN TUNGGAL (KASIR) --}}
    <div id="paymentModal" class="fixed inset-0 z-50 flex items-center justify-center hidden" style="background:rgba(4,38,35,0.6); backdrop-filter:blur(4px);">
        <div class="w-full max-w-md p-6 rounded-3xl bg-white border border-[#e8e3d5] shadow-2xl">
            <div class="flex justify-between items-center mb-4 pb-3 border-b border-slate-200">
                <h3 class="text-base font-bold text-slate-900">📝 Catat Pembayaran Angsuran (Kasir)</h3>
                <button onclick="closePaymentModal()" class="text-slate-400 hover:text-slate-700 text-xl font-bold">&times;</button>
            </div>
            <form id="paymentForm" method="POST" action="">
                @csrf
                <input type="hidden" name="payment_id" id="modal_payment_id">
                
                <p class="text-xs text-slate-600 font-medium mb-4">Mencatat pembayaran untuk angsuran <strong id="modal_installment_number" class="text-slate-900 font-bold"></strong>.</p>

                <div class="mb-4">
                    <label class="input-label">Jumlah Bayar (Rp) <span class="text-red-600">*</span></label>
                    <input type="text" inputmode="numeric" name="amount_paid" id="modal_amount_paid" class="input-field format-rupiah font-extrabold text-slate-900" required>
                </div>

                <div class="mb-4">
                    <label class="input-label">Metode Pembayaran <span class="text-red-600">*</span></label>
                    <select name="payment_method" class="input-field cursor-pointer font-semibold" required>
                        @foreach($paymentMethods as $pm)
                        <option value="{{ $pm->code }}">{{ $pm->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-6">
                    <label class="input-label">Catatan Kasir</label>
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

    {{-- MODAL CATAT PEMBAYARAN SEKALIGUS BEBERAPA BULAN (KASIR) --}}
    <div id="batchPaymentModal" class="fixed inset-0 z-50 flex items-center justify-center hidden" style="background:rgba(4,38,35,0.6); backdrop-filter:blur(4px);">
        <div class="w-full max-w-md p-6 rounded-3xl bg-white border border-[#e8e3d5] shadow-2xl">
            <div class="flex justify-between items-center mb-4 pb-3 border-b border-slate-200">
                <h3 class="text-base font-bold text-slate-900">➕ Catat Bayar Sekaligus Beberapa Bulan</h3>
                <button onclick="closeBatchPaymentModal()" class="text-slate-400 hover:text-slate-700 text-xl font-bold">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.installments.pay-batch', $installmentPlan) }}">
                @csrf
                <div class="mb-4">
                    <label class="input-label">Jumlah Bulan Yang Dibayar <span class="text-red-600">*</span></label>
                    <select name="month_count" id="batch_month_count" class="input-field font-bold cursor-pointer" onchange="updateBatchTotal()" required>
                        @for($i = 1; $i <= $unpaidPayments->count(); $i++)
                        <option value="{{ $i }}" {{ $i == 1 ? 'selected' : '' }}>
                            {{ $i }} Bulan Sekaligus (Mulai Bulan ke-{{ $unpaidPayments->first()?->installment_number }})
                        </option>
                        @endfor
                    </select>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 mb-4">
                    <p class="text-xs text-slate-500 font-bold uppercase">Total Yang Harus Diterima</p>
                    <p class="text-xl font-black text-[#085C54]" id="batch-total-display">Rp 0</p>
                </div>

                <div class="mb-4">
                    <label class="input-label">Metode Pembayaran <span class="text-red-600">*</span></label>
                    <select name="payment_method" class="input-field cursor-pointer font-semibold" required>
                        @foreach($paymentMethods as $pm)
                        <option value="{{ $pm->code }}">{{ $pm->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-6">
                    <label class="input-label">Catatan Kasir</label>
                    <textarea name="notes" rows="2" class="input-field text-sm" placeholder="cth: Pelanggan membayar langsung tunai di kasir..."></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeBatchPaymentModal()" class="flex-1 py-2.5 rounded-xl text-sm font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl text-sm font-extrabold text-[#042623] gold-gradient border border-[#C6A443] shadow-md transition hover:scale-105">
                        Simpan Bayar Sekaligus
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL TOLAK PEMBAYARAN --}}
    <div id="rejectModal" class="fixed inset-0 z-50 flex items-center justify-center hidden" style="background:rgba(4,38,35,0.6); backdrop-filter:blur(4px);">
        <div class="w-full max-w-md p-6 rounded-3xl bg-white border border-[#e8e3d5] shadow-2xl">
            <div class="flex justify-between items-center mb-4 pb-3 border-b border-slate-200">
                <h3 class="text-base font-bold text-red-900">❌ Tolak Pembayaran Cicilan</h3>
                <button onclick="closeRejectModal()" class="text-slate-400 hover:text-slate-700 text-xl font-bold">&times;</button>
            </div>
            <form id="rejectForm" method="POST" action="">
                @csrf
                <p class="text-xs text-slate-600 font-medium mb-3">
                    Anda akan menolak pembayaran <strong id="reject_code"></strong> (<span id="reject_months"></span>). Status tagihan angsuran akan dikembalikan ke pending.
                </p>

                <div class="mb-4">
                    <label class="input-label">Alasan Penolakan <span class="text-red-600">*</span></label>
                    <textarea name="rejection_reason" rows="3" class="input-field text-sm" required placeholder="cth: Bukti transfer buram/tidak terbaca, nominal tidak sesuai, atau dana belum masuk mutasi..."></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeRejectModal()" class="flex-1 py-2.5 rounded-xl text-sm font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl text-sm font-extrabold text-white bg-red-600 hover:bg-red-700 shadow-md transition">
                        Konfirmasi Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL PREVIEW FOTO BUKTI TRANSFER --}}
    <div id="proofModal" class="fixed inset-0 z-50 flex items-center justify-center hidden" style="background:rgba(4,38,35,0.7); backdrop-filter:blur(6px);">
        <div class="w-full max-w-lg p-5 rounded-3xl bg-white border border-[#e8e3d5] shadow-2xl relative mx-4">
            <div class="flex justify-between items-center mb-3 pb-2 border-b border-slate-200">
                <h4 class="font-bold text-slate-900 text-sm" id="modal-proof-title">Foto Bukti Transfer</h4>
                <button type="button" onclick="closeProofModal()" class="text-slate-400 hover:text-slate-800 text-2xl font-bold leading-none">&times;</button>
            </div>
            <div class="max-h-[70vh] overflow-auto flex items-center justify-center bg-slate-100 rounded-2xl p-2">
                <img id="modal-proof-img" src="#" alt="Bukti Transfer" class="max-w-full rounded-xl object-contain shadow-sm">
            </div>
            <div class="mt-3 flex justify-end">
                <button type="button" onclick="closeProofModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        const monthlyAmount = {{ (float) $installmentPlan->monthly_amount }};

        function openPaymentModal(id, number, amount) {
            const modal = document.getElementById('paymentModal');
            const form = document.getElementById('paymentForm');
            const url = `{{ route('admin.installments.payments.pay', [$installmentPlan->id, ':paymentId']) }}`.replace(':paymentId', id);
            
            form.action = url;
            document.getElementById('modal_payment_id').value = id;
            document.getElementById('modal_installment_number').textContent = 'Bulan ke-' + number;
            
            const amountInput = document.getElementById('modal_amount_paid');
            amountInput.value = window.formatRupiah ? window.formatRupiah(amount) : amount;

            modal.classList.remove('hidden');
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
        }

        function openBatchPaymentModal() {
            updateBatchTotal();
            document.getElementById('batchPaymentModal').classList.remove('hidden');
        }

        function closeBatchPaymentModal() {
            document.getElementById('batchPaymentModal').classList.add('hidden');
        }

        function updateBatchTotal() {
            const count = parseInt(document.getElementById('batch_month_count')?.value || 1);
            const total = count * monthlyAmount;
            const display = document.getElementById('batch-total-display');
            if (display) {
                display.textContent = 'Rp ' + Math.round(total).toLocaleString('id-ID');
            }
        }

        function openRejectModal(actionUrl, code, months) {
            document.getElementById('rejectForm').action = actionUrl;
            document.getElementById('reject_code').textContent = code;
            document.getElementById('reject_months').textContent = months;
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }

        function openProofModal(imgUrl, code) {
            document.getElementById('modal-proof-img').src = imgUrl;
            document.getElementById('modal-proof-title').textContent = 'Bukti Transfer: ' + code;
            document.getElementById('proofModal').classList.remove('hidden');
        }

        function closeProofModal() {
            document.getElementById('proofModal').classList.add('hidden');
        }
    </script>
</x-admin-app>
