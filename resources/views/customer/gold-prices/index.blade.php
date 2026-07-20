<x-customer-app>
    <x-slot name="pageTitle">Harga Emas Real-time</x-slot>
    <x-slot name="breadcrumb">Pantau fluktuasi harga emas harian untuk transaksi Anda</x-slot>

    {{-- Today's Price Widget --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        @if($todayPrice)
            <div class="glass rounded-2xl p-6 flex flex-col justify-between" style="border-left: 4px solid #10b981;">
                <div>
                    <span class="text-xs text-green-400 font-semibold tracking-wider uppercase">Harga Beli Toko (Buyback)</span>
                    <h3 class="text-gray-400 text-sm mt-1">Harga saat toko membeli kembali emas Anda</h3>
                </div>
                <div class="mt-6">
                    <p class="text-4xl font-extrabold text-white">Rp {{ number_format($todayPrice->buy_price_per_gram, 0, ',', '.') }}<span class="text-sm font-normal text-gray-400"> / gram</span></p>
                    <p class="text-xs text-gray-500 mt-2">Kadar Murni: 24 Karat (99.9%)</p>
                </div>
            </div>

            <div class="glass rounded-2xl p-6 flex flex-col justify-between" style="border-left: 4px solid #f59e0b;">
                <div>
                    <span class="text-xs text-yellow-400 font-semibold tracking-wider uppercase">Harga Jual Toko</span>
                    <h3 class="text-gray-400 text-sm mt-1">Harga saat Anda membeli perhiasan/emas dari toko</h3>
                </div>
                <div class="mt-6">
                    <p class="text-4xl font-extrabold text-yellow-400">Rp {{ number_format($todayPrice->sell_price_per_gram, 0, ',', '.') }}<span class="text-sm font-normal text-gray-400"> / gram</span></p>
                    <p class="text-xs text-gray-500 mt-2">Kadar Murni: 24 Karat (99.9%)</p>
                </div>
            </div>
        @else
            <div class="col-span-2 glass rounded-2xl p-8 text-center">
                <span class="text-4xl">⏳</span>
                <h3 class="text-lg font-semibold mt-2">Harga Emas Sedang Diperbarui</h3>
                <p class="text-gray-400 text-sm mt-1">Hubungi kontak toko untuk mengetahui harga terupdate secara langsung.</p>
            </div>
        @endif
    </div>

    @if($todayPrice)
    <div class="glass rounded-2xl p-4 mb-8 flex items-center justify-between text-xs text-gray-400">
        <span>📅 Terakhir Diperbarui: {{ \Carbon\Carbon::parse($todayPrice->price_date)->isoFormat('dddd, D MMMM Y') }}</span>
        <span>📡 Sumber Data: <strong class="text-yellow-500">{{ $todayPrice->source }}</strong></span>
    </div>
    @endif

    {{-- Historical Table --}}
    <div class="glass rounded-2xl p-6">
        <h3 class="text-lg font-semibold text-yellow-400 mb-6">📅 Riwayat Harga Emas (30 Hari Terakhir)</h3>
        
        @if($history->isEmpty())
            <div class="text-center py-10">
                <p class="text-gray-500 text-sm">Belum ada data riwayat harga emas.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-xs font-semibold text-gray-400 uppercase border-b border-white/10">
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4 text-right">Harga Beli (/gram)</th>
                            <th class="py-3 px-4 text-right">Harga Jual (/gram)</th>
                            <th class="py-3 px-4 text-center">Sumber</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-sm">
                        @foreach($history as $price)
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="py-3.5 px-4 font-medium text-white">
                                    {{ \Carbon\Carbon::parse($price->price_date)->isoFormat('D MMMM Y') }}
                                </td>
                                <td class="py-3.5 px-4 text-right text-green-400 font-semibold">
                                    Rp {{ number_format($price->buy_price_per_gram, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right text-yellow-400 font-semibold">
                                    Rp {{ number_format($price->sell_price_per_gram, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center text-xs text-gray-500">
                                    {{ $price->source }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6">
                {{ $history->links() }}
            </div>
        @endif
    </div>
</x-customer-app>
