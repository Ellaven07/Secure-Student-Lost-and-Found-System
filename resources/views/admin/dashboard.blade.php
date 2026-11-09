@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="mx-auto max-w-6xl space-y-7">
    <section class="overflow-hidden rounded-2xl bg-[#174b38] px-6 py-8 text-white shadow-sm sm:px-9 sm:py-10">
        <p class="text-xs font-bold uppercase tracking-[.18em] text-[#e2c66f]">Administrator portal</p>
        <h1 class="mt-3 text-3xl font-extrabold tracking-tight sm:text-4xl">Welcome, {{ auth()->user()->name }}</h1>
        <p class="mt-3 max-w-2xl text-sm leading-6 text-green-50/85">Manage student accounts and keep the CvSU Lost &amp; Found system organized from your dashboard.</p>
    </section>
    <section>
        <div class="mb-4">
            <h2 class="text-lg font-bold text-gray-800">Administration</h2>
            <p class="mt-1 text-sm text-gray-500">Choose an area to manage.</p>
        </div>
        <div class="grid gap-5 md:grid-cols-2">
            <a href="{{ route('admin.students.index') }}" class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-[#b8cfc0] hover:shadow-md">
                <span class="grid h-11 w-11 place-items-center rounded-xl bg-[#e8eee9] text-xl text-[#174b38]" aria-hidden="true">♙</span>
                <h3 class="mt-5 text-base font-bold text-gray-800 group-hover:text-[#174b38]">Student accounts</h3>
                <p class="mt-2 text-sm leading-6 text-gray-500">Create student accounts and manage access to the student portal.</p>
                <span class="mt-5 inline-block text-sm font-bold text-[#28634c]">Manage students <span aria-hidden="true">→</span></span>
            </a>
            <a href="{{ route('admin.categories') }}" class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-[#b8cfc0] hover:shadow-md">
                <span class="grid h-11 w-11 place-items-center rounded-xl bg-[#f5f0df] text-xl text-[#846e2b]" aria-hidden="true">▦</span>
                <h3 class="mt-5 text-base font-bold text-gray-800 group-hover:text-[#174b38]">Item categories</h3>
                <p class="mt-2 text-sm leading-6 text-gray-500">View and organize categories used by the Lost &amp; Found system.</p>
                <span class="mt-5 inline-block text-sm font-bold text-[#28634c]">Manage categories <span aria-hidden="true">→</span></span>
            </a>
        </div>
    </section>
</div>
@endsection
