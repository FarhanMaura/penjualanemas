<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerReward;
use App\Models\RewardRedemption;

class RewardController extends Controller
{
    public function index()
    {
        $totalPoints = CustomerReward::sum('current_points');
        $activeCount = CustomerReward::where('current_points', '>', 0)->count();
        $usedCount   = RewardRedemption::count();

        $recentRedemptions = RewardRedemption::with(['user'])
            ->latest()
            ->take(10)
            ->get();

        $tierConfig = [
            ['icon'=>'🥉','name'=>'Bronze',   'range'=>'0–4 transaksi',   'benefit'=>'Akses dasar',               'color'=>'#b45309'],
            ['icon'=>'🥈','name'=>'Silver',   'range'=>'5–9 transaksi',   'benefit'=>'Prioritas reservasi',       'color'=>'#9ca3af'],
            ['icon'=>'🥇','name'=>'Gold',     'range'=>'10–19 transaksi', 'benefit'=>'Diskon + Cuci Emas GRATIS', 'color'=>'#f59e0b'],
            ['icon'=>'💎','name'=>'Platinum', 'range'=>'20+ transaksi',   'benefit'=>'VIP service & diskon max',  'color'=>'#60a5fa'],
        ];

        return view('admin.rewards.index', compact(
            'totalPoints', 'activeCount', 'usedCount', 'recentRedemptions', 'tierConfig'
        ));
    }
}
