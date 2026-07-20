<x-customer-app>
    <x-slot name="pageTitle">Surat Emas Digital</x-slot>
    <x-slot name="breadcrumb">Daftar sertifikat keaslian digital perhiasan emas Anda</x-slot>

    <div class="glass rounded-2xl p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-yellow-400">📜 Sertifikat Keaslian Digital Saya</h3>
            <span class="text-xs text-gray-400">Menampilkan surat resmi dari Toko Emas Sinar Baru II</span>
        </div>

        @if($certificates->isEmpty())
            <div class="text-center py-20">
                <span class="text-6xl">📜</span>
                <p class="text-gray-400 text-base mt-4">Belum ada Surat Emas Digital diterbitkan.</p>
                <p class="text-gray-600 text-xs mt-1">Sertifikat akan terbit otomatis setelah transaksi pembelian perhiasan Anda selesai diproses admin.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($certificates as $cert)
                    <div class="glass rounded-2xl p-5 flex flex-col justify-between hover:border-yellow-500/40 transition duration-300 relative overflow-hidden"
                         style="background: linear-gradient(135deg, rgba(255,255,255,0.02) 0%, rgba(245,158,11,0.02) 100%);">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-xs font-mono text-yellow-400 tracking-wider uppercase">No: {{ $cert->certificate_number }}</span>
                                <h4 class="font-bold text-white text-base mt-1.5">Sertifikat Keaslian Perhiasan</h4>
                                <p class="text-xs text-gray-500 mt-1">Diterbitkan: {{ $cert->issued_at->isoFormat('D MMM Y') }}</p>
                            </div>
                            <span class="text-xs px-2.5 py-0.5 rounded-full font-semibold {{ $cert->is_valid ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                {{ $cert->is_valid ? '✔ Valid' : '✘ Tidak Valid' }}
                            </span>
                        </div>

                        <div class="mt-5 pt-4 border-t border-white/5 text-sm text-gray-300 space-y-1">
                            <p class="text-xs text-gray-500">Item Pembelian:</p>
                            @if($cert->transaction && $cert->transaction->transactionItems->isNotEmpty())
                                @foreach($cert->transaction->transactionItems as $item)
                                    <div class="flex justify-between text-xs mt-1">
                                        <span class="font-medium text-white">{{ $item->product->name ?? 'Produk' }}</span>
                                        <span class="text-gray-400">{{ $item->product->gold_purity ?? '24K' }} • {{ number_format($item->product->weight_gram ?? 0, 3) }}g</span>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-xs text-gray-500 italic">Data item tidak tersedia</p>
                            @endif
                        </div>

                        <div class="mt-6 flex gap-3">
                            <a href="{{ route('customer.certificates.show', $cert->id) }}"
                               class="flex-1 text-center py-2 text-xs font-semibold text-white orange-gradient rounded-xl hover:opacity-90 transition">
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
