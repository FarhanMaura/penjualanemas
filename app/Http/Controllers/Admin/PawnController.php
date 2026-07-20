<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pawn;
use Illuminate\Http\Request;

class PawnController extends Controller
{
    public function index(Request $request)
    {
        $query = Pawn::with(['transaction.user'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pawns = $query->paginate(20)->withQueryString();

        $stats = [
            'active'      => Pawn::where('status', 'active')->count(),
            'redeemed'    => Pawn::where('status', 'redeemed')->count(),
            'overdue'     => Pawn::where('status', 'active')->whereDate('due_date', '<', today())->count(),
            'total_loans' => Pawn::where('status', 'active')->sum('loan_amount'),
        ];

        return view('admin.pawns.index', compact('pawns', 'stats'));
    }

    public function show(Pawn $pawn)
    {
        $pawn->load(['transaction.user.profile']);
        return view('admin.pawns.show', compact('pawn'));
    }

    public function redeem(Request $request, Pawn $pawn)
    {
        abort_if($pawn->status !== 'active', 400, 'Gadai ini tidak aktif.');

        $request->validate([
            'redemption_amount' => ['required', 'numeric', 'min:0'],
            'notes'             => ['nullable', 'string', 'max:500'],
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $pawn) {
            $pawn->update([
                'status'            => 'redeemed',
                'redemption_date'   => now(),
                'redemption_amount' => $request->redemption_amount,
                'notes'             => $request->notes,
            ]);

            // Update associated transaction status to completed
            $transaction = $pawn->transaction;
            $transaction->update(['status' => 'completed']);
        });

        return back()->with('success', 'Gadai berhasil ditebus.');
    }
}
