@extends('layouts.student')

@section('title', 'My Claim Status')
@section('page-label', 'My Claim Status')

@section('content')
<div class="mx-auto max-w-6xl space-y-6">
    <section class="rounded-2xl bg-[#174b38] px-6 py-7 text-white shadow-sm sm:px-8">
        <p class="text-xs font-bold uppercase tracking-[.18em] text-[#e2c66f]">Claim requests</p>
        <h1 class="mt-2 text-2xl font-extrabold sm:text-3xl">My Claim Status</h1>
        <p class="mt-2 text-sm text-green-50/85">Check the review status and any administrator remarks for your claims.</p>
    </section>

    <section class="overflow-hidden rounded-2xl border border-[#e4eae6] bg-white shadow-sm">
        <div class="flex flex-col gap-4 border-b border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div class="flex items-center gap-3"><h2 class="font-bold text-[#174b38]">Your requests</h2><span class="rounded-full bg-[#edf4ef] px-3 py-1 text-xs font-semibold text-[#28634c]">{{ $claims->total() }} total</span></div>
            <form method="GET" action="{{ route('student.claims.index') }}" class="flex flex-wrap gap-2">
                <label for="status" class="sr-only">Filter claims by status</label>
                <select id="status" name="status" class="rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-700 outline-none focus:border-[#56846b]">
                    <option value="">All statuses</option>
                    <option value="pending" @selected($selectedStatus === 'pending')>Pending</option>
                    <option value="approved" @selected($selectedStatus === 'approved')>Approved</option>
                    <option value="rejected" @selected($selectedStatus === 'rejected')>Rejected</option>
                </select>
                <button type="submit" class="rounded-lg bg-[#174b38] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#103b2b]">Filter</button>
                @if ($selectedStatus !== '')<a href="{{ route('student.claims.index') }}" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-50">Clear</a>@endif
            </form>
        </div>

        @if ($claims->isEmpty())
            <div class="px-6 py-14 text-center">
                <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-[#edf4ef] text-[#28634c]" aria-hidden="true"><svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12.8 11.2 15 16 9.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg></span>
                <h3 class="mt-4 font-bold text-gray-800">{{ $selectedStatus !== '' ? 'No '.$selectedStatus.' claims' : 'No claim requests yet' }}</h3>
                <p class="mt-2 text-sm text-gray-500">Browse found items and submit a request if you recognize something that belongs to you.</p>
                <a href="{{ route('student.items.index', ['type' => 'found']) }}" class="mt-5 inline-flex rounded-lg bg-[#174b38] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#103b2b]">Browse found items</a>
            </div>
        @else
            <div class="divide-y divide-gray-100">
                @foreach ($claims as $claim)
                    <article class="flex flex-col gap-4 p-5 sm:flex-row sm:items-start sm:p-6">
                        @if ($claim->photo_path)
                            <img src="{{ app(\App\Services\SupabaseStorageService::class)->url($claim->photo_path) }}" alt="Photo of {{ $claim->item_name }}" class="h-20 w-20 shrink-0 rounded-xl object-cover">
                        @else
                            <div class="grid h-20 w-20 shrink-0 place-items-center rounded-xl bg-[#edf4ef] text-2xl text-[#28634c]" aria-hidden="true">+</div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="{{ route('student.items.show', $claim->item_id) }}" class="font-bold text-gray-800 hover:text-[#174b38]">{{ $claim->item_name }}</a>
                                <span class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide {{ strtolower($claim->status) === 'approved' ? 'bg-emerald-50 text-emerald-700' : (strtolower($claim->status) === 'rejected' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700') }}">{{ $claim->status }}</span>
                                <span class="text-xs text-gray-500">{{ $claim->category_name ?? 'Uncategorized' }}</span>
                            </div>
                            <p class="mt-2 text-sm leading-6 text-gray-600">{{ $claim->proof_description }}</p>
                            @if ($claim->remarks)
                                <div class="mt-3 rounded-lg border border-[#e4eae6] bg-[#f6f8f6] px-3.5 py-3"><p class="text-[10px] font-bold uppercase tracking-wide text-[#846e2b]">Administrator remarks</p><p class="mt-1 text-sm text-gray-600">{{ $claim->remarks }}</p></div>
                            @endif
                            <p class="mt-3 text-xs text-gray-400">Submitted {{ \Illuminate\Support\Carbon::parse($claim->created_at)->format('M j, Y') }}@if ($claim->reviewed_at) · Reviewed {{ \Illuminate\Support\Carbon::parse($claim->reviewed_at)->format('M j, Y') }}@endif</p>
                        </div>
                        <a href="{{ route('student.items.show', $claim->item_id) }}" class="shrink-0 text-sm font-semibold text-[#28634c] hover:text-[#174b38]">View item →</a>
                    </article>
                @endforeach
            </div>
            @if ($claims->hasPages())<div class="border-t border-gray-100 px-5 py-4 sm:px-6">{{ $claims->links() }}</div>@endif
        @endif
    </section>
</div>
@endsection
