<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'De La Renaissance — Hotel & Residences')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    @include('partials.theme')
</head>
<body class="min-h-screen flex flex-col bg-[#fbf6ec] text-[#2e1013]" style="font-family: 'Inter', sans-serif;">
    <header class="border-b border-[#e9dcc2] bg-[#fbf6ec]/90 backdrop-blur sticky top-0 z-40">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 flex items-center justify-between h-16">
            <a href="{{ route('home') }}" class="text-xl tracking-wide text-[#5a1f28]" style="font-family: 'Playfair Display', serif;">
                De La <span class="text-[#a9853f]">Renaissance</span>
            </a>
            <nav class="flex items-center gap-6 text-sm">
                <button type="button" onclick="dlrToggleTheme()" title="Switch light / dark mode" class="text-base leading-none hover:opacity-75 transition"><span data-theme-icon>🌙</span></button>
                <a href="{{ route('rooms.search') }}" class="text-[#75323d] hover:text-[#a9853f] transition">Rooms &amp; Suites</a>
                @auth
                    <a href="{{ route('bookings.index') }}" class="text-[#75323d] hover:text-[#a9853f] transition">My Bookings</a>
                    @if (auth()->user()->isStaff())
                        <a href="{{ route('admin.dashboard') }}" class="text-[#75323d] hover:text-[#a9853f] transition">Back Office</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-[#75323d] hover:text-[#a9853f] transition">Sign out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-[#75323d] hover:text-[#a9853f] transition">Sign in</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 rounded-full bg-[#5a1f28] text-[#f6ecd9] hover:bg-[#75323d] transition">Create account</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="flex-1">
        @if (session('status'))
            <div class="max-w-6xl mx-auto px-4 sm:px-6 mt-6">
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-900 px-4 py-3 text-sm">
                    {{ session('status') }}
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="max-w-6xl mx-auto px-4 sm:px-6 mt-6">
                <div class="rounded-lg border border-rose-200 bg-rose-50 text-rose-900 px-4 py-3 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="border-t border-[#e9dcc2] bg-[#2e1013] text-[#e9dcc2] mt-16">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-10 grid gap-6 sm:grid-cols-3 text-sm">
            <div>
                <p class="text-lg" style="font-family: 'Playfair Display', serif;">De La Renaissance</p>
                <p class="mt-2 text-[#cfae76]">Timeless hospitality, reimagined.</p>
            </div>
            <div>
                <p class="uppercase tracking-widest text-xs text-[#cfae76]">Contact</p>
                <p class="mt-2">{{ \App\Models\HotelSetting::get('phone', '+855 23 000 000') }}</p>
                <p>{{ \App\Models\HotelSetting::get('email', 'reservations@delarenaissance.example') }}</p>
            </div>
            <div>
                <p class="uppercase tracking-widest text-xs text-[#cfae76]">Reception</p>
                <p class="mt-2">Check-in from {{ \App\Models\HotelSetting::get('check_in_time', '14:00') }}</p>
                <p>Check-out by {{ \App\Models\HotelSetting::get('check_out_time', '12:00') }}</p>
            </div>
        </div>
    </footer>
</body>
</html>
