@extends('layouts.app')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Products</h1>
        @auth
            @if (auth()->user()->isAdmin())
                <a class="rounded bg-slate-900 px-4 py-2 text-white" href="{{ route('products.create') }}">Add Product</a>
            @endif
        @endauth
    </div>

    @php
        $nextDirection = $direction === 'asc' ? 'desc' : 'asc';
        $sortLink = fn ($field) => route('products.index', ['sort' => $field, 'direction' => $sort === $field ? $nextDirection : 'asc']);
    @endphp

    <div class="overflow-hidden rounded border border-slate-200 bg-white">
        <table class="w-full border-collapse text-left text-sm">
            <thead class="bg-slate-100">
                <tr>
                    <th class="px-4 py-3"><a href="{{ $sortLink('id') }}">ID</a></th>
                    <th class="px-4 py-3"><a href="{{ $sortLink('name') }}">Name</a></th>
                    <th class="px-4 py-3"><a href="{{ $sortLink('price') }}">Price</a></th>
                    <th class="px-4 py-3"><a href="{{ $sortLink('quantity_available') }}">Quantity</a></th>
                    <th class="px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr class="border-t border-slate-200">
                        <td class="px-4 py-3">{{ $product->id }}</td>
                        <td class="px-4 py-3">{{ $product->name }}</td>
                        <td class="px-4 py-3">${{ number_format((float) $product->price, 3) }}</td>
                        <td class="px-4 py-3">{{ $product->quantity_available }}</td>
                        <td class="flex flex-wrap gap-2 px-4 py-3">
                            <a class="rounded border border-slate-300 px-3 py-1.5" href="{{ route('products.show', $product) }}">View</a>
                            @auth
                                <a class="rounded bg-emerald-700 px-3 py-1.5 text-white" href="{{ route('products.purchase', $product) }}">Buy</a>
                                @if (auth()->user()->isAdmin())
                                    <a class="rounded border border-slate-300 px-3 py-1.5" href="{{ route('products.edit', $product) }}">Edit</a>
                                    <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Delete this product?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded bg-red-700 px-3 py-1.5 text-white">Delete</button>
                                    </form>
                                @endif
                            @endauth
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $products->links() }}</div>
@endsection
