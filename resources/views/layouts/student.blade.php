<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <title>@yield('title', 'Student Portal') | CvSU Lost & Found</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="min-h-screen bg-[#f6f8f6] font-['Poppins'] text-gray-800 antialiased">
    @php($profilePhotoUrl = app(\App\Services\SupabaseStorageService::class)->url(auth()->user()->profile_photo_path))

    <div class="min-h-screen md:flex">

        <!-- Mobile sidebar backdrop -->
        <div
            id="studentSidebarBackdrop"
            class="fixed inset-0 z-40 hidden bg-black/40 md:hidden"
            onclick="toggleStudentSidebar()"
        ></div>

        <!-- Student Sidebar - Admin Design -->
        <aside
            id="studentSidebar"
            class="fixed inset-y-0 left-0 z-50 flex h-screen w-64 -translate-x-full flex-col bg-[#174b38] text-white transition-transform duration-200 md:inset-y-0 md:left-0 md:flex md:w-64 md:translate-x-0"
        >
            <!-- CvSU Branding -->
            <div class="flex items-center gap-3 border-b border-white/10 px-5 py-5">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border-2 border-[#d7b65d] bg-white p-0.5">
                    <img src="{{ asset('cvsu-logo.png') }}" alt="Cavite State University seal" class="h-full w-full object-contain">
                </div>

                <div class="min-w-0">
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

                <!-- Mobile close button -->
            </div>

            <!-- Student Navigation -->
            <nav class="flex-1 px-4 py-5">
                <p class="mb-3 px-3 text-[10px] font-bold uppercase tracking-widest text-green-200/70">
                    Main Navigation
                </p>

                <div class="space-y-1">

                    <a
                        href="{{ route('student.dashboard') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition
                        {{ request()->routeIs('student.dashboard') ? 'border-l-[3px] border-[#d7b65d] bg-[#28634c] text-white' : 'text-green-100/80 hover:bg-[#28634c] hover:text-white' }}"
                    >
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1V10Z" />
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('student.reports.lost.create') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('student.reports.lost.*') ? 'border-l-[3px] border-[#d7b65d] bg-[#28634c] text-white' : 'text-green-100/80 hover:bg-[#28634c] hover:text-white' }}">
                        <span class="grid h-5 w-5 shrink-0 place-items-center" aria-hidden="true">−</span>
                        <span>Report Lost Item</span>
                    </a>

                    <a href="{{ route('student.reports.found.create') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('student.reports.found.*') ? 'border-l-[3px] border-[#d7b65d] bg-[#28634c] text-white' : 'text-green-100/80 hover:bg-[#28634c] hover:text-white' }}">
                        <span class="grid h-5 w-5 shrink-0 place-items-center" aria-hidden="true">+</span>
                        <span>Report Found Item</span>
                    </a>

                    <a href="{{ route('student.reports.history') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('student.reports.history') ? 'border-l-[3px] border-[#d7b65d] bg-[#28634c] text-white' : 'text-green-100/80 hover:bg-[#28634c] hover:text-white' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 2m6-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        <span>My Report History</span>
                    </a>

                    <a href="{{ route('student.items.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('student.items.*') || request()->routeIs('student.claims.create') ? 'border-l-[3px] border-[#d7b65d] bg-[#28634c] text-white' : 'text-green-100/80 hover:bg-[#28634c] hover:text-white' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-4.3-4.3M19 10.5a8.5 8.5 0 1 1-17 0 8.5 8.5 0 0 1 17 0Z" /></svg>
                        <span>Browse Items</span>
                    </a>

                    <a href="{{ route('student.claims.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('student.claims.index') ? 'border-l-[3px] border-[#d7b65d] bg-[#28634c] text-white' : 'text-green-100/80 hover:bg-[#28634c] hover:text-white' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12.8 11.2 15 16 9.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        <span>My Claim Status</span>
                    </a>

                    <a
                        href="{{ route('student.profile') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition
                        {{ request()->routeIs('student.profile') ? 'border-l-[3px] border-[#d7b65d] bg-[#28634c] text-white' : 'text-green-100/80 hover:bg-[#28634c] hover:text-white' }}"
                    >
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm7 9a7 7 0 0 0-14 0" />
                        </svg>
                        <span>Settings</span>
                    </a>

                </div>
            </nav>

            <!-- Student Account and Logout -->
            <div class="border-t border-white/10 px-5 py-4">
                <div class="mb-3 flex items-center gap-3">
                    <div class="grid h-10 w-10 shrink-0 place-items-center overflow-hidden rounded-full border border-[#d7b65d] bg-white text-sm font-bold text-[#174b38]">
                        @if ($profilePhotoUrl)
                            <img src="{{ $profilePhotoUrl }}" alt="Profile photo of {{ auth()->user()->name }}" class="h-full w-full object-cover">
                        @else
                            {{ strtoupper(mb_substr(auth()->user()->name ?? 'S', 0, 1)) }}
                        @endif
                    </div>

                    <div class="min-w-0">
                        <p class="truncate text-xs font-semibold text-white">
                            {{ auth()->user()->name }}
                        </p>
                        <p class="mt-1 text-[10px] text-green-200/70">
                            Student Account
                        </p>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm font-semibold text-green-100/80 transition hover:bg-red-500/15 hover:text-white"
                    >
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17l5-5-5-5m5 5H9m3 5v1a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v1" />
                        </svg>
                        <span>Log out</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main content -->
        <div class="min-h-screen min-w-0 flex-1 md:ml-64">

            <!-- Top header -->
            <header class="sticky top-0 z-30 flex h-20 items-center justify-between border-b border-gray-200 bg-white px-4 shadow-sm sm:px-7">
                <div class="flex min-w-0 items-center gap-3">
                    <button
                        type="button"
                        class="rounded-lg p-2 text-gray-600 transition hover:bg-gray-100 md:hidden"
                        onclick="toggleStudentSidebar()"
                        aria-label="Open navigation"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <div class="min-w-0">
                        <p class="truncate text-sm font-bold text-[#174b38]">
                            @yield('page-label', 'Student Portal')
                        </p>
                        <p class="hidden text-[11px] text-gray-400 sm:block">
                            Cavite State University &middot; Lost & Found
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3 sm:gap-5">
                    <!-- Notifications -->
                    <a
                        href="{{ route('student.notifications') }}"
                        title="Notifications"
                        aria-label="Notifications"
                        class="relative rounded-full p-2 text-gray-500 transition hover:bg-gray-100 hover:text-[#174b38]"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 0 0-5-5.9V4a1 1 0 0 0-2 0v1.1A6 6 0 0 0 6 11v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0a3 3 0 0 1-6 0" />
                        </svg>

                        @if (($unreadNotificationCount ?? 0) > 0)
                            <span class="absolute right-1 top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-600 px-1 text-[9px] font-bold text-white">
                                {{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}
                            </span>
                        @endif
                    </a>

                    <div class="hidden h-8 w-px bg-gray-200 sm:block"></div>

                    <a
                        href="{{ route('student.profile') }}"
                        aria-label="Open your student profile"
                        title="My Profile"
                        class="group flex items-center gap-3 rounded-xl px-2 py-1.5 transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#28634c] focus:ring-offset-2"
                    >
                        <div class="hidden text-right sm:block">
                            <p class="text-xs font-semibold text-gray-700 group-hover:text-[#174b38]">
                                {{ auth()->user()->name }}
                            </p>
                            <p class="text-[10px] text-gray-400">My Profile</p>
                        </div>

                        <div class="grid h-9 w-9 shrink-0 place-items-center overflow-hidden rounded-full bg-[#e8eee9] text-sm font-bold text-[#174b38] transition group-hover:bg-[#dce9df]">
                            @if ($profilePhotoUrl)
                                <img src="{{ $profilePhotoUrl }}" alt="Profile photo of {{ auth()->user()->name }}" class="h-full w-full object-cover">
                            @else
                                {{ strtoupper(mb_substr(auth()->user()->name ?? 'S', 0, 1)) }}
                            @endif
                        </div>
                    </a>
                </div>
            </header>

            <!-- Page content -->
            <main class="w-full flex-1 px-4 py-7 sm:px-7 lg:px-9">
                @if (session('success'))<div hidden data-ui-toast data-kind="success">{{ session('success') }}</div>@endif
                @if (session('error'))<div hidden data-ui-toast data-kind="error">{{ session('error') }}</div>@endif

                @if ($errors->any())
                    <div class="mx-auto mb-5 max-w-6xl rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        <p class="mb-1 font-semibold">Please check the following:</p>
                        <ul class="list-inside list-disc">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>

            <footer class="border-t border-gray-200 bg-white px-5 py-4 text-center text-[11px] text-gray-400">
                &copy; {{ date('Y') }} Cavite State University &middot; Lost & Found System
            </footer>
        </div>
    </div>
    <div id="uiToastRoot" class="ui-toast-root" aria-live="polite" aria-atomic="false"></div>
    <div id="uiModalRoot"></div>

    <script>
        function toggleStudentSidebar() {
            const sidebar = document.getElementById('studentSidebar');
            const backdrop = document.getElementById('studentSidebarBackdrop');

            sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('hidden');
        }
    </script>

    @stack('scripts')
</body>
</html>
