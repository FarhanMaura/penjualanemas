<x-admin-app>
    <x-slot name="pageTitle">Tambah Metode Pembayaran & Rekening</x-slot>
    <x-slot name="breadcrumb">Daftarkan akun bank atau metode pembayaran baru toko</x-slot>

    <div class="max-w-3xl mx-auto">
        <a href="{{ route('admin.payment-methods.index') }}" class="text-xs font-bold text-[#085C54] hover:underline mb-6 inline-flex items-center gap-1">
            ← Kembali ke Daftar Metode Pembayaran
        </a>

        <div class="glass rounded-3xl p-6 sm:p-8 bg-white border border-[#e8e3d5] shadow-lg">
            <div class="flex items-center gap-3.5 mb-6 pb-4 border-b border-slate-200">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl shadow-md gold-gradient border border-[#C6A443]">
                    💳
                </div>
                <div>
                    <h2 class="text-xl font-bold font-playfair text-slate-900">Form Tambah Metode Pembayaran</h2>
                    <p class="text-xs text-slate-500 font-medium">Lengkapi rincian nama bank, nomor rekening, dan nama pemilik rekening toko.</p>
                </div>
            </div>

            @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-300 shadow-sm">
                <p class="text-sm text-red-900 font-bold mb-2">Mohon periksa kesalahan input:</p>
                <ul class="list-disc list-inside text-xs text-red-800 font-medium space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('admin.payment-methods.store') }}" method="POST" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="input-label">Nama Metode Pembayaran <span class="text-red-600">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="cth: Transfer Bank BCA" required class="input-field font-bold">
                    </div>
                    <div>
                        <label class="input-label">Kode Unik (Opsional)</label>
                        <input type="text" name="code" value="{{ old('code') }}" placeholder="cth: bca (otomatis jika kosong)" class="input-field font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="input-label">Tipe Metode <span class="text-red-600">*</span></label>
                        <select name="type" required class="input-field font-bold cursor-pointer">
                            <option value="bank_transfer" {{ old('type') == 'bank_transfer' ? 'selected' : '' }}>🏦 Transfer Bank</option>
                            <option value="cash" {{ old('type') == 'cash' ? 'selected' : '' }}>💵 Tunai di Toko (Cash)</option>
                            <option value="qris" {{ old('type') == 'qris' ? 'selected' : '' }}>📱 QRIS / E-Wallet</option>
                            <option value="debit" {{ old('type') == 'debit' ? 'selected' : '' }}>💳 Kartu Debit</option>
                            <option value="credit" {{ old('type') == 'credit' ? 'selected' : '' }}>💳 Kartu Kredit</option>
                        </select>
                    </div>
                    <div>
                        <label class="input-label">Nama Bank / Institusi</label>
                        <input type="text" name="bank_name" value="{{ old('bank_name') }}" placeholder="cth: Bank Central Asia" class="input-field">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-5 rounded-2xl bg-[#F4EDD9]/40 border border-[#e8e3d5]">
                    <div>
                        <label class="input-label">Nomor Rekening / No. Akun</label>
                        <input type="text" name="account_number" value="{{ old('account_number') }}" placeholder="cth: 8820 9182 34" class="input-field font-mono font-bold text-slate-900">
                        <p class="text-[11px] text-slate-500 mt-1">Kosongkan jika metode Tunai / Cash.</p>
                    </div>
                    <div>
                        <label class="input-label">Nama Pemilik Rekening (Atas Nama)</label>
                        <input type="text" name="account_name" value="{{ old('account_name') }}" placeholder="cth: TOKO EMAS SINAR BARU II" class="input-field font-bold text-slate-900">
                    </div>
                </div>

                <div>
                    <label class="input-label">Instruksi / Catatan Pembayaran</label>
                    <textarea name="instructions" rows="3" placeholder="cth: Harap sertakan kode transaksi pada berita transfer dan tunjukkan bukti transfer saat datang ke toko..." class="input-field">{{ old('instructions') }}</textarea>
                </div>

                <div class="flex items-center justify-between p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <div>
                        <p class="font-bold text-sm text-slate-900">Aktifkan Metode Pembayaran</p>
                        <p class="text-xs text-slate-500">Tampilkan pilihan ini di form reservasi dan transaksi pelanggan.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#085C54]"></div>
                    </label>
                </div>

                <div class="mt-8 flex gap-4 pt-2">
                    <button type="submit" class="flex-1 py-3.5 rounded-xl font-extrabold text-[#042623] gold-gradient border border-[#C6A443] shadow-md hover:brightness-110 transition text-sm">
                        💾 Simpan Metode Pembayaran
                    </button>
                    <a href="{{ route('admin.payment-methods.index') }}" class="px-6 py-3.5 rounded-xl font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition text-center text-sm shadow-sm">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-admin-app>
