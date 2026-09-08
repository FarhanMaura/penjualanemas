<x-customer-app>
    <x-slot name="pageTitle">Detail Cicilan & Pembayaran</x-slot>
    <x-slot name="breadcrumb">Rincian, pembayaran angsuran, dan histori transaksi cicilan emas Anda</x-slot>

    @php
        $paid    = $installmentPlan->settledOrSubmittedCount();
        $total   = $installmentPlan->tenure_months;
        $pct     = $total > 0 ? round(($paid / $total) * 100) : 0;
        $product = $installmentPlan->transaction->items->first()->product ?? null;
        $firstUnpaid = $unpaidPayments->first();
        $allUnpaidCount = $unpaidPayments->count();
        $allUnpaidTotal = $unpaidPayments->sum('amount_due');
    @endphp

    <div class="max-w-4xl mx-auto space-y-8">
        <a href="{{ route('customer.installments.index') }}" class="text-xs font-bold text-[#085C54] hover:underline inline-flex items-center gap-1.5 transition">
            <span class="text-base leading-none">←</span> Kembali ke Daftar Cicilan
        </a>

        @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-950 font-bold text-sm shadow-sm flex items-center gap-3">
            <span class="text-xl">✅</span> <div>{{ session('success') }}</div>
        </div>
        @endif

        @if(session('error'))
        <div class="p-4 rounded-2xl bg-red-50 border border-red-300 text-red-950 font-bold text-sm shadow-sm flex items-center gap-3">
            <span class="text-xl">⚠️</span> <div>{{ session('error') }}</div>
        </div>
        @endif

        @if($errors->any())
        <div class="p-4 rounded-2xl bg-red-50 border border-red-300 text-red-950 font-bold text-sm shadow-sm space-y-1">
            <div class="flex items-center gap-2 text-red-900 font-extrabold mb-1">
                <span>⚠️</span> <span>Mohon periksa kesalahan pengisian:</span>
            </div>
            <ul class="list-disc list-inside text-xs font-semibold pl-2 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Main Plan Summary Card --}}
        <div class="glass rounded-3xl overflow-hidden bg-white border border-[#e8e3d5] shadow-lg">
            <div class="p-6 bg-gradient-to-r from-[#F4EDD9]/80 to-[#faeec7]/40 border-b border-[#e8e3d5]">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div class="flex items-center gap-4">
                        @if($product?->image)
                        <img src="{{ asset('storage/' . $product->image) }}"
                             alt="{{ $product->name }}"
                             class="w-16 h-16 rounded-2xl object-cover border border-[#C6A443]/40 shadow-sm bg-white p-1">
                        @endif
                        <div>
                            <span class="text-[11px] uppercase tracking-wider font-extrabold px-2.5 py-0.5 rounded-full bg-[#085C54]/10 text-[#085C54]">
                                {{ $installmentPlan->transaction->transaction_code }}
                            </span>
                            <h2 class="text-xl sm:text-2xl font-bold font-playfair text-slate-900 mt-1">{{ $product->name ?? 'Cicilan Emas' }}</h2>
                            <p class="text-xs text-slate-600 font-semibold mt-0.5">
                                Kadar: <strong class="text-slate-900">{{ $product->gold_purity ?? '24K' }}</strong> • Berat: <strong class="text-slate-900">{{ number_format($product->weight_gram ?? 0, 3) }} gram</strong>
                            </p>
                        </div>
                    </div>
                    <div>
                        <span class="px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider border shadow-sm
                            {{ $installmentPlan->status === 'active' ? 'bg-blue-100 text-blue-900 border-blue-300' : ($installmentPlan->status === 'completed' ? 'bg-emerald-100 text-emerald-900 border-emerald-300' : 'bg-slate-100 text-slate-800 border-slate-300') }}">
                            {{ $installmentPlan->status === 'completed' ? '🎉 Lunas & Selesai' : '⚡ ' . ucfirst($installmentPlan->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-6">
                {{-- Progress Bar Besar --}}
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200">
                    <div class="flex justify-between items-center mb-2.5">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-600">Progres Pembayaran Angsuran</span>
                            @if($installmentPlan->waitingVerificationCount() > 0)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-900 border border-amber-300 animate-pulse">
                                {{ $installmentPlan->waitingVerificationCount() }} Menunggu Verifikasi
                            </span>
                            @endif
                        </div>
                        <span class="text-sm font-extrabold text-[#085C54]">{{ $paid }} / {{ $total }} Bulan ({{ $pct }}%)</span>
                    </div>
                    <div class="h-4 rounded-full overflow-hidden bg-slate-200 border border-slate-300/60 p-0.5">
                        <div class="h-full rounded-full transition-all duration-500" style="width:{{ $pct }}%; background:linear-gradient(90deg,#085C54,#C6A443);"></div>
                    </div>
                    <div class="mt-2.5 flex justify-between text-xs text-slate-500 font-semibold">
                        <span>Mulai: {{ $installmentPlan->start_date?->isoFormat('D MMM Y') ?? '-' }}</span>
                        <span>Sisa: <strong class="text-slate-900">{{ $installmentPlan->remainingMonths() }} bulan</strong></span>
                        <span>Selesai: {{ $installmentPlan->end_date?->isoFormat('D MMM Y') ?? '-' }}</span>
                    </div>
                </div>

                {{-- Detail Keuangan --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="p-3.5 rounded-xl bg-white border border-[#e8e3d5] shadow-sm">
                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mb-0.5">Angsuran / Bulan</p>
                        <p class="font-extrabold text-[#085C54] text-sm sm:text-base">Rp {{ number_format($installmentPlan->monthly_amount, 0, ',', '.') }}</p>
                    </div>
                    <div class="p-3.5 rounded-xl bg-white border border-[#e8e3d5] shadow-sm">
                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mb-0.5">Total Nilai Emas</p>
                        <p class="font-extrabold text-slate-900 text-sm sm:text-base">Rp {{ number_format($installmentPlan->total_installment, 0, ',', '.') }}</p>
                    </div>
                    <div class="p-3.5 rounded-xl bg-white border border-[#e8e3d5] shadow-sm">
                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mb-0.5">Tenor Dipilih</p>
                        <p class="font-extrabold text-amber-700 text-sm sm:text-base">{{ $installmentPlan->tenure_months }} Bulan</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- FITUR PEMBAYARAN CICILAN ALA PEGADAIAN & SHOPEE --}}
        @if($installmentPlan->status === 'active' && $unpaidPayments->isNotEmpty())
        <div class="glass rounded-3xl overflow-hidden bg-white border-2 border-[#C6A443]/50 shadow-xl" id="payment-section">
            <div class="p-6 bg-gradient-to-br from-[#085C54] to-[#042623] text-white">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-[#C6A443] text-[#042623]">
                                Finansial Mandiri
                            </span>
                            <span class="text-xs text-amber-200/80 font-bold">• Layanan Pembayaran Online</span>
                        </div>
                        <h3 class="text-xl sm:text-2xl font-extrabold font-playfair tracking-tight">Bayar Angsuran Cicilan Emas</h3>
                        <p class="text-xs text-white/80 mt-1">
                            Pilih pembayaran bulan berjalan, beberapa bulan sekaligus, atau pelunasan seluruh sisa angsuran.
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-[11px] text-amber-200 font-bold uppercase">Sisa Tagihan Belum Lunas</p>
                        <p class="text-2xl font-black text-[#C6A443]">Rp {{ number_format($allUnpaidTotal, 0, ',', '.') }}</p>
                        <p class="text-[11px] text-white/70">{{ $allUnpaidCount }} bulan tersisa</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('customer.installments.pay', $installmentPlan) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  id="installment-pay-form"
                  class="p-6 sm:p-8 space-y-6">
                @csrf

                {{-- 1. Pilihan Skema Pembayaran (1 Bulan / Beberapa Bulan / Lunasi) --}}
                <div>
                    <label class="input-label text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                        <span>1️⃣</span> Pilih Skema Pembayaran
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        {{-- Opsi 1: Bulan Berikutnya --}}
                        <label class="payment-option-card relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition select-none bg-white hover:border-[#085C54] border-slate-200">
                            <input type="radio" name="payment_option" value="next_month" class="sr-only" checked onchange="handleOptionChange()">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-extrabold text-[#085C54] px-2 py-0.5 rounded-full bg-[#085C54]/10">Bulan Berjalan</span>
                                <span class="check-indicator w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center text-xs font-bold text-white">✓</span>
                            </div>
                            <p class="font-extrabold text-slate-900 text-sm">Bulan ke-{{ $firstUnpaid->installment_number ?? 1 }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">Jatuh tempo: {{ $firstUnpaid->due_date?->isoFormat('D MMM Y') ?? '-' }}</p>
                            <p class="text-base font-black text-[#085C54] mt-3">Rp {{ number_format($firstUnpaid->amount_due ?? 0, 0, ',', '.') }}</p>
                        </label>

                        {{-- Opsi 2: Pilih Beberapa Bulan --}}
                        @if($allUnpaidCount > 1)
                        <label class="payment-option-card relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition select-none bg-white hover:border-[#085C54] border-slate-200">
                            <input type="radio" name="payment_option" value="custom_months" class="sr-only" onchange="handleOptionChange()">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-extrabold text-blue-700 px-2 py-0.5 rounded-full bg-blue-50">Beberapa Bulan</span>
                                <span class="check-indicator w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center text-xs font-bold text-white">✓</span>
                            </div>
                            <p class="font-extrabold text-slate-900 text-sm">Pilih 2 atau Lebih</p>
                            <p class="text-xs text-slate-500 mt-0.5">Pilih bulan berurutan</p>
                            <p class="text-xs font-extrabold text-blue-800 mt-3">Kustom Bulan Terdekat →</p>
                        </label>
                        @endif

                        {{-- Opsi 3: Pelunasan Dipercepat --}}
                        <label class="payment-option-card relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition select-none bg-white hover:border-[#085C54] border-slate-200">
                            <input type="radio" name="payment_option" value="pay_all" class="sr-only" onchange="handleOptionChange()">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-extrabold text-amber-800 px-2 py-0.5 rounded-full bg-amber-100">Pelunasan Penuh</span>
                                <span class="check-indicator w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center text-xs font-bold text-white">✓</span>
                            </div>
                            <p class="font-extrabold text-slate-900 text-sm">Lunasi Sekaligus</p>
                            <p class="text-xs text-slate-500 mt-0.5">Semua sisa {{ $allUnpaidCount }} bulan</p>
                            <p class="text-base font-black text-amber-700 mt-3">Rp {{ number_format($allUnpaidTotal, 0, ',', '.') }}</p>
                        </label>
                    </div>
                </div>

                {{-- Box Checklist Bulan (Hanya muncul jika memilih custom_months) --}}
                <div id="custom-months-box" class="p-5 rounded-2xl bg-blue-50/60 border border-blue-200 hidden">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-xs font-extrabold text-blue-950 uppercase tracking-wider">Pilih Bulan Angsuran Yang Ingin Dibayar:</span>
                        <span class="text-xs text-blue-700 font-semibold">*Harus berurutan dari bulan terdekat</span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @foreach($unpaidPayments as $index => $payment)
                        <label class="month-checkbox-label flex items-center gap-3 p-3 rounded-xl bg-white border border-blue-200 cursor-pointer hover:border-blue-500 transition shadow-sm select-none">
                            <input type="checkbox"
                                   name="selected_months[]"
                                   value="{{ $payment->installment_number }}"
                                   data-amount="{{ $payment->amount_due }}"
                                   data-index="{{ $index }}"
                                   class="month-checkbox rounded text-[#085C54] focus:ring-[#085C54] w-4 h-4 cursor-pointer"
                                   onchange="handleMonthCheckboxChange(this)">
                            <div>
                                <p class="text-xs font-extrabold text-slate-900">Bulan ke-{{ $payment->installment_number }}</p>
                                <p class="text-[11px] text-slate-500 font-semibold">Rp {{ number_format($payment->amount_due, 0, ',', '.') }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- 2. Kalkulasi Realtime Total Bayar --}}
                <div class="p-5 rounded-2xl bg-[#F4EDD9]/60 border border-[#e8e3d5] flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-600">Rincian Angsuran Yang Akan Dibayar</p>
                        <p class="text-base font-extrabold text-slate-900 mt-0.5" id="summary-months-text">
                            Bulan ke-{{ $firstUnpaid->installment_number ?? 1 }} (1 Bulan)
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-600">Total Nominal Pembayaran</p>
                        <p class="text-2xl font-black text-[#085C54]" id="summary-total-text">
                            Rp {{ number_format($firstUnpaid->amount_due ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                {{-- 3. Metode Pembayaran & Info Rekening Toko --}}
                <div class="space-y-4">
                    <label class="input-label text-sm font-bold text-slate-900 flex items-center gap-2">
                        <span>2️⃣</span> Pilih Metode Pembayaran & Rekening Tujuan
                    </label>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <select name="payment_method" id="payment_method_select" class="input-field font-bold cursor-pointer" required onchange="handlePaymentMethodChange()">
                                @foreach($paymentMethods as $pm)
                                <option value="{{ $pm->code }}"
                                        data-name="{{ $pm->name }}"
                                        data-bank="{{ $pm->bank_name }}"
                                        data-acc-no="{{ $pm->account_number }}"
                                        data-acc-holder="{{ $pm->account_holder }}"
                                        data-instructions="{{ $pm->instructions }}"
                                        {{ $loop->first ? 'selected' : '' }}>
                                    {{ $pm->name }} {{ $pm->account_number ? '('.$pm->account_number.')' : '' }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="text-xs text-slate-600 flex items-center">
                            <span>Silakan transfer tepat sesuai total nominal ke rekening resmi Toko Emas Sinar Baru II di samping.</span>
                        </div>
                    </div>

                    {{-- Dynamic Bank Card Display --}}
                    <div id="bank-info-card" class="p-5 rounded-2xl bg-gradient-to-br from-emerald-50 to-teal-50 border-2 border-emerald-300 shadow-sm">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 pb-3 border-b border-emerald-200">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-800">Rekening Resmi Toko Emas Sinar Baru II</span>
                                <h4 class="text-base font-extrabold text-emerald-950 mt-0.5" id="card-bank-name">Transfer Bank</h4>
                            </div>
                            <div class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-xl border border-emerald-300 shadow-sm">
                                <span class="font-mono font-bold text-emerald-900 text-sm" id="card-acc-number">-</span>
                                <button type="button" onclick="copyAccountNumber()" class="text-xs font-extrabold text-[#085C54] hover:underline flex items-center gap-1">
                                    📋 <span id="copy-btn-text">Salin</span>
                                </button>
                            </div>
                        </div>
                        <div class="pt-3 flex flex-col sm:flex-row justify-between gap-2 text-xs text-emerald-900 font-semibold">
                            <p>Atas Nama: <strong class="text-slate-900 font-extrabold" id="card-acc-holder">-</strong></p>
                            <p id="card-instructions" class="text-emerald-800 italic">-</p>
                        </div>
                    </div>
                </div>

                {{-- 4. Upload Bukti Transfer --}}
                <div class="space-y-4 pt-2">
                    <label class="input-label text-sm font-bold text-slate-900 flex items-center gap-2">
                        <span>3️⃣</span> Upload Bukti Transfer Pembayaran <span class="text-red-600">*</span>
                    </label>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Dropzone / File Picker --}}
                        <div class="border-2 border-dashed border-slate-300 hover:border-[#085C54] rounded-2xl p-6 text-center cursor-pointer transition bg-slate-50 relative"
                             id="dropzone-area"
                             onclick="document.getElementById('proof_image_input').click()">
                            <input type="file"
                                   name="proof_image"
                                   id="proof_image_input"
                                   accept="image/jpeg,image/png,image/webp"
                                   class="sr-only"
                                   required
                                   onchange="previewProofImage(event)">
                            <div class="space-y-2">
                                <span class="text-4xl inline-block">📸</span>
                                <p class="text-xs font-bold text-slate-800">Klik untuk upload bukti transfer</p>
                                <p class="text-[11px] text-slate-500">Format: JPG, PNG, WebP (Maks. 5MB)</p>
                            </div>
                        </div>

                        {{-- Preview Box --}}
                        <div class="rounded-2xl border border-slate-200 bg-slate-100 p-3 flex flex-col items-center justify-center min-h-[160px] relative overflow-hidden" id="preview-container">
                            <img id="proof-preview" src="#" alt="Preview Bukti Transfer" class="max-h-48 rounded-xl object-contain shadow-sm hidden">
                            <div id="no-preview-text" class="text-center text-xs text-slate-400 font-medium">
                                <span>Pratinjau foto bukti pembayaran akan muncul di sini</span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                        <div>
                            <label class="input-label text-xs">Nama Pemilik Rekening Pengirim (Opsional)</label>
                            <input type="text" name="sender_name" placeholder="cth: Budi Santoso" class="input-field text-xs">
                        </div>
                        <div>
                            <label class="input-label text-xs">Nama Bank Pengirim (Opsional)</label>
                            <input type="text" name="sender_bank" placeholder="cth: BCA / Mandiri / BRI" class="input-field text-xs">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="input-label text-xs">Catatan Pembayaran (Opsional)</label>
                            <input type="text" name="notes" placeholder="cth: Pembayaran angsuran cicilan emas bulan ini..." class="input-field text-xs">
                        </div>
                    </div>
                </div>

                {{-- 5. Combined Section: Jadwal Pengambilan Emas Fisik di Toko (Bulan Terakhir) --}}
                @if($installmentPlan->canSchedulePickup() && ! $installmentPlan->pickupReservation)
                <div class="space-y-4 pt-4 border-t border-slate-200" id="pickup-schedule-combined">
                    <div class="p-5 rounded-2xl bg-gradient-to-br from-amber-50 to-emerald-50 border-2 border-emerald-400 shadow-md">
                        <div class="flex items-start gap-3 mb-3">
                            <span class="text-3xl">📦</span>
                            <div>
                                <h4 class="font-bold text-emerald-950 text-base font-playfair">Jadwal Pengambilan Emas Fisik di Toko (Bulan Terakhir)</h4>
                                <p class="text-xs text-emerald-800 mt-1 leading-relaxed">
                                    Selamat! Anda telah memasuki pembayaran bulan terakhir. Tentukan jadwal kunjungan Anda ke Toko Emas Sinar Baru II sekaligus saat mengirimkan bukti pembayaran ini.
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                            <div>
                                <label class="input-label text-xs">Tanggal Pengambilan Emas <span class="text-red-600">*</span></label>
                                <input type="date" name="preferred_date" min="{{ date('Y-m-d') }}" value="{{ old('preferred_date', date('Y-m-d', strtotime('+1 day'))) }}" required class="input-field font-bold">
                            </div>
                            <div>
                                <label class="input-label text-xs">Jam Kunjungan (08:00 - 17:00) <span class="text-red-600">*</span></label>
                                <input type="time" name="preferred_time" value="{{ old('preferred_time', '10:00') }}" required class="input-field font-bold">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="input-label text-xs">Catatan Tambahan Pengambilan (Opsional)</label>
                                <input type="text" name="pickup_notes" value="{{ old('pickup_notes') }}" placeholder="cth: Pengambilan fisik membawa KTP..." class="input-field text-xs">
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Submit Button --}}
                <div class="pt-4 border-t border-slate-200">
                    <button type="submit"
                            id="submit-pay-btn"
                            class="w-full py-4 rounded-2xl font-extrabold text-base text-[#042623] gold-gradient border border-[#C6A443] shadow-lg hover:brightness-110 transition flex items-center justify-center gap-2">
                        <span>📤</span>
                        <span id="submit-btn-text">Kirim Bukti Pembayaran & Konfirmasi</span>
                    </button>
                    <p class="text-center text-[11px] text-slate-500 font-medium mt-2">
                        Admin toko akan memverifikasi bukti transfer Anda dalam 1x24 jam kerja.
                    </p>
                </div>
            </form>
        </div>
        @elseif($installmentPlan->status === 'active' && $installmentPlan->waitingVerificationCount() > 0)
        <div class="p-6 rounded-3xl bg-amber-50 border-2 border-amber-300 shadow-md flex items-start gap-4">
            <span class="text-3xl">⏳</span>
            <div>
                <h3 class="text-base font-bold text-amber-950">Pembayaran Sedang Diverifikasi Admin</h3>
                <p class="text-xs text-amber-800 mt-1 leading-relaxed">
                    Anda telah mengunggah bukti pembayaran untuk {{ $installmentPlan->waitingVerificationCount() }} angsuran. Tim kasir Toko Emas Sinar Baru II sedang melakukan verifikasi mutasi rekening. Silakan cek berkala status pada riwayat transaksi di bawah.
                </p>
            </div>
        </div>
        @endif

        {{-- JADWAL RESERVASI PENGAMBILAN EMAS FISIK --}}
        <div class="glass rounded-3xl p-6 bg-white border border-[#e8e3d5] shadow-lg">
            <h3 class="font-bold text-slate-900 mb-4 text-base flex items-center gap-2">
                <span>📦</span> Jadwal Pengambilan Emas Fisik di Toko
            </h3>

            @if($installmentPlan->pickupReservation)
            {{-- Sudah Ada Jadwal Reservasi Pengambilan --}}
            <div class="p-5 rounded-2xl bg-emerald-50/80 border-2 border-emerald-300 shadow-sm">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-3 pb-3 border-b border-emerald-200">
                    <div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-emerald-600 text-white shadow-sm">
                            ✔ Jadwal Pengambilan Terjadwal
                        </span>
                        <h4 class="font-bold text-slate-900 text-base mt-2">Kode Reservasi: <span class="font-mono text-[#085C54]">{{ $installmentPlan->pickupReservation->reservation_code }}</span></h4>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-white text-emerald-800 border border-emerald-300">
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
                    <div class="sm:col-span-2">
                        <p class="text-slate-500 font-bold uppercase">Lokasi Pengambilan</p>
                        <p class="font-bold text-slate-900 mt-0.5">📍 Toko Emas Sinar Baru II — Teluk Lubuk, Belimbing, Muara Enim</p>
                    </div>
                </div>
            </div>
            @elseif($installmentPlan->canSchedulePickup())
            {{-- Memasuki Bulan Terakhir --}}
            <div class="p-6 rounded-2xl bg-gradient-to-br from-amber-50 to-emerald-50 border-2 border-emerald-400 shadow-md">
                <div class="flex items-start gap-3 mb-4">
                    <span class="text-3xl">🎉</span>
                    <div>
                        <h4 class="font-bold text-emerald-950 text-base font-playfair">Form Pengambilan Emas Berada di Form Pembayaran</h4>
                        <p class="text-xs text-emerald-800 mt-1 leading-relaxed">
                            Formulir penentuan jadwal pengambilan emas fisik telah **digabungkan secara otomatis ke dalam Formulir Pembayaran Angsuran di atas**. Silakan tentukan tanggal dan jam kunjungan Anda pada form pembayaran saat mengunggah bukti transfer.
                        </p>
                    </div>
                </div>

                @if($unpaidPayments->isEmpty())
                <form action="{{ route('customer.installments.schedule-pickup', $installmentPlan) }}" method="POST" class="space-y-4 pt-2">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="input-label text-xs">Pilih Tanggal Pengambilan <span class="text-red-600">*</span></label>
                            <input type="date" name="preferred_date" min="{{ date('Y-m-d') }}" value="{{ old('preferred_date', date('Y-m-d', strtotime('+1 day'))) }}" required class="input-field font-bold">
                        </div>
                        <div>
                            <label class="input-label text-xs">Pilih Jam (08:00 - 17:00) <span class="text-red-600">*</span></label>
                            <input type="time" name="preferred_time" value="{{ old('preferred_time', '10:00') }}" required class="input-field font-bold">
                        </div>
                    </div>
                    <div>
                        <label class="input-label text-xs">Catatan (Opsional)</label>
                        <input type="text" name="notes" placeholder="cth: Pengambilan langsung membawa KTP..." class="input-field text-xs">
                    </div>
                    <button type="submit" class="w-full py-3 rounded-xl font-extrabold text-[#042623] gold-gradient border border-[#C6A443] shadow-md hover:brightness-110 transition text-sm">
                        📅 Konfirmasi Jadwal Pengambilan Emas
                    </button>
                </form>
                @endif
            </div>
            @else
            {{-- Belum Masuk Bulan Terakhir: Terkunci --}}
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200">
                <div class="flex items-start gap-3">
                    <span class="text-2xl">🔒</span>
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="font-bold text-slate-800 text-sm">Jadwal Pengambilan Belum Terbuka</h4>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                Perlu {{ max(1, $installmentPlan->requiredPaidForPickup() - $paid) }} Bulan Lagi
                            </span>
                        </div>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                            Jadwal reservasi pengambilan emas fisik baru dapat dibuat saat memasuki <strong>pembayaran bulan terakhir</strong> (setelah angsuran bulan ke-1 s/d ke-{{ $installmentPlan->requiredPaidForPickup() }} dibayar atau diajukan).
                        </p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- HISTORI TRANSAKSI CICILAN (RIWAYAT SETORAN PEMBAYARAN) --}}
        <div class="glass rounded-3xl p-6 bg-white border border-[#e8e3d5] shadow-lg" id="history-section">
            <div class="flex justify-between items-center mb-5 pb-3 border-b border-slate-200">
                <div>
                    <h3 class="font-bold text-slate-900 text-lg flex items-center gap-2">
                        <span>🧾</span> Histori Transaksi Pembayaran Cicilan
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Catatan seluruh bukti transfer dan transaksi angsuran yang Anda ajukan</p>
                </div>
                <span class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-full border border-slate-200">
                    {{ $installmentPlan->installmentTransactions->count() }} Transaksi
                </span>
            </div>

            @if($installmentPlan->installmentTransactions->isEmpty())
            <div class="text-center py-10 bg-slate-50 rounded-2xl border border-dashed border-slate-300">
                <span class="text-3xl block mb-2">📋</span>
                <p class="text-sm font-bold text-slate-700">Belum ada histori transaksi pembayaran</p>
                <p class="text-xs text-slate-500 mt-1">Gunakan formulir di atas untuk melakukan pembayaran angsuran pertama Anda.</p>
            </div>
            @else
            <div class="space-y-4">
                @foreach($installmentPlan->installmentTransactions as $trx)
                <div class="p-5 rounded-2xl border transition shadow-sm
                    {{ $trx->isVerified() ? 'bg-emerald-50/40 border-emerald-200' : ($trx->isRejected() ? 'bg-red-50/40 border-red-200' : 'bg-amber-50/40 border-amber-200') }}">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 pb-3 border-b border-slate-200/80">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">
                                {{ $trx->isVerified() ? '✅' : ($trx->isRejected() ? '❌' : '⏳') }}
                            </span>
                            <div>
                                <span class="text-xs font-mono font-bold text-slate-700">{{ $trx->transaction_code }}</span>
                                <h4 class="text-base font-extrabold text-slate-900">{{ $trx->formattedMonths() }}</h4>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider border shadow-sm
                                {{ $trx->isVerified() ? 'bg-emerald-100 text-emerald-900 border-emerald-300' : ($trx->isRejected() ? 'bg-red-100 text-red-900 border-red-300' : 'bg-amber-100 text-amber-900 border-amber-300') }}">
                                {{ $trx->isVerified() ? 'Terverifikasi' : ($trx->isRejected() ? 'Ditolak' : 'Menunggu Verifikasi') }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 py-3 text-xs">
                        <div>
                            <p class="text-slate-500 font-bold uppercase text-[10px]">Tanggal Bayar</p>
                            <p class="font-bold text-slate-900 mt-0.5">{{ $trx->created_at->isoFormat('D MMM Y, HH:mm') }} WIB</p>
                        </div>
                        <div>
                            <p class="text-slate-500 font-bold uppercase text-[10px]">Nominal Dibayar</p>
                            <p class="font-black text-[#085C54] text-sm mt-0.5">Rp {{ number_format($trx->amount, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-slate-500 font-bold uppercase text-[10px]">Metode Pembayaran</p>
                            <p class="font-bold text-slate-900 mt-0.5 uppercase">{{ $trx->payment_method }}</p>
                        </div>
                        <div>
                            <p class="text-slate-500 font-bold uppercase text-[10px]">Bukti Transfer</p>
                            @if($trx->proof_image)
                            <button type="button"
                                    onclick="openProofModal('{{ asset('storage/' . $trx->proof_image) }}', '{{ $trx->transaction_code }}')"
                                    class="text-xs font-extrabold text-[#085C54] hover:underline flex items-center gap-1 mt-0.5">
                                🖼️ <span>Lihat Foto Bukti</span>
                            </button>
                            @else
                            <span class="text-slate-400 italic">Langsung di Kasir</span>
                            @endif
                        </div>
                    </div>

                    @if($trx->isRejected() && $trx->rejection_reason)
                    <div class="mt-2 p-3 rounded-xl bg-red-100/70 border border-red-300 text-xs text-red-900">
                        <strong class="font-bold">Alasan Penolakan:</strong> {{ $trx->rejection_reason }}
                    </div>
                    @endif

                    <div class="pt-3 border-t border-slate-200/80 flex justify-between items-center text-xs">
                        <span class="text-slate-500">
                            @if($trx->isVerified() && $trx->verified_at)
                                Diverifikasi pada {{ $trx->verified_at->isoFormat('D MMM Y, HH:mm') }}
                            @elseif($trx->isWaitingVerification())
                                Menunggu pengecekan mutasi oleh tim verifikasi
                            @endif
                        </span>

                        {{-- Tombol Kuitansi Pembayaran Digital --}}
                        <button type="button"
                                onclick="openReceiptModal({{ json_encode([
                                    'code' => $trx->transaction_code,
                                    'date' => $trx->created_at->isoFormat('D MMMM Y, HH:mm') . ' WIB',
                                    'plan_code' => $installmentPlan->transaction->transaction_code,
                                    'product' => $product->name ?? 'Perhiasan Emas',
                                    'purity' => $product->gold_purity ?? '24K',
                                    'weight' => number_format($product->weight_gram ?? 0, 3) . ' gram',
                                    'months' => $trx->formattedMonths(),
                                    'amount' => 'Rp ' . number_format($trx->amount, 0, ',', '.'),
                                    'method' => strtoupper($trx->payment_method),
                                    'status' => $trx->status,
                                    'customer' => auth()->user()->name,
                                ]) }})"
                                class="px-3 py-1.5 rounded-xl font-extrabold text-xs text-[#085C54] bg-[#e2f2f0] hover:bg-[#c9e8e4] border border-[#085C54]/30 transition shadow-sm">
                            🧾 Kuitansi Digital
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- JADWAL SELURUH ANGSURAN BULANAN --}}
        <div class="glass rounded-3xl p-6 bg-white border border-[#e8e3d5] shadow-lg">
            <h3 class="font-bold text-slate-900 mb-4 text-base flex items-center gap-2">
                <span>📅</span> Rincian Tagihan Per Bulan
            </h3>

            <div class="space-y-2.5">
                @foreach($installmentPlan->payments->sortBy('installment_number') as $payment)
                <div class="flex justify-between items-center p-3.5 rounded-xl border transition shadow-sm
                    {{ $payment->isPaid() ? 'bg-emerald-50/50 border-emerald-200' : ($payment->isWaitingVerification() ? 'bg-amber-50/60 border-amber-300' : 'bg-slate-50 border-slate-200') }}">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-extrabold text-slate-900">Bulan ke-{{ $payment->installment_number }}</span>
                            @if($payment->isPaid())
                            <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-900 border border-emerald-300">Lunas</span>
                            @elseif($payment->isWaitingVerification())
                            <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-full bg-amber-100 text-amber-900 border border-amber-300 animate-pulse">Menunggu Verifikasi</span>
                            @else
                            <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full bg-slate-200 text-slate-700">Belum Dibayar</span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 font-medium mt-0.5">
                            Jatuh Tempo: {{ $payment->due_date?->isoFormat('D MMM Y') ?? '-' }}
                            @if($payment->paid_date)
                            • Dibayar: {{ $payment->paid_date->isoFormat('D MMM Y') }}
                            @endif
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-black text-slate-900">Rp {{ number_format($payment->amount_due, 0, ',', '.') }}</p>
                        @if($payment->isPaid())
                        <span class="text-xs font-bold text-emerald-700">✅ Terbayar</span>
                        @elseif($payment->isWaitingVerification())
                        <span class="text-xs font-bold text-amber-700">⏳ Sedang Diperiksa</span>
                        @else
                        <span class="text-xs font-bold text-slate-400">⭕ Belum Lunas</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
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

    {{-- MODAL KUITANSI PEMBAYARAN DIGITAL (CETAK RESMI) --}}
    <div id="receiptModal" class="fixed inset-0 z-50 flex items-center justify-center hidden" style="background:rgba(4,38,35,0.7); backdrop-filter:blur(6px);">
        <div class="w-full max-w-md p-6 rounded-3xl bg-white border border-[#e8e3d5] shadow-2xl relative mx-4" id="printable-receipt">
            <div class="flex justify-between items-start pb-4 border-b border-slate-200">
                <div>
                    <span class="text-[10px] font-black uppercase tracking-wider text-[#085C54]">Kuitansi Pembayaran Angsuran</span>
                    <h3 class="text-lg font-bold font-playfair text-slate-900">Toko Emas Sinar Baru II</h3>
                    <p class="text-[10px] text-slate-500">Teluk Lubuk, Belimbing, Muara Enim</p>
                </div>
                <button type="button" onclick="closeReceiptModal()" class="text-slate-400 hover:text-slate-800 text-2xl font-bold leading-none print:hidden">&times;</button>
            </div>

            <div class="py-4 space-y-3 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-500">No. Transaksi:</span>
                    <strong class="font-mono text-slate-900" id="receipt-code">-</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Tanggal:</span>
                    <strong class="text-slate-900" id="receipt-date">-</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Pelanggan:</span>
                    <strong class="text-slate-900" id="receipt-customer">-</strong>
                </div>
                <hr class="border-dashed border-slate-200">
                <div class="flex justify-between">
                    <span class="text-slate-500">Item Perhiasan:</span>
                    <strong class="text-slate-900" id="receipt-product">-</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Kadar / Berat:</span>
                    <span class="text-slate-700" id="receipt-gold">-</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Angsuran Dibayar:</span>
                    <strong class="text-emerald-900" id="receipt-months">-</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Metode Bayar:</span>
                    <span class="text-slate-900 font-bold" id="receipt-method">-</span>
                </div>
                <hr class="border-slate-200">
                <div class="flex justify-between items-center text-sm">
                    <strong class="text-slate-900">Total Pembayaran:</strong>
                    <strong class="text-base font-black text-[#085C54]" id="receipt-amount">Rp 0</strong>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-center">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-700" id="receipt-status">-</span>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-200 flex gap-3 print:hidden">
                <button type="button" onclick="window.print()" class="flex-1 py-2.5 rounded-xl text-xs font-bold text-[#085C54] bg-[#e2f2f0] hover:bg-[#c9e8e4] border border-[#085C54]/30 transition">
                    🖨️ Cetak Kuitansi
                </button>
                <button type="button" onclick="closeReceiptModal()" class="flex-1 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- JAVASCRIPT LOGIC INTERAKTIF ALA SHOPEE & PEGADAIAN --}}
    <script>
        const monthlyAmount = {{ (float) $installmentPlan->monthly_amount }};
        const firstUnpaidMonth = {{ $firstUnpaid->installment_number ?? 1 }};
        const allUnpaidCount = {{ $allUnpaidCount }};
        const allUnpaidTotal = {{ (float) $allUnpaidTotal }};

        function handleOptionChange() {
            const selectedOption = document.querySelector('input[name="payment_option"]:checked')?.value;
            const customBox = document.getElementById('custom-months-box');
            const monthsText = document.getElementById('summary-months-text');
            const totalText = document.getElementById('summary-total-text');

            // Update border highlight on option cards
            document.querySelectorAll('.payment-option-card').forEach(card => {
                const radio = card.querySelector('input[type="radio"]');
                const indicator = card.querySelector('.check-indicator');
                if (radio && radio.checked) {
                    card.classList.add('border-[#085C54]', 'bg-emerald-50/30', 'ring-2', 'ring-[#085C54]/20');
                    card.classList.remove('border-slate-200');
                    if (indicator) {
                        indicator.classList.add('bg-[#085C54]', 'border-[#085C54]');
                        indicator.classList.remove('border-slate-300');
                    }
                } else {
                    card.classList.remove('border-[#085C54]', 'bg-emerald-50/30', 'ring-2', 'ring-[#085C54]/20');
                    card.classList.add('border-slate-200');
                    if (indicator) {
                        indicator.classList.remove('bg-[#085C54]', 'border-[#085C54]');
                        indicator.classList.add('border-slate-300');
                    }
                }
            });

            if (selectedOption === 'next_month') {
                if (customBox) customBox.classList.add('hidden');
                monthsText.textContent = `Bulan ke-${firstUnpaidMonth} (1 Bulan)`;
                totalText.textContent = 'Rp ' + Math.round(monthlyAmount).toLocaleString('id-ID');
            } else if (selectedOption === 'pay_all') {
                if (customBox) customBox.classList.add('hidden');
                monthsText.textContent = `Pelunasan Seluruh Sisa (${allUnpaidCount} Bulan Sekaligus)`;
                totalText.textContent = 'Rp ' + Math.round(allUnpaidTotal).toLocaleString('id-ID');
            } else if (selectedOption === 'custom_months') {
                if (customBox) customBox.classList.remove('hidden');
                updateCustomMonthsCalculation();
            }
        }

        function handleMonthCheckboxChange(changedEl) {
            const checkboxes = Array.from(document.querySelectorAll('.month-checkbox'));
            const changedIndex = parseInt(changedEl.getAttribute('data-index'));

            // Memastikan pemilihan berurutan (mirip Shopee & Pegadaian: tidak bisa melewati bulan yang belum dibayar)
            if (changedEl.checked) {
                // Centang semua bulan sebelumnya jika belum dicentang
                for (let i = 0; i < changedIndex; i++) {
                    checkboxes[i].checked = true;
                }
            } else {
                // Hapus centang semua bulan setelahnya jika di-uncheck
                for (let i = changedIndex + 1; i < checkboxes.length; i++) {
                    checkboxes[i].checked = false;
                }
            }

            updateCustomMonthsCalculation();
        }

        function updateCustomMonthsCalculation() {
            const checkedBoxes = Array.from(document.querySelectorAll('.month-checkbox:checked'));
            const monthsText = document.getElementById('summary-months-text');
            const totalText = document.getElementById('summary-total-text');

            if (checkedBoxes.length === 0) {
                // Auto check the first one if none selected
                const firstBox = document.querySelector('.month-checkbox');
                if (firstBox) {
                    firstBox.checked = true;
                    return updateCustomMonthsCalculation();
                }
            }

            const selectedMonths = checkedBoxes.map(cb => parseInt(cb.value)).sort((a, b) => a - b);
            const count = selectedMonths.length;
            const total = selectedMonths.reduce((sum, m) => sum + monthlyAmount, 0);

            if (count === 1) {
                monthsText.textContent = `Bulan ke-${selectedMonths[0]} (1 Bulan)`;
            } else if (count === 2) {
                monthsText.textContent = `Bulan ke-${selectedMonths[0]} & ke-${selectedMonths[1]} (2 Bulan Sekaligus)`;
            } else {
                monthsText.textContent = `Bulan ke-${selectedMonths[0]} s/d ke-${selectedMonths[count - 1]} (${count} Bulan Sekaligus)`;
            }

            totalText.textContent = 'Rp ' + Math.round(total).toLocaleString('id-ID');
        }

        function handlePaymentMethodChange() {
            const select = document.getElementById('payment_method_select');
            if (!select) return;

            const selectedOpt = select.options[select.selectedIndex];
            const bankName = selectedOpt.getAttribute('data-bank') || selectedOpt.getAttribute('data-name') || 'Transfer Bank';
            const accNo = selectedOpt.getAttribute('data-acc-no') || '-';
            const accHolder = selectedOpt.getAttribute('data-acc-holder') || 'Toko Emas Sinar Baru II';
            const instructions = selectedOpt.getAttribute('data-instructions') || 'Silakan transfer sesuai total nominal.';

            document.getElementById('card-bank-name').textContent = bankName;
            document.getElementById('card-acc-number').textContent = accNo;
            document.getElementById('card-acc-holder').textContent = accHolder;
            document.getElementById('card-instructions').textContent = instructions;
        }

        function copyAccountNumber() {
            const accNo = document.getElementById('card-acc-number').textContent.trim();
            if (accNo && accNo !== '-') {
                navigator.clipboard.writeText(accNo).then(() => {
                    const btnText = document.getElementById('copy-btn-text');
                    btnText.textContent = 'Tersalin!';
                    setTimeout(() => { btnText.textContent = 'Salin'; }, 2000);
                });
            }
        }

        function previewProofImage(event) {
            const input = event.target;
            const preview = document.getElementById('proof-preview');
            const placeholder = document.getElementById('no-preview-text');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function openProofModal(imgUrl, code) {
            document.getElementById('modal-proof-img').src = imgUrl;
            document.getElementById('modal-proof-title').textContent = 'Bukti Transfer: ' + code;
            document.getElementById('proofModal').classList.remove('hidden');
        }

        function closeProofModal() {
            document.getElementById('proofModal').classList.add('hidden');
        }

        function openReceiptModal(data) {
            document.getElementById('receipt-code').textContent = data.code;
            document.getElementById('receipt-date').textContent = data.date;
            document.getElementById('receipt-customer').textContent = data.customer;
            document.getElementById('receipt-product').textContent = data.product;
            document.getElementById('receipt-gold').textContent = `${data.purity} • ${data.weight}`;
            document.getElementById('receipt-months').textContent = data.months;
            document.getElementById('receipt-method').textContent = data.method;
            document.getElementById('receipt-amount').textContent = data.amount;

            let statusLabel = '⏳ Menunggu Verifikasi Admin';
            if (data.status === 'verified') statusLabel = '✅ Pembayaran Terverifikasi & Sah';
            else if (data.status === 'rejected') statusLabel = '❌ Pembayaran Ditolak';
            document.getElementById('receipt-status').textContent = statusLabel;

            document.getElementById('receiptModal').classList.remove('hidden');
        }

        function closeReceiptModal() {
            document.getElementById('receiptModal').classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', () => {
            handleOptionChange();
            handlePaymentMethodChange();
        });
    </script>
</x-customer-app>
