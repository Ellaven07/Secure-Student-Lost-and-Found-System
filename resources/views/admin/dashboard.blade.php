@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="mx-auto max-w-7xl space-y-4">
    <section class="overflow-hidden rounded-2xl bg-[#174b38] px-6 py-5 text-white shadow-sm sm:px-9 sm:py-6">
        <p class="text-xs font-bold uppercase tracking-[.18em] text-[#e2c66f]">Administrator portal</p>
        <div class="mt-3 flex flex-wrap items-end justify-between gap-5">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight sm:text-3xl">Dashboard overview</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-green-50/85">Reports, claims, student activity, and items waiting for review.</p>
            </div>
            <a href="{{ route('admin.generate-reports.index') }}" class="inline-flex items-center rounded-lg bg-[#d7b65d] px-4 py-2.5 text-sm font-bold text-[#173c2d] transition hover:bg-[#e3c976]">Generate Reports</a>
        </div>
    </section>

    <nav aria-label="Dashboard actions" class="grid gap-3 sm:grid-cols-3">
        <a href="{{ route('admin.reports.index') }}" class="rounded-xl border border-gray-200 bg-white px-4 py-3 text-center text-sm font-bold text-[#28634c] shadow-sm transition hover:border-[#b8cfc0] hover:bg-[#f8fbf9]">Review Item Reports</a>
        <a href="{{ route('admin.claims.index') }}" class="rounded-xl border border-gray-200 bg-white px-4 py-3 text-center text-sm font-bold text-[#28634c] shadow-sm transition hover:border-[#b8cfc0] hover:bg-[#f8fbf9]">Review Claim Requests</a>
        <a href="{{ route('admin.students.index') }}" class="rounded-xl border border-gray-200 bg-white px-4 py-3 text-center text-sm font-bold text-[#28634c] shadow-sm transition hover:border-[#b8cfc0] hover:bg-[#f8fbf9]">Manage Student Accounts</a>
    </nav>

    <section id="pending-actions" class="scroll-mt-24 space-y-4">
        <div>
            <h2 class="text-lg font-bold text-gray-800">Pending actions</h2>
            <p class="mt-1 text-sm text-gray-500">Items that need an administrator's review.</p>
        </div>
        <div class="grid gap-4 lg:grid-cols-2">
            <article id="pending-reports" class="scroll-mt-24 rounded-2xl border border-amber-200 bg-white shadow-sm">
                <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-5 py-4">
                    <h3 class="text-sm font-bold text-gray-800">Reports waiting for approval</h3>
                    <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-800">{{ number_format($pendingReportCount) }}</span>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse ($pendingReports->take(3) as $report)
                        <div class="px-5 py-3">
                            <p class="text-sm font-semibold text-gray-700">{{ $report->item_name }} <span class="font-normal capitalize text-gray-500">({{ $report->type }})</span></p>
                            <p class="mt-1 text-xs text-gray-500">Submitted by {{ $report->student_name }} · {{ \Carbon\Carbon::parse($report->created_at)->diffForHumans() }}</p>
                        </div>
                    @empty
                        <p class="px-5 py-6 text-sm text-gray-400">No reports are waiting for approval.</p>
                    @endforelse
                </div>
            </article>

            <article id="pending-claims" class="scroll-mt-24 rounded-2xl border border-amber-200 bg-white shadow-sm">
                <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-5 py-4">
                    <h3 class="text-sm font-bold text-gray-800">Claims waiting for review</h3>
                    <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-800">{{ number_format($pendingClaimCount) }}</span>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse ($pendingClaims->take(3) as $claim)
                        <div class="px-5 py-3">
                            <p class="text-sm font-semibold text-gray-700">{{ $claim->item_name }}</p>
                            <p class="mt-1 text-xs text-gray-500">Claim from {{ $claim->student_name }} · {{ \Carbon\Carbon::parse($claim->created_at)->diffForHumans() }}</p>
                        </div>
                    @empty
                        <p class="px-5 py-6 text-sm text-gray-400">No claims are waiting for review.</p>
                    @endforelse
                </div>
            </article>
        </div>
    </section>

</div>
@endsection
