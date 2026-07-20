<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\InstallmentPlan;
use Illuminate\Http\Request;

class InstallmentController extends Controller
{
    public function index()
    {
        $installments = InstallmentPlan::whereHas('transaction', fn($q) => $q->where('user_id', auth()->id()))
            ->with(['transaction.items.product', 'payments'])
            ->latest()
            ->paginate(10);

        $summary = [
            'active'    => InstallmentPlan::whereHas('transaction', fn($q) => $q->where('user_id', auth()->id()))
                            ->where('status', 'active')->count(),
            'completed' => InstallmentPlan::whereHas('transaction', fn($q) => $q->where('user_id', auth()->id()))
                            ->where('status', 'completed')->count(),
        ];

        return view('customer.installments.index', compact('installments', 'summary'));
    }

    public function show(InstallmentPlan $installmentPlan)
    {
        abort_if($installmentPlan->transaction->user_id !== auth()->id(), 403);
        $installmentPlan->load(['transaction.items.product', 'payments']);
        return view('customer.installments.show', compact('installmentPlan'));
    }
}
