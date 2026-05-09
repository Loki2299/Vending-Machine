@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-xl rounded border border-slate-200 bg-white p-6">
        <h1 class="mb-6 text-2xl font-semibold">Create Product</h1>
        <form method="POST" action="{{ route('products.store') }}" class="space-y-4">
            @include('products._form', ['button' => 'Create Product'])
        </form>
    </section>
@endsection
