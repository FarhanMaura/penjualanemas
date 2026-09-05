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
        $installmentPlan->load([
            'transaction.items.product',
            'payments',
            'pickupReservation',
            'installmentTransactions.verifier'
        ]);

        $paymentMethods = \App\Models\PaymentMethod::active()->ordered()->get();
        $unpaidPayments = $installmentPlan->payments()->whereIn('status', ['pending', 'overdue'])->orderBy('installment_number')->get();

        return view('customer.installments.show', compact('installmentPlan', 'paymentMethods', 'unpaidPayments'));
    }

    public function pay(Request $request, InstallmentPlan $installmentPlan)
    {
        abort_if($installmentPlan->transaction->user_id !== auth()->id(), 403);

        if ($installmentPlan->status !== 'active') {
            return back()->with('error', 'Rencana cicilan ini sudah tidak aktif atau telah selesai.');
        }

        $request->validate([
            'payment_option'    => ['required', 'in:next_month,custom_months,pay_all'],
            'selected_months'   => ['required_if:payment_option,custom_months', 'nullable', 'array'],
            'selected_months.*' => ['integer', 'min:1', 'max:' . $installmentPlan->tenure_months],
            'payment_method'    => ['required', 'string', 'max:50'],
            'proof_image'       => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'sender_bank'       => ['nullable', 'string', 'max:50'],
            'sender_name'       => ['nullable', 'string', 'max:100'],
            'notes'             => ['nullable', 'string', 'max:500'],
        ], [
            'proof_image.required' => 'Wajib mengunggah foto / file bukti transfer pembayaran.',
            'proof_image.image'    => 'Bukti pembayaran harus berupa gambar (JPG, PNG, atau WebP).',
            'proof_image.max'      => 'Ukuran bukti pembayaran maksimal 5MB.',
        ]);

        $unpaidPayments = $installmentPlan->payments()
            ->whereIn('status', ['pending', 'overdue'])
            ->orderBy('installment_number')
            ->get();

        if ($unpaidPayments->isEmpty()) {
            return back()->with('error', 'Semua tagihan angsuran telah dibayar atau sedang menunggu verifikasi.');
        }

        if ($request->payment_option === 'next_month') {
            $targetPayments = $unpaidPayments->take(1);
        } elseif ($request->payment_option === 'pay_all') {
            $targetPayments = $unpaidPayments;
        } else {
            $selected = collect($request->selected_months)->map(fn($m) => (int) $m)->sort()->values();
            if ($selected->isEmpty()) {
                return back()->with('error', 'Pilih minimal satu bulan angsuran yang ingin dibayar.');
            }

            // Validasi berurutan dari bulan terdekat (seperti Shopee & Pegadaian)
            $expectedMonths = $unpaidPayments->take($selected->count())->pluck('installment_number')->values();
            if ($selected->toArray() !== $expectedMonths->toArray()) {
                return back()->with('error', 'Pembayaran angsuran harus berurutan mulai dari angsuran tertua yang belum dibayar.');
            }

            $targetPayments = $unpaidPayments->whereIn('installment_number', $selected);
        }

        $totalAmount = $targetPayments->sum('amount_due');
        $proofPath = $request->file('proof_image')->store('installment_proofs', 'public');
        $transactionCode = 'TRX-CCL-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(5));

        \Illuminate\Support\Facades\DB::transaction(function () use (
            $installmentPlan, $targetPayments, $totalAmount, $proofPath, $transactionCode, $request
        ) {
            $installmentTransaction = \App\Models\InstallmentTransaction::create([
                'installment_plan_id' => $installmentPlan->id,
                'user_id'             => auth()->id(),
                'transaction_code'    => $transactionCode,
                'amount'              => $totalAmount,
                'months_paid'         => $targetPayments->pluck('installment_number')->toArray(),
                'month_count'         => $targetPayments->count(),
                'payment_method'      => $request->payment_method,
                'proof_image'         => $proofPath,
                'sender_bank'         => $request->sender_bank,
                'sender_name'         => $request->sender_name,
                'notes'               => $request->notes,
                'status'              => 'waiting_verification',
            ]);

            foreach ($targetPayments as $payment) {
                $payment->update([
                    'status'                     => 'waiting_verification',
                    'installment_transaction_id' => $installmentTransaction->id,
                    'proof_image'                => $proofPath,
                    'payment_method'             => $request->payment_method,
                ]);
            }

            // Notifikasi ke Admin
            $productName = $installmentPlan->transaction->items->first()?->product?->name ?? 'Cicilan Emas';
            $monthDesc = $installmentTransaction->formattedMonths();
            $admins = \App\Models\User::where('role', 'admin')->get();

            foreach ($admins as $admin) {
                \App\Models\Notification::create([
                    'user_id'    => $admin->id,
                    'type'       => 'installment_payment',
                    'title'      => 'Bukti Pembayaran Cicilan Masuk',
                    'message'    => "Pelanggan " . auth()->user()->name . " mengunggah bukti transfer untuk {$productName} ({$monthDesc}) senilai Rp " . number_format($totalAmount, 0, ',', '.') . ". Mohon segera verifikasi.",
                    'data'       => [
                        'installment_plan_id'        => $installmentPlan->id,
                        'installment_transaction_id' => $installmentTransaction->id,
                    ],
                    'created_at' => now(),
                ]);
            }

            // Notifikasi ke Pelanggan
            \App\Models\Notification::create([
                'user_id'    => auth()->id(),
                'type'       => 'installment_payment',
                'title'      => 'Bukti Bayar Berhasil Dikirim',
                'message'    => "Bukti transfer untuk pembayaran ({$monthDesc}) berhasil diunggah. Mohon menunggu konfirmasi verifikasi admin toko.",
                'data'       => [
                    'installment_plan_id'        => $installmentPlan->id,
                    'installment_transaction_id' => $installmentTransaction->id,
                ],
                'created_at' => now(),
            ]);
        });

        return back()->with('success', "Bukti pembayaran ({$targetPayments->count()} Bulan) sebesar Rp " . number_format($totalAmount, 0, ',', '.') . " berhasil dikirim! Status saat ini menunggu verifikasi admin toko.");
    }

    public function schedulePickup(Request $request, InstallmentPlan $installmentPlan)
    {
        abort_if($installmentPlan->transaction->user_id !== auth()->id(), 403);

        if (! $installmentPlan->canSchedulePickup()) {
            $required = $installmentPlan->requiredPaidForPickup();
            return back()->with('error', "Jadwal reservasi pengambilan emas belum dapat dibuat. Pembayaran bulan ke-1 sampai ke-{$required} harus lunas terlebih dahulu (saat memasuki bulan terakhir).");
        }

        $request->validate([
            'preferred_date' => ['required', 'date', 'after_or_equal:today'],
            'preferred_time' => ['required', 'date_format:H:i'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ]);

        $product = $installmentPlan->transaction->items->first()?->product;

        $reservation = \App\Models\Reservation::create([
            'reservation_code' => 'RSV-PKP-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(4)),
            'user_id'          => auth()->id(),
            'type'             => 'installment',
            'product_id'       => $product?->id,
            'quantity'         => 1,
            'preferred_date'   => $request->preferred_date,
            'preferred_time'   => $request->preferred_time,
            'payment_method'   => $installmentPlan->transaction->payment_method ?? 'transfer',
            'notes'            => 'Reservasi Pengambilan Emas Fisik (Cicilan ' . $installmentPlan->transaction->transaction_code . '). ' . ($request->notes ?? ''),
            'status'           => 'confirmed',
        ]);

        $installmentPlan->update(['pickup_reservation_id' => $reservation->id]);

        return back()->with('success', "Jadwal reservasi pengambilan emas berhasil dibuat ({$reservation->reservation_code})! Silakan datang ke toko sesuai jadwal.");
    }
}
