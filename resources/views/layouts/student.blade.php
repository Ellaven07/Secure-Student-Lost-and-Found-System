<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Student Dashboard') | CvSU Lost &amp; Found</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'DM Sans', sans-serif; background: #f5f7f6; }
        .portal-link { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px; color: #cbdad2; font-size: 13px; font-weight: 600; transition: background .2s, color .2s; }
        .portal-link:hover, .portal-link.active { background: #28634c; color: white; }
        .portal-link.active { box-shadow: inset 3px 0 0 #d7b65d; }
    </style>
</head>
<body class="min-h-screen">
    <div class="min-h-screen md:flex">
        <aside class="w-full bg-[#174b38] text-white md:fixed md:inset-y-0 md:left-0 md:flex md:w-64 md:flex-col">
            <div class="flex items-center gap-3 border-b border-white/10 px-5 py-5">
                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-full border-2 border-[#d7b65d] bg-white text-sm font-extrabold text-[#174b38]">CvSU</div>
                <div>
                    <h1 class="text-sm font-extrabold tracking-wide">CAVITE STATE UNIVERSITY</h1>
                    <p class="mt-1 text-xs text-green-100">Student Lost &amp; Found</p>
                    <p class="text-[10px] text-green-200/70">Student Portal</p>
                </div>
            </div>
            <nav class="flex-1 px-4 py-5">
                <p class="mb-3 px-3 text-[10px] font-bold uppercase tracking-widest text-green-200/70">Student Navigation</p>
                <div class="space-y-1">
                    <a href="{{ route('student.dashboard') }}" class="portal-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}"><span aria-hidden="true">⌂</span><span>Dashboard</span></a>
                    <a href="{{ route('student.profile') }}" class="portal-link {{ request()->routeIs('student.profile') ? 'active' : '' }}"><span aria-hidden="true">♙</span><span>My Profile</span></a>
                    <a href="{{ route('student.notifications') }}" class="portal-link {{ request()->routeIs('student.notifications') ? 'active' : '' }}"><span aria-hidden="true">♢</span><span>Notifications</span></a>
                </div>
            </nav>
            <div class="border-t border-white/10 px-5 py-4">
                <p class="text-xs font-semibold text-white">{{ auth()->user()->name }}</p>
                <p class="mt-1 text-[10px] text-green-200/70">Student Account</p>
            </div>
        </aside>

        <div class="min-w-0 flex-1 md:ml-64">
            <header class="sticky top-0 z-10 flex min-h-[72px] items-center justify-between border-b border-gray-200 bg-white px-5 py-4 shadow-sm sm:px-8">
                <div>
                    <p class="text-xs font-medium text-gray-400">CvSU / STUDENT</p>
                    <h2 class="mt-1 text-lg font-bold text-gray-800">@yield('title', 'Student Dashboard')</h2>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('student.notifications') }}" title="Notifications" aria-label="Notifications" class="rounded-full p-2 text-gray-500 transition hover:bg-gray-100 hover:text-[#174b38]">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 0 0-5-5.9V4a1 1 0 0 0-2 0v1.1A6 6 0 0 0 6 11v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0a3 3 0 0 1-6 0" />
                        </svg>
                    </a>
                    <span class="hidden text-sm font-semibold text-gray-700 sm:inline">{{ auth()->user()->name }}</span>
                    <div class="grid h-10 w-10 place-items-center rounded-full bg-[#e8eee9] font-bold text-[#174b38]">{{ str(auth()->user()->name)->substr(0, 1)->upper() }}</div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-lg border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-600 transition hover:border-[#174b38] hover:text-[#174b38]">Sign out</button>
                    </form>
                </div>
            </header>
            <main class="p-5 sm:p-8">@yield('content')</main>
            <footer class="border-t border-gray-200 px-5 py-5 text-center text-xs text-gray-400 sm:px-8">© {{ date('Y') }} Cavite State University Student Lost &amp; Found System.</footer>
        </div>
    </div>
</body>
</html>
