<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerReward;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'customer')
            ->with(['profile', 'customerReward']);

        if ($request->filled('tier')) {
            $query->whereHas('customerReward', fn($q) => $q->where('tier', $request->tier));
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) =>
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
            );
        }

        $customers = $query->latest()->paginate(20)->withQueryString();

        $tierCounts = [
            'bronze'   => CustomerReward::where('tier','bronze')->count(),
            'silver'   => CustomerReward::where('tier','silver')->count(),
            'gold'     => CustomerReward::where('tier','gold')->count(),
            'platinum' => CustomerReward::where('tier','platinum')->count(),
        ];

        return view('admin.customers.index', compact('customers', 'tierCounts'));
    }

    public function show(User $user)
    {
        abort_if($user->isAdmin(), 404);

        $user->load([
            'profile',
            'customerReward',
            'reservations.product',
            'transactions.items.product',
        ]);

        return view('admin.customers.show', compact('user'));
    }
}
