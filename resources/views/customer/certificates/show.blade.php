<x-customer-app>
    <x-slot name="pageTitle">Detail Surat Emas Digital</x-slot>
    <x-slot name="breadcrumb">Bukti kepemilikan dan jaminan keaslian perhiasan emas Anda</x-slot>

    <div class="mb-6">
        <a href="{{ route('customer.certificates.index') }}" class="text-xs font-bold text-[#085C54] hover:underline">← Kembali ke Daftar Surat</a>
    </div>

    <div class="flex justify-center mb-8">
        {{-- Certificate Container (Formal Certificate Ivory & Gold Style) --}}
        <div class="w-full max-w-2xl bg-white rounded-3xl p-8 relative overflow-hidden shadow-2xl border-4 border-[#C6A443]">
            
            {{-- Watermark --}}
            <div class="absolute inset-0 pointer-events-none opacity-5 flex items-center justify-center">
                <span class="text-9xl font-bold text-[#085C54]" style="font-family:'Playfair Display',serif;">SB</span>
            </div>

            {{-- Certificate Header --}}
            <div class="text-center border-b border-[#e8e3d5] pb-6 mb-6">
                <div class="w-14 h-14 gold-gradient rounded-2xl flex items-center justify-center font-extrabold text-[#042623] text-lg mx-auto mb-3 shadow-md border border-[#C6A443]">SB</div>
                <h2 class="text-2xl font-black text-[#042623] tracking-wide uppercase font-playfair">Toko Emas Sinar Baru II</h2>
                <p class="text-xs text-slate-600 font-semibold mt-1">Jaminan Keaslian Emas & Perhiasan Terpercaya Sejak 1995</p>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Teluk Lubuk, Muara Enim, Sumatera Selatan • WA/Telp Toko</p>
            </div>

            <div class="text-center mb-8">
                <h3 class="text-xl font-black text-[#085C54] uppercase tracking-widest font-playfair">Surat Bukti Keaslian</h3>
                <p class="text-xs text-[#866a20] font-mono font-bold mt-1 bg-[#F4EDD9] inline-block px-3 py-1 rounded-full border border-[#E3D193]">No: {{ $certificate->certificate_number }}</p>
            </div>

            {{-- Certificate Body / Table --}}
            <div class="space-y-4 max-w-md mx-auto mb-8 text-sm">
                <div class="flex justify-between border-b border-slate-200 py-2.5">
                    <span class="text-slate-600 font-semibold">Nama Pemilik</span>
                    <span class="text-slate-900 font-bold">{{ $certificate->user->name }}</span>
                </div>
                <div class="flex justify-between border-b border-slate-200 py-2.5">
                    <span class="text-slate-600 font-semibold">Tanggal Terbit</span>
                    <span class="text-slate-900 font-bold">{{ $certificate->issued_at->isoFormat('D MMMM Y') }}</span>
                </div>

                @if($certificate->transaction && $certificate->transaction->items->isNotEmpty())
                    @php $item = $certificate->transaction->items->first(); @endphp
                    <div class="flex justify-between border-b border-slate-200 py-2.5">
                        <span class="text-slate-600 font-semibold">Nama Perhiasan</span>
                        <span class="text-slate-900 font-bold">{{ $item->product_name ?? ($item->product->name ?? 'Produk') }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-200 py-2.5">
                        <span class="text-slate-600 font-semibold">Kadar Emas (Karat)</span>
                        <span class="text-[#085C54] font-extrabold">{{ $item->gold_purity ?? ($item->product->gold_purity ?? '24K') }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-200 py-2.5">
                        <span class="text-slate-600 font-semibold">Berat Bersih (Gram)</span>
                        <span class="text-slate-900 font-bold">{{ number_format($item->weight_gram ?? ($item->product->weight_gram ?? 0), 3) }} gram</span>
                    </div>
                @else
                    <p class="text-xs text-slate-500 italic text-center py-4">Data pembelian perhiasan tidak lengkap.</p>
                @endif

                <div class="flex justify-between border-b border-slate-200 py-2.5">
                    <span class="text-slate-600 font-semibold">Status Validitas</span>
                    <span class="font-extrabold {{ $certificate->is_valid ? 'text-emerald-700' : 'text-red-700' }}">
                        {{ $certificate->is_valid ? 'SAH / ASLI ✔' : 'TIDAK VALID ✘' }}
                    </span>
                </div>
            </div>

            {{-- Footer Verification / Signatures --}}
            <div class="grid grid-cols-2 gap-6 items-center pt-6 border-t border-[#e8e3d5]">
                {{-- QR Verification --}}
                <div class="flex flex-col items-center">
                    <div class="w-24 h-24 bg-white p-2 rounded-xl flex items-center justify-center shadow border border-slate-200 relative">
                        <svg class="w-full h-full text-slate-900" viewBox="0 0 100 100">
                            <rect width="100" height="100" fill="white" />
                            <rect x="5" y="5" width="25" height="25" fill="black" />
                            <rect x="10" y="10" width="15" height="15" fill="white" />
                            <rect x="70" y="5" width="25" height="25" fill="black" />
                            <rect x="75" y="10" width="15" height="15" fill="white" />
                            <rect x="5" y="70" width="25" height="25" fill="black" />
                            <rect x="10" y="75" width="15" height="15" fill="white" />
                            <rect x="40" y="10" width="10" height="10" fill="black" />
                            <rect x="50" y="25" width="10" height="10" fill="black" />
                            <rect x="35" y="45" width="15" height="15" fill="black" />
                            <rect x="60" y="50" width="10" height="15" fill="black" />
                            <rect x="45" y="70" width="15" height="10" fill="black" />
                            <rect x="75" y="40" width="15" height="15" fill="black" />
                            <rect x="80" y="75" width="10" height="10" fill="black" />
                        </svg>
                    </div>
                    <span class="text-[9px] font-bold text-slate-500 mt-2 tracking-widest uppercase">Pindai untuk Verifikasi</span>
                </div>

                {{-- Signature / Cap --}}
                <div class="text-center">
                    <p class="text-xs text-slate-600 font-semibold">Penanggung Jawab,</p>
                    <div class="h-16 flex items-center justify-center relative">
                        <div class="absolute w-24 h-12 border-2 border-red-600/40 rounded-full flex items-center justify-center text-[10px] text-red-600 font-extrabold uppercase rotate-12 bg-red-50/20">
                            SINAR BARU II
                        </div>
                        <span class="font-playfair text-xl italic text-[#042623] font-black select-none rotate-3">H. Sulaiman</span>
                    </div>
                    <p class="text-xs text-slate-900 font-bold border-t border-slate-300 pt-1.5 inline-block px-4">H. Sulaiman, M.M.</p>
                    <p class="text-[10px] text-slate-500 font-semibold mt-0.5">Pemilik Toko</p>
                </div>
            </div>

        </div>
    </div>
</x-customer-app>
