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
        $query = InstallmentPlan::whereHas('transaction', fn($q) => $q->where('type', 'installment'))
            ->with(['transaction.user', 'transaction.items.product', 'payments'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $installments = $query->paginate(20)->withQueryString();

        $stats = [
            'active'    => InstallmentPlan::whereHas('transaction', fn($q) => $q->where('type', 'installment'))->where('status', 'active')->count(),
            'completed' => InstallmentPlan::whereHas('transaction', fn($q) => $q->where('type', 'installment'))->where('status', 'completed')->count(),
            'overdue'   => InstallmentPlan::whereHas('transaction', fn($q) => $q->where('type', 'installment'))->where('status', 'overdue')->count(),
            'revenue'   => InstallmentPayment::whereHas('installmentPlan.transaction', fn($q) => $q->where('type', 'installment'))->where('status', 'paid')->sum('amount_paid'),
        ];

        return view('admin.installments.index', compact('installments', 'stats'));
    }

    public function show(InstallmentPlan $installmentPlan)
    {
        $installmentPlan->load([
            'transaction.user.profile',
            'transaction.items.product',
            'payments',
            'installmentTransactions.verifier'
        ]);
        $paymentMethods = \App\Models\PaymentMethod::active()->ordered()->get();
        $unpaidPayments = $installmentPlan->payments()->whereIn('status', ['pending', 'overdue'])->orderBy('installment_number')->get();

        return view('admin.installments.show', compact('installmentPlan', 'paymentMethods', 'unpaidPayments'));
    }

    public function verifyPayment(\App\Models\InstallmentTransaction $installmentTransaction)
    {
        if ($installmentTransaction->status !== 'waiting_verification') {
            return back()->with('error', 'Transaksi pembayaran ini sudah tidak dalam status menunggu verifikasi.');
        }

        $installmentPlan = $installmentTransaction->installmentPlan;

        \Illuminate\Support\Facades\DB::transaction(function () use ($installmentTransaction, $installmentPlan) {
            $installmentTransaction->update([
                'status'      => 'verified',
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ]);

            // Ambil pembayaran angsuran terkait
            $payments = $installmentPlan->payments()
                ->whereIn('installment_number', (array) $installmentTransaction->months_paid)
                ->get();

            foreach ($payments as $payment) {
                $payment->update([
                    'status'         => 'paid',
                    'paid_date'      => now(),
                    'amount_paid'    => $payment->amount_due,
                    'payment_method' => $installmentTransaction->payment_method,
                    'received_by'    => auth()->id(),
                ]);
            }

            // Cek apakah seluruh angsuran sudah lunas
            $remainingPending = $installmentPlan->payments()->where('status', '!=', 'paid')->count();
            if ($remainingPending === 0) {
                $installmentPlan->update(['status' => 'completed']);
                $mainTransaction = $installmentPlan->transaction;
                $mainTransaction->update(['status' => 'completed']);
                $this->rewardService->awardPoint($mainTransaction->user, $mainTransaction);
                $this->certificateService->generateForTransaction($mainTransaction);
            }

            // Kirim notifikasi ke pelanggan
            $monthDesc = $installmentTransaction->formattedMonths();
            \App\Models\Notification::create([
                'user_id'    => $installmentTransaction->user_id,
                'type'       => 'installment_verified',
                'title'      => 'Pembayaran Cicilan Diterima & Diverifikasi',
                'message'    => "Pembayaran cicilan Anda ({$monthDesc}) sebesar Rp " . number_format($installmentTransaction->amount, 0, ',', '.') . " telah diverifikasi dan diterima. Terima kasih!",
                'data'       => [
                    'installment_plan_id'        => $installmentPlan->id,
                    'installment_transaction_id' => $installmentTransaction->id,
                ],
                'created_at' => now(),
            ]);
        });

        return back()->with('success', 'Pembayaran cicilan (' . $installmentTransaction->formattedMonths() . ') berhasil diverifikasi dan disetujui.');
    }

    public function rejectPayment(Request $request, \App\Models\InstallmentTransaction $installmentTransaction)
    {
        if ($installmentTransaction->status !== 'waiting_verification') {
            return back()->with('error', 'Transaksi pembayaran ini sudah tidak dalam status menunggu verifikasi.');
        }

        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ], [
            'rejection_reason.required' => 'Wajib mengisi alasan penolakan pembayaran.',
        ]);

        $installmentPlan = $installmentTransaction->installmentPlan;

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $installmentTransaction, $installmentPlan) {
            $installmentTransaction->update([
                'status'           => 'rejected',
                'verified_by'      => auth()->id(),
                'verified_at'      => now(),
                'rejection_reason' => $request->rejection_reason,
            ]);

            // Kembalikan angsuran terkait ke pending
            $payments = $installmentPlan->payments()
                ->whereIn('installment_number', (array) $installmentTransaction->months_paid)
                ->get();

            foreach ($payments as $payment) {
                if ($payment->status === 'waiting_verification') {
                    $payment->update([
                        'status' => 'pending',
                    ]);
                }
            }

            // Kirim notifikasi ke pelanggan
            $monthDesc = $installmentTransaction->formattedMonths();
            \App\Models\Notification::create([
                'user_id'    => $installmentTransaction->user_id,
                'type'       => 'installment_rejected',
                'title'      => 'Pembayaran Cicilan Ditolak',
                'message'    => "Pembayaran cicilan Anda ({$monthDesc}) ditolak. Alasan: {$request->rejection_reason}. Silakan periksa kembali dan unggah bukti transfer yang valid.",
                'data'       => [
                    'installment_plan_id'        => $installmentPlan->id,
                    'installment_transaction_id' => $installmentTransaction->id,
                ],
                'created_at' => now(),
            ]);
        });

        return back()->with('success', 'Pembayaran cicilan telah ditolak dan notifikasi telah dikirim ke pelanggan.');
    }

    public function recordBatchPayment(Request $request, InstallmentPlan $installmentPlan)
    {
        $request->validate([
            'payment_method' => ['required', 'string', 'max:50'],
            'month_count'    => ['required', 'integer', 'min:1', 'max:' . $installmentPlan->tenure_months],
            'notes'          => ['nullable', 'string', 'max:500'],
        ]);

        $unpaidPayments = $installmentPlan->payments()
            ->whereIn('status', ['pending', 'overdue'])
            ->orderBy('installment_number')
            ->take($request->month_count)
            ->get();

        if ($unpaidPayments->isEmpty()) {
            return back()->with('error', 'Tidak ada angsuran yang belum dibayar.');
        }

        $totalAmount = $unpaidPayments->sum('amount_due');
        $months = $unpaidPayments->pluck('installment_number')->toArray();
        $transactionCode = 'TRX-CCL-DIR-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(4));

        \Illuminate\Support\Facades\DB::transaction(function () use (
            $installmentPlan, $unpaidPayments, $totalAmount, $months, $transactionCode, $request
        ) {
            $trx = \App\Models\InstallmentTransaction::create([
                'installment_plan_id' => $installmentPlan->id,
                'user_id'             => $installmentPlan->transaction->user_id,
                'transaction_code'    => $transactionCode,
                'amount'              => $totalAmount,
                'months_paid'         => $months,
                'month_count'         => count($months),
                'payment_method'      => $request->payment_method,
                'proof_image'         => null,
                'notes'               => 'Pembayaran langsung kasir toko. ' . ($request->notes ?? ''),
                'status'              => 'verified',
                'verified_by'         => auth()->id(),
                'verified_at'         => now(),
            ]);

            foreach ($unpaidPayments as $p) {
                $p->update([
                    'status'                     => 'paid',
                    'paid_date'                  => now(),
                    'amount_paid'                => $p->amount_due,
                    'payment_method'             => $request->payment_method,
                    'received_by'                => auth()->id(),
                    'installment_transaction_id' => $trx->id,
                    'notes'                      => $request->notes,
                ]);
            }

            $remaining = $installmentPlan->payments()->where('status', '!=', 'paid')->count();
            if ($remaining === 0) {
                $installmentPlan->update(['status' => 'completed']);
                $mainTransaction = $installmentPlan->transaction;
                $mainTransaction->update(['status' => 'completed']);
                $this->rewardService->awardPoint($mainTransaction->user, $mainTransaction);
                $this->certificateService->generateForTransaction($mainTransaction);
            }
        });

        return back()->with('success', 'Pembayaran langsung ' . count($months) . ' bulan angsuran berhasil dicatat!');
    }

    public function recordPayment(Request $request, InstallmentPlan $installmentPlan, InstallmentPayment $installmentPayment)
    {
        abort_if($installmentPayment->installment_plan_id !== $installmentPlan->id, 404);

        if ($installmentPayment->isPaid()) {
            return back()->with('error', 'Cicilan ini sudah dibayar.');
        }

        $request->validate([
            'payment_method' => ['required', 'string', 'max:50'],
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

                // Notifikasi cicilan lunas
                \App\Models\Notification::create([
                    'user_id' => $transaction->user_id,
                    'type'    => 'installment.completed',
                    'title'   => "🎉 Cicilan Emas LUNAS!",
                    'message' => "Selamat! Cicilan emas Anda untuk transaksi {$transaction->transaction_code} telah LUNAS sepenuhnya.",
                    'data'    => ['transaction_id' => $transaction->id],
                ]);
            }

            // Notifikasi pembayaran angsuran
            $transaction = $installmentPlan->transaction;
            \App\Models\Notification::create([
                'user_id' => $transaction->user_id,
                'type'    => 'installment.paid',
                'title'   => "Pembayaran Angsuran Bulan ke-{$installmentPayment->installment_number} Lunas",
                'message' => "Pembayaran angsuran ke-{$installmentPayment->installment_number} sebesar Rp " . number_format($request->amount_paid, 0, ',', '.') . " telah diterima.",
                'data'    => ['installment_plan_id' => $installmentPlan->id, 'payment_id' => $installmentPayment->id],
            ]);
        });

        return back()->with('success', 'Pembayaran angsuran ke-' . $installmentPayment->installment_number . ' berhasil dicatat.');
    }
}
