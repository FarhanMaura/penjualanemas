<x-admin-app>
<x-slot name="pageTitle">Pelanggan</x-slot>

{{-- Search & Filter --}}
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div class="flex items-center gap-3">
        <div class="glass flex items-center gap-2 px-3 py-2 rounded-xl text-sm">
            <span class="text-gray-400">Tier:</span>
            <select class="bg-transparent text-white text-sm focus:outline-none cursor-pointer">
                <option value="">Semua</option>
                <option>Bronze</option>
                <option>Silver</option>
                <option>Gold</option>
                <option>Diamond</option>
            </select>
        </div>
    </div>
    <div class="flex items-center gap-3">
        <div class="glass flex items-center gap-2 px-3 py-2 rounded-xl">
            <span class="text-gray-400 text-sm">🔍</span>
            <input type="text" placeholder="Cari nama atau email..." class="bg-transparent text-sm text-white placeholder-gray-500 focus:outline-none w-52">
        </div>
        <button class="px-4 py-2 text-sm rounded-xl font-medium text-white transition" style="background:linear-gradient(135deg,#f97316,#ea580c);">
            + Tambah Pelanggan
        </button>
    </div>
</div>

{{-- Tier Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['tier'=>'🥉 Bronze','count'=>'0','range'=>'0–4 transaksi','color'=>'#b45309'],
        ['tier'=>'🥈 Silver','count'=>'0','range'=>'5–9 transaksi','color'=>'#9ca3af'],
        ['tier'=>'🥇 Gold','count'=>'0','range'=>'10–19 transaksi','color'=>'#f59e0b'],
        ['tier'=>'💎 Diamond','count'=>'0','range'=>'20+ transaksi','color'=>'#60a5fa'],
    ] as $t)
    <div class="glass rounded-2xl p-4">
        <p class="text-sm font-semibold" style="color:{{ $t['color'] }}">{{ $t['tier'] }}</p>
        <p class="text-3xl font-bold text-white mt-1">{{ $t['count'] }}</p>
        <p class="text-xs text-gray-500 mt-0.5">{{ $t['range'] }}</p>
    </div>
    @endforeach
</div>

{{-- Customer Table --}}
<div class="glass rounded-2xl overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4" style="border-bottom:1px solid rgba(245,158,11,0.1);">
        <h3 class="font-semibold text-yellow-400">👥 Data Pelanggan</h3>
        <span class="text-xs text-gray-500">0 pelanggan terdaftar</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[700px]">
            <thead>
                <tr class="text-xs text-gray-400 uppercase" style="border-bottom:1px solid rgba(255,255,255,0.05); background:rgba(255,255,255,0.02);">
                    <th class="text-left px-6 py-3">Pelanggan</th>
                    <th class="text-left px-6 py-3">No. HP</th>
                    <th class="text-left px-6 py-3">Tier</th>
                    <th class="text-left px-6 py-3">Total Transaksi</th>
                    <th class="text-left px-6 py-3">Bergabung</th>
                    <th class="text-left px-6 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach([
                    ['name'=>'Siti Rahayu','init'=>'SR','color'=>'bg-purple-700','email'=>'siti@gmail.com','phone'=>'+62 812-xxx-xxxx','tier'=>'Silver','tier_color'=>'text-gray-300','trx'=>'7','join'=>'Jan 2024'],
                    ['name'=>'Budi Wijaya','init'=>'BW','color'=>'bg-blue-700','email'=>'budi@gmail.com','phone'=>'+62 813-xxx-xxxx','tier'=>'Gold','tier_color'=>'text-yellow-400','trx'=>'12','join'=>'Mar 2023'],
                    ['name'=>'Ani Kusuma','init'=>'AK','color'=>'bg-green-700','email'=>'ani@gmail.com','phone'=>'+62 856-xxx-xxxx','tier'=>'Bronze','tier_color'=>'text-amber-600','trx'=>'2','join'=>'Jun 2024'],
                ] as $c)
                <tr class="hover:bg-white/5 transition" style="border-bottom:1px solid rgba(255,255,255,0.04);">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 {{ $c['color'] }} rounded-full flex items-center justify-center text-sm font-bold">{{ $c['init'] }}</div>
                            <div>
                                <p class="font-medium text-white">{{ $c['name'] }}</p>
                                <p class="text-xs text-gray-500">{{ $c['email'] }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-400">{{ $c['phone'] }}</td>
                    <td class="px-6 py-4"><span class="font-semibold {{ $c['tier_color'] }}">{{ $c['tier'] }}</span></td>
                    <td class="px-6 py-4 text-white font-bold">{{ $c['trx'] }}</td>
                    <td class="px-6 py-4 text-gray-500">{{ $c['join'] }}</td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <button class="text-xs px-3 py-1.5 rounded-lg font-medium" style="background:rgba(245,158,11,0.15); color:#fbbf24; border:1px solid rgba(245,158,11,0.3);">Detail</button>
                            <button class="text-xs px-3 py-1.5 rounded-lg font-medium" style="background:rgba(255,255,255,0.05); color:#9ca3af; border:1px solid rgba(255,255,255,0.1);">Riwayat</button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

</x-admin-app>
