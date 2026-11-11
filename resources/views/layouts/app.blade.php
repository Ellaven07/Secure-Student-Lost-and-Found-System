
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Admin Dashboard') | CvSU Lost & Found</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'DM Sans', sans-serif;
            background: #f5f7f6;
        }

        .admin-menu summary {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 12px;
            border-radius: 9px;
            color: white;
            cursor: pointer;
            font-size: 13px;
            font-weight: 700;
            list-style: none;
            transition: background .15s ease;
        }

        .admin-menu summary::-webkit-details-marker {
            display: none;
        }

        .admin-menu summary:hover {
            background: #28634c;
        }

        .admin-menu[open] .admin-arrow {
            transform: rotate(180deg);
        }

        .admin-arrow {
            transition: transform .15s ease;
        }

        .submenu-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px 9px 32px;
            border-radius: 8px;
            color: #cbdad2;
            font-size: 12px;
            font-weight: 600;
            transition: background .15s ease, color .15s ease;
        }

        .submenu-link svg {
            width: 16px;
            height: 16px;
            flex: none;
            opacity: .85;
        }

        .submenu-link:hover,
        .submenu-link.active {
            background: #28634c;
            color: white;
        }

        .submenu-link.active {
            box-shadow: inset 3px 0 0 #d7b65d;
        }

    </style>
</head>

<body class="min-h-screen">

    <div class="min-h-screen md:flex">

        <aside class="w-full overflow-y-auto bg-[#174b38] text-white md:fixed md:inset-y-0 md:left-0 md:flex md:w-64 md:flex-col">
            <div class="flex items-center gap-3 border-b border-white/10 px-5 py-6">
                <div class="grid h-12 w-12 shrink-0 place-items-center rounded-full border-2 border-[#d7b65d] bg-white text-sm font-extrabold text-[#174b38]">CvSU</div>
                <div class="min-w-0">
                    <h1 class="truncate text-xs font-extrabold tracking-[.08em]">CAVITE STATE UNIVERSITY</h1>
                    <p class="mt-1 text-xs text-green-100">Student Lost &amp; Found</p>
                    <p class="text-[10px] text-green-200/70">Administrator Portal</p>
                </div>
            </div>

            <nav aria-label="Admin navigation" class="flex-1 px-4 py-5">
                <p class="mb-3 px-3 text-[10px] font-bold uppercase tracking-widest text-green-200/70">Main Navigation</p>

                <details class="admin-menu" {{ request()->is('admin/*') ? 'open' : '' }}>
                    <summary>
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm0-5v2m0 14v2m9-9h-2M5 12H3m15.36-6.36-1.42 1.42M7.06 16.94l-1.42 1.42m12.72 0-1.42-1.42M7.06 7.06 5.64 5.64" /></svg>
                        <span class="flex-1">Student Information</span>
                        <svg class="admin-arrow h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6" /></svg>
                    </summary>

                    <div class="mt-2 space-y-1">
                        <a href="{{ route('admin.dashboard') }}" class="submenu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1z" /></svg>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('admin.students.index') }}" class="submenu-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m16 0v-2a4 4 0 0 0-3-3.87M14 3.13a4 4 0 0 1 0 7.75M14 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" /></svg>
                            <span>Manage Student Accounts</span>
                        </a>
                        <a href="{{ route('admin.categories') }}" class="submenu-link {{ request()->routeIs('admin.categories') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5h16M4 12h16M4 19h16M8 5v14m8-14v14" /></svg>
                            <span>Manage Categories</span>
                        </a>

                        <a href="{{ route('admin.reports.index') }}" class="submenu-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round' stroke-linejoin='round' stroke-width='1.8' d='M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01' /></svg>
                            <span>Review Item Reports</span>
                        </a>
                        <a href="{{ route('admin.claims.index') }}" class="submenu-link {{ request()->routeIs('admin.claims.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap='round' stroke-linejoin='round' stroke-width='1.8' d='m5 12 4 4L19 6' /></svg>
                            <span>Review Claim Requests</span>
                        </a>

                        <p class="px-10 pt-3 text-[10px] font-bold uppercase tracking-widest text-green-200/70">Admin Pages</p>
                        <a href="{{ route('admin.claims.index') }}" class="submenu-link {{ request()->routeIs('admin.claims.*') ? 'active' : '' }}"><span>Claim Requests</span></a>
                        <a href="{{ route('admin.reports.index') }}" class="submenu-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"><span>All Reports</span></a>
                        <a href="{{ route('admin.dashboard') }}#activity-logs-page" class="submenu-link"><span>Activity Logs</span></a>
                        <a href="{{ route('admin.dashboard') }}#generate-print-reports-page" class="submenu-link"><span>Generate and Print Reports</span></a>
                    </div>
                </details>

                <a href="{{ route('admin.profile') }}" class="submenu-link mt-2 {{ request()->routeIs('admin.profile') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round' stroke-linejoin='round' stroke-width='1.8' d='M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm0-5v2m0 14v2m9-9h-2M5 12H3' /></svg>
                    <span>Settings</span>
                </a>
            </nav>

            <div class="border-t border-white/10 p-4">
                <div class="flex items-center gap-3 rounded-xl bg-white/5 p-3">
                    <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-[#d7b65d] font-bold text-[#173c2d]">{{ str(auth()->user()->name)->substr(0, 1)->upper() }}</div>
                    <div class="min-w-0">
                        <p class="truncate text-xs font-semibold text-white">{{ auth()->user()->name }}</p>
                        <p class="mt-1 text-[10px] text-green-100/70">Administrator</p>
                    </div>
                </div>
            </div>
        </aside>

        <div class="min-w-0 flex-1 md:ml-64">

            <header class="sticky top-0 z-10 flex min-h-[72px] items-center justify-between border-b border-gray-200 bg-white px-5 py-4 shadow-sm sm:px-8">

                <div>
                    <p class="text-xs font-medium text-gray-400">
                        CvSU / ADMIN
                    </p>

                    <h2 class="mt-1 text-lg font-bold text-gray-800">
                        @yield('title', 'Admin Dashboard')
                    </h2>
                </div>

                <div class="flex items-center gap-4">

                    

<!-- Admin Notifications -->
<a
    href="{{ route('admin.notifications') }}"
    title="Notifications"
    aria-label="Notifications"
    class="relative inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-gray-500 transition hover:bg-gray-100 hover:text-[#174b38]"
>
    <svg
        class="h-6 w-6"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        aria-hidden="true"
    >
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="1.8"
            d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 0 0-5-5.9V4a1 1 0 0 0-2 0v1.1A6 6 0 0 0 6 11v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0a3 3 0 0 1-6 0"
        />
    </svg>

    @if (($unreadNotificationCount ?? 0) > 0)
        <span class="absolute -right-1 -top-1 z-10 flex h-5 min-w-5 items-center justify-center rounded-md bg-red-700 px-1.5 text-[10px] font-bold leading-none text-white">
            {{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}
        </span>
    @endif
</a>


                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            type="submit"
                            title="Sign out"
                            aria-label="Sign out"
                            class="flex h-10 items-center justify-center gap-2 rounded-lg border border-gray-200 px-3 text-sm font-semibold text-gray-600 transition hover:border-red-200 hover:bg-red-50 hover:text-red-700"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5m5 5H3" />
                            </svg>
                            <span class="hidden sm:inline">Sign out</span>
                        </button>
                    </form>

                    <div class="hidden text-right sm:block">
                        <p class="text-sm font-semibold text-gray-800">
                            {{ auth()->user()->name }}
                        </p>
                        <p class="text-xs text-gray-500">
                            Administrator
                        </p>
                    </div>

                    <a href="{{ route('admin.profile') }}" title="View admin profile" aria-label="View admin profile" class="flex h-10 w-10 items-center justify-center rounded-full bg-[#e8eee9] font-bold text-[#174b38] transition hover:bg-[#d7e4dc] focus:outline-none focus:ring-2 focus:ring-[#174b38] focus:ring-offset-2">
                        {{ str(auth()->user()->name)->substr(0, 1)->upper() }}
                    </a>

                </div>

            </header>

            <main class="p-5 sm:p-8">
                @yield('content')
            </main>

            <footer class="border-t border-gray-200 px-5 py-5 text-center text-xs text-gray-400 sm:px-8">
                &copy; {{ date('Y') }} Cavite State University Student Lost & Found System. All rights reserved.
            </footer>

        </div>

    </div>


</body>
</html>
