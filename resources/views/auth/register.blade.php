@extends('layouts.app')

@section('title', 'Create account — De La Renaissance')

@section('content')
<div class="max-w-md mx-auto px-4 sm:px-6 py-16">
    <div class="bg-white rounded-2xl border border-[#e9dcc2] shadow-sm p-8">
        <h1 class="text-2xl text-center" style="font-family: 'Playfair Display', serif;">Create your account</h1>
        <p class="mt-1 text-sm text-center text-[#75323d]/70">Book faster and keep track of your stays.</p>

        <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-5">
            @csrf
            <div>
                <label for="name" class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1">Full name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                       class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#c6a15b]">
            </div>
            <div>
                <label for="email" class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                       class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#c6a15b]">
            </div>
            <div>
                <label for="password" class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1">Password</label>
                <input id="password" type="password" name="password" required
                       class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#c6a15b]">
            </div>
            <div>
                <label for="password_confirmation" class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1">Confirm password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                       class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#c6a15b]">
            </div>
            <button type="submit" class="w-full rounded-lg bg-[#5a1f28] text-[#f6ecd9] py-2.5 text-sm hover:bg-[#75323d] transition">
                Create account
            </button>
        </form>

        <p class="mt-6 text-sm text-center text-[#75323d]/70">
            Already have an account?
            <a href="{{ route('login') }}" class="text-[#a9853f] hover:underline">Sign in</a>
        </p>
    </div>
</div>
@endsection
