@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-md rounded border border-slate-200 bg-white p-6">
        <h1 class="mb-6 text-2xl font-semibold">Register</h1>
        <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
            @csrf
            <label class="block">
                <span class="text-sm font-medium">Name</span>
                <input class="mt-1 w-full rounded border border-slate-300 px-3 py-2" name="name" value="{{ old('name') }}" required maxlength="255">
            </label>
            <label class="block">
                <span class="text-sm font-medium">Email</span>
                <input class="mt-1 w-full rounded border border-slate-300 px-3 py-2" name="email" type="email" value="{{ old('email') }}" required>
            </label>
            <label class="block">
                <span class="text-sm font-medium">Password</span>
                <input class="mt-1 w-full rounded border border-slate-300 px-3 py-2" name="password" type="password" required minlength="8">
            </label>
            <label class="block">
                <span class="text-sm font-medium">Confirm Password</span>
                <input class="mt-1 w-full rounded border border-slate-300 px-3 py-2" name="password_confirmation" type="password" required minlength="8">
            </label>
            <button class="w-full rounded bg-slate-900 px-4 py-2 text-white">Create account</button>
        </form>
    </section>
@endsection
