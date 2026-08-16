<x-customer-app>
    <x-slot name="pageTitle">Surat Emas Digital</x-slot>
    <x-slot name="breadcrumb">Daftar sertifikat keaslian digital perhiasan emas Anda</x-slot>

    <div class="glass rounded-2xl p-6 bg-white border border-[#e8e3d5] shadow-md">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-[#042623] font-playfair">📜 Sertifikat Keaslian Digital Saya</h3>
            <span class="text-xs text-slate-500 font-semibold">Menampilkan surat resmi dari Toko Emas Sinar Baru II</span>
        </div>

        @if($certificates->isEmpty())
            <div class="text-center py-20">
                <span class="text-6xl">📜</span>
                <p class="text-slate-800 font-bold text-base mt-4">Belum ada Surat Emas Digital diterbitkan.</p>
                <p class="text-slate-600 text-xs mt-1 font-medium">Sertifikat akan terbit otomatis setelah transaksi pembelian perhiasan Anda selesai diproses admin.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($certificates as $cert)
                    <div class="glass rounded-2xl p-5 flex flex-col justify-between hover:border-[#085C54] transition duration-300 relative overflow-hidden bg-amber-50/40 border border-[#e8e3d5] shadow-sm">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-xs font-mono font-bold text-[#085C54] tracking-wider uppercase bg-[#e2f2f0] px-2 py-0.5 rounded border border-[#085C54]/20">No: {{ $cert->certificate_number }}</span>
                                <h4 class="font-bold text-slate-900 text-base mt-2">Sertifikat Keaslian Perhiasan</h4>
                                <p class="text-xs text-slate-500 font-semibold mt-1">Diterbitkan: {{ $cert->issued_at->isoFormat('D MMM Y') }}</p>
                            </div>
                            <span class="text-xs px-2.5 py-0.5 rounded-full font-bold border {{ $cert->is_valid ? 'bg-emerald-100 text-emerald-900 border-emerald-300' : 'bg-red-100 text-red-900 border-red-300' }}">
                                {{ $cert->is_valid ? '✔ Valid' : '✘ Tidak Valid' }}
                            </span>
                        </div>

                        <div class="mt-5 pt-4 border-t border-slate-200 text-sm text-slate-800 space-y-1">
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Item Pembelian:</p>
                            @if($cert->transaction && $cert->transaction->items->isNotEmpty())
                                @foreach($cert->transaction->items as $item)
                                    <div class="flex justify-between text-xs mt-1">
                                        <span class="font-bold text-slate-900">{{ $item->product_name ?? ($item->product->name ?? 'Produk') }}</span>
                                        <span class="text-slate-600 font-semibold">{{ $item->gold_purity ?? ($item->product->gold_purity ?? '24K') }} • {{ number_format($item->weight_gram ?? ($item->product->weight_gram ?? 0), 3) }}g</span>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-xs text-slate-500 italic">Data item tidak tersedia</p>
                            @endif
                        </div>

                        <div class="mt-6 flex gap-3">
                            <a href="{{ route('customer.certificates.show', $cert->id) }}"
                               class="flex-1 text-center py-2.5 text-xs font-extrabold text-[#042623] gold-gradient rounded-xl hover:brightness-110 transition shadow border border-[#C6A443]">
                                Lihat Detail Surat →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-6">
                {{ $certificates->links() }}
            </div>
        @endif
    </div>
</x-customer-app>
