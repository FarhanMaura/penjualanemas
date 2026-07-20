<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstallmentPlan;
use App\Models\InstallmentPayment;
use Illuminate\Http\Request;

class InstallmentController extends Controller
{
    public function __construct(
        private \App\Services\RewardService $rewardService,
        private \App\Services\CertificateService $certificateService
    ) {}

    public function index(Request $request)
    {
        $query = InstallmentPlan::with(['transaction.user', 'transaction.items.product', 'payments'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $installments = $query->paginate(20)->withQueryString();

        $stats = [
            'active'    => InstallmentPlan::where('status', 'active')->count(),
            'completed' => InstallmentPlan::where('status', 'completed')->count(),
            'overdue'   => InstallmentPlan::where('status', 'overdue')->count(),
            'revenue'   => InstallmentPayment::where('status', 'paid')->sum('amount_paid'),
        ];

        return view('admin.installments.index', compact('installments', 'stats'));
    }

    public function show(InstallmentPlan $installmentPlan)
    {
        $installmentPlan->load(['transaction.user.profile', 'transaction.items.product', 'payments']);
        return view('admin.installments.show', compact('installmentPlan'));
    }

    public function recordPayment(Request $request, InstallmentPlan $installmentPlan, InstallmentPayment $installmentPayment)
    {
        abort_if($installmentPayment->installment_plan_id !== $installmentPlan->id, 404);

        if ($installmentPayment->isPaid()) {
            return back()->with('error', 'Cicilan ini sudah dibayar.');
        }

        $request->validate([
            'payment_method' => ['required', 'in:cash,transfer,debit,credit'],
            'amount_paid'    => ['required', 'numeric', 'min:0'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $installmentPlan, $installmentPayment) {
            $installmentPayment->update([
                'status'         => 'paid',
                'paid_date'      => now(),
                'amount_paid'    => $request->amount_paid,
                'payment_method' => $request->payment_method,
                'received_by'    => auth()->id(),
                'notes'          => $request->notes,
            ]);

            // Check if all payments of the plan are now paid
            $remainingPending = $installmentPlan->payments()->where('status', '!=', 'paid')->count();
            if ($remainingPending === 0) {
                // Mark plan as completed
                $installmentPlan->update(['status' => 'completed']);

                // Mark transaction as completed
                $transaction = $installmentPlan->transaction;
                $transaction->update(['status' => 'completed']);

                // Award points
                $this->rewardService->awardPoint($transaction->user, $transaction);

                // Issue certificate
                $this->certificateService->generateForTransaction($transaction);
            }
        });

        return back()->with('success', 'Pembayaran angsuran ke-' . $installmentPayment->installment_number . ' berhasil dicatat.');
    }
}
