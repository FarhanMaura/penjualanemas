<x-admin-app>
    <x-slot name="pageTitle">Detail Pengajuan Tawar Harga</x-slot>

    <div class="max-w-4xl mx-auto">
        <a href="{{ route('admin.negotiations.index') }}" class="inline-flex items-center gap-2 text-xs text-amber-400 hover:underline mb-6">
            ← Kembali ke Daftar Pengajuan Tawar Harga
        </a>

        @if(session('success'))
        <div class="glass rounded-xl p-4 mb-6 border-green-500/30 text-green-400 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-xl">✅</span>
                <p class="text-sm font-medium">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="glass rounded-xl p-4 mb-6 border-red-500/30 text-red-400 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-xl">⚠️</span>
                <p class="text-sm font-medium">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        {{-- Main Detail Card --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            {{-- Left Column: Negotiation Summary --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="glass rounded-2xl p-6 sm:p-8 space-y-6">
                    <div class="flex justify-between items-start pb-4 border-b border-white/10">
                        <div>
                            <span class="text-xs text-gray-400">Kode Pengajuan</span>
                            <h3 class="text-xl font-mono font-bold text-amber-400">{{ $negotiation->negotiation_code }}</h3>
                            <p class="text-xs text-gray-500 mt-1">Diajukan pada {{ $negotiation->created_at->format('d M Y H:i WIB') }}</p>
                        </div>
                        <div>
                            @if($negotiation->status === 'pending')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                    ⏳ Menunggu Konfirmasi Admin
                                </span>
                            @elseif($negotiation->status === 'approved')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-500/10 text-green-400 border border-green-500/20">
                                    ✅ Disetujui
                                </span>
                            @elseif($negotiation->status === 'rejected')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-500/10 text-red-400 border border-red-500/20">
                                    ❌ Ditolak
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- User & Product Details --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Informasi Pelanggan</h4>
                            <p class="text-sm font-semibold text-white">{{ $negotiation->user->name ?? 'Unknown' }}</p>
                            <p class="text-xs text-gray-400">{{ $negotiation->user->email ?? '' }}</p>
                            <p class="text-xs text-gray-400">No. HP: {{ $negotiation->user->profile?->phone ?? '-' }}</p>
                        </div>
                        <div class="space-y-2">
                            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Detail Produk</h4>
                            <p class="text-sm font-semibold text-white">{{ $negotiation->product->name ?? '-' }}</p>
                            <p class="text-xs text-gray-400">Berat: {{ number_format($negotiation->product->weight_gram ?? 0, 3) }}g • Kategori: {{ $negotiation->product->category->name ?? '-' }}</p>
                            <p class="text-xs text-gray-400">Jumlah Dipesan (Qty): {{ $negotiation->quantity }}</p>
                        </div>
                    </div>

                    {{-- Comparison Price Cards --}}
                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-white/10">
                        <div class="glass rounded-xl p-4 bg-white/5">
                            <p class="text-xs text-gray-400">Harga Original Total</p>
                            <p class="text-lg font-bold text-gray-300">Rp {{ number_format($negotiation->original_price, 0, ',', '.') }}</p>
                        </div>
                        <div class="glass rounded-xl p-4 bg-amber-500/10 border-amber-500/30">
                            <p class="text-xs text-amber-300">Harga Penawaran Pembeli</p>
                            <p class="text-xl font-extrabold text-amber-400">Rp {{ number_format($negotiation->offered_price, 0, ',', '.') }}</p>
                            @php $selisih = $negotiation->original_price - $negotiation->offered_price; @endphp
                            @if($selisih > 0)
                            <p class="text-xs text-green-400 mt-0.5">Nawar turun: Rp {{ number_format($selisih, 0, ',', '.') }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- Customer Notes --}}
                    @if($negotiation->notes)
                    <div class="pt-4 border-t border-white/10">
                        <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Catatan Pembeli</h4>
                        <div class="glass rounded-xl p-3.5 text-sm text-gray-300 italic">
                            "{{ $negotiation->notes }}"
                        </div>
                    </div>
                    @endif

                    {{-- Previous Response if Processed --}}
                    @if($negotiation->status !== 'pending')
                    <div class="pt-4 border-t border-white/10 space-y-2">
                        <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Hasil Respon Admin</h4>
                        <div class="glass rounded-xl p-4 bg-white/5 space-y-2 text-sm">
                            @if($negotiation->status === 'approved')
                            <p class="text-green-400 font-semibold">Harga Kesepakatan Final: Rp {{ number_format($negotiation->agreed_price, 0, ',', '.') }}</p>
                            @endif
                            @if($negotiation->admin_notes)
                            <p class="text-gray-300 italic">Catatan Admin: "{{ $negotiation->admin_notes }}"</p>
                            @endif
                            <p class="text-xs text-gray-500">Diproses oleh {{ $negotiation->respondedByAdmin->name ?? 'Admin' }} pada {{ $negotiation->responded_at?->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Right Column: Admin Action Forms --}}
            <div class="space-y-6">
                @if($negotiation->status === 'pending')
                {{-- Form Disetujui / Counter Price --}}
                <div class="glass rounded-2xl p-6 border-green-500/30">
                    <h3 class="text-base font-bold text-green-400 flex items-center gap-2 mb-4">
                        <span>✅</span> Setujui Penawaran
                    </h3>
                    <form method="POST" action="{{ route('admin.negotiations.approve', $negotiation) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-gray-300 mb-1">
                                Tentukan Harga Disetujui (Rp) <span class="text-red-400">*</span>
                            </label>
                            <input type="number" name="agreed_price" value="{{ old('agreed_price', $negotiation->offered_price) }}" required step="1000" min="10000"
                                   class="w-full rounded-xl px-4 py-2.5 text-sm text-white font-bold outline-none focus:ring-2 focus:ring-green-500"
                                   style="background:rgba(255,255,255,0.06); border:1px solid rgba(34,197,94,0.3);">
                            <p class="text-xs text-gray-400 mt-1">Anda bisa menyetujui di Rp {{ number_format($negotiation->offered_price, 0, ',', '.') }} atau memberikan harga kontra lain.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-300 mb-1">
                                Catatan Admin untuk Pembeli (Opsional)
                            </label>
                            <textarea name="admin_notes" rows="2" placeholder="Misal: Penawaran disetujui, harap lakukan reservasi dalam 24 jam..."
                                      class="w-full rounded-xl px-4 py-2.5 text-sm text-white outline-none focus:ring-2 focus:ring-green-500"
                                      style="background:rgba(255,255,255,0.06); border:1px solid rgba(34,197,94,0.3);"></textarea>
                        </div>

                        <button type="submit" class="w-full py-2.5 rounded-xl text-sm font-bold text-gray-950 bg-green-400 hover:bg-green-300 transition shadow">
                            ✔ Setujui Harga Ini
                        </button>
                    </form>
                </div>

                {{-- Form Tolak Penawaran --}}
                <div class="glass rounded-2xl p-6 border-red-500/30">
                    <h3 class="text-base font-bold text-red-400 flex items-center gap-2 mb-4">
                        <span>❌</span> Tolak Penawaran
                    </h3>
                    <form method="POST" action="{{ route('admin.negotiations.reject', $negotiation) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-gray-300 mb-1">
                                Alasan Penolakan (Opsional)
                            </label>
                            <textarea name="admin_notes" rows="2" placeholder="Misal: Maaf harga belum dapat diberikan karena harga modal emas naik..."
                                      class="w-full rounded-xl px-4 py-2.5 text-sm text-white outline-none focus:ring-2 focus:ring-red-500"
                                      style="background:rgba(255,255,255,0.06); border:1px solid rgba(239,68,68,0.3);"></textarea>
                        </div>

                        <button type="submit" class="w-full py-2.5 rounded-xl text-sm font-bold text-white bg-red-600 hover:bg-red-500 transition shadow"
                                onclick="return confirm('Apakah Anda yakin ingin menolak pengajuan tawar harga ini?')">
                            ✖ Tolak Penawaran
                        </button>
                    </form>
                </div>
                @else
                <div class="glass rounded-2xl p-6 text-center">
                    <span class="text-4xl">🔒</span>
                    <p class="text-sm font-semibold text-gray-300 mt-2">Pengajuan Telah Diproses</p>
                    <p class="text-xs text-gray-500 mt-1">Status pengajuan ini sudah final dan tidak dapat diubah lagi.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-app>
