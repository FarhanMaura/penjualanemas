<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerReward;
use App\Models\Reservation;
use App\Models\Transaction;
use App\Models\User;
use App\Services\GoldPriceService;

class DashboardController extends Controller
{
    public function __construct(private GoldPriceService $goldPriceService) {}

    public function index()
    {
        $goldPrice = $this->goldPriceService->getTodayPrice();

        $stats = [
            'customers'          => User::where('role', 'customer')->count(),
            'reservations_today' => Reservation::whereDate('created_at', today())->count(),
            'pending_reservations'=> Reservation::where('status', 'pending')->count(),
            'transactions_month' => Transaction::whereMonth('created_at', now()->month)
                                        ->whereYear('created_at', now()->year)
                                        ->count(),
            'pawn_active'        => Transaction::where('type', 'pawn')
                                        ->where('status', 'in_progress')->count(),
        ];

        $recentReservations = Reservation::with(['user', 'product'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        $tierDistribution = [
            'bronze'   => CustomerReward::where('tier', 'bronze')->count(),
            'silver'   => CustomerReward::where('tier', 'silver')->count(),
            'gold'     => CustomerReward::where('tier', 'gold')->count(),
            'platinum' => CustomerReward::where('tier', 'platinum')->count(),
        ];

        return view('admin.dashboard', compact(
            'goldPrice', 'stats', 'recentReservations', 'tierDistribution'
        ));
    }
}
