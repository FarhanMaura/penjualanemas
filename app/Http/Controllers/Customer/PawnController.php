<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Pawn;
use Illuminate\Http\Request;

class PawnController extends Controller
{
    public function index()
    {
        $pawns = Pawn::whereHas('transaction', fn($q) => $q->where('user_id', auth()->id()))
            ->with(['transaction'])
            ->latest()
            ->paginate(10);

        $summary = [
            'active'    => Pawn::whereHas('transaction', fn($q) => $q->where('user_id', auth()->id()))
                            ->where('status', 'active')->count(),
            'redeemed'  => Pawn::whereHas('transaction', fn($q) => $q->where('user_id', auth()->id()))
                            ->where('status', 'redeemed')->count(),
            'total_loan'=> Pawn::whereHas('transaction', fn($q) => $q->where('user_id', auth()->id()))
                            ->where('status', 'active')->sum('loan_amount'),
        ];

        return view('customer.pawns.index', compact('pawns', 'summary'));
    }

    public function show(Pawn $pawn)
    {
        abort_if($pawn->transaction->user_id !== auth()->id(), 403);
        $pawn->load(['transaction']);
        return view('customer.pawns.show', compact('pawn'));
    }
}
