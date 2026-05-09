@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-xl rounded border border-slate-200 bg-white p-6">
        <h1 class="mb-6 text-2xl font-semibold">Edit Product</h1>
        <form method="POST" action="{{ route('products.update', $product) }}" class="space-y-4">
            @method('PUT')
            @include('products._form', ['button' => 'Update Product'])
        </form>
    </section>
@endsection
