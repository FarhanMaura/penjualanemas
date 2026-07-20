<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Reservation;
use App\Models\User;
use App\Models\GoldPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'month'); // month | quarter | year
        $startDate = match($period) {
            'week'    => now()->startOfWeek(),
            'month'   => now()->startOfMonth(),
            'quarter' => now()->startOfQuarter(),
            'year'    => now()->startOfYear(),
            default   => now()->startOfMonth(),
        };

        // Transaksi Summary
        $transactions = Transaction::where('created_at', '>=', $startDate);
        $totalRevenue    = (clone $transactions)->where('status', 'completed')->sum('total_amount');
        $totalTrx        = (clone $transactions)->count();
        $purchaseCount   = (clone $transactions)->where('type', 'purchase')->count();
        $buybackCount    = (clone $transactions)->where('type', 'buyback')->count();

        // Reservasi Summary
        $reservationStats = [
            'total'     => Reservation::where('created_at', '>=', $startDate)->count(),
            'confirmed' => Reservation::where('created_at', '>=', $startDate)->where('status', 'confirmed')->count(),
            'cancelled' => Reservation::where('created_at', '>=', $startDate)->where('status', 'cancelled')->count(),
        ];

        // New Customers
        $newCustomers = User::where('role', 'customer')->where('created_at', '>=', $startDate)->count();
        $totalCustomers = User::where('role', 'customer')->count();

        // Revenue per hari (7 hari terakhir) untuk chart
        $dailyRevenue = Transaction::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total')
            )
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        // Isi hari yang kosong dengan 0
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = now()->subDays($i)->toDateString();
            $chartData[$d] = (float) ($dailyRevenue[$d] ?? 0);
        }

        // Transaksi terbaru
        $recentTransactions = Transaction::with(['user', 'items.product'])
            ->where('status', 'completed')
            ->latest()
            ->take(8)
            ->get();

        // Harga emas terbaru
        $latestGoldPrice = GoldPrice::latest('price_date')->first();

        return view('admin.reports.index', compact(
            'period', 'totalRevenue', 'totalTrx', 'purchaseCount', 'buybackCount',
            'reservationStats', 'newCustomers', 'totalCustomers',
            'chartData', 'recentTransactions', 'latestGoldPrice'
        ));
    }
}
