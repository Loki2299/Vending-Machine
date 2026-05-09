@extends('layouts.app')

@section('content')
    <section class="rounded border border-slate-200 bg-white p-6">
        <div class="flex items-start justify-between gap-6">
            <div>
                <h1 class="text-2xl font-semibold">{{ $product->name }}</h1>
                <p class="mt-2 text-slate-600">Product #{{ $product->id }}</p>
            </div>
            @auth
                <a class="rounded bg-emerald-700 px-4 py-2 text-white" href="{{ route('products.purchase', $product) }}">Buy</a>
            @endauth
        </div>
        <dl class="mt-6 grid gap-4 sm:grid-cols-2">
            <div class="rounded border border-slate-200 p-4">
                <dt class="text-sm text-slate-500">Price</dt>
                <dd class="text-xl font-semibold">${{ number_format((float) $product->price, 3) }}</dd>
            </div>
            <div class="rounded border border-slate-200 p-4">
                <dt class="text-sm text-slate-500">Available</dt>
                <dd class="text-xl font-semibold">{{ $product->quantity_available }}</dd>
            </div>
        </dl>
    </section>
@endsection
