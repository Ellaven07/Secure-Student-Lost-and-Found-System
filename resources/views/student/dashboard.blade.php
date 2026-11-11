@extends('layouts.student')

@section('title', 'Student Dashboard')

@section('content')
<div class="mx-auto max-w-6xl space-y-7">
    <section class="overflow-hidden rounded-2xl bg-[#174b38] px-6 py-8 text-white shadow-sm sm:px-9 sm:py-10">
        <p class="text-xs font-bold uppercase tracking-[.18em] text-[#e2c66f]">Student portal</p>
        <h1 class="mt-3 text-3xl font-extrabold tracking-tight sm:text-4xl">Welcome, {{ auth()->user()->name }}</h1>
        <p class="mt-3 max-w-2xl text-sm leading-6 text-green-50/85">Your CvSU Lost &amp; Found dashboard. Keep your account details up to date and check notifications for updates.</p>
    </section>
    <section class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        <a href="{{ route('student.reports.lost.create') }}" class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-[#b8cfc0] hover:shadow-md">
            <span class="grid h-11 w-11 place-items-center rounded-xl bg-[#f5f0df] text-2xl font-semibold text-[#846e2b]" aria-hidden="true">−</span>
            <h2 class="mt-5 text-base font-bold text-gray-800 group-hover:text-[#174b38]">Report a lost item</h2>
            <p class="mt-2 text-sm leading-6 text-gray-500">Tell the campus community what you lost and where you last saw it.</p>
            <span class="mt-5 inline-block text-sm font-bold text-[#28634c]">Create lost report <span aria-hidden="true">→</span></span>
        </a>
        <a href="{{ route('student.reports.found.create') }}" class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-[#b8cfc0] hover:shadow-md">
            <span class="grid h-11 w-11 place-items-center rounded-xl bg-[#e8eee9] text-2xl font-semibold text-[#174b38]" aria-hidden="true">+</span>
            <h2 class="mt-5 text-base font-bold text-gray-800 group-hover:text-[#174b38]">Report a found item</h2>
            <p class="mt-2 text-sm leading-6 text-gray-500">Help reunite a found item with its owner by sharing its details.</p>
            <span class="mt-5 inline-block text-sm font-bold text-[#28634c]">Create found report <span aria-hidden="true">→</span></span>
        </a>
        <a href="{{ route('student.reports.history') }}" class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-[#b8cfc0] hover:shadow-md">
            <span class="grid h-11 w-11 place-items-center rounded-xl bg-[#e8eee9] text-[#174b38]" aria-hidden="true"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 2m6-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg></span>
            <h2 class="mt-5 text-base font-bold text-gray-800 group-hover:text-[#174b38]">My report history</h2>
            <p class="mt-2 text-sm leading-6 text-gray-500">Review your past reports and check their current status.</p>
            <span class="mt-5 inline-block text-sm font-bold text-[#28634c]">View report history <span aria-hidden="true">→</span></span>
        </a>
        <a href="{{ route('student.items.index') }}" class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-[#b8cfc0] hover:shadow-md">
            <span class="grid h-11 w-11 place-items-center rounded-xl bg-[#e8eee9] text-[#174b38]" aria-hidden="true"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m21 21-4.3-4.3M19 10.5a8.5 8.5 0 1 1-17 0 8.5 8.5 0 0 1 17 0Z" /></svg></span>
            <h2 class="mt-5 text-base font-bold text-gray-800 group-hover:text-[#174b38]">Browse and search items</h2>
            <p class="mt-2 text-sm leading-6 text-gray-500">Look through verified reports and search by name, type, or category.</p>
            <span class="mt-5 inline-block text-sm font-bold text-[#28634c]">Browse items <span aria-hidden="true">→</span></span>
        </a>
        <a href="{{ route('student.claims.index') }}" class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-[#b8cfc0] hover:shadow-md">
            <span class="grid h-11 w-11 place-items-center rounded-xl bg-[#f5f0df] text-[#846e2b]" aria-hidden="true"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12.8 11.2 15 16 9.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg></span>
            <h2 class="mt-5 text-base font-bold text-gray-800 group-hover:text-[#174b38]">My claim status</h2>
            <p class="mt-2 text-sm leading-6 text-gray-500">Check your claim requests and read administrator feedback.</p>
            <span class="mt-5 inline-block text-sm font-bold text-[#28634c]">View claim status <span aria-hidden="true">→</span></span>
        </a>
        <a href="{{ route('student.profile') }}" class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-[#b8cfc0] hover:shadow-md">
            <span class="grid h-11 w-11 place-items-center rounded-xl bg-[#e8eee9] text-xl text-[#174b38]" aria-hidden="true">♙</span>
            <h2 class="mt-5 text-base font-bold text-gray-800 group-hover:text-[#174b38]">My profile</h2>
            <p class="mt-2 text-sm leading-6 text-gray-500">Review the student information connected to your account.</p>
            <span class="mt-5 inline-block text-sm font-bold text-[#28634c]">View profile <span aria-hidden="true">→</span></span>
        </a>
        <a href="{{ route('student.notifications') }}" class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-[#b8cfc0] hover:shadow-md">
            <span class="grid h-11 w-11 place-items-center rounded-xl bg-[#f5f0df] text-xl text-[#846e2b]" aria-hidden="true">♢</span>
            <h2 class="mt-5 text-base font-bold text-gray-800 group-hover:text-[#174b38]">Notifications</h2>
            <p class="mt-2 text-sm leading-6 text-gray-500">Check alerts and updates from the Lost &amp; Found system.</p>
            <span class="mt-5 inline-block text-sm font-bold text-[#28634c]">View notifications <span aria-hidden="true">→</span></span>
        </a>
    </section>
    <section class="rounded-2xl border border-[#e4eae6] bg-white p-6 shadow-sm">
        <p class="text-xs font-bold uppercase tracking-[.14em] text-[#9a8546]">Account</p>
        <p class="mt-2 text-sm text-gray-600">Signed in as <span class="font-semibold text-gray-800">{{ auth()->user()->email }}</span></p>
    </section>
</div>
@endsection
