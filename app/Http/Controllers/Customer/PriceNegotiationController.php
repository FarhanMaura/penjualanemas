<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\PriceNegotiation;
use App\Models\Product;
use App\Services\GoldPriceService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PriceNegotiationController extends Controller
{
    public function index(Request $request)
    {
        $query = PriceNegotiation::with(['product.category'])
            ->where('user_id', auth()->id())
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $negotiations = $query->paginate(10)->withQueryString();

        return view('customer.negotiations.index', compact('negotiations'));
    }

    public function create(Request $request, GoldPriceService $goldPriceService)
    {
        $selectedProduct = null;
        if ($request->filled('product_id')) {
            $selectedProduct = Product::where('is_available', true)->find($request->product_id);
        }

        $products = Product::where('is_available', true)
            ->where('stock', '>', 0)
            ->with('category')
            ->orderBy('name')
            ->get();

        $goldPrice = $goldPriceService->getTodayPrice();

        return view('customer.negotiations.create', compact('products', 'selectedProduct', 'goldPrice'));
    }

    public function store(Request $request, GoldPriceService $goldPriceService)
    {
        $request->validate([
            'product_id'    => ['required', 'exists:products,id'],
            'offered_price' => ['required', 'numeric', 'min:10000'],
            'quantity'      => ['required', 'integer', 'min:1', 'max:100'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ]);

        $product = Product::findOrFail($request->product_id);

        // Hitung harga original saat penawaran diajukan
        $goldPrice = $goldPriceService->getTodayPrice();
        $unitOriginalPrice = $goldPrice
            ? round($goldPrice->sell_price_per_gram * $product->weight_gram, -3)
            : $product->base_price;

        $totalOriginalPrice = $unitOriginalPrice * $request->quantity;

        // Cek penawaran pending berulang untuk produk yang sama
        $existing = PriceNegotiation::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->where('status', 'pending')
            ->exists();

        if ($existing) {
            return back()->with('error', 'Anda masih memiliki pengajuan tawar harga yang sedang diproses untuk produk ini.');
        }

        $negotiation = PriceNegotiation::create([
            'negotiation_code' => 'TWR-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
            'user_id'          => auth()->id(),
            'product_id'       => $product->id,
            'original_price'   => $totalOriginalPrice,
            'offered_price'    => $request->offered_price,
            'quantity'         => $request->quantity,
            'status'           => 'pending',
            'notes'            => $request->notes,
        ]);

        // Notifikasi Customer
        \App\Models\Notification::create([
            'user_id' => auth()->id(),
            'type'    => 'negotiation.created',
            'title'   => "Pengajuan Tawar Harga Berhasil ({$negotiation->negotiation_code})",
            'message' => "Penawaran harga Anda sebesar Rp " . number_format($negotiation->offered_price, 0, ',', '.') . " untuk {$product->name} berhasil dikirim.",
            'data'    => ['negotiation_id' => $negotiation->id],
        ]);

        // Notifikasi Admin
        foreach (\App\Models\User::where('role', 'admin')->get() as $adm) {
            \App\Models\Notification::create([
                'user_id' => $adm->id,
                'type'    => 'negotiation.created',
                'title'   => "Pengajuan Tawar Harga Baru (#{$negotiation->negotiation_code})",
                'message' => auth()->user()->name . " menawar produk {$product->name} seharga Rp " . number_format($negotiation->offered_price, 0, ',', '.') . ".",
                'data'    => ['negotiation_id' => $negotiation->id],
            ]);
        }

        return redirect()->route('customer.negotiations.index')
            ->with('success', "Pengajuan tawar harga {$negotiation->negotiation_code} berhasil dikirim! Menunggu konfirmasi admin.");
    }
}
