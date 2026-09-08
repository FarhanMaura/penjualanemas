<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Models\GoldPrice;
use App\Models\Reservation;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use App\Services\RewardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function __construct(
        private RewardService $rewardService,
        private \App\Services\CertificateService $certificateService
    ) {}

    public function index(Request $request)
    {
        $query = Transaction::with(['user', 'items.product'])->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) =>
                $q->where('transaction_code', 'like', "%{$s}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$s}%"))
            );
        }

        $transactions = $query->paginate(20)->withQueryString();

        $stats = [
            'total'       => Transaction::count(),
            'purchase'    => Transaction::where('type', 'purchase')->count(),
            'buyback'     => Transaction::whereIn('type', ['buyback', 'sell'])->count(),
            'installment' => Transaction::where('type', 'installment')->where('status', 'in_progress')->count(),
            'pawn'        => Transaction::where('type', 'pawn')->where('status', 'in_progress')->count(),
        ];

        return view('admin.transactions.index', compact('transactions', 'stats'));
    }

    public function create(Request $request)
    {
        $customers    = User::where('role', 'customer')->orderBy('name')->get();
        $goldPrices   = GoldPrice::latest('price_date')->take(7)->get();
        $reservations = Reservation::where('status', 'confirmed')
            ->with(['user','product','priceNegotiation'])->latest()->get();
        $products     = \App\Models\Product::where('is_available', true)
            ->orderBy('name')->get();

        $selectedReservation = null;
        if ($request->filled('reservation_id')) {
            $selectedReservation = Reservation::with(['user','product','priceNegotiation'])->find($request->reservation_id);
        }

        $paymentMethods = \App\Models\PaymentMethod::active()->ordered()->get();

        return view('admin.transactions.create', compact(
            'customers', 'goldPrices', 'reservations', 'selectedReservation', 'products', 'paymentMethods'
        ));
    }

    public function store(StoreTransactionRequest $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validated();

            $subtotal  = collect($validated['items'] ?? [])->sum(fn($i) => $i['unit_price'] * $i['quantity']);
            $adminFee  = $validated['admin_fee'] ?? 0;
            $discount  = $validated['discount']  ?? 0;
            $total     = $subtotal + $adminFee - $discount;

            if ($validated['type'] === 'pawn') {
                $subtotal = $validated['pawn_loan_amount'];
                $total = $validated['pawn_loan_amount'];
            } elseif ($validated['type'] === 'buyback') {
                $subtotal = 0;
                $adminFee = 0;
                $discount = 0;
                $total    = 0;
            }

            $status = 'completed';
            if ($validated['type'] === 'pawn' || $validated['type'] === 'installment') {
                $status = 'in_progress';
            }

            // Validasi stok produk sebelum memproses transaksi
            if (in_array($validated['type'], ['purchase', 'installment']) && isset($validated['items'])) {
                foreach ($validated['items'] as $item) {
                    $product = \App\Models\Product::find($item['product_id']);
                    if ($product && $product->stock < $item['quantity']) {
                        DB::rollBack();
                        return back()->withInput()->with('error', "Stok produk '{$product->name}' tidak mencukupi. (Stok tersedia: {$product->stock} pcs).");
                    }
                }
            }

            $transaction = Transaction::create([
                'transaction_code' => 'TRX-' . strtoupper(Str::random(8)),
                'user_id'          => $validated['user_id'],
                'type'             => $validated['type'],
                'status'           => $status,
                'gold_price_id'    => $validated['gold_price_id'] ?? null,
                'reservation_id'   => $validated['reservation_id'] ?? null,
                'subtotal'         => $subtotal,
                'admin_fee'        => $adminFee,
                'discount'         => $discount,
                'total_amount'     => $total,
                'payment_method'   => $validated['payment_method'],
                'payment_date'     => $validated['payment_date'],
                'processed_by'     => auth()->id(),
                'notes'            => $validated['notes'] ?? null,
            ]);

            // Jika transaksi berasal dari reservasi, update status reservasi ke completed/confirmed
            if (!empty($validated['reservation_id'])) {
                \App\Models\Reservation::where('id', $validated['reservation_id'])
                    ->update([
                        'status'         => 'completed',
                        'transaction_id' => $transaction->id,
                    ]);
            }

            // Simpan item transaksi & update stok
            if ($validated['type'] === 'buyback') {
                $desc = 'Buyback Emas (Transaksi Langsung di Toko)';
                if (!empty($validated['reservation_id'])) {
                    $res = \App\Models\Reservation::find($validated['reservation_id']);
                    if ($res && $res->pawn_gold_description) {
                        $desc = 'Buyback: ' . $res->pawn_gold_description;
                    }
                }
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id'     => null,
                    'product_name'   => $desc,
                    'gold_purity'    => null,
                    'weight_gram'    => 0,
                    'quantity'       => 1,
                    'price_per_unit' => 0,
                    'subtotal'       => 0,
                ]);
            } elseif ($validated['type'] !== 'pawn' && isset($validated['items'])) {
                foreach ($validated['items'] as $item) {
                    $product = \App\Models\Product::find($item['product_id']);

                    TransactionItem::create([
                        'transaction_id' => $transaction->id,
                        'product_id'     => $item['product_id'],
                        'product_name'   => $product ? $product->name : 'Produk Tidak Dikenal',
                        'gold_purity'    => $product ? $product->gold_purity : null,
                        'weight_gram'    => $product ? $product->weight_gram : 0,
                        'quantity'       => $item['quantity'],
                        'price_per_unit' => $item['unit_price'],
                        'subtotal'       => $item['unit_price'] * $item['quantity'],
                    ]);

                    // Update stok produk secara konsisten
                    if ($product) {
                        $product->reduceStock($item['quantity']);
                    }
                }
            }

            // Create Installment Plan
            if ($validated['type'] === 'installment') {
                $tenureMonths = (int) $validated['installment_tenure'];
                $downPayment  = 0;
                $totalInstallment = $total;
                $monthlyAmount    = round($totalInstallment / $tenureMonths);

                $installmentPlan = \App\Models\InstallmentPlan::create([
                    'transaction_id'    => $transaction->id,
                    'down_payment'      => 0,
                    'total_installment' => $totalInstallment,
                    'tenure_months'     => $tenureMonths,
                    'monthly_amount'    => $monthlyAmount,
                    'start_date'        => $validated['payment_date'],
                    'end_date'          => \Carbon\Carbon::parse($validated['payment_date'])->addMonths($tenureMonths),
                    'status'            => 'active',
                ]);

                // Buat installment_payments schedule
                for ($m = 1; $m <= $tenureMonths; $m++) {
                    \App\Models\InstallmentPayment::create([
                        'installment_plan_id' => $installmentPlan->id,
                        'installment_number'  => $m,
                        'due_date'            => \Carbon\Carbon::parse($validated['payment_date'])->addMonths($m),
                        'amount_due'          => $monthlyAmount,
                        'status'              => 'pending',
                    ]);
                }
            }

            // Create Pawn Record & Installment Plan for Pawn
            if ($validated['type'] === 'pawn') {
                $pawnCode = 'PWN-' . date('Ymd') . '-' . strtoupper(Str::random(4));
                $tenureMonths = max(1, (int) ($validated['pawn_tenure'] ?? 4));
                $loanAmount = (float) $validated['pawn_loan_amount'];
                $monthlyAmount = round($loanAmount / $tenureMonths);

                $pawn = \App\Models\Pawn::create([
                    'transaction_id'   => $transaction->id,
                    'pawn_code'        => $pawnCode,
                    'gold_description' => $validated['pawn_gold_description'],
                    'gold_purity'      => $validated['pawn_gold_purity'],
                    'weight_gram'      => $validated['pawn_weight_gram'],
                    'appraised_value'  => $validated['pawn_appraised_value'],
                    'loan_amount'      => $loanAmount,
                    'interest_rate'    => (float) ($validated['pawn_interest_rate'] ?? 0),
                    'start_date'       => $validated['payment_date'],
                    'due_date'         => $validated['pawn_due_date'],
                    'status'           => 'active',
                ]);

                // Create Installment Plan & Payments for Pawn
                $installmentPlan = \App\Models\InstallmentPlan::create([
                    'transaction_id'    => $transaction->id,
                    'down_payment'      => 0,
                    'total_installment' => $loanAmount,
                    'tenure_months'     => $tenureMonths,
                    'monthly_amount'    => $monthlyAmount,
                    'start_date'        => $validated['payment_date'],
                    'end_date'          => \Carbon\Carbon::parse($validated['payment_date'])->addMonths($tenureMonths),
                    'status'            => 'active',
                    'notes'             => "Skema Cicilan Gadai {$pawnCode}",
                ]);

                for ($m = 1; $m <= $tenureMonths; $m++) {
                    \App\Models\InstallmentPayment::create([
                        'installment_plan_id' => $installmentPlan->id,
                        'installment_number'  => $m,
                        'due_date'            => \Carbon\Carbon::parse($validated['payment_date'])->addMonths($m),
                        'amount_due'          => $monthlyAmount,
                        'status'              => 'pending',
                    ]);
                }

                // TransactionItem snapshot for invoice/receipt
                \App\Models\TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id'     => null,
                    'product_name'   => 'Gadai: ' . $validated['pawn_gold_description'],
                    'gold_purity'    => $validated['pawn_gold_purity'],
                    'weight_gram'    => (float) $validated['pawn_weight_gram'],
                    'quantity'       => 1,
                    'price_per_unit' => $loanAmount,
                    'subtotal'       => $loanAmount,
                ]);
            }

            // Update reservasi ke completed jika ada
            if ($transaction->reservation_id) {
                Reservation::where('id', $transaction->reservation_id)
                    ->update(['status' => 'completed']);
            }

            // Award reward point — hanya untuk transaksi purchase yang langsung completed
            if ($transaction->status === 'completed' && $transaction->type === 'purchase') {
                $customer = User::find($transaction->user_id);
                if ($customer) {
                    $this->rewardService->awardPoint($customer, $transaction);
                }
            }

            // Generate digital certificate for completed purchase/installment transactions
            $this->certificateService->generateForTransaction($transaction);

            // Buat notifikasi otomatis
            $typeLabel = match($transaction->type) {
                'purchase'    => 'Pembelian Emas',
                'buyback'     => 'Jual Emas (Buyback)',
                'installment' => 'Cicilan Emas',
                'pawn'        => 'Gadai Emas',
                default       => 'Transaksi',
            };

            \App\Models\Notification::create([
                'user_id' => $transaction->user_id,
                'type'    => 'transaction.created',
                'title'   => "Transaksi {$typeLabel} ({$transaction->transaction_code})",
                'message' => "Transaksi {$typeLabel} Anda senilai Rp " . number_format($transaction->total_amount, 0, ',', '.') . " telah dicatat.",
                'data'    => ['transaction_id' => $transaction->id, 'type' => $transaction->type],
            ]);

            foreach (User::where('role', 'admin')->get() as $adm) {
                \App\Models\Notification::create([
                    'user_id' => $adm->id,
                    'type'    => 'transaction.created',
                    'title'   => "Transaksi {$typeLabel} Baru",
                    'message' => "Transaksi {$transaction->transaction_code} sebesar Rp " . number_format($transaction->total_amount, 0, ',', '.') . " telah berhasil dibuat.",
                    'data'    => ['transaction_id' => $transaction->id],
                ]);
            }

            DB::commit();

            return redirect()->route('admin.transactions.index')
                ->with('success', "Transaksi {$transaction->transaction_code} berhasil dicatat.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage());
        }
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['user.profile', 'items.product', 'reservation']);
        return view('admin.transactions.show', compact('transaction'));
    }
}
