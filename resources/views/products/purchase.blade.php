@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-xl rounded border border-slate-200 bg-white p-6">
        <h1 class="mb-6 text-2xl font-semibold">Purchase {{ $product->name }}</h1>
        <dl class="mb-6 grid gap-4 sm:grid-cols-2">
            <div>
                <dt class="text-sm text-slate-500">Price</dt>
                <dd class="font-semibold">${{ number_format((float) $product->price, 3) }}</dd>
            </div>
            <div>
                <dt class="text-sm text-slate-500">Available</dt>
                <dd class="font-semibold">{{ $product->quantity_available }}</dd>
            </div>
        </dl>
        <form method="POST" action="{{ route('products.purchase.store', $product) }}" class="space-y-4">
            @csrf
            <label class="block">
                <span class="text-sm font-medium">Quantity</span>
                <input class="mt-1 w-full rounded border border-slate-300 px-3 py-2" name="quantity" type="number" min="1" max="{{ $product->quantity_available }}" step="1" value="{{ old('quantity', 1) }}" required>
            </label>
            <button class="rounded bg-emerald-700 px-4 py-2 text-white">Complete Purchase</button>
            <a class="ml-2 rounded border border-slate-300 px-4 py-2" href="{{ route('products.show', $product) }}">Cancel</a>
        </form>
    </section>
@endsection
