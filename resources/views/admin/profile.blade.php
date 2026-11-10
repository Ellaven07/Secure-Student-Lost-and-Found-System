@extends('layouts.app')

@section('title', 'Admin Profile')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <div>
        <p class="text-sm text-gray-500">Administration / Admin Profile</p>
        <h1 class="mt-1 text-2xl font-bold text-gray-800">Admin Profile</h1>
        <p class="mt-1 text-sm text-gray-500">Account details for the signed-in administrator.</p>
    </div>

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

    <a href="{{ route('admin.dashboard') }}" class="inline-flex rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:border-[#174b38] hover:text-[#174b38]">
        Back to dashboard
    </a>
</div>
@endsection
