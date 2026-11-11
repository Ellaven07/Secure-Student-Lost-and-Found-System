<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <title>@yield('title', 'Browse Items') | CvSU Lost &amp; Found</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f6f8f6] font-['Poppins'] text-gray-800 antialiased">
    <div class="min-h-screen">
        <header class="border-b border-gray-200 bg-white shadow-sm">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-7">
                <a href="{{ route('visitor.items.index') }}" class="flex min-w-0 items-center gap-3">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full border-2 border-[#d7b65d] bg-white p-0.5">
                        <img src="{{ asset('cvsu-logo.png') }}" alt="Cavite State University seal" class="h-full w-full object-contain">
                    </span>
                    <span class="min-w-0">
                        <span class="block truncate text-xs font-extrabold tracking-wide text-[#174b38]">CAVITE STATE UNIVERSITY</span>
                        <span class="mt-1 block text-[11px] text-gray-500">Visitor · Browse Items</span>
                    </span>
                </a>

                <div class="flex shrink-0 items-center gap-3">
                    <a href="{{ route('visitor.items.index') }}" class="hidden rounded-lg bg-[#edf4ef] px-3 py-2 text-xs font-bold text-[#174b38] sm:inline-flex sm:px-4 sm:text-sm">Browse Items</a>
                    <a href="{{ route('visitor.profile') }}" aria-label="View your profile" class="flex shrink-0 items-center gap-2 rounded-lg px-2 py-1.5 text-[#174b38] transition hover:bg-[#edf4ef] focus:outline-none focus:ring-2 focus:ring-[#56846b]">
                        <div class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-[#e8eee9] text-sm font-bold text-[#174b38]" aria-hidden="true">
                            {{ strtoupper(mb_substr(auth()->user()->name ?? 'V', 0, 1)) }}
                        </div>
                        <div class="hidden min-w-0 sm:block">
                            <p class="max-w-36 truncate text-xs font-semibold text-gray-700">{{ auth()->user()->name }}</p>
                            <p class="max-w-36 truncate text-[10px] text-gray-500">{{ auth()->user()->email }} · Visitor</p>
                        </div>
                        <span class="text-xs font-semibold sm:hidden">Profile</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-600 transition hover:bg-gray-50 sm:px-4 sm:text-sm">Log out</button>
                    </form>
                </div>
            </div>
        </header>

        <main class="w-full px-4 py-7 sm:px-7 lg:px-9">
            @yield('content')
        </main>
    </div>
</body>
</html>
