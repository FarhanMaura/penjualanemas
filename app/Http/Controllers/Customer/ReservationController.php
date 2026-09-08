<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservationRequest;
use App\Models\PriceNegotiation;
use App\Models\Product;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        // Auto-expire pending reservations past expired_at
        Reservation::where('status', 'pending')
            ->where('expired_at', '<', now())
            ->update(['status' => 'expired']);

        $query = Reservation::with(['product.category', 'priceNegotiation', 'paymentMethodDetail'])
            ->where('user_id', auth()->id())
            // Sembunyikan reservasi cicilan awal yang sudah diproses menjadi cicilan aktif
            // (sudah ada transaction_id). Kecuali RSV-PKP- (pengambilan fisik) yang tetap harus tampil.
            ->where(function ($q) {
                $q->where(function ($inner) {
                    // Semua reservasi non-installment tampil semua
                    $inner->where('type', '!=', 'installment');
                })->orWhere(function ($inner) {
                    // Reservasi installment: tampilkan HANYA yang belum diproses (transaction_id null)
                    // ATAU yang merupakan RSV-PKP- (pickup reservation)
                    $inner->where('type', 'installment')
                        ->where(function ($q2) {
                            $q2->whereNull('transaction_id')
                               ->orWhere('reservation_code', 'like', 'RSV-PKP-%');
                        });
                });
            })
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reservations = $query->paginate(10)->withQueryString();

        return view('customer.reservations.index', compact('reservations'));
    }

    public function __construct(
        private \App\Services\GoldPriceService $goldPriceService
    ) {}

    public function create(Request $request)
    {
        $product = null;
        $negotiation = null;
        $todayGoldPrice = $this->goldPriceService->getTodayPrice();

        if ($request->filled('negotiation_id')) {
            $negotiation = PriceNegotiation::where('user_id', auth()->id())
                ->where('status', 'approved')
                ->with('product')
                ->find($request->negotiation_id);

            if ($negotiation) {
                $product = $negotiation->product;
            } else {
                $checkUsed = PriceNegotiation::where('user_id', auth()->id())->find($request->negotiation_id);
                if ($checkUsed && $checkUsed->status === 'used') {
                    return redirect()->route('customer.reservations.index')
                        ->with('error', 'Penawaran harga tersebut sudah digunakan untuk membuat reservasi.');
                }
            }
        }

        if (! $product && $request->filled('product_id')) {
            $product = Product::where('is_reservable', true)->find($request->product_id);
        }

        $products = Product::where('is_reservable', true)
            ->where('is_available', true)
            ->where('stock', '>', 0)
            ->with('category')
            ->orderBy('name')
            ->get();

        $paymentMethods = \App\Models\PaymentMethod::active()
            ->orderByRaw("CASE WHEN type = 'cash' THEN 1 ELSE 0 END")
            ->orderBy('sort_order')
            ->get();

        return view('customer.reservations.create', compact('products', 'product', 'negotiation', 'todayGoldPrice', 'paymentMethods'));
    }

    public function store(StoreReservationRequest $request)
    {
        if (in_array($request->type, ['purchase', 'installment'])) {
            // BUG FIX: gunakan find() + manual 404 agar tidak throw jika product_id null
            $product = $request->product_id ? Product::find($request->product_id) : null;

            if (! $product) {
                return back()->withInput()->with('error', 'Produk tidak ditemukan. Silakan pilih produk yang valid.');
            }

            if (! $product->is_reservable) {
                return back()->with('error', 'Produk ini tidak bisa direservasi.');
            }

            $requestedQty = (int) ($request->quantity ?? 1);
            if ($product->stock < $requestedQty) {
                return back()->withInput()->with('error', "Stok produk '{$product->name}' tidak mencukupi. (Stok tersedia: {$product->stock} pcs).");
            }

            // Cek apakah user punya reservasi pending untuk produk yang sama
            $existing = Reservation::where('user_id', auth()->id())
                ->where('product_id', $product->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->exists();

            if ($existing) {
                return back()->with('error', 'Anda sudah memiliki reservasi aktif untuk produk ini.');
            }
        }

        $negotiationId = null;
        $agreedPrice   = null;
        $negotiation   = null;

        if ($request->filled('price_negotiation_id')) {
            $negotiation = PriceNegotiation::where('user_id', auth()->id())
                ->where('status', 'approved')
                ->find($request->price_negotiation_id);

            if (! $negotiation) {
                return back()->withInput()->with('error', 'Penawaran harga ini tidak valid atau sudah pernah digunakan untuk reservasi.');
            }

            $negotiationId = $negotiation->id;
            $agreedPrice   = $negotiation->agreed_price; // Always use agreed_price from negotiation record
        }

        $reservation = \Illuminate\Support\Facades\DB::transaction(function () use ($request, $negotiationId, $agreedPrice, $negotiation) {
            $res = Reservation::create([
                'reservation_code'          => 'RSV-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
                'user_id'                   => auth()->id(),
                'type'                      => $request->type,
                'product_id'                => $request->product_id,
                'price_negotiation_id'     => $negotiationId,
                'quantity'                  => $request->quantity ?? 1,
                'agreed_price'              => $agreedPrice,
                'preferred_date'            => $request->preferred_date ?? today()->toDateString(),
                'preferred_time'            => $request->preferred_time,
                'payment_method'            => $request->payment_method,
                'notes'                     => $request->notes,
                'pawn_gold_description'     => $request->pawn_gold_description,
                'pawn_gold_purity'          => $request->pawn_gold_purity,
                'pawn_weight_gram'          => $request->pawn_weight_gram,
                'pawn_amount_requested'     => $request->pawn_amount_requested,
                'installment_tenure'        => $request->installment_tenure,
                'installment_down_payment'  => 0,
                'status'                    => 'pending',
                'expired_at'                => now()->addDays(3),
            ]);

            if ($negotiation) {
                $negotiation->update(['status' => 'used']);
            }

            return $res;
        });

        // Notifikasi ke Customer
        \App\Models\Notification::create([
            'user_id' => auth()->id(),
            'type'    => 'reservation.created',
            'title'   => "Reservasi Berhasil Dibuat ({$reservation->reservation_code})",
            'message' => "Reservasi {$reservation->reservation_code} Anda telah berhasil dibuat dan sedang menunggu konfirmasi toko.",
            'data'    => ['reservation_id' => $reservation->id],
        ]);

        // Notifikasi ke Admin
        foreach (\App\Models\User::where('role', 'admin')->get() as $adm) {
            \App\Models\Notification::create([
                'user_id' => $adm->id,
                'type'    => 'reservation.created',
                'title'   => "Pengajuan Reservasi Baru (#{$reservation->reservation_code})",
                'message' => auth()->user()->name . " mengajukan reservasi baru {$reservation->reservation_code}.",
                'data'    => ['reservation_id' => $reservation->id],
            ]);
        }

        // BUG FIX: pesan sukses disesuaikan — tidak tampilkan harga Rp 0 untuk reservasi biasa
        $successMsg = "Reservasi {$reservation->reservation_code} berhasil dibuat!";
        if ($agreedPrice && $agreedPrice > 0) {
            $successMsg .= " Harga kesepakatan: Rp " . number_format($agreedPrice, 0, ',', '.') . ".";
        }
        $successMsg .= " Tunggu konfirmasi dari toko.";

        return redirect()->route('customer.reservations.index')
            ->with('success', $successMsg);
    }

    public function cancel(Reservation $reservation)
    {
        // Hanya pemilik yang bisa batalkan
        abort_if($reservation->user_id !== auth()->id(), 403);

        if (! in_array($reservation->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Reservasi tidak dapat dibatalkan.');
        }

        $reservation->update(['status' => 'cancelled']);

        return back()->with('success', 'Reservasi berhasil dibatalkan.');
    }
}
