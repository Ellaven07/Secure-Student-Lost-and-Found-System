<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Create Visitor Account | CvSU Lost &amp; Found</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f3f6f3] font-['DM_Sans'] text-[#23352d] antialiased">
    <main class="mx-auto grid min-h-screen max-w-[1500px] lg:grid-cols-[.85fr_1.15fr]">
        <aside class="relative hidden min-h-screen flex-col justify-between overflow-hidden bg-[#174b38] p-10 text-white lg:flex xl:p-14" aria-label="CvSU Lost and Found visitor information">
            <div class="pointer-events-none absolute -right-44 top-1/4 h-[34rem] w-[34rem] rounded-full border border-white/10"></div>
            <div class="pointer-events-none absolute -right-24 top-[34%] h-[24rem] w-[24rem] rounded-full border border-white/10"></div>

            <a href="{{ route('login') }}" class="relative z-10 flex items-center gap-3 text-white no-underline">
                <span class="grid h-14 w-14 place-items-center rounded-full border-2 border-[#d7b65d] bg-white text-sm font-extrabold text-[#174b38]">CvSU</span>
                <span>
                    <span class="block text-sm font-extrabold tracking-wide">CAVITE STATE UNIVERSITY</span>
                    <span class="mt-1 block text-xs text-green-100">Student Lost &amp; Found</span>
                </span>
            </a>

            <div class="relative z-10 max-w-lg py-14">
                <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3.5 py-2 text-[11px] font-bold uppercase tracking-[.16em] text-[#f0d983]">
                    <span class="h-1.5 w-1.5 rounded-full bg-[#e2c66f]"></span>
                    Visitor access
                </span>
                <h1 class="mt-7 text-5xl font-extrabold leading-[1.08] tracking-tight xl:text-6xl">A lost item could be waiting for you.</h1>
                <p class="mt-6 max-w-md text-base leading-8 text-green-50/85">Create your account to browse verified lost and found items from the CvSU community.</p>

                <div class="mt-10 flex items-start gap-4 rounded-2xl border border-white/10 bg-white/[.07] p-5 backdrop-blur-sm">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-[#e2c66f]/15 text-[#f0d983]" aria-hidden="true">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75 5.25 6v5.4c0 4.3 2.7 7.5 6.75 8.85 4.05-1.35 6.75-4.55 6.75-8.85V6L12 3.75Z"/><path stroke-linecap="round" stroke-linejoin="round" d="m9.25 12 1.8 1.8 3.7-3.8"/></svg>
                    </span>
                    <div>
                        <p class="text-sm font-bold text-white">Browse-only visitor account</p>
                        <p class="mt-1 text-xs leading-5 text-green-100/75">Visitors can explore items. Reporting and claiming remain available to student accounts.</p>
                    </div>
                </div>
            </div>

            <p class="relative z-10 text-xs text-green-100/65">CvSU Student Lost &amp; Found Management System</p>
        </aside>

        <section class="flex items-center justify-center px-4 py-8 sm:px-8 lg:px-10 xl:px-14">
            <div class="w-full max-w-[650px]">
                <div class="mb-6 flex items-center justify-between gap-4 lg:mb-8">
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-lg px-2 py-2 text-sm font-semibold text-[#28634c] transition hover:bg-[#e8efe9] hover:text-[#174b38]">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6M9 12h12"/></svg>
                        Back to sign in
                    </a>
                    <span class="inline-flex items-center gap-2 rounded-full bg-[#e7efe9] px-3 py-1.5 text-[11px] font-bold text-[#28634c]">
                        <span class="h-1.5 w-1.5 rounded-full bg-[#4e8b68]"></span>
                        Visitor registration
                    </span>
                </div>

                <div class="rounded-[1.75rem] border border-[#e4eae5] bg-white p-5 shadow-[0_24px_70px_-35px_rgba(23,75,56,.28)] sm:p-8 xl:p-10">
                    <div class="mb-8">
                        <p class="text-xs font-bold uppercase tracking-[.16em] text-[#9a7931]">Join the community</p>
                        <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-[#183e30] sm:text-[2.1rem]">Create your account</h2>
                        <p class="mt-2 text-sm leading-6 text-gray-500">Enter your details below. Use a Gmail address to register as a visitor.</p>
                    </div>

                    <form method="POST" action="{{ route('visitor.register.store') }}" class="space-y-7">
                        @csrf

                        <section aria-labelledby="personal-details-heading">
                            <div class="mb-4 flex items-center gap-3">
                                <span class="grid h-9 w-9 place-items-center rounded-xl bg-[#edf4ef] text-[#28634c]" aria-hidden="true">
                                    <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="3.25"/><path stroke-linecap="round" stroke-linejoin="round" d="M5.5 20v-1.25a6.5 6.5 0 0 1 13 0V20h-13Z"/></svg>
                                </span>
                                <div>
                                    <h3 id="personal-details-heading" class="text-sm font-extrabold text-[#244537]">Personal information</h3>
                                    <p class="mt-0.5 text-xs text-gray-500">Use the name you want shown on your account.</p>
                                </div>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="first_name" class="mb-1.5 block text-xs font-bold text-gray-700">First name <span class="text-red-500">*</span></label>
                                    <input id="first_name" name="first_name" type="text" value="{{ old('first_name') }}" autocomplete="given-name" required maxlength="80" placeholder="e.g. Juan" @error('first_name') aria-invalid="true" @enderror class="w-full rounded-xl border border-gray-200 bg-[#fbfcfb] px-3.5 py-3 text-sm text-[#23352d] outline-none transition placeholder:text-gray-400 hover:border-gray-300 focus:border-[#56846b] focus:bg-white focus:ring-4 focus:ring-[#174b38]/10 @error('first_name') border-red-300 focus:border-red-400 focus:ring-red-100 @enderror">
                                    @error('first_name')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="middle_name" class="mb-1.5 block text-xs font-bold text-gray-700">Middle name <span class="font-medium text-gray-400">(optional)</span></label>
                                    <input id="middle_name" name="middle_name" type="text" value="{{ old('middle_name') }}" autocomplete="additional-name" maxlength="80" placeholder="e.g. Santos" @error('middle_name') aria-invalid="true" @enderror class="w-full rounded-xl border border-gray-200 bg-[#fbfcfb] px-3.5 py-3 text-sm text-[#23352d] outline-none transition placeholder:text-gray-400 hover:border-gray-300 focus:border-[#56846b] focus:bg-white focus:ring-4 focus:ring-[#174b38]/10 @error('middle_name') border-red-300 focus:border-red-400 focus:ring-red-100 @enderror">
                                    @error('middle_name')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="last_name" class="mb-1.5 block text-xs font-bold text-gray-700">Last name <span class="text-red-500">*</span></label>
                                    <input id="last_name" name="last_name" type="text" value="{{ old('last_name') }}" autocomplete="family-name" required maxlength="80" placeholder="e.g. Dela Cruz" @error('last_name') aria-invalid="true" @enderror class="w-full rounded-xl border border-gray-200 bg-[#fbfcfb] px-3.5 py-3 text-sm text-[#23352d] outline-none transition placeholder:text-gray-400 hover:border-gray-300 focus:border-[#56846b] focus:bg-white focus:ring-4 focus:ring-[#174b38]/10 @error('last_name') border-red-300 focus:border-red-400 focus:ring-red-100 @enderror">
                                    @error('last_name')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </section>

                        <section aria-labelledby="account-details-heading">
                            <div class="mb-4 flex items-center gap-3">
                                <span class="grid h-9 w-9 place-items-center rounded-xl bg-[#edf4ef] text-[#28634c]" aria-hidden="true">
                                    <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3.5" y="5.5" width="17" height="13" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 7 7.5 5.5L19.5 7"/></svg>
                                </span>
                                <div>
                                    <h3 id="account-details-heading" class="text-sm font-extrabold text-[#244537]">Contact and sign-in</h3>
                                    <p class="mt-0.5 text-xs text-gray-500">Use a Gmail address and a number where you can be reached.</p>
                                </div>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="phone_number" class="mb-1.5 block text-xs font-bold text-gray-700">Contact number <span class="text-red-500">*</span></label>
                                    <input id="phone_number" name="phone_number" type="tel" value="{{ old('phone_number') }}" placeholder="09123456789" autocomplete="tel" required maxlength="30" pattern="[0-9+().\-\s]{7,30}" @error('phone_number') aria-invalid="true" @enderror class="w-full rounded-xl border border-gray-200 bg-[#fbfcfb] px-3.5 py-3 text-sm text-[#23352d] outline-none transition placeholder:text-gray-400 hover:border-gray-300 focus:border-[#56846b] focus:bg-white focus:ring-4 focus:ring-[#174b38]/10 @error('phone_number') border-red-300 focus:border-red-400 focus:ring-red-100 @enderror">
                                    @error('phone_number')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="email" class="mb-1.5 block text-xs font-bold text-gray-700">Gmail address <span class="text-red-500">*</span></label>
                                    <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="you@gmail.com" autocomplete="email" required maxlength="255" @error('email') aria-invalid="true" @enderror class="w-full rounded-xl border border-gray-200 bg-[#fbfcfb] px-3.5 py-3 text-sm text-[#23352d] outline-none transition placeholder:text-gray-400 hover:border-gray-300 focus:border-[#56846b] focus:bg-white focus:ring-4 focus:ring-[#174b38]/10 @error('email') border-red-300 focus:border-red-400 focus:ring-red-100 @enderror">
                                    @error('email')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="password" class="mb-1.5 block text-xs font-bold text-gray-700">Password <span class="text-red-500">*</span></label>
                                    <input id="password" name="password" type="password" autocomplete="new-password" required @error('password') aria-invalid="true" @enderror class="w-full rounded-xl border border-gray-200 bg-[#fbfcfb] px-3.5 py-3 text-sm text-[#23352d] outline-none transition placeholder:text-gray-400 hover:border-gray-300 focus:border-[#56846b] focus:bg-white focus:ring-4 focus:ring-[#174b38]/10 @error('password') border-red-300 focus:border-red-400 focus:ring-red-100 @enderror">
                                    @error('password')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="password_confirmation" class="mb-1.5 block text-xs font-bold text-gray-700">Confirm password <span class="text-red-500">*</span></label>
                                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="w-full rounded-xl border border-gray-200 bg-[#fbfcfb] px-3.5 py-3 text-sm text-[#23352d] outline-none transition placeholder:text-gray-400 hover:border-gray-300 focus:border-[#56846b] focus:bg-white focus:ring-4 focus:ring-[#174b38]/10">
                                </div>
                            </div>
                        </section>

                        <div class="space-y-4 border-t border-gray-100 pt-5">
                            <p class="flex items-start gap-2.5 text-xs leading-5 text-gray-500">
                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-[#56846b]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75 5.25 6v5.4c0 4.3 2.7 7.5 6.75 8.85 4.05-1.35 6.75-4.55 6.75-8.85V6L12 3.75Z"/><path stroke-linecap="round" stroke-linejoin="round" d="m9.25 12 1.8 1.8 3.7-3.8"/></svg>
                                <span>Your account will have visitor access, which lets you browse verified items only.</span>
                            </p>
                            <button type="submit" class="group flex w-full items-center justify-center gap-2 rounded-xl bg-[#174b38] px-5 py-3.5 text-sm font-extrabold text-white shadow-lg shadow-[#174b38]/15 transition hover:-translate-y-0.5 hover:bg-[#216248] hover:shadow-xl hover:shadow-[#174b38]/20 focus:outline-none focus:ring-4 focus:ring-[#174b38]/20">
                                Create visitor account
                                <svg class="h-4 w-4 transition-transform group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/></svg>
                            </button>
                        </div>
                    </form>
                </div>

                <p class="mt-6 text-center text-sm text-gray-500">Already have an account? <a href="{{ route('login') }}" class="font-bold text-[#28634c] underline-offset-4 hover:text-[#174b38] hover:underline">Sign in</a></p>
                <p class="mt-3 text-center text-[11px] text-gray-400">Cavite State University · Student Lost &amp; Found</p>
            </div>
        </section>
    </main>
</body>
</html>
