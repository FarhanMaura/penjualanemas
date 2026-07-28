<x-admin-app>
<x-slot name="pageTitle">Tambah Produk Baru</x-slot>

<div class="max-w-3xl">
    @if($errors->any())
    <div class="flash-error" data-flash>
        <ul class="list-disc list-inside text-sm space-y-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="glass rounded-2xl p-6 space-y-5">

            <div class="grid grid-cols-2 gap-5">
                <div class="col-span-2">
                    <label class="input-label">Nama Produk *</label>
                    <input id="product-name" type="text" name="name" value="{{ old('name') }}"
                           class="input-field {{ $errors->has('name') ? 'input-field-error' : '' }}"
                           placeholder="cth: Cincin Polos Elegan" required>
                    @error('name')<p class="field-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="input-label">SKU *</label>
                    <input id="product-sku" type="text" name="sku" value="{{ old('sku') }}"
                           class="input-field {{ $errors->has('sku') ? 'input-field-error' : '' }}"
                           placeholder="cth: CNC-24K-001" required>
                    @error('sku')<p class="field-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="input-label">Kategori *</label>
                    <select name="category_id" class="input-field" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->icon }} {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('category_id')<p class="field-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="input-label">Kadar Emas *</label>
                    <select name="gold_purity" class="input-field" required>
                        <option value="24K" selected>24K</option>
                    </select>
                </div>

                <div>
                    <label class="input-label">Berat (gram) *</label>
                    <input type="number" name="weight_gram" value="{{ old('weight_gram') }}"
                           class="input-field" step="0.001" min="0.001" placeholder="cth: 2.500" required>
                    @error('weight_gram')<p class="field-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="input-label">Harga Jual Dasar (Rp) *</label>
                    <input type="number" name="base_price" value="{{ old('base_price') }}"
                           class="input-field" min="1" placeholder="cth: 4050000" required>
                    @error('base_price')<p class="field-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="input-label">Harga Buyback (Rp)</label>
                    <input type="number" name="buy_back_price" value="{{ old('buy_back_price') }}"
                           class="input-field" min="0" placeholder="Biarkan kosong = otomatis 97%">
                </div>

                <div>
                    <label class="input-label">Stok *</label>
                    <input type="number" name="stock" value="{{ old('stock', 0) }}"
                           class="input-field" min="0" required>
                </div>

                <div class="col-span-2">
                    <label class="input-label">Deskripsi</label>
                    <textarea name="description" rows="3" class="input-field" placeholder="Deskripsi produk...">{{ old('description') }}</textarea>
                </div>

                <div class="col-span-2">
                    <label class="input-label">Foto Produk</label>
                    <input id="product-image-input" type="file" name="image" accept="image/*" class="input-field">
                    <img id="product-image-preview" src="" class="hidden mt-3 w-32 h-32 rounded-xl object-cover" alt="preview">
                    @error('image')<p class="field-error">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_available" value="1" {{ old('is_available',true) ? 'checked':'' }}
                               class="w-4 h-4 rounded" style="accent-color:#f59e0b;">
                        <span class="text-sm text-gray-300">Tampilkan di Katalog</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_reservable" value="1" {{ old('is_reservable',true) ? 'checked':'' }}
                               class="w-4 h-4 rounded" style="accent-color:#f59e0b;">
                        <span class="text-sm text-gray-300">Bisa Direservasi</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_basic" value="1" {{ old('is_basic') ? 'checked':'' }}
                               class="w-4 h-4 rounded" style="accent-color:#f59e0b;">
                        <span class="text-sm text-gray-300">Produk Ori/Basic (Default Halaman Utama)</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2" style="border-top:1px solid rgba(255,255,255,0.06);">
                <button type="submit" class="btn-orange">Simpan Produk</button>
                <a href="{{ route('admin.products.index') }}" class="btn-ghost">Batal</a>
            </div>
        </div>
    </form>
</div>

<x-slot name="scripts">
    @vite('resources/js/admin/products.js')
</x-slot>
</x-admin-app>
