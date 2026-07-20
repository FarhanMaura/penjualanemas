<x-admin-app>
<x-slot name="pageTitle">Edit Produk</x-slot>

<div class="max-w-3xl">
    @if($errors->any())
    <div class="flash-error" data-flash>
        <ul class="list-disc list-inside text-sm space-y-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="glass rounded-2xl p-6 space-y-5">

            <div class="grid grid-cols-2 gap-5">
                <div class="col-span-2">
                    <label class="input-label">Nama Produk *</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}"
                           class="input-field" required>
                    @error('name')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="input-label">SKU *</label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" class="input-field" required>
                    @error('sku')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="input-label">Kategori *</label>
                    <select name="category_id" class="input-field" required>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id',$product->category_id) == $cat->id ? 'selected':'' }}>
                            {{ $cat->icon }} {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="input-label">Kadar Emas *</label>
                    <select name="gold_purity" class="input-field" required>
                        @foreach(['24K','22K','18K','14K','9K'] as $k)
                        <option value="{{ $k }}" {{ old('gold_purity',$product->gold_purity) == $k ? 'selected':'' }}>{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="input-label">Berat (gram) *</label>
                    <input type="number" name="weight_gram" value="{{ old('weight_gram', $product->weight_gram) }}"
                           class="input-field" step="0.001" min="0.001" required>
                </div>
                <div>
                    <label class="input-label">Harga Jual (Rp) *</label>
                    <input type="number" name="base_price" value="{{ old('base_price', $product->base_price) }}"
                           class="input-field" min="1" required>
                </div>
                <div>
                    <label class="input-label">Harga Buyback (Rp)</label>
                    <input type="number" name="buy_back_price" value="{{ old('buy_back_price', $product->buy_back_price) }}"
                           class="input-field" min="0">
                </div>
                <div>
                    <label class="input-label">Stok *</label>
                    <input type="number" name="stock" value="{{ old('stock', $product->stock) }}"
                           class="input-field" min="0" required>
                </div>
                <div class="col-span-2">
                    <label class="input-label">Deskripsi</label>
                    <textarea name="description" rows="3" class="input-field">{{ old('description', $product->description) }}</textarea>
                </div>
                <div class="col-span-2">
                    <label class="input-label">Ganti Foto Produk</label>
                    @if($product->thumbnail)
                    <img src="{{ Storage::url($product->thumbnail) }}" class="w-32 h-32 rounded-xl object-cover mb-3">
                    @endif
                    <input id="product-image-input" type="file" name="image" accept="image/*" class="input-field">
                    <img id="product-image-preview" src="" class="hidden mt-3 w-32 h-32 rounded-xl object-cover">
                </div>
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_available" value="1" {{ $product->is_available ? 'checked':'' }}
                               class="w-4 h-4 rounded" style="accent-color:#f59e0b;">
                        <span class="text-sm text-gray-300">Tampilkan di Katalog</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_reservable" value="1" {{ $product->is_reservable ? 'checked':'' }}
                               class="w-4 h-4 rounded" style="accent-color:#f59e0b;">
                        <span class="text-sm text-gray-300">Bisa Direservasi</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2" style="border-top:1px solid rgba(255,255,255,0.06);">
                <button type="submit" class="btn-orange">Simpan Perubahan</button>
                <a href="{{ route('admin.products.index') }}" class="btn-ghost">Batal</a>
            </div>
        </div>
    </form>
</div>

@vite('resources/js/admin/products.js')
</x-admin-app>
