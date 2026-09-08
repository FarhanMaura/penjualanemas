<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Pawn;
use App\Models\PaymentMethod;
use App\Models\InstallmentTransaction;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PawnController extends Controller
{
    public function index()
    {
        $pawns = Pawn::whereHas('transaction', fn($q) => $q->where('user_id', auth()->id()))
            ->with(['transaction.installmentPlan'])
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

        $pawn->load([
            'transaction.installmentPlan.payments',
            'transaction.installmentPlan.installmentTransactions.verifier'
        ]);

        $installmentPlan = $pawn->transaction->installmentPlan;
        $paymentMethods = PaymentMethod::active()
            ->orderByRaw("CASE WHEN type = 'cash' THEN 1 ELSE 0 END")
            ->orderBy('sort_order')
            ->get();

        $unpaidPayments = $installmentPlan
            ? $installmentPlan->payments()->whereIn('status', ['pending', 'overdue'])->orderBy('installment_number')->get()
            : collect();

        return view('customer.pawns.show', compact('pawn', 'installmentPlan', 'paymentMethods', 'unpaidPayments'));
    }

    public function pay(Request $request, Pawn $pawn)
    {
        abort_if($pawn->transaction->user_id !== auth()->id(), 403);

        if ($pawn->status !== 'active') {
            return back()->with('error', 'Gadai ini sudah tidak aktif atau telah lunas ditebus.');
        }

        $installmentPlan = $pawn->transaction->installmentPlan;
        if (! $installmentPlan) {
            return back()->with('error', 'Rencana cicilan untuk gadai ini tidak ditemukan.');
        }

        $rules = [
            'payment_option'    => ['required', 'in:next_month,custom_months,pay_all'],
            'selected_months'   => ['required_if:payment_option,custom_months', 'nullable', 'array'],
            'selected_months.*' => ['integer', 'min:1', 'max:' . $installmentPlan->tenure_months],
            'payment_method'    => ['required', 'string', 'max:50'],
            'proof_image'       => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'sender_bank'       => ['nullable', 'string', 'max:50'],
            'sender_name'       => ['nullable', 'string', 'max:100'],
            'notes'             => ['nullable', 'string', 'max:500'],
        ];

        $request->validate($rules, [
            'proof_image.required' => 'Wajib mengunggah foto / file bukti transfer pembayaran.',
            'proof_image.image'    => 'Bukti pembayaran harus berupa gambar (JPG, PNG, atau WebP).',
            'proof_image.max'      => 'Ukuran bukti pembayaran maksimal 5MB.',
        ]);

        $unpaidPayments = $installmentPlan->payments()
            ->whereIn('status', ['pending', 'overdue'])
            ->orderBy('installment_number')
            ->get();

        if ($unpaidPayments->isEmpty()) {
            return back()->with('error', 'Semua tagihan telah dibayar atau sedang menunggu verifikasi kasir.');
        }

        if ($request->payment_option === 'next_month') {
            $targetPayments = $unpaidPayments->take(1);
        } elseif ($request->payment_option === 'custom_months') {
            $selectedMonths = array_map('intval', (array) $request->selected_months);
            $targetPayments = $unpaidPayments->whereIn('installment_number', $selectedMonths);
            if ($targetPayments->isEmpty()) {
                return back()->with('error', 'Pilih minimal satu bulan angsuran yang valid untuk dibayar.');
            }
        } elseif ($request->payment_option === 'pay_all') {
            $targetPayments = $unpaidPayments;
        }

        $totalAmount = $targetPayments->sum('amount_due');
        $imagePath = $request->file('proof_image')->store('pawn_payments', 'public');
        $transactionCode = 'TRX-PWN-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        DB::transaction(function () use ($request, $installmentPlan, $targetPayments, $totalAmount, $imagePath, $pawn, $transactionCode) {
            $paymentTrx = InstallmentTransaction::create([
                'installment_plan_id' => $installmentPlan->id,
                'user_id'             => auth()->id(),
                'transaction_code'    => $transactionCode,
                'amount'              => $totalAmount,
                'months_paid'         => $targetPayments->pluck('installment_number')->toArray(),
                'month_count'         => $targetPayments->count(),
                'payment_method'      => $request->payment_method,
                'proof_image'         => $imagePath,
                'sender_bank'         => $request->sender_bank,
                'sender_name'         => $request->sender_name,
                'status'              => 'waiting_verification',
                'notes'               => ($request->payment_option === 'pay_all' ? '[TEBUS LANGSUNG] ' : '') . ($request->notes ?? ''),
            ]);

            foreach ($targetPayments as $pmt) {
                $pmt->update([
                    'status'                     => 'waiting_verification',
                    'installment_transaction_id' => $paymentTrx->id,
                    'proof_image'                => $imagePath,
                    'payment_method'             => $request->payment_method,
                ]);
            }

            // Notifikasi admin
            $admin = User::where('role', 'admin')->first();
            if ($admin) {
                $isTebus = $request->payment_option === 'pay_all';
                Notification::create([
                    'user_id' => $admin->id,
                    'type'    => 'pawn.payment_submitted',
                    'title'   => $isTebus ? "Pengajuan Tebus Gadai Langsung ({$pawn->pawn_code})" : "Pembayaran Angsuran Gadai ({$pawn->pawn_code})",
                    'message' => auth()->user()->name . " telah mengunggah bukti " . ($isTebus ? "tebus langsung" : "pembayaran angsuran") . " sebesar Rp " . number_format($totalAmount, 0, ',', '.') . " untuk gadai #{$pawn->pawn_code}.",
                    'data'    => ['pawn_id' => $pawn->id, 'installment_transaction_id' => $paymentTrx->id],
                ]);
            }
        });

        $msg = $request->payment_option === 'pay_all'
            ? 'Bukti pelunasan tebus gadai berhasil dikirim! Menunggu konfirmasi verifikasi kasir.'
            : 'Bukti pembayaran angsuran berhasil dikirim! Menunggu konfirmasi verifikasi kasir.';

        return back()->with('success', $msg);
    }
}
