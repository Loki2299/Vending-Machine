@csrf

<label class="block">
    <span class="text-sm font-medium">Name</span>
    <input class="mt-1 w-full rounded border border-slate-300 px-3 py-2" name="name" value="{{ old('name', $product->name ?? '') }}"  maxlength="255">
</label>

<label class="block">
    <span class="text-sm font-medium">Price</span>
    <input class="mt-1 w-full rounded border border-slate-300 px-3 py-2" name="price" type="number" min="0.001" step="0.001" value="{{ old('price', $product->price ?? '') }}" >
</label>

<label class="block">
    <span class="text-sm font-medium">Quantity Available</span>
    <input class="mt-1 w-full rounded border border-slate-300 px-3 py-2" name="quantity_available" type="number" min="0" step="1" value="{{ old('quantity_available', $product->quantity_available ?? 0) }}" >
</label>

<div class="flex gap-3">
    <button class="rounded bg-slate-900 px-4 py-2 text-white">{{ $button }}</button>
    <a class="rounded border border-slate-300 px-4 py-2" href="{{ route('products.index') }}">Cancel</a>
</div>
