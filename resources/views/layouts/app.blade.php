
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
            list-style: none;
            cursor: pointer;
        }

        .admin-menu summary::-webkit-details-marker {
            display: none;
        }

        .admin-menu[open] .admin-arrow {
            transform: rotate(180deg);
        }

        .admin-arrow {
            transition: transform .2s ease;
        }

        .submenu-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px 8px 32px;
            border-radius: 7px;
            color: #cbdad2;
            font-size: 12px;
            transition: all .2s ease;
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

        <aside class="w-full bg-[#174b38] text-white md:fixed md:inset-y-0 md:left-0 md:flex md:w-64 md:flex-col">

            <div class="flex items-center gap-3 border-b border-white/10 px-5 py-5">

                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border-2 border-[#d7b65d] bg-white text-sm font-extrabold text-[#174b38]">
                    CvSU
                </div>

                <div>
                    <h1 class="text-sm font-extrabold tracking-wide">
                        CAVITE STATE UNIVERSITY
                    </h1>
                    <p class="mt-1 text-xs text-green-100">
                        Student Lost & Found
                    </p>
                    <p class="text-[10px] text-green-200/70">
                        Management System
                    </p>
                </div>

            </div>

            <nav class="flex-1 px-4 py-5">

                <p class="mb-3 px-3 text-[10px] font-bold uppercase tracking-widest text-green-200/70">
                    Main Navigation
                </p>

                <details class="admin-menu" id="adminMenu" data-force-open="{{ request()->routeIs('admin.dashboard', 'admin.students.*', 'admin.categories', 'admin.notifications') ? 'true' : 'false' }}" {{ request()->routeIs('admin.dashboard', 'admin.students.*', 'admin.categories', 'admin.notifications') ? 'open' : '' }}>

                    <summary class="flex items-center gap-2 rounded-lg px-3 py-2.5 text-sm font-semibold text-white transition hover:bg-[#28634c]">

                        <span class="text-base">⚙</span>
                        <span class="flex-1">Student Information</span>

                        <svg
                            class="admin-arrow h-3.5 w-3.5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="m6 9 6 6 6-6"
                            />
                        </svg>

                    </summary>

                    <div class="mt-1 space-y-1">

                        <a
                            href="{{ route('admin.dashboard') }}"
                            class="submenu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                        >
                            <span>▪</span>
                            <span>Dashboard</span>
                        </a>

                        <a
                            href="{{ route('admin.students.index') }}"
                            class="submenu-link {{ request()->routeIs('admin.students.index') ? 'active' : '' }}"
                        >
                            <span>♟</span>
                            <span>Manage Student Accounts</span>
                        </a>

                        <a
                            href="{{ route('admin.categories') }}"
                            class="submenu-link {{ request()->routeIs('admin.categories') ? 'active' : '' }}"
                        >
                            <span>▪</span>
                            <span>Manage Categories</span>
                        </a>

                    </div>

                </details>

            </nav>

            <div class="border-t border-white/10 px-5 py-4">
                <p class="text-xs font-semibold text-white">
                    CvSU Lost & Found
                </p>
                <p class="mt-1 text-[10px] text-green-200/70">
                    Administrator Portal
                </p>
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
        <span class="absolute right-0 top-0 z-10 flex h-[19px] min-w-[19px] items-center justify-center rounded-full border-2 border-white bg-red-600 px-1 text-[9px] font-extrabold leading-none text-white shadow">
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const adminMenu = document.getElementById('adminMenu');

            if (!adminMenu) return;

            const storageKey = 'cvsuAdminMenuOpenV2';

            adminMenu.open = adminMenu.dataset.forceOpen === 'true'
                || adminMenu.querySelector('.submenu-link.active') !== null
                || sessionStorage.getItem(storageKey) === 'true';

            adminMenu.addEventListener('toggle', function () {
                sessionStorage.setItem(
                    storageKey,
                    adminMenu.open ? 'true' : 'false'
                );
            });

            adminMenu.querySelectorAll('a').forEach(function (link) {
                link.addEventListener('click', function () {
                    sessionStorage.setItem(storageKey, 'true');
                });
            });
        });
    </script>

</body>
</html>
