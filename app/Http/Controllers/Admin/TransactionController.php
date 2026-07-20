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
            ->with(['user','product'])->latest()->get();
        $products     = \App\Models\Product::where('is_available', true)
            ->orderBy('name')->get();

        $selectedReservation = null;
        if ($request->filled('reservation_id')) {
            $selectedReservation = Reservation::with(['user','product'])->find($request->reservation_id);
        }

        return view('admin.transactions.create', compact(
            'customers', 'goldPrices', 'reservations', 'selectedReservation', 'products'
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
            }

            $status = 'completed';
            if ($validated['type'] === 'pawn' || $validated['type'] === 'installment') {
                $status = 'in_progress';
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

            // Simpan item transaksi & kurangi stok
            if ($validated['type'] !== 'pawn' && isset($validated['items'])) {
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

                    // Kurangi stok produk
                    if ($product) {
                        $newStock = max(0, $product->stock - $item['quantity']);
                        $product->update([
                            'stock'        => $newStock,
                            'is_available' => $newStock > 0,
                            'is_reservable'=> $newStock > 0,
                        ]);
                    }
                }
            }

            // Create Installment Plan
            if ($validated['type'] === 'installment') {
                $installmentPlan = \App\Models\InstallmentPlan::create([
                    'transaction_id'    => $transaction->id,
                    'down_payment'      => $validated['installment_down_payment'],
                    'total_installment' => $total - $validated['installment_down_payment'],
                    'tenure_months'     => $validated['installment_tenure'],
                    'monthly_amount'    => ($total - $validated['installment_down_payment']) / $validated['installment_tenure'],
                    'start_date'        => $validated['payment_date'],
                    'end_date'          => \Carbon\Carbon::parse($validated['payment_date'])->addMonths($validated['installment_tenure']),
                    'status'            => 'active',
                ]);

                // Buat installment_payments schedule
                $monthlyAmount = ($total - $validated['installment_down_payment']) / $validated['installment_tenure'];
                for ($m = 1; $m <= $validated['installment_tenure']; $m++) {
                    \App\Models\InstallmentPayment::create([
                        'installment_plan_id' => $installmentPlan->id,
                        'installment_number'  => $m,
                        'due_date'            => \Carbon\Carbon::parse($validated['payment_date'])->addMonths($m),
                        'amount_due'          => $monthlyAmount,
                        'status'              => 'pending',
                    ]);
                }
            }

            // Create Pawn Record
            if ($validated['type'] === 'pawn') {
                $pawnCode = 'PWN-' . date('Ymd') . '-' . strtoupper(Str::random(4));
                \App\Models\Pawn::create([
                    'transaction_id'   => $transaction->id,
                    'pawn_code'        => $pawnCode,
                    'gold_description' => $validated['pawn_gold_description'],
                    'gold_purity'      => $validated['pawn_gold_purity'],
                    'weight_gram'      => $validated['pawn_weight_gram'],
                    'appraised_value'  => $validated['pawn_appraised_value'],
                    'loan_amount'      => $validated['pawn_loan_amount'],
                    'interest_rate'    => $validated['pawn_interest_rate'],
                    'start_date'       => $validated['payment_date'],
                    'due_date'         => $validated['pawn_due_date'],
                    'status'           => 'active',
                ]);
            }

            // Update reservasi ke completed jika ada
            if ($transaction->reservation_id) {
                Reservation::where('id', $transaction->reservation_id)
                    ->update(['status' => 'completed']);
            }

            // Award reward point
            $customer = User::find($transaction->user_id);
            $this->rewardService->awardPoint($customer, $transaction);

            // Generate digital certificate for completed transactions
            $this->certificateService->generateForTransaction($transaction);

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
