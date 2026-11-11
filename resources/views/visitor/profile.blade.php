@extends('layouts.visitor')

@section('title', 'My Profile')

@section('content')
    @php
        $visitor = auth()->user();
        $initials = collect(preg_split('/\s+/', trim($visitor->name)))
            ->filter()
            ->take(2)
            ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');
    @endphp

    <div class="mx-auto max-w-5xl space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[.16em] text-[#9a7931]">Your account</p>
                <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-[#174b38] sm:text-3xl">My Profile</h1>
                <p class="mt-1 text-sm text-gray-500">View the information connected to your visitor account.</p>
            </div>
            <a href="{{ route('visitor.items.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-[#d9e4dc] bg-white px-4 py-2.5 text-sm font-semibold text-[#28634c] shadow-sm transition hover:border-[#b7cbbb] hover:bg-[#f7faf7] focus:outline-none focus:ring-2 focus:ring-[#56846b]">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 12h16m-6-6 6 6-6 6"/></svg>
                Browse items
            </a>
        </div>

        <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm" aria-labelledby="profile-overview-heading">
            <div class="h-1.5 bg-gradient-to-r from-[#174b38] via-[#28634c] to-[#d7b65d]"></div>
            <div class="flex flex-col gap-5 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-7">
                <div class="flex min-w-0 items-center gap-4 sm:gap-5">
                    <div class="grid h-16 w-16 shrink-0 place-items-center rounded-2xl border border-[#dce8df] bg-[#edf4ef] text-xl font-extrabold text-[#174b38] sm:h-20 sm:w-20 sm:text-2xl" aria-hidden="true">
                        {{ $initials ?: 'V' }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] font-bold uppercase tracking-wide text-gray-400">Visitor profile</p>
                        <h2 id="profile-overview-heading" class="mt-1 break-words text-xl font-extrabold text-gray-900 sm:text-2xl">{{ $visitor->name }}</h2>
                        <p class="mt-1 break-all text-sm text-gray-500">{{ $visitor->email }}</p>
                    </div>
                </div>
                <span class="inline-flex w-fit items-center gap-2 rounded-full border border-[#dcecdf] bg-[#f0f7f1] px-3.5 py-2 text-xs font-bold text-[#28634c]">
                    <span class="h-2 w-2 rounded-full bg-[#4e8b68]"></span>
                    Visitor account
                </span>
            </div>
        </section>

        <div class="grid gap-5 lg:grid-cols-2">
            <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-7" aria-labelledby="personal-info-heading">
                <div class="mb-5 flex items-center gap-3 border-b border-gray-100 pb-4">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-[#edf4ef] text-[#28634c]" aria-hidden="true">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="3.25"/><path stroke-linecap="round" stroke-linejoin="round" d="M5.5 20v-1.25a6.5 6.5 0 0 1 13 0V20h-13Z"/></svg>
                    </span>
                    <div>
                        <h3 id="personal-info-heading" class="text-sm font-extrabold text-[#244537]">Personal information</h3>
                        <p class="mt-0.5 text-xs text-gray-500">Your name as provided during registration.</p>
                    </div>
                </div>

                <dl class="space-y-4">
                    <div>
                        <dt class="text-xs font-semibold text-gray-400">First name</dt>
                        <dd class="mt-1 break-words text-sm font-semibold text-gray-800">{{ $visitor->first_name ?: 'Not provided' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-gray-400">Middle name</dt>
                        <dd class="mt-1 break-words text-sm font-semibold text-gray-800">{{ $visitor->middle_name ?: 'Not provided' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-gray-400">Last name</dt>
                        <dd class="mt-1 break-words text-sm font-semibold text-gray-800">{{ $visitor->last_name ?: 'Not provided' }}</dd>
                    </div>
                </dl>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-7" aria-labelledby="account-info-heading">
                <div class="mb-5 flex items-center gap-3 border-b border-gray-100 pb-4">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-[#edf4ef] text-[#28634c]" aria-hidden="true">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3.5" y="5.5" width="17" height="13" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 7 7.5 5.5L19.5 7"/></svg>
                    </span>
                    <div>
                        <h3 id="account-info-heading" class="text-sm font-extrabold text-[#244537]">Account information</h3>
                        <p class="mt-0.5 text-xs text-gray-500">Your contact and account details.</p>
                    </div>
                </div>

                <dl class="space-y-4">
                    <div>
                        <dt class="text-xs font-semibold text-gray-400">Gmail address</dt>
                        <dd class="mt-1 break-all text-sm font-semibold text-gray-800">{{ $visitor->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-gray-400">Contact number</dt>
                        <dd class="mt-1 break-words text-sm font-semibold text-gray-800">{{ $visitor->phone_number ?: 'Not provided' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-gray-400">Member since</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-800">{{ $visitor->created_at?->format('F d, Y') ?? 'Not available' }}</dd>
                    </div>
                </dl>
            </section>
        </div>

        <section class="flex items-start gap-3 rounded-2xl border border-[#dce8df] bg-[#f0f7f1] p-4 sm:p-5">
            <svg class="mt-0.5 h-5 w-5 shrink-0 text-[#28634c]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75 5.25 6v5.4c0 4.3 2.7 7.5 6.75 8.85 4.05-1.35 6.75-4.55 6.75-8.85V6L12 3.75Z"/><path stroke-linecap="round" stroke-linejoin="round" d="m9.25 12 1.8 1.8 3.7-3.8"/></svg>
            <div>
                <h3 class="text-sm font-bold text-[#244537]">Visitor access</h3>
                <p class="mt-1 text-xs leading-5 text-[#587063]">This account can browse verified lost and found items. Reporting and claiming items require a student account.</p>
            </div>
        </section>
    </div>
@endsection
