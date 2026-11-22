<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Back Office') — De La Renaissance</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    @include('partials.theme')
</head>
<body class="min-h-screen bg-[#f7f1e6] text-[#2e1013]" style="font-family: 'Inter', sans-serif;">
    <div class="flex min-h-screen">
        <aside class="w-60 shrink-0 bg-[#2e1013] text-[#e9dcc2] flex flex-col">
            <div class="px-5 py-5 border-b border-[#75323d]">
                <a href="{{ route('admin.dashboard') }}" class="text-lg" style="font-family: 'Playfair Display', serif;">
                    De La <span class="text-[#cfae76]">Renaissance</span>
                </a>
                <p class="text-xs text-[#cfae76] mt-1 uppercase tracking-widest">Back Office</p>
            </div>
            @php
                $navLink = 'block px-5 py-2.5 text-sm hover:bg-[#5a1f28] hover:text-[#cfae76] transition';
                $navActive = 'bg-[#5a1f28] text-[#cfae76]';
            @endphp
            <nav class="py-4 flex-1">
                <a href="{{ route('admin.dashboard') }}" class="{{ $navLink }} {{ request()->routeIs('admin.dashboard') ? $navActive : '' }}">Dashboard</a>
                <a href="{{ route('admin.reservations.index') }}" class="{{ $navLink }} {{ request()->routeIs('admin.reservations.index', 'admin.reservations.show') ? $navActive : '' }}">Reservations</a>
                <a href="{{ route('admin.reservations.calendar') }}" class="{{ $navLink }} {{ request()->routeIs('admin.reservations.calendar') ? $navActive : '' }}">Calendar</a>
                <a href="{{ route('admin.reservations.create') }}" class="{{ $navLink }} {{ request()->routeIs('admin.reservations.create') ? $navActive : '' }}">Walk-in Booking</a>
                <a href="{{ route('admin.frontdesk.board') }}" class="{{ $navLink }} {{ request()->routeIs('admin.frontdesk.*') ? $navActive : '' }}">Room Board</a>
                <a href="{{ route('admin.guests.index') }}" class="{{ $navLink }} {{ request()->routeIs('admin.guests.*') ? $navActive : '' }}">Guests</a>
                @role('admin')
                    <p class="px-5 pt-5 pb-1 text-[10px] uppercase tracking-widest text-[#cfae76]/70">Administration</p>
                    <a href="{{ route('admin.room-types.index') }}" class="{{ $navLink }} {{ request()->routeIs('admin.room-types.*') ? $navActive : '' }}">Room Types</a>
                    <a href="{{ route('admin.rooms.index') }}" class="{{ $navLink }} {{ request()->routeIs('admin.rooms.*') ? $navActive : '' }}">Rooms</a>
                    <a href="{{ route('admin.services.index') }}" class="{{ $navLink }} {{ request()->routeIs('admin.services.*') ? $navActive : '' }}">Services</a>
                @endrole
            </nav>
            <div class="px-5 py-4 border-t border-[#75323d] text-sm">
                <button type="button" onclick="dlrToggleTheme()" class="w-full mb-3 rounded-lg border border-[#75323d] py-1.5 text-xs text-[#cfae76] hover:bg-[#5a1f28] transition"><span data-theme-icon>🌙</span> <span data-theme-label>Dark mode</span></button>
                <p class="truncate text-[#e9dcc2]">{{ auth()->user()->name }}</p>
                <div class="flex items-center justify-between mt-2">
                    <a href="{{ route('home') }}" class="text-[#cfae76] hover:underline text-xs">View site</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-[#cfae76] hover:underline text-xs">Sign out</button>
                    </form>
                </div>
            </div>
        </aside>

        <main class="flex-1 min-w-0">
            <div class="max-w-6xl mx-auto px-6 py-8">
                @if (session('status'))
                    <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-900 px-4 py-3 text-sm">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 rounded-lg border border-rose-200 bg-rose-50 text-rose-900 px-4 py-3 text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
