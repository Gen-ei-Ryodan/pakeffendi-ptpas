@extends('admin.layouts.app')

@section('title', 'Edit Product')
@section('breadcrumb', 'Home / Stock / Edit')
@section('header', 'Edit Product')

@section('content')
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm w-full">
        <form method="post" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <div class="text-sm font-semibold mb-3">General</div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Photo</label>
                        <input type="file" name="photo" accept="image/*" class="w-full">
                        @if($product->photo_path)
                            <div class="mt-2">
                                <img src="{{ $product->photo_url }}" class="w-32 h-32 object-cover rounded-lg border">
                            </div>
                        @endif
                    </div>
                    <div></div>
                    <div>
                        <label class="block text-sm font-medium mb-1">SKU *</label>
                        <input name="sku" value="{{ old('sku', $product->sku) }}" required class="w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Nama *</label>
                        <input name="name" value="{{ old('name', $product->name) }}" required class="w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Variant</label>
                        <input name="variant" value="{{ old('variant', $product->variant) }}" class="w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Brand *</label>
                        <select name="product_brand_code" required class="w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-sky-500">
                            <option value="">Pilih Brand</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->brand_code }}" @selected(old('product_brand_code', $product->product_brand_code) === $brand->brand_code)>{{ $brand->brand_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Category *</label>
                        <select name="product_category_code" required class="w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-sky-500">
                            <option value="">Pilih Category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->category_code }}" @selected(old('product_category_code', $product->product_category_code) === $category->category_code)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Deskripsi Stok *</label>
                        <textarea name="description" rows="5" required class="w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-sky-500">{{ old('description', $product->description) }}</textarea>
                    </div>
                </div>
            </div>

            <div>
                <div class="text-sm font-semibold mb-3">Tiered Price</div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Harga 1 *</label>
                        <input type="number" step="0.01" name="price_1" value="{{ old('price_1', $product->price_1) }}" required class="w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Quantity 1</label>
                        <input type="number" name="qty_1" value="{{ old('qty_1', $product->qty_1) }}" class="w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Disc 1</label>
                        <input type="number" step="0.01" name="disc_1" value="{{ old('disc_1', $product->disc_1) }}" class="w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-sky-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Harga 2</label>
                        <input type="number" step="0.01" name="price_2" value="{{ old('price_2', $product->price_2) }}" class="w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Quantity 2</label>
                        <input type="number" name="qty_2" value="{{ old('qty_2', $product->qty_2) }}" class="w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Disc 2</label>
                        <input type="number" step="0.01" name="disc_2" value="{{ old('disc_2', $product->disc_2) }}" class="w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-sky-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Harga 3</label>
                        <input type="number" step="0.01" name="price_3" value="{{ old('price_3', $product->price_3) }}" class="w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Quantity 3</label>
                        <input type="number" name="qty_3" value="{{ old('qty_3', $product->qty_3) }}" class="w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Disc 3</label>
                        <input type="number" step="0.01" name="disc_3" value="{{ old('disc_3', $product->disc_3) }}" class="w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-sky-500">
                    </div>
                </div>
            </div>

            <div>
                <div class="text-sm font-semibold mb-3">Advance</div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Satuan Unit *</label>
                        <input name="unit" value="{{ old('unit', $product->unit) }}" required class="w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Berat (Kg) *</label>
                        <input type="number" step="0.01" name="weight_kg" value="{{ old('weight_kg', $product->weight_kg) }}" required class="w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-sky-500">
                    </div>
                    <div class="flex items-end">
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="checkbox" name="discontinued" value="1" class="rounded border-slate-300 text-sky-600 focus:ring-sky-500" @checked(old('discontinued', $product->discontinued))>
                            Discontinued
                        </label>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Status Product</label>
                        <input name="status_product" value="{{ old('status_product', $product->status_product) }}" class="w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-sky-500" placeholder="TERLARIS, PROMO, TERBARU, DLL">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">No. Urut Status</label>
                        <input type="number" name="no_urut_status" value="{{ old('no_urut_status', $product->no_urut_status ?? 0) }}" min="0" class="w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-sky-500">
                    </div>
                    <div></div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 rounded-lg bg-sky-600 text-white font-semibold hover:bg-sky-700">Simpan</button>
                <a href="{{ route('admin.products.index') }}" class="px-4 py-2 rounded-lg border border-slate-200 hover:bg-slate-100">Batal</a>
            </div>
        </form>
    </div>
@endsection
