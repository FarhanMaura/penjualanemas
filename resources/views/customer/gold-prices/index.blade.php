<x-customer-app>
    <x-slot name="pageTitle">Harga Emas Real-time</x-slot>
    <x-slot name="breadcrumb">Pantau fluktuasi harga emas harian untuk transaksi Anda</x-slot>

    {{-- Today's Price Widget --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        @if($todayPrice)
            <div class="glass rounded-2xl p-6 flex flex-col justify-between bg-emerald-50/70 border border-emerald-200 shadow-md">
                <div>
                    <span class="text-xs text-emerald-900 font-extrabold tracking-wider uppercase bg-emerald-100 px-3 py-1 rounded-full border border-emerald-300">Harga Beli Toko (Buyback)</span>
                    <h3 class="text-slate-700 text-sm mt-3 font-medium">Harga saat toko membeli kembali emas dari Anda</h3>
                </div>
                <div class="mt-6">
                    <p class="text-4xl font-black text-[#085C54] font-playfair">Rp {{ number_format($todayPrice->buy_price_per_gram, 0, ',', '.') }}<span class="text-sm font-semibold text-slate-600"> / gram</span></p>
                    <p class="text-xs text-emerald-800 font-semibold mt-2">Kadar Murni: 24 Karat (99.9%)</p>
                </div>
            </div>

            <div class="glass rounded-2xl p-6 flex flex-col justify-between bg-amber-50/70 border border-amber-200 shadow-md">
                <div>
                    <span class="text-xs text-amber-900 font-extrabold tracking-wider uppercase bg-amber-100 px-3 py-1 rounded-full border border-amber-300">Harga Jual Toko</span>
                    <h3 class="text-slate-700 text-sm mt-3 font-medium">Harga saat Anda membeli perhiasan/emas dari toko</h3>
                </div>
                <div class="mt-6">
                    <p class="text-4xl font-black text-[#C6A443] font-playfair">Rp {{ number_format($todayPrice->sell_price_per_gram, 0, ',', '.') }}<span class="text-sm font-semibold text-slate-600"> / gram</span></p>
                    <p class="text-xs text-amber-900 font-semibold mt-2">Kadar Murni: 24 Karat (99.9%)</p>
                </div>
            </div>
        @else
            <div class="col-span-2 glass rounded-2xl p-8 text-center bg-white border border-[#e8e3d5] shadow-md">
                <span class="text-4xl">⏳</span>
                <h3 class="text-lg font-bold text-slate-900 mt-2">Harga Emas Sedang Diperbarui</h3>
                <p class="text-slate-600 text-sm mt-1">Hubungi kontak toko untuk mengetahui harga terupdate secara langsung.</p>
            </div>
        @endif
    </div>

    @if($todayPrice)
    <div class="glass rounded-2xl p-4 mb-8 flex items-center justify-between text-xs font-bold text-slate-700 bg-white border border-[#e8e3d5] shadow-sm">
        <span>📅 Terakhir Diperbarui: {{ \Carbon\Carbon::parse($todayPrice->price_date)->isoFormat('dddd, D MMMM Y') }}</span>
        <span>📡 Sumber Data: <strong class="text-[#085C54]">{{ $todayPrice->source }}</strong></span>
    </div>
    @endif

    {{-- Historical Table --}}
    <div class="glass rounded-2xl p-6 bg-white border border-[#e8e3d5] shadow-md">
        <h3 class="text-lg font-bold text-[#042623] font-playfair mb-6">📅 Riwayat Harga Emas (30 Hari Terakhir)</h3>
        
        @if($history->isEmpty())
            <div class="text-center py-10">
                <p class="text-slate-500 text-sm font-medium">Belum ada data riwayat harga emas.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[600px]">
                    <thead>
                        <tr class="text-xs font-bold text-slate-700 uppercase bg-[#F4EDD9]/60 border-b border-[#e8e3d5]">
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4 text-right">Harga Beli (/gram)</th>
                            <th class="py-3 px-4 text-right">Harga Jual (/gram)</th>
                            <th class="py-3 px-4 text-center">Sumber</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-800">
                        @foreach($history as $price)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    {{ \Carbon\Carbon::parse($price->price_date)->isoFormat('D MMMM Y') }}
                                </td>
                                <td class="py-3.5 px-4 text-right text-[#085C54] font-extrabold">
                                    Rp {{ number_format($price->buy_price_per_gram, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right text-[#C6A443] font-extrabold">
                                    Rp {{ number_format($price->sell_price_per_gram, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center text-xs font-semibold text-slate-600">
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
