<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            match ($request->status) {
                'available' => $query->where('is_available', true)->where('stock', '>', 0),
                'empty'     => $query->where('stock', 0),
                'hidden'    => $query->where('is_available', false),
                default     => null,
            };
        }

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(fn($qb) => $qb->where('name', 'like', "%{$q}%")
                                        ->orWhere('sku', 'like', "%{$q}%"));
        }

        $products   = $query->orderBy('sort_order')->orderBy('name')->paginate(15)->withQueryString();
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();

        $stats = [
            'total'     => Product::count(),
            'available' => Product::where('is_available', true)->where('stock', '>', 0)->count(),
            'empty'     => Product::where('stock', 0)->count(),
            'categories'=> Category::where('is_active', true)->count(),
        ];

        return view('admin.products.index', compact('products', 'categories', 'stats'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        // Upload gambar
        $images = [];
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $images[] = $path;
        }
        $data['images']        = $images ?: null;
        $data['is_available']  = $request->boolean('is_available', true);
        $data['is_reservable'] = $request->boolean('is_reservable', true);
        $data['is_basic']      = $request->boolean('is_basic', false);

        Product::create($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        // Ganti gambar jika ada upload baru
        if ($request->hasFile('image')) {
            // Hapus gambar lama
            if ($product->images) {
                foreach ($product->images as $old) {
                    Storage::disk('public')->delete($old);
                }
            }
            $path = $request->file('image')->store('products', 'public');
            $data['images'] = [$path];
        }

        $data['is_available']  = $request->boolean('is_available');
        $data['is_reservable'] = $request->boolean('is_reservable');
        $data['is_basic']      = $request->boolean('is_basic');

        $product->update($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete(); // soft delete

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}
