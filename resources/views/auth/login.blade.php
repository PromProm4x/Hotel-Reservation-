@extends('layouts.app')

@section('title', 'Sign in — De La Renaissance')

@section('content')
<div class="max-w-md mx-auto px-4 sm:px-6 py-16">
    <div class="bg-white rounded-2xl border border-[#e9dcc2] shadow-sm p-8">
        <h1 class="text-2xl text-center" style="font-family: 'Playfair Display', serif;">Welcome back</h1>
        <p class="mt-1 text-sm text-center text-[#75323d]/70">Sign in to manage your reservations.</p>

        <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
            @csrf
            <div>
                <label for="email" class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#c6a15b]">
            </div>
            <div>
                <label for="password" class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1">Password</label>
                <input id="password" type="password" name="password" required
                       class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#c6a15b]">
            </div>
            <label class="flex items-center gap-2 text-sm text-[#75323d]/80">
                <input type="checkbox" name="remember" class="rounded border-[#e9dcc2]">
                Remember me
            </label>
            <button type="submit" class="w-full rounded-lg bg-[#5a1f28] text-[#f6ecd9] py-2.5 text-sm hover:bg-[#75323d] transition">
                Sign in
            </button>
        </form>

        <p class="mt-6 text-sm text-center text-[#75323d]/70">
            New to De La Renaissance?
            <a href="{{ route('register') }}" class="text-[#a9853f] hover:underline">Create an account</a>
        </p>
    </div>
</div>
@endsection
