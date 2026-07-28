<x-admin-app>
<x-slot name="pageTitle">Harga Emas</x-slot>

@if(session('success'))
<div class="flash-success" data-flash>✅ {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="flash-error" data-flash>❌ {{ session('error') }}</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 lg:gap-6 mb-6">
    {{-- Form input harga --}}
    <div class="col-span-1">
        <div class="glass rounded-2xl p-5">
            <h3 class="font-semibold text-yellow-400 mb-4">✏️ Input Harga Hari Ini</h3>
            <form method="POST" action="{{ route('admin.gold-prices.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="input-label">Tanggal</label>
                    <input type="date" name="price_date" value="{{ today()->toDateString() }}" class="input-field" required>
                </div>
                <div>
                    <label class="input-label">Harga Beli /gram (Rp) *</label>
                    <input type="number" name="buy_price_per_gram" value="{{ old('buy_price_per_gram', $today?->buy_price_per_gram) }}"
                           class="input-field" min="100000" placeholder="cth: 1580000" required>
                </div>
                <div>
                    <label class="input-label">Harga Jual /gram (Rp) *</label>
                    <input type="number" name="sell_price_per_gram" value="{{ old('sell_price_per_gram', $today?->sell_price_per_gram) }}"
                           class="input-field" min="100000" placeholder="cth: 1620000" required>
                </div>
                <div>
                    <label class="input-label">Sumber</label>
                    <input type="text" name="source" value="{{ old('source','Manual') }}" class="input-field" placeholder="ANTAM, Manual, dll.">
                </div>
                <div>
                    <label class="input-label">Catatan</label>
                    <textarea name="notes" rows="2" class="input-field" placeholder="Opsional..."></textarea>
                </div>
                <button type="submit" class="btn-gold w-full">Simpan Harga</button>
            </form>
            <div class="mt-3" style="border-top:1px solid rgba(255,255,255,0.06); padding-top:1rem;">
                <form method="POST" action="{{ route('admin.gold-prices.fetch') }}">
                    @csrf
                    <button type="submit" class="btn-ghost w-full">🌐 Auto-fetch dari API</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Tabel riwayat --}}
    <div class="col-span-1 md:col-span-2">
        <div class="glass rounded-2xl overflow-hidden">
            <div class="px-6 py-4" style="border-bottom:1px solid rgba(245,158,11,0.1);">
                <h3 class="font-semibold text-yellow-400">📊 Riwayat Harga Emas</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Harga Beli</th>
                            <th>Harga Jual</th>
                            <th>Spread</th>
                            <th>Sumber</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($prices as $p)
                        <tr class="{{ $p->price_date == today()->toDateString() ? 'bg-yellow-900/10' : '' }}">
                            <td class="text-gray-300 font-medium">
                                {{ \Carbon\Carbon::parse($p->price_date)->isoFormat('D MMM Y') }}
                                @if($p->price_date == today()->toDateString())
                                <span class="badge badge-yellow ml-1">Hari ini</span>
                                @endif
                            </td>
                            <td class="text-green-400 font-semibold">Rp {{ number_format($p->buy_price_per_gram,0,',','.') }}</td>
                            <td class="text-yellow-400 font-semibold">Rp {{ number_format($p->sell_price_per_gram,0,',','.') }}</td>
                            <td class="text-gray-400">Rp {{ number_format($p->sell_price_per_gram - $p->buy_price_per_gram,0,',','.') }}</td>
                            <td class="text-gray-500 text-xs">{{ $p->source }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-12 text-gray-500">Belum ada data harga.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4">{{ $prices->links() }}</div>
        </div>
    </div>
</div>
</x-admin-app>
