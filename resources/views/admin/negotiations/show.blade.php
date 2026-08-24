<x-admin-app>
    <x-slot name="pageTitle">Detail Pengajuan Tawar Harga</x-slot>

    <div class="max-w-4xl mx-auto">
        <a href="{{ route('admin.negotiations.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-[#085C54] hover:underline mb-6">
            ← Kembali ke Daftar Pengajuan Tawar Harga
        </a>

        @if(session('success'))
        <div class="rounded-xl p-4 mb-6 bg-emerald-50 border border-emerald-300 text-emerald-900 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <span class="text-xl">✅</span>
                <p class="text-sm font-bold">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="rounded-xl p-4 mb-6 bg-red-50 border border-red-300 text-red-900 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <span class="text-xl">⚠️</span>
                <p class="text-sm font-bold">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        {{-- Main Detail Card --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            {{-- Left Column: Negotiation Summary --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="glass rounded-2xl p-6 sm:p-8 space-y-6 bg-white border border-[#e8e3d5] shadow-lg">
                    <div class="flex justify-between items-start pb-4 border-b border-slate-200">
                        <div>
                            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Kode Pengajuan</span>
                            <h3 class="text-xl font-mono font-bold text-[#085C54]">{{ $negotiation->negotiation_code }}</h3>
                            <p class="text-xs text-slate-500 font-medium mt-1">Diajukan pada {{ $negotiation->created_at->format('d M Y H:i WIB') }}</p>
                        </div>
                        <div>
                            @if($negotiation->status === 'pending')
                                <span class="px-3 py-1.5 rounded-full text-xs font-extrabold bg-amber-100 text-amber-900 border border-amber-300">
                                    ⏳ Menunggu Konfirmasi
                                </span>
                            @elseif($negotiation->status === 'approved')
                                <span class="px-3 py-1.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-900 border border-emerald-300">
                                    ✅ Disetujui
                                </span>
                            @elseif($negotiation->status === 'rejected')
                                <span class="px-3 py-1.5 rounded-full text-xs font-extrabold bg-red-100 text-red-900 border border-red-300">
                                    ❌ Ditolak
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- User & Product Details --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Informasi Pelanggan</h4>
                            <p class="text-base font-bold text-slate-900">{{ $negotiation->user->name ?? 'Unknown' }}</p>
                            <p class="text-xs text-slate-600 font-medium">{{ $negotiation->user->email ?? '' }}</p>
                            <p class="text-xs text-slate-600 font-medium">No. HP: {{ $negotiation->user->profile?->phone ?? '-' }}</p>
                        </div>
                        <div class="space-y-1.5">
                            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Detail Produk</h4>
                            <p class="text-base font-bold text-slate-900">{{ $negotiation->product->name ?? '-' }}</p>
                            <p class="text-xs text-slate-600 font-medium">Berat: {{ number_format($negotiation->product->weight_gram ?? 0, 3) }}g • Kategori: {{ $negotiation->product->category->name ?? '-' }}</p>
                            <p class="text-xs text-slate-600 font-medium">Jumlah Dipesan (Qty): <strong class="text-slate-900">{{ $negotiation->quantity }}</strong></p>
                        </div>
                    </div>

                    {{-- Comparison Price Cards --}}
                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-200">
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Harga Normal Total</p>
                            <p class="text-lg font-bold text-slate-700">Rp {{ number_format($negotiation->original_price, 0, ',', '.') }}</p>
                        </div>
                        <div class="p-4 rounded-xl bg-amber-50 border border-amber-200">
                            <p class="text-xs text-amber-900 font-bold uppercase tracking-wider">Penawaran Pembeli</p>
                            <p class="text-xl font-extrabold text-[#C6A443]">Rp {{ number_format($negotiation->offered_price, 0, ',', '.') }}</p>
                            @php $selisih = $negotiation->original_price - $negotiation->offered_price; @endphp
                            @if($selisih > 0)
                            <p class="text-xs text-emerald-700 font-bold mt-0.5">Turun: Rp {{ number_format($selisih, 0, ',', '.') }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- Customer Notes --}}
                    @if($negotiation->notes)
                    <div class="pt-4 border-t border-slate-200">
                        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Catatan Pembeli</h4>
                        <div class="p-3.5 rounded-xl text-sm text-slate-800 bg-slate-50 border border-slate-200 italic font-medium">
                            "{{ $negotiation->notes }}"
                        </div>
                    </div>
                    @endif

                    {{-- Previous Response if Processed --}}
                    @if($negotiation->status !== 'pending')
                    <div class="pt-4 border-t border-slate-200 space-y-2">
                        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Hasil Respon Admin</h4>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-1.5 text-sm">
                            @if($negotiation->status === 'approved')
                            <p class="text-emerald-800 font-extrabold">Harga Kesepakatan Final: Rp {{ number_format($negotiation->agreed_price, 0, ',', '.') }}</p>
                            @endif
                            @if($negotiation->admin_notes)
                            <p class="text-slate-700 italic font-medium">Catatan Admin: "{{ $negotiation->admin_notes }}"</p>
                            @endif
                            <p class="text-xs text-slate-500 font-medium">Diproses oleh {{ $negotiation->respondedByAdmin->name ?? 'Admin' }} pada {{ $negotiation->responded_at?->format('d M Y H:i') }} WIB</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Right Column: Admin Action Forms --}}
            <div class="space-y-6">
                @if($negotiation->status === 'pending')
                {{-- Form Disetujui / Counter Price --}}
                <div class="glass rounded-2xl p-6 bg-white border border-emerald-300 shadow-md">
                    <h3 class="text-base font-bold text-emerald-900 flex items-center gap-2 mb-4">
                        <span>✅</span> Setujui Penawaran
                    </h3>
                    <form method="POST" action="{{ route('admin.negotiations.approve', $negotiation) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Tentukan Harga Disetujui (Rp) <span class="text-red-600">*</span>
                            </label>
                            <input type="number" name="agreed_price" value="{{ old('agreed_price', $negotiation->offered_price) }}" required step="1000" min="10000"
                                   class="input-field font-extrabold text-slate-900">
                            <p class="text-xs text-slate-500 font-medium mt-1">Bisa disetujui di Rp {{ number_format($negotiation->offered_price, 0, ',', '.') }} atau tawar balik.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Catatan Admin untuk Pembeli (Opsional)
                            </label>
                            <textarea name="admin_notes" rows="2" placeholder="Misal: Penawaran disetujui, harap lakukan reservasi dalam 24 jam..."
                                      class="input-field text-sm"></textarea>
                        </div>

                        <button type="submit" class="w-full py-2.5 rounded-xl text-sm font-extrabold text-white bg-emerald-700 hover:bg-emerald-800 transition shadow-md">
                            ✔ Setujui Harga Ini
                        </button>
                    </form>
                </div>

                {{-- Form Tolak Penawaran --}}
                <div class="glass rounded-2xl p-6 bg-white border border-red-300 shadow-md">
                    <h3 class="text-base font-bold text-red-900 flex items-center gap-2 mb-4">
                        <span>❌</span> Tolak Penawaran
                    </h3>
                    <form method="POST" action="{{ route('admin.negotiations.reject', $negotiation) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Alasan Penolakan (Opsional)
                            </label>
                            <textarea name="admin_notes" rows="2" placeholder="Misal: Maaf harga belum dapat diberikan..."
                                      class="input-field text-sm"></textarea>
                        </div>

                        <button type="submit" class="w-full py-2.5 rounded-xl text-sm font-bold text-white bg-red-600 hover:bg-red-700 transition shadow"
                                onclick="return confirm('Apakah Anda yakin ingin menolak pengajuan tawar harga ini?')">
                            ✖ Tolak Penawaran
                        </button>
                    </form>
                </div>
                @else
                <div class="glass rounded-2xl p-6 text-center bg-white border border-[#e8e3d5] shadow-sm">
                    <span class="text-4xl">🔒</span>
                    <p class="text-sm font-bold text-slate-900 mt-2">Pengajuan Telah Diproses</p>
                    <p class="text-xs text-slate-500 mt-1">Status pengajuan ini sudah final.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-app>

