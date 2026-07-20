<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['items.product'])
            ->where('user_id', auth()->id())
            ->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $transactions = $query->paginate(10)->withQueryString();

        $summary = [
            'total'    => Transaction::where('user_id', auth()->id())->count(),
            'purchase' => Transaction::where('user_id', auth()->id())->where('type', 'purchase')
                            ->where('status', 'completed')->sum('total_amount'),
            'income'   => Transaction::where('user_id', auth()->id())->where('type', 'buyback')
                            ->where('status', 'completed')->sum('total_amount'),
        ];

        return view('customer.transactions.index', compact('transactions', 'summary'));
    }

    public function show(Transaction $transaction)
    {
        abort_if($transaction->user_id !== auth()->id(), 403);
        $transaction->load(['items.product', 'reservation']);
        return view('customer.transactions.show', compact('transaction'));
    }
}
