<x-customer-app>
    <x-slot name="pageTitle">Notifikasi</x-slot>
    <x-slot name="breadcrumb">Informasi penting mengenai transaksi, reservasi, dan reward Anda</x-slot>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="mb-6 px-4 py-3 rounded-xl text-sm font-semibold flex items-center gap-2 bg-emerald-50 border border-emerald-300 text-emerald-900 shadow-sm">
        ✅ {{ session('success') }}
    </div>
    @endif

    {{-- Header Action Bar --}}
    <div class="glass rounded-2xl p-4 mb-6 flex flex-wrap gap-4 items-center justify-between bg-white border border-[#e8e3d5] shadow-sm">
        <div class="flex items-center gap-3">
            <span class="text-sm font-bold text-slate-700">Filter:</span>
            <a href="{{ route('customer.notifications.index') }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ !request('filter') ? 'bg-[#085C54] text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                Semua Notifikasi
            </a>
            <a href="{{ route('customer.notifications.index', ['filter'=>'unread']) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ request('filter') === 'unread' ? 'bg-[#085C54] text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                Belum Dibaca @if($unreadCount > 0) <span class="ml-1 px-1.5 py-0.5 rounded-full bg-amber-500 text-slate-950 font-extrabold text-[10px]">{{ $unreadCount }}</span> @endif
            </a>
        </div>

        @if($unreadCount > 0)
        <form method="POST" action="{{ route('customer.notifications.mark-all-read') }}">
            @csrf
            <button type="submit" class="text-xs font-bold text-[#085C54] hover:underline flex items-center gap-1.5">
                <span>✓✓</span> Tandai Semua Dibaca
            </button>
        </form>
        @endif
    </div>

    {{-- Notifications List --}}
    @if($notifications->isEmpty())
    <div class="glass rounded-3xl p-12 text-center bg-white border border-[#e8e3d5] shadow-md">
        <span class="text-6xl">🔔</span>
        <p class="text-slate-900 text-lg mt-4 font-bold">Tidak Ada Notifikasi</p>
        <p class="text-slate-600 text-sm mt-2">Semua informasi terbaru seputar transaksi dan akun Anda akan muncul di sini.</p>
    </div>
    @else
    <div class="space-y-3 mb-8">
        @foreach($notifications as $n)
        @php
            $isUnread = $n->isUnread();
            $icon = match(true) {
                str_contains($n->type, 'reservation') => '📅',
                str_contains($n->type, 'transaction') => '🧾',
                str_contains($n->type, 'reward')      => '🎁',
                str_contains($n->type, 'pawn')        => '🏦',
                str_contains($n->type, 'installment') => '💳',
                str_contains($n->type, 'price')       => '🤝',
                default                               => '🔔',
            };
        @endphp
        <div class="glass rounded-2xl p-5 border transition {{ $isUnread ? 'bg-amber-50/50 border-amber-300 shadow-sm' : 'bg-white border-[#e8e3d5]' }} flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl shrink-0 {{ $isUnread ? 'bg-amber-100 border border-amber-300' : 'bg-slate-100 border border-slate-200' }}">
                    {{ $icon }}
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h4 class="text-base font-bold text-slate-900">{{ $n->title }}</h4>
                        @if($isUnread)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-500 text-slate-950 uppercase tracking-wider">Baru</span>
                        @endif
                    </div>
                    <p class="text-sm text-slate-700 font-medium mt-1 leading-relaxed">{{ $n->message }}</p>
                    <p class="text-xs text-slate-500 font-semibold mt-2">
                        {{ \Carbon\Carbon::parse($n->created_at)->diffForHumans() }} • {{ \Carbon\Carbon::parse($n->created_at)->isoFormat('D MMM Y, HH:mm') }} WIB
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                @if($isUnread)
                <form method="POST" action="{{ route('customer.notifications.read', $n) }}">
                    @csrf
                    <button type="submit" class="px-3.5 py-1.5 rounded-xl text-xs font-bold text-[#085C54] bg-[#e2f2f0] hover:bg-[#c9e8e4] border border-[#085C54]/20 transition shadow-sm">
                        Tandai Dibaca
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="flex justify-center">
        {{ $notifications->links() }}
    </div>
    @endif
</x-customer-app>
