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
        $query = Product::with('category')
            ->where('is_available', true);

        if ($request->filled('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%{$s}%")
                                      ->orWhere('gold_purity', 'like', "%{$s}%"));
        }

        $products   = $query->orderBy('sort_order')->orderBy('name')->paginate(12)->withQueryString();
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $goldPrice  = $this->goldPriceService->getTodayPrice();

        return view('customer.catalog.index', compact('products', 'categories', 'goldPrice'));
    }
}
