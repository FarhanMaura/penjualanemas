<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Transaction;
use App\Services\GoldPriceService;
use App\Services\RewardService;

class DashboardController extends Controller
{
    public function __construct(
        private GoldPriceService $goldPriceService,
        private RewardService $rewardService
    ) {}

    public function index()
    {
        $user      = auth()->user();
        $goldPrice = $this->goldPriceService->getTodayPrice();
        $reward    = $this->rewardService->getRewardSummary($user);

        $activeReservations = Reservation::where('user_id', $user->id)
            ->whereIn('status', ['pending','confirmed'])
            ->with('product')
            ->latest()
            ->take(3)
            ->get();

        $recentTransactions = Transaction::where('user_id', $user->id)
            ->with('items.product')
            ->latest()
            ->take(3)
            ->get();

        $activeInstallments = Transaction::where('user_id', $user->id)
            ->where('type', 'installment')
            ->where('status', 'in_progress')
            ->count();

        $activePawns = Transaction::where('user_id', $user->id)
            ->where('type', 'pawn')
            ->where('status', 'in_progress')
            ->count();

        return view('customer.dashboard', compact(
            'goldPrice', 'reward', 'activeReservations',
            'recentTransactions', 'activeInstallments', 'activePawns'
        ));
    }
}
