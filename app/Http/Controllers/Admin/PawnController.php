<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pawn;
use App\Models\InstallmentTransaction;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PawnController extends Controller
{
    public function index(Request $request)
    {
        $query = Pawn::with(['transaction.user', 'transaction.installmentPlan'])->latest();

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
        $pawn->load([
            'transaction.user.profile',
            'transaction.installmentPlan.payments',
            'transaction.installmentPlan.installmentTransactions.verifier'
        ]);
        return view('admin.pawns.show', compact('pawn'));
    }

    public function redeem(Request $request, Pawn $pawn)
    {
        abort_if($pawn->status !== 'active', 400, 'Gadai ini tidak aktif.');

        if ($request->filled('redemption_amount')) {
            $request->merge([
                'redemption_amount' => (float) str_replace(['.', ','], '', (string) $request->redemption_amount),
            ]);
        }

        $request->validate([
            'redemption_amount' => ['required', 'numeric', 'min:0'],
            'notes'             => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($request, $pawn) {
            $pawn->update([
                'status'            => 'redeemed',
                'redemption_date'   => now(),
                'redemption_amount' => $request->redemption_amount,
                'notes'             => $request->notes,
            ]);

            // Update associated transaction status to completed
            $transaction = $pawn->transaction;
            $transaction->update(['status' => 'completed']);

            // Selesaikan installment plan & tandai semua pembayaran lunas jika ada
            if ($transaction->installmentPlan) {
                $plan = $transaction->installmentPlan;
                $plan->update(['status' => 'completed']);
                foreach ($plan->payments()->where('status', '!=', 'paid')->get() as $p) {
                    $p->update([
                        'status'      => 'paid',
                        'paid_date'   => now(),
                        'amount_paid' => $p->amount_due,
                        'received_by' => auth()->id(),
                        'notes'       => 'Lunas ditebus langsung di kasir toko',
                    ]);
                }
            }

            // Notifikasi tebus gadai
            Notification::create([
                'user_id' => $transaction->user_id,
                'type'    => 'pawn.redeemed',
                'title'   => "Gadai Berhasil Ditebus ({$pawn->pawn_code})",
                'message' => "Gadai {$pawn->pawn_code} Anda sebesar Rp " . number_format($request->redemption_amount, 0, ',', '.') . " telah lunas ditebus di toko. Emas Anda siap diambil kembali.",
                'data'    => ['pawn_id' => $pawn->id],
            ]);
        });

        return back()->with('success', 'Gadai berhasil ditebus dan ditandai lunas.');
    }

    public function verifyPayment(Request $request, Pawn $pawn, InstallmentTransaction $installmentTransaction)
    {
        abort_if(!in_array($installmentTransaction->status, ['pending', 'waiting_verification']), 400, 'Transaksi pembayaran ini sudah diproses.');

        DB::transaction(function () use ($pawn, $installmentTransaction) {
            $installmentTransaction->update([
                'status'      => 'verified',
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ]);

            // Update payments associated with this transaction to paid
            foreach ($installmentTransaction->payments as $pmt) {
                $pmt->update([
                    'status'         => 'paid',
                    'paid_date'      => now(),
                    'amount_paid'    => $pmt->amount_due,
                    'payment_method' => $installmentTransaction->payment_method,
                    'received_by'    => auth()->id(),
                ]);
            }

            // Check if all payments in plan are paid
            $plan = $pawn->transaction?->installmentPlan;
            if ($plan) {
                $unpaidCount = $plan->payments()->where('status', '!=', 'paid')->count();
                if ($unpaidCount === 0) {
                    $plan->update(['status' => 'completed']);
                    $totalPaid = $plan->payments()->sum('amount_due');
                    $pawn->update([
                        'status'            => 'redeemed',
                        'redemption_date'   => now(),
                        'redemption_amount' => $totalPaid,
                    ]);
                    $pawn->transaction->update(['status' => 'completed']);

                    \App\Models\Notification::create([
                        'user_id' => $pawn->transaction->user_id,
                        'type'    => 'pawn.redeemed',
                        'title'   => "Gadai Berhasil Ditebus ({$pawn->pawn_code})",
                        'message' => "Selamat! Seluruh pembayaran gadai #{$pawn->pawn_code} telah lunas diverifikasi. Emas Anda siap diambil kembali di Toko Emas Sinar Baru II.",
                        'data'    => ['pawn_id' => $pawn->id],
                    ]);
                }
            }
        });

        return back()->with('success', 'Pembayaran angsuran/tebus gadai berhasil diverifikasi.');
    }

    public function rejectPayment(Request $request, Pawn $pawn, InstallmentTransaction $installmentTransaction)
    {
        abort_if(!in_array($installmentTransaction->status, ['pending', 'waiting_verification']), 400, 'Transaksi pembayaran ini sudah diproses.');

        $request->validate(['rejection_reason' => ['required', 'string', 'max:500']]);

        DB::transaction(function () use ($request, $pawn, $installmentTransaction) {
            $installmentTransaction->update([
                'status'           => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'verified_by'      => auth()->id(),
                'verified_at'      => now(),
            ]);

            // Return payments back to pending
            $installmentTransaction->payments()->update([
                'status' => 'pending',
            ]);

            \App\Models\Notification::create([
                'user_id' => $pawn->transaction->user_id,
                'type'    => 'pawn.payment_rejected',
                'title'   => "Pembayaran Gadai Ditolak ({$pawn->pawn_code})",
                'message' => "Bukti pembayaran gadai Anda ditolak. Alasan: {$request->rejection_reason}. Silakan unggah bukti yang valid.",
                'data'    => ['pawn_id' => $pawn->id],
            ]);
        });

        return back()->with('success', 'Bukti pembayaran berhasil ditolak.');
    }
}
