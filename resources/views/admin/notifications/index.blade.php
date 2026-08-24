<x-admin-app>
    <x-slot name="pageTitle">Notifikasi Sistem</x-slot>
    <x-slot name="breadcrumb">Pemberitahuan aktivitas reservasi, negosiasi, dan transaksi pelanggan</x-slot>

    @if(session('success'))
    <div class="mb-6 p-4 rounded-xl text-sm font-semibold text-emerald-900 bg-emerald-50 border border-emerald-300 shadow-sm">
        ✅ {{ session('success') }}
    </div>
    @endif

    {{-- Filter & Action Bar --}}
    <div class="glass rounded-2xl p-4 mb-6 flex flex-wrap gap-4 items-center justify-between bg-white border border-[#e8e3d5] shadow-sm">
        <div class="flex items-center gap-3">
            <span class="text-sm font-bold text-slate-700">Filter:</span>
            <a href="{{ route('admin.notifications.index') }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ !request('filter') ? 'bg-[#085C54] text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                Semua
            </a>
            <a href="{{ route('admin.notifications.index', ['filter'=>'unread']) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ request('filter') === 'unread' ? 'bg-[#085C54] text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                Belum Dibaca @if($unreadCount > 0) <span class="ml-1 px-1.5 py-0.5 rounded-full bg-amber-500 text-slate-950 font-extrabold text-[10px]">{{ $unreadCount }}</span> @endif
            </a>
        </div>

        @if($unreadCount > 0)
        <form method="POST" action="{{ route('admin.notifications.mark-all-read') }}">
            @csrf
            <button type="submit" class="text-xs font-bold text-[#085C54] hover:underline flex items-center gap-1.5">
                <span>✓✓</span> Tandai Semua Dibaca
            </button>
        </form>
        @endif
    </div>

    {{-- Notification List --}}
    <div class="glass rounded-2xl overflow-hidden bg-white border border-[#e8e3d5] shadow-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#e8e3d5] bg-[#F4EDD9]/60">
            <h3 class="font-bold text-[#042623] font-playfair">🔔 Daftar Notifikasi Masuk</h3>
            <span class="text-xs text-slate-600 font-bold">{{ $notifications->total() }} pemberitahuan</span>
        </div>

        @if($notifications->isEmpty())
        <div class="text-center py-16">
            <p class="text-5xl mb-3">📭</p>
            <p class="text-slate-600 font-medium">Belum ada notifikasi.</p>
        </div>
        @else
        <div class="divide-y divide-slate-100">
            @foreach($notifications as $n)
            @php
                $isUnread = $n->isUnread();
                $icon = match(true) {
                    str_contains($n->type, 'reservation') => '📅',
                    str_contains($n->type, 'transaction') => '🧾',
                    str_contains($n->type, 'price')       => '🤝',
                    str_contains($n->type, 'pawn')        => '🏦',
                    str_contains($n->type, 'installment') => '💳',
                    default                               => '🔔',
                };
            @endphp
            <div class="p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 transition {{ $isUnread ? 'bg-amber-50/40 hover:bg-amber-50/70' : 'hover:bg-slate-50' }}">
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
                    <form method="POST" action="{{ route('admin.notifications.read', $n) }}">
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
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
            {{ $notifications->links() }}
        </div>
        @endif
    </div>
</x-admin-app>
