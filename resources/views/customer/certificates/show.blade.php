<x-customer-app>
    <x-slot name="pageTitle">Detail Surat Emas Digital</x-slot>
    <x-slot name="breadcrumb">Bukti kepemilikan dan jaminan keaslian perhiasan emas Anda</x-slot>

    <div class="mb-6">
        <a href="{{ route('customer.certificates.index') }}" class="text-sm text-yellow-400 hover:underline">← Kembali ke Daftar Surat</a>
    </div>

    <div class="flex justify-center mb-8">
        {{-- Premium Certificate Container --}}
        <div class="w-full max-w-2xl bg-[#1e1a32] rounded-3xl p-8 relative overflow-hidden"
             style="border: 8px double rgba(245, 158, 11, 0.3); box-shadow: 0 10px 40px rgba(0,0,0,0.5);">
            
            {{-- Watermark / Background Graphics --}}
            <div class="absolute inset-0 pointer-events-none opacity-5 flex items-center justify-center">
                <span class="text-9xl font-bold" style="font-family:'Playfair Display',serif;">SB</span>
            </div>

            {{-- Certificate Header --}}
            <div class="text-center border-b border-yellow-500/25 pb-6 mb-6">
                <div class="w-12 h-12 gold-gradient rounded-xl flex items-center justify-center font-bold text-white text-base mx-auto mb-3 shadow-lg">SB</div>
                <h2 class="text-2xl font-bold text-yellow-400 tracking-wide uppercase" style="font-family:'Playfair Display',serif;">Toko Emas Sinar Baru II</h2>
                <p class="text-xs text-gray-400 mt-1">Jaminan Keaslian Emas & Perhiasan Terpercaya Sejak 1995</p>
                <p class="text-xs text-gray-500">Jl. Contoh No. 123, Kota Anda • Telp: +62 812-3456-7890</p>
            </div>

            <div class="text-center mb-8">
                <h3 class="text-lg font-bold text-white uppercase tracking-widest" style="font-family:'Playfair Display',serif;">Surat Bukti Keaslian</h3>
                <p class="text-xs text-yellow-500/80 font-mono mt-1">No: {{ $certificate->certificate_number }}</p>
            </div>

            {{-- Certificate Body / Table --}}
            <div class="space-y-4 max-w-md mx-auto mb-8 text-sm">
                <div class="flex justify-between border-b border-white/5 py-2">
                    <span class="text-gray-400">Nama Pemilik</span>
                    <span class="text-white font-medium">{{ $certificate->user->name }}</span>
                </div>
                <div class="flex justify-between border-b border-white/5 py-2">
                    <span class="text-gray-400">Tanggal Terbit</span>
                    <span class="text-white font-medium">{{ $certificate->issued_at->isoFormat('D MMMM Y') }}</span>
                </div>

                @if($certificate->transaction && $certificate->transaction->transactionItems->isNotEmpty())
                    @php $item = $certificate->transaction->transactionItems->first(); @endphp
                    <div class="flex justify-between border-b border-white/5 py-2">
                        <span class="text-gray-400">Nama Perhiasan</span>
                        <span class="text-white font-medium">{{ $item->product->name ?? 'Produk' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-white/5 py-2">
                        <span class="text-gray-400">Kadar Emas (Karat)</span>
                        <span class="text-yellow-400 font-bold">{{ $item->product->gold_purity ?? '18K' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-white/5 py-2">
                        <span class="text-gray-400">Berat Bersih (Gram)</span>
                        <span class="text-white font-medium">{{ number_format($item->product->weight_gram ?? 0, 3) }} gram</span>
                    </div>
                @else
                    <p class="text-xs text-gray-500 italic text-center py-4">Data pembelian perhiasan tidak lengkap.</p>
                @endif

                <div class="flex justify-between border-b border-white/5 py-2">
                    <span class="text-gray-400">Status Validitas</span>
                    <span class="font-bold {{ $certificate->is_valid ? 'text-green-400' : 'text-red-400' }}">
                        {{ $certificate->is_valid ? 'SAH / ASLI' : 'TIDAK VALID' }}
                    </span>
                </div>
            </div>

            {{-- Footer Verification / Signatures --}}
            <div class="grid grid-cols-2 gap-6 items-center pt-6 border-t border-yellow-500/20">
                {{-- QR Verification --}}
                <div class="flex flex-col items-center">
                    {{-- Simulated QR Code Box --}}
                    <div class="w-24 h-24 bg-white p-2 rounded-xl flex items-center justify-center shadow-lg relative">
                        {{-- Draw a mock QR using simple SVG grid for premium look --}}
                        <svg class="w-full h-full text-black" viewBox="0 0 100 100">
                            <rect width="100" height="100" fill="white" />
                            <rect x="5" y="5" width="25" height="25" fill="black" />
                            <rect x="10" y="10" width="15" height="15" fill="white" />
                            <rect x="70" y="5" width="25" height="25" fill="black" />
                            <rect x="75" y="10" width="15" height="15" fill="white" />
                            <rect x="5" y="70" width="25" height="25" fill="black" />
                            <rect x="10" y="75" width="15" height="15" fill="white" />
                            {{-- random noise --}}
                            <rect x="40" y="10" width="10" height="10" fill="black" />
                            <rect x="50" y="25" width="10" height="10" fill="black" />
                            <rect x="35" y="45" width="15" height="15" fill="black" />
                            <rect x="60" y="50" width="10" height="15" fill="black" />
                            <rect x="45" y="70" width="15" height="10" fill="black" />
                            <rect x="75" y="40" width="15" height="15" fill="black" />
                            <rect x="80" y="75" width="10" height="10" fill="black" />
                        </svg>
                    </div>
                    <span class="text-[9px] text-gray-500 mt-2 tracking-widest uppercase">Pindai untuk Verifikasi</span>
                </div>

                {{-- Signature / Cap --}}
                <div class="text-center">
                    <p class="text-xs text-gray-400">Penanggung Jawab,</p>
                    <div class="h-16 flex items-center justify-center relative">
                        {{-- Simulated Stamp --}}
                        <div class="absolute w-20 h-10 border border-red-500/30 rounded-full flex items-center justify-center text-[10px] text-red-500/35 font-bold uppercase rotate-12">
                            SINAR BARU II
                        </div>
                        <span class="font-playfair text-xl italic text-yellow-400/80 font-bold select-none rotate-3">H. Sulaiman</span>
                    </div>
                    <p class="text-xs text-white font-medium border-t border-white/10 pt-1.5 inline-block px-4">H. Sulaiman, M.M.</p>
                    <p class="text-[9px] text-gray-500 mt-0.5">Pemilik Toko</p>
                </div>
            </div>

        </div>
    </div>
</x-customer-app>
