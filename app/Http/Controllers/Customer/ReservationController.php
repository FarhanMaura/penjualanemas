<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservationRequest;
use App\Models\Product;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservation::with(['product.category'])
            ->where('user_id', auth()->id())
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reservations = $query->paginate(10)->withQueryString();

        return view('customer.reservations.index', compact('reservations'));
    }

    public function create(Request $request)
    {
        $product = null;
        if ($request->filled('product_id')) {
            $product = Product::where('is_reservable', true)->find($request->product_id);
        }

        $products = Product::where('is_reservable', true)
            ->where('is_available', true)
            ->with('category')
            ->orderBy('name')
            ->get();

        return view('customer.reservations.create', compact('products', 'product'));
    }

    public function store(StoreReservationRequest $request)
    {
        if (in_array($request->type, ['purchase', 'installment'])) {
            $product = Product::findOrFail($request->product_id);

            if (! $product->is_reservable) {
                return back()->with('error', 'Produk ini tidak bisa direservasi.');
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

        $reservation = Reservation::create([
            'reservation_code'          => 'RSV-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
            'user_id'                   => auth()->id(),
            'type'                      => $request->type,
            'product_id'                => $request->product_id,
            'quantity'                  => $request->quantity,
            'preferred_date'            => $request->preferred_date,
            'preferred_time'            => $request->preferred_time,
            'payment_method'            => $request->payment_method,
            'notes'                     => $request->notes,
            'pawn_gold_description'     => $request->pawn_gold_description,
            'pawn_gold_purity'          => $request->pawn_gold_purity,
            'pawn_weight_gram'          => $request->pawn_weight_gram,
            'pawn_amount_requested'     => $request->pawn_amount_requested,
            'installment_tenure'        => $request->installment_tenure,
            'installment_down_payment'  => $request->installment_down_payment,
            'status'                    => 'pending',
            'expired_at'                => now()->addDays(3),
        ]);

        return redirect()->route('customer.reservations.index')
            ->with('success', "Reservasi {$reservation->reservation_code} berhasil dibuat! Tunggu konfirmasi dari toko.");
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
