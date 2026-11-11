@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <div>
        <p class="text-sm text-gray-500">Administration / Settings</p>
        <h1 class="mt-1 text-2xl font-bold text-gray-800">Settings</h1>
        <p class="mt-1 text-sm text-gray-500">Manage your administrator account and security.</p>
    </div>

    @if (session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif

    <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-wrap items-center gap-5 bg-[#174b38] px-6 py-7 text-white sm:px-8">
            <div class="grid h-16 w-16 place-items-center rounded-full border-2 border-[#d7b65d] bg-white text-2xl font-extrabold text-[#174b38]" aria-hidden="true">
                {{ str(auth()->user()->name)->substr(0, 1)->upper() }}
            </div>
            <div>
                <h2 class="text-xl font-bold">{{ auth()->user()->name }}</h2>
                <p class="mt-1 text-sm text-green-100">Administrator</p>
            </div>
        </div>

        <dl class="grid gap-5 p-6 sm:grid-cols-2 sm:p-8">
            <div>
                <dt class="text-xs font-bold uppercase tracking-wide text-gray-400">Name</dt>
                <dd class="mt-2 text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</dd>
            </div>
            <div>
                <dt class="text-xs font-bold uppercase tracking-wide text-gray-400">Email</dt>
                <dd class="mt-2 text-sm font-semibold text-gray-800">{{ auth()->user()->email }}</dd>
            </div>
            <div>
                <dt class="text-xs font-bold uppercase tracking-wide text-gray-400">Role</dt>
                <dd class="mt-2 text-sm font-semibold capitalize text-gray-800">{{ auth()->user()->role }}</dd>
            </div>
            <div>
                <dt class="text-xs font-bold uppercase tracking-wide text-gray-400">Account status</dt>
                <dd class="mt-2 text-sm font-semibold capitalize text-gray-800">{{ auth()->user()->status }}</dd>
            </div>
        </dl>
    </section>

    <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
        <div class="mb-5">
            <h2 class="text-lg font-bold text-gray-800">Password &amp; Security</h2>
            <p class="mt-1 text-sm text-gray-500">Change your administrator password.</p>
        </div>

        <form method="POST" action="{{ route('admin.profile.password') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="current_password" class="mb-1.5 block text-sm font-semibold text-gray-700">Current password</label>
                <input id="current_password" name="current_password" type="password" autocomplete="current-password" required class="w-full rounded-lg border border-gray-300 px-3.5 py-3 text-sm outline-none focus:border-[#56846b] focus:ring-2 focus:ring-[#174b38]/10 sm:max-w-xl">
                @error('current_password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="password" class="mb-1.5 block text-sm font-semibold text-gray-700">New password</label>
                    <input id="password" name="password" type="password" autocomplete="new-password" required class="w-full rounded-lg border border-gray-300 px-3.5 py-3 text-sm outline-none focus:border-[#56846b] focus:ring-2 focus:ring-[#174b38]/10">
                    @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password_confirmation" class="mb-1.5 block text-sm font-semibold text-gray-700">Confirm new password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="w-full rounded-lg border border-gray-300 px-3.5 py-3 text-sm outline-none focus:border-[#56846b] focus:ring-2 focus:ring-[#174b38]/10">
                </div>
            </div>

            <div class="flex justify-end border-t border-gray-100 pt-4">
                <button type="submit" class="rounded-lg bg-[#174b38] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#28634c]">Update password</button>
            </div>
        </form>
    </section>

    <a href="{{ route('admin.dashboard') }}" class="inline-flex rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:border-[#174b38] hover:text-[#174b38]">
        Back to dashboard
    </a>
</div>
@endsection
