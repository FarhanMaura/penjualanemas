<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\GoldPriceService;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function __construct(private GoldPriceService $goldPriceService) {}

    public function index(Request $request)
    {
        $selectedCategory = $request->get('category', 'all');
        $search = $request->get('search');

        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $goldPrice  = $this->goldPriceService->getTodayPrice();

        if ($search) {
            $products = Product::with('category')
                ->where('is_available', true)
                ->where(fn($q) => $q->where('name', 'like', "%{$search}%")
                                          ->orWhere('gold_purity', 'like', "%{$search}%"))
                ->orderBy('sort_order')
                ->orderBy('name')
                ->paginate(12)
                ->withQueryString();
        } elseif ($selectedCategory && $selectedCategory !== 'all') {
            // Filter kategori spesifik -> Tampilkan SEMUA produk dari tipe/kategori ini
            $products = Product::with('category')
                ->where('is_available', true)
                ->whereHas('category', fn($q) => $q->where('slug', $selectedCategory))
                ->orderBy('sort_order')
                ->orderBy('name')
                ->paginate(12)
                ->withQueryString();
        } else {
            // "Semua Produk" -> Hanya tampilkan 1 produk perwakilan dari masing-masing tipe/kategori
            $representativeIds = Product::where('is_available', true)
                ->whereNotNull('category_id')
                ->selectRaw('MIN(id) as id')
                ->groupBy('category_id')
                ->pluck('id');

            $products = Product::with('category')
                ->whereIn('id', $representativeIds)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->paginate(12)
                ->withQueryString();
        }

        return view('customer.catalog.index', compact('products', 'categories', 'goldPrice'));
    }
}
