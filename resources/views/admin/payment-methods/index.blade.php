<x-admin-app>
    <x-slot name="pageTitle">Metode Pembayaran</x-slot>
    <x-slot name="breadcrumb">Kelola metode pembayaran dan nomor rekening toko</x-slot>

    <div class="space-y-6">
        @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 font-bold text-sm shadow-sm flex items-center gap-2">
            <span>✅</span> {{ session('success') }}
        </div>
        @endif

        {{-- Header & Action Button --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-3xl border border-[#e8e3d5] shadow-sm">
            <div>
                <h2 class="text-xl font-bold font-playfair text-slate-900">Daftar Rekening & Metode Pembayaran</h2>
                <p class="text-xs text-slate-500 mt-1">Atur rekening bank, nomor akun, dan metode pembayaran yang dapat dipilih customer saat reservasi & cicilan.</p>
            </div>
            <a href="{{ route('admin.payment-methods.create') }}" class="px-5 py-2.5 rounded-xl font-extrabold text-sm text-[#042623] gold-gradient border border-[#C6A443] shadow-md hover:brightness-110 transition flex items-center gap-2">
                <span>➕</span> Tambah Rekening / Metode
            </a>
        </div>

        {{-- Table List --}}
        <div class="bg-white rounded-3xl border border-[#e8e3d5] shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 bg-[#F4EDD9]/40 text-xs font-bold text-slate-700 uppercase tracking-wider">
                            <th class="py-4 px-6">Metode / Bank</th>
                            <th class="py-4 px-6">Tipe</th>
                            <th class="py-4 px-6">Nomor Rekening</th>
                            <th class="py-4 px-6">Atas Nama (A.N.)</th>
                            <th class="py-4 px-6 text-center">Status</th>
                            <th class="py-4 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($paymentMethods as $pm)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-lg border border-slate-200 shrink-0">
                                        @if($pm->type === 'bank_transfer') 🏦
                                        @elseif($pm->type === 'cash') 💵
                                        @elseif($pm->type === 'qris') 📱
                                        @else 💳 @endif
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900">{{ $pm->name }}</p>
                                        <p class="text-[11px] font-mono text-slate-500">Kode: {{ $pm->code }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 font-medium text-slate-600">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                    {{ $pm->type === 'bank_transfer' ? 'bg-blue-50 text-blue-800 border border-blue-200' :
                                       ($pm->type === 'cash' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' :
                                       ($pm->type === 'qris' ? 'bg-purple-50 text-purple-800 border border-purple-200' : 'bg-slate-100 text-slate-800 border border-slate-200')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $pm->type)) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 font-mono font-bold text-slate-800">
                                {{ $pm->account_number ?: '—' }}
                            </td>
                            <td class="py-4 px-6 font-medium text-slate-700">
                                {{ $pm->account_name ?: '—' }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($pm->is_active)
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                    ✔ Aktif
                                </span>
                                @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-500 border border-slate-300">
                                    Nonaktif
                                </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.payment-methods.edit', $pm) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold text-[#085C54] bg-[#e2f2f0] hover:bg-[#cbeae6] transition border border-[#085C54]/20">
                                        ✏ Edit Rekening
                                    </a>
                                    <form action="{{ route('admin.payment-methods.destroy', $pm) }}" method="POST" onsubmit="return confirm('Hapus metode pembayaran {{ $pm->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-red-700 bg-red-50 hover:bg-red-100 transition border border-red-200">
                                            🗑
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-slate-500">
                                <p class="text-3xl mb-2">💳</p>
                                <p class="font-bold text-base text-slate-700">Belum ada metode pembayaran yang terdaftar</p>
                                <p class="text-xs text-slate-500 mt-1">Silakan tambahkan nomor rekening atau metode pembayaran baru.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-app>
