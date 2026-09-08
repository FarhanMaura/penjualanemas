<x-customer-app>
    <x-slot name="pageTitle">Detail Gadai & Pembayaran</x-slot>
    <x-slot name="breadcrumb">Rincian gadai, angsuran, dan tebusan emas #{{ $pawn->pawn_code }}</x-slot>

    @php
        $daysLeft  = now()->diffInDays($pawn->due_date, false);
        $isExpired = $daysLeft < 0 && $pawn->status === 'active';
        $plan = $installmentPlan;
        $paidMonths = $plan ? $plan->paidCount() : 0;
        $totalMonths = $plan ? $plan->tenure_months : 1;
        $unpaidAmount = $plan ? $plan->payments()->where('status', '!=', 'paid')->sum('amount_due') : $pawn->loan_amount;
        $pct = round(($paidMonths / max(1, $totalMonths)) * 100);
        $monthlyAmount = $plan ? $plan->monthly_amount : $pawn->loan_amount;
    @endphp

    <div class="max-w-4xl mx-auto space-y-6">
        <a href="{{ route('customer.pawns.index') }}" class="text-xs font-bold text-[#085C54] hover:underline inline-flex items-center gap-1">
            ← Kembali ke Daftar Gadai
        </a>

        @if(session('success'))
        <div class="p-4 rounded-2xl text-sm font-bold text-emerald-950 bg-emerald-50 border border-emerald-300 shadow-sm flex items-center gap-2">
            <span>✅</span> <span>{{ session('success') }}</span>
        </div>
        @endif
        @if(session('error'))
        <div class="p-4 rounded-2xl text-sm font-bold text-red-950 bg-red-50 border border-red-300 shadow-sm flex items-center gap-2">
            <span>❌</span> <span>{{ session('error') }}</span>
        </div>
        @endif
        @if($errors->any())
        <div class="p-4 rounded-2xl text-sm font-bold text-red-950 bg-red-50 border border-red-300 shadow-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
        @endif

        {{-- Card Detail Gadai --}}
        <div class="glass rounded-3xl overflow-hidden bg-white border border-[#e8e3d5] shadow-lg {{ $isExpired ? 'ring-2 ring-red-500' : '' }}">
            <div class="p-6 bg-[#F4EDD9]/60 border-b border-[#e8e3d5]">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-mono text-xs font-bold text-[#085C54]">{{ $pawn->pawn_code }}</p>
                        <h2 class="text-2xl font-bold font-playfair text-slate-900 mt-1">{{ $pawn->gold_description }}</h2>
                        <p class="text-sm text-slate-600 font-semibold mt-1">{{ $pawn->gold_purity }} • {{ number_format($pawn->weight_gram, 2) }} gram</p>
                    </div>
                    <span class="px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider border shadow-sm
                        {{ $pawn->status === 'active' ? 'bg-amber-100 text-amber-900 border-amber-300' : ($pawn->status === 'redeemed' ? 'bg-emerald-100 text-emerald-900 border-emerald-300' : 'bg-red-100 text-red-900 border-red-300') }}">
                        {{ $pawn->status === 'redeemed' ? '🎉 Lunas Ditebus' : ($pawn->status === 'active' ? '⚡ Aktif' : ucfirst($pawn->status)) }}
                    </span>
                </div>
            </div>

            <div class="p-6 space-y-6">
                {{-- Alert jika jatuh tempo --}}
                @if($isExpired)
                <div class="p-4 rounded-2xl bg-red-50 border border-red-300 text-red-950">
                    <p class="font-bold text-sm">⚠️ Gadai ini telah melewati jatuh tempo {{ abs($daysLeft) }} hari!</p>
                    <p class="text-xs font-medium mt-1">Segera lakukan pembayaran tebusan atau hubungi toko agar barang gadai Anda tetap aman.</p>
                </div>
                @elseif($pawn->status === 'active' && $daysLeft <= 7)
                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-300 text-amber-950">
                    <p class="font-bold text-sm">⏰ Waktu tersisa {{ $daysLeft }} hari lagi menuju jatuh tempo!</p>
                    <p class="text-xs font-medium mt-1">Anda dapat mencicil angsuran bulanan atau langsung melunasi tebusan di bawah.</p>
                </div>
                @endif

                {{-- Status Lunas Ditebus Banner --}}
                @if($pawn->status === 'redeemed')
                <div class="p-6 rounded-3xl bg-emerald-50 border-2 border-emerald-300 text-emerald-950 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <h4 class="font-black text-lg text-emerald-900 flex items-center gap-2">
                            <span>🎉</span> <span>Gadai Telah Lunas Ditebus!</span>
                        </h4>
                        <p class="text-xs font-medium text-emerald-800 mt-1">
                            Ditebus pada <strong>{{ $pawn->redemption_date?->isoFormat('dddd, D MMMM Y') }}</strong> dengan total tebusan <strong>Rp {{ number_format($pawn->redemption_amount, 0, ',', '.') }}</strong>.
                        </p>
                        <p class="text-xs text-slate-700 font-semibold mt-2">
                            💍 Silakan ambil kembali perhiasan emas Anda di Toko Emas Sinar Baru II dengan menunjukkan kode gadai ini.
                        </p>
                    </div>
                    <a href="{{ route('customer.pawns.index') }}" class="px-5 py-2.5 rounded-xl font-extrabold text-xs text-[#042623] gold-gradient border border-[#C6A443] shadow-md hover:brightness-110 transition shrink-0">
                        Lihat Gadai Lainnya →
                    </a>
                </div>
                @endif

                {{-- Ringkasan Pinjaman --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="glass p-3.5 rounded-2xl bg-white border border-[#e8e3d5]">
                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mb-0.5">Nilai Taksiran</p>
                        <p class="font-extrabold text-slate-900 text-sm sm:text-base">Rp {{ number_format($pawn->appraised_value, 0, ',', '.') }}</p>
                    </div>
                    <div class="glass p-3.5 rounded-2xl bg-amber-50/70 border border-amber-200">
                        <p class="text-[10px] text-amber-900 font-bold uppercase tracking-wider mb-0.5">Total Pinjaman</p>
                        <p class="font-extrabold text-[#C6A443] text-sm sm:text-base">Rp {{ number_format($pawn->loan_amount, 0, ',', '.') }}</p>
                    </div>
                    <div class="glass p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-200">
                        <p class="text-[10px] text-emerald-900 font-bold uppercase tracking-wider mb-0.5">Sisa Belum Ditebus</p>
                        <p class="font-extrabold text-emerald-800 text-sm sm:text-base">Rp {{ number_format($unpaidAmount, 0, ',', '.') }}</p>
                    </div>
                    <div class="glass p-3.5 rounded-2xl bg-white border border-[#e8e3d5]">
                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mb-0.5">Jatuh Tempo</p>
                        <p class="font-extrabold text-sm sm:text-base {{ $isExpired ? 'text-red-700' : 'text-slate-900' }}">{{ $pawn->due_date?->isoFormat('D MMM Y') }}</p>
                    </div>
                </div>

                {{-- Progres Angsuran Gadai --}}
                @if($plan)
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                    <div class="flex justify-between items-center text-xs font-bold">
                        <span class="uppercase tracking-wider text-slate-600">Progres Angsuran Pelunasan</span>
                        <span class="text-[#085C54] font-extrabold">{{ $paidMonths }} / {{ $totalMonths }} Bulan ({{ $pct }}%)</span>
                    </div>
                    <div class="h-3.5 rounded-full overflow-hidden bg-slate-200 border border-slate-300 p-0.5">
                        <div class="h-full rounded-full transition-all duration-500" style="width:{{ $pct }}%; background:linear-gradient(90deg,#085C54,#C6A443);"></div>
                    </div>
                    <div class="flex justify-between text-xs text-slate-500 font-semibold">
                        <span>Angsuran: <strong>Rp {{ number_format($plan->monthly_amount, 0, ',', '.') }} / bulan</strong></span>
                        <span>Tempo: <strong>{{ $plan->tenure_months }} Bulan</strong></span>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- FITUR PEMBAYARAN & TEBUS LANGSUNG --}}
        @if($pawn->status === 'active' && $unpaidPayments->isNotEmpty())
        <div class="glass rounded-3xl overflow-hidden bg-white border-2 border-[#C6A443]/60 shadow-xl" id="payment-box">
            <div class="p-6 bg-gradient-to-br from-[#085C54] to-[#042623] text-white">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                    <div>
                        <h3 class="text-lg font-bold font-playfair flex items-center gap-2 text-[#F4EDD9]">
                            <span>💳</span> Pembayaran Angsuran & Tebus Gadai
                        </h3>
                        <p class="text-xs text-slate-300 mt-1">Pilih metode angsuran bulanan atau lakukan pelunasan langsung untuk menebus emas Anda.</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-[#C6A443]/30 border border-[#C6A443] text-[#F4EDD9]">
                        Online-to-Offline (O2O)
                    </span>
                </div>
            </div>

            <form action="{{ route('customer.pawns.pay', $pawn) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6" onsubmit="if(this.dataset.submitted) return false; this.dataset.submitted = true;">
                @csrf

                {{-- 1. Pilihan Skema Pembayaran --}}
                <div>
                    <label class="input-label text-sm font-bold text-slate-900 mb-3">1. Pilih Skema Pembayaran <span class="text-red-600">*</span></label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        {{-- Opsi A: 1 Bulan --}}
                        <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all hover:shadow-md bg-white border-slate-200" id="card_opt_next">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-extrabold text-slate-900">Angsuran Bulan Ini</span>
                                <input type="radio" name="payment_option" value="next_month" checked class="text-[#085C54] focus:ring-[#085C54]" onchange="onPaymentOptionChange('next_month')">
                            </div>
                            <p class="text-xs text-slate-500 font-medium">Bayar 1 bulan angsuran berjalan</p>
                            <p class="text-base font-black text-[#085C54] mt-2">Rp {{ number_format($monthlyAmount, 0, ',', '.') }}</p>
                        </label>

                        {{-- Opsi B: Custom Bulan --}}
                        <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all hover:shadow-md bg-white border-slate-200" id="card_opt_custom">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-extrabold text-slate-900">Pilih Beberapa Bulan</span>
                                <input type="radio" name="payment_option" value="custom_months" class="text-[#085C54] focus:ring-[#085C54]" onchange="onPaymentOptionChange('custom_months')">
                            </div>
                            <p class="text-xs text-slate-500 font-medium">Pilih 2 atau lebih bulan sekaligus</p>
                            <p class="text-base font-black text-amber-800 mt-2">Kustomisasi</p>
                        </label>

                        {{-- Opsi C: LANGSUNG TEBUS (Pelunasan Total) --}}
                        <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all hover:shadow-lg bg-gradient-to-br from-amber-50 to-emerald-50/50 border-[#C6A443]" id="card_opt_all">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-black text-[#042623] flex items-center gap-1">
                                    <span>🔓</span> LANGSUNG TEBUS
                                </span>
                                <input type="radio" name="payment_option" value="pay_all" class="text-[#085C54] focus:ring-[#085C54]" onchange="onPaymentOptionChange('pay_all')">
                            </div>
                            <p class="text-xs text-emerald-900 font-bold">Pelunasan Seluruh Pinjaman</p>
                            <p class="text-base font-black text-emerald-800 mt-2">Rp {{ number_format($unpaidAmount, 0, ',', '.') }}</p>
                        </label>
                    </div>
                </div>

                {{-- Multi-month Selector (jika pilih custom) --}}
                <div id="custom_months_selector" class="p-4 rounded-2xl bg-amber-50/60 border border-amber-200 space-y-2" style="display: none;">
                    <p class="text-xs font-bold text-amber-950 mb-2">Centang bulan angsuran yang ingin Anda bayar sekaligus:</p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        @foreach($unpaidPayments as $p)
                        <label class="flex items-center gap-2 p-2.5 rounded-xl bg-white border border-amber-200 cursor-pointer text-xs font-bold text-slate-800 hover:bg-amber-100/50">
                            <input type="checkbox" name="selected_months[]" value="{{ $p->installment_number }}" class="custom-month-cb text-[#085C54] rounded focus:ring-[#085C54]" onchange="updateTotalPayment()">
                            <span>Bulan #{{ $p->installment_number }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Total Tagihan Box --}}
                <div class="p-4 rounded-2xl bg-[#F4EDD9]/40 border border-[#C6A443]/40 flex justify-between items-center">
                    <div>
                        <p class="text-xs font-bold text-slate-600 uppercase tracking-wider">Total Pembayaran / Tebusan</p>
                        <p class="text-xs text-slate-500 font-medium" id="payment_desc_label">1x Angsuran Bulanan</p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-black text-[#085C54]" id="total_payment_display">Rp {{ number_format($monthlyAmount, 0, ',', '.') }}</p>
                    </div>
                </div>

                {{-- 2. Metode Pembayaran --}}
                <div>
                    <label class="input-label text-sm font-bold text-slate-900 mb-2">2. Pilih Metode Pembayaran <span class="text-red-600">*</span></label>
                    <select name="payment_method" id="pawn_payment_method" class="input-field cursor-pointer font-bold" required onchange="onPawnMethodChange(this)">
                        @if(isset($paymentMethods) && $paymentMethods->count())
                            @foreach($paymentMethods as $pm)
                            <option value="{{ $pm->code }}" data-type="{{ $pm->type }}" data-bank="{{ $pm->name }}" data-acc="{{ $pm->account_number }}" data-holder="{{ $pm->account_holder ?? 'TOKO EMAS SINAR BARU II' }}" data-inst="{{ $pm->instructions }}">
                                {{ $pm->name }} {{ $pm->account_number ? '('.$pm->account_number.')' : '' }}
                            </option>
                            @endforeach
                        @else
                            <option value="bca" data-type="bank_transfer" data-bank="Bank BCA" data-acc="8820 9182 34" data-holder="TOKO EMAS SINAR BARU II">Transfer Bank BCA (8820 9182 34)</option>
                            <option value="mandiri" data-type="bank_transfer" data-bank="Bank Mandiri" data-acc="113 00 1829 4432" data-holder="TOKO EMAS SINAR BARU II">Transfer Bank Mandiri (113 00 1829 4432)</option>
                            <option value="cash" data-type="cash" data-bank="Tunai di Toko" data-acc="" data-holder="">Bayar Tunai di Kasir Toko</option>
                        @endif
                    </select>

                    {{-- Rekening Info Box --}}
                    <div id="pawn_bank_info" class="mt-3 p-4 rounded-2xl bg-white border border-[#085C54]/30 shadow-sm">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-800 flex items-center justify-center text-lg shrink-0 border border-emerald-200">
                                🏦
                            </div>
                            <div class="flex-1 text-xs">
                                <p id="pawn_display_bank" class="font-bold text-slate-600 uppercase">Transfer Bank</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span id="pawn_display_acc" class="text-base font-extrabold font-mono text-[#085C54] tracking-wide">-</span>
                                    <button type="button" onclick="copyPawnAcc()" class="px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition border border-slate-300 shadow-sm">
                                        📋 Salin
                                    </button>
                                </div>
                                <p id="pawn_display_holder" class="font-semibold text-slate-700 mt-1">a.n. TOKO EMAS SINAR BARU II</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. Upload Bukti Transfer / Pembayaran --}}
                <div class="space-y-4">
                    <label class="input-label text-sm font-bold text-slate-900 mb-1">3. Unggah Bukti Pembayaran / Transfer <span class="text-red-600">*</span></label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-bold text-slate-600 block mb-1">Nama Pemilik Rekening Pengirim (Opsional)</label>
                            <input type="text" name="sender_name" value="{{ old('sender_name', auth()->user()->name) }}" placeholder="cth: Budi Santoso" class="input-field">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-600 block mb-1">Bank Pengirim (Opsional)</label>
                            <input type="text" name="sender_bank" value="{{ old('sender_bank') }}" placeholder="cth: BCA / Mandiri / BRI" class="input-field">
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-600 block mb-1">Foto Bukti Transfer (Struk / Screenshot M-Banking) <span class="text-red-600">*</span></label>
                        <input type="file" name="proof_image" accept="image/jpeg,image/png,image/webp" required class="input-field file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#085C54] file:text-white hover:file:bg-[#064741] file:cursor-pointer">
                        <p class="text-[11px] text-slate-500 mt-1">Format: JPG, PNG, WebP. Ukuran maks: 5MB.</p>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-600 block mb-1">Catatan Tambahan (Opsional)</label>
                        <textarea name="notes" rows="2" placeholder="Tuliskan catatan tambahan jika ada..." class="input-field text-sm">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" id="submit_pawn_payment_btn" class="w-full py-3.5 rounded-2xl font-black text-sm text-[#042623] gold-gradient border border-[#C6A443] shadow-lg hover:brightness-110 transition flex items-center justify-center gap-2">
                        <span>🚀</span> <span>Kirim Bukti Pembayaran & Konfirmasi Tebus →</span>
                    </button>
                </div>
            </form>
        </div>
        @endif

        {{-- Jadwal Angsuran Detail --}}
        @if($plan)
        <div class="glass rounded-3xl overflow-hidden bg-white border border-[#e8e3d5] shadow-lg">
            <div class="p-6 bg-slate-50 border-b border-slate-200">
                <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                    <span>📋</span> Rincian Angsuran per Bulan
                </h3>
            </div>
            <div class="p-6 overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-800">
                    <thead>
                        <tr class="text-xs text-slate-600 font-bold uppercase tracking-wider border-b border-slate-200">
                            <th class="py-3 px-3">Bulan</th>
                            <th class="py-3 px-3">Jatuh Tempo</th>
                            <th class="py-3 px-3 text-right">Nominal Angsuran</th>
                            <th class="py-3 px-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($plan->payments->sortBy('installment_number') as $pmt)
                        <tr>
                            <td class="py-3 px-3 font-bold text-slate-900">Bulan Ke-{{ $pmt->installment_number }}</td>
                            <td class="py-3 px-3 text-xs text-slate-600">{{ $pmt->due_date?->isoFormat('D MMM Y') }}</td>
                            <td class="py-3 px-3 text-right font-extrabold text-slate-900">Rp {{ number_format($pmt->amount_due, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-center">
                                @if($pmt->status === 'paid')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">✓ Lunas</span>
                                @elseif($pmt->status === 'waiting_verification')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300 animate-pulse">Menunggu Verifikasi</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-300">Belum Bayar</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Riwayat Pembayaran Pelanggan --}}
        @if($plan->installmentTransactions->isNotEmpty())
        <div class="glass rounded-3xl overflow-hidden bg-white border border-[#e8e3d5] shadow-lg">
            <div class="p-6 bg-slate-50 border-b border-slate-200">
                <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                    <span>🧾</span> Riwayat Pengiriman Pembayaran Anda
                </h3>
            </div>
            <div class="p-6 space-y-3">
                @foreach($plan->installmentTransactions as $trx)
                <div class="p-4 rounded-2xl border {{ $trx->status === 'pending' ? 'bg-amber-50/50 border-amber-300' : ($trx->status === 'verified' ? 'bg-emerald-50/40 border-emerald-200' : 'bg-red-50/40 border-red-200') }} flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs font-bold text-[#085C54]">{{ $trx->payment_code }}</span>
                            <span class="text-xs px-2.5 py-0.5 rounded-full font-bold border {{ $trx->status === 'pending' ? 'bg-amber-100 text-amber-900 border-amber-300' : ($trx->status === 'verified' ? 'bg-emerald-100 text-emerald-900 border-emerald-300' : 'bg-red-100 text-red-900 border-red-300') }}">
                                {{ $trx->status === 'pending' ? 'Sedang Diverifikasi Kasir' : ($trx->status === 'verified' ? '✓ Disetujui' : '✕ Ditolak') }}
                            </span>
                        </div>
                        <p class="text-base font-extrabold text-slate-900 mt-1">Rp {{ number_format($trx->amount, 0, ',', '.') }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">Tanggal: {{ $trx->created_at->isoFormat('D MMMM Y, HH:mm') }} WIB</p>
                        @if($trx->rejection_reason)
                        <p class="text-xs text-red-700 font-semibold mt-1">Alasan Penolakan: {{ $trx->rejection_reason }}</p>
                        @endif
                    </div>

                    @if($trx->proof_image)
                    <a href="{{ asset('storage/' . $trx->proof_image) }}" target="_blank" class="px-3.5 py-2 rounded-xl text-xs font-bold text-[#085C54] bg-[#e2f2f0] border border-[#085C54]/30 hover:bg-[#c9e8e4] transition">
                        🔍 Lihat Bukti
                    </a>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @endif
    </div>

    <script>
        const monthlyAmount = {{ (float) $monthlyAmount }};
        const totalUnpaidAmount = {{ (float) $unpaidAmount }};

        function onPaymentOptionChange(opt) {
            const cardNext = document.getElementById('card_opt_next');
            const cardCustom = document.getElementById('card_opt_custom');
            const cardAll = document.getElementById('card_opt_all');
            const customSelector = document.getElementById('custom_months_selector');
            const descLabel = document.getElementById('payment_desc_label');

            [cardNext, cardCustom, cardAll].forEach(c => {
                if (c) {
                    c.classList.remove('ring-2', 'ring-[#085C54]', 'border-[#085C54]');
                }
            });

            if (opt === 'next_month') {
                if (cardNext) cardNext.classList.add('ring-2', 'ring-[#085C54]', 'border-[#085C54]');
                if (customSelector) customSelector.style.display = 'none';
                if (descLabel) descLabel.textContent = '1x Angsuran Bulanan';
                document.getElementById('total_payment_display').textContent = 'Rp ' + monthlyAmount.toLocaleString('id-ID');
            } else if (opt === 'custom_months') {
                if (cardCustom) cardCustom.classList.add('ring-2', 'ring-[#085C54]', 'border-[#085C54]');
                if (customSelector) customSelector.style.display = 'block';
                updateTotalPayment();
            } else if (opt === 'pay_all') {
                if (cardAll) cardAll.classList.add('ring-2', 'ring-[#085C54]', 'border-[#085C54]');
                if (customSelector) customSelector.style.display = 'none';
                if (descLabel) descLabel.textContent = 'Pelunasan Total Seluruh Pinjaman (Tebus Langsung)';
                document.getElementById('total_payment_display').textContent = 'Rp ' + totalUnpaidAmount.toLocaleString('id-ID');
            }
        }

        function updateTotalPayment() {
            const cbs = document.querySelectorAll('.custom-month-cb:checked');
            const count = cbs.length;
            const total = count * monthlyAmount;
            document.getElementById('total_payment_display').textContent = 'Rp ' + total.toLocaleString('id-ID');
            const descLabel = document.getElementById('payment_desc_label');
            if (descLabel) descLabel.textContent = `${count} Bulan Angsuran Terpilih`;
        }

        function onPawnMethodChange(select) {
            const opt = select.options[select.selectedIndex];
            const bank = opt.getAttribute('data-bank') || '';
            const acc = opt.getAttribute('data-acc') || '-';
            const holder = opt.getAttribute('data-holder') || '';

            const bankInfo = document.getElementById('pawn_bank_info');
            const dispBank = document.getElementById('pawn_display_bank');
            const dispAcc = document.getElementById('pawn_display_acc');
            const dispHolder = document.getElementById('pawn_display_holder');

            if (opt.getAttribute('data-type') === 'cash') {
                if (bankInfo) bankInfo.style.display = 'none';
            } else {
                if (bankInfo) bankInfo.style.display = 'block';
                if (dispBank) dispBank.textContent = bank;
                if (dispAcc) dispAcc.textContent = acc;
                if (dispHolder) dispHolder.textContent = holder ? 'a.n. ' + holder : '';
            }
        }

        function copyPawnAcc() {
            const acc = document.getElementById('pawn_display_acc')?.textContent;
            if (acc && acc !== '-') {
                navigator.clipboard.writeText(acc.replace(/\s+/g, ''));
                alert('Nomor rekening ' + acc + ' berhasil disalin!');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const sel = document.getElementById('pawn_payment_method');
            if (sel) onPawnMethodChange(sel);
            onPaymentOptionChange('next_month');
        });
    </script>
</x-customer-app>
