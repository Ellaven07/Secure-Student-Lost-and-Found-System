@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="mx-auto max-w-7xl space-y-7">
    <section class="overflow-hidden rounded-2xl bg-[#174b38] px-6 py-8 text-white shadow-sm sm:px-9 sm:py-9">
        <p class="text-xs font-bold uppercase tracking-[.18em] text-[#e2c66f]">Administrator portal</p>
        <div class="mt-3 flex flex-wrap items-end justify-between gap-5">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl">Dashboard overview</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-green-50/85">Reports, claims, student activity, and items waiting for review.</p>
            </div>
            <a href="{{ route('admin.dashboard.export') }}" class="inline-flex items-center rounded-lg bg-[#d7b65d] px-4 py-2.5 text-sm font-bold text-[#173c2d] transition hover:bg-[#e3c976]">Generate Reports</a>
        </div>
    </section>

    <nav aria-label="Dashboard actions" class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
        <a href="{{ route('admin.reports.index') }}" class="rounded-xl border border-gray-200 bg-white px-4 py-3 text-center text-sm font-bold text-[#28634c] shadow-sm transition hover:border-[#b8cfc0] hover:bg-[#f8fbf9]">Review Item Reports</a>
        <a href="{{ route('admin.claims.index') }}" class="rounded-xl border border-gray-200 bg-white px-4 py-3 text-center text-sm font-bold text-[#28634c] shadow-sm transition hover:border-[#b8cfc0] hover:bg-[#f8fbf9]">Review Claim Requests</a>
        <a href="{{ route('admin.students.index') }}" class="rounded-xl border border-gray-200 bg-white px-4 py-3 text-center text-sm font-bold text-[#28634c] shadow-sm transition hover:border-[#b8cfc0] hover:bg-[#f8fbf9]">Manage Student Accounts</a>
        <a href="#recent-activity" class="rounded-xl border border-gray-200 bg-white px-4 py-3 text-center text-sm font-bold text-[#28634c] shadow-sm transition hover:border-[#b8cfc0] hover:bg-[#f8fbf9]">View Activity Logs</a>
        <a href="{{ route('admin.dashboard.export') }}" class="rounded-xl border border-gray-200 bg-white px-4 py-3 text-center text-sm font-bold text-[#28634c] shadow-sm transition hover:border-[#b8cfc0] hover:bg-[#f8fbf9]">Generate Reports</a>
    </nav>

    <section aria-label="Admin page placeholders" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article id="claim-requests-page" class="scroll-mt-24 min-h-24 rounded-xl border border-gray-200 bg-white px-5 py-4 shadow-sm">
            <h2 class="text-base font-bold text-gray-800">Claim Requests</h2>
        </article>
        <article id="all-reports-page" class="scroll-mt-24 min-h-24 rounded-xl border border-gray-200 bg-white px-5 py-4 shadow-sm">
            <h2 class="text-base font-bold text-gray-800">All Reports</h2>
        </article>
        <article id="activity-logs-page" class="scroll-mt-24 min-h-24 rounded-xl border border-gray-200 bg-white px-5 py-4 shadow-sm">
            <h2 class="text-base font-bold text-gray-800">Activity Logs</h2>
        </article>
        <article id="generate-print-reports-page" class="scroll-mt-24 min-h-24 rounded-xl border border-gray-200 bg-white px-5 py-4 shadow-sm">
            <h2 class="text-base font-bold text-gray-800">Generate and Print Reports</h2>
        </article>
    </section>

    <section id="report-charts" class="scroll-mt-24 space-y-4">
        <div>
            <h2 class="text-lg font-bold text-gray-800">Reports and claims</h2>
            <p class="mt-1 text-sm text-gray-500">Current totals from submitted reports and claim requests.</p>
        </div>
        <div class="grid gap-5 lg:grid-cols-3">
            <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-bold text-gray-800">Lost vs found reports</h3>
                <div class="mt-5 space-y-4">
                    @forelse ($lostFoundChart as $bar)
                        <div>
                            <div class="mb-1.5 flex justify-between gap-3 text-xs"><span class="capitalize text-gray-600">{{ $bar['label'] }}</span><span class="font-bold text-gray-800">{{ number_format($bar['total']) }}</span></div>
                            <div class="h-2 overflow-hidden rounded-full bg-gray-100"><div class="h-full rounded-full" style="width: {{ $bar['width'] }}%; background-color: {{ $loop->even ? '#d7b65d' : '#174b38' }}"></div></div>
                        </div>
                    @empty
                        <p class="py-6 text-center text-sm text-gray-400">No reports submitted yet.</p>
                    @endforelse
                </div>
            </article>

            <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-bold text-gray-800">Reports by approval status</h3>
                <div class="mt-5 space-y-4">
                    @foreach ($approvalChart as $bar)
                        <div>
                            <div class="mb-1.5 flex justify-between gap-3 text-xs"><span class="text-gray-600">{{ $bar['label'] }}</span><span class="font-bold text-gray-800">{{ number_format($bar['total']) }}</span></div>
                            <div class="h-2 overflow-hidden rounded-full bg-gray-100"><div class="h-full rounded-full" style="width: {{ $bar['width'] }}%; background-color: {{ $loop->first ? '#28634c' : '#d7b65d' }}"></div></div>
                        </div>
                    @endforeach
                </div>
            </article>

            <article id="claims-by-status" class="scroll-mt-24 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-bold text-gray-800">Claims by status</h3>
                <div class="mt-5 space-y-4">
                    @forelse ($claimStatusChart as $bar)
                        <div>
                            <div class="mb-1.5 flex justify-between gap-3 text-xs"><span class="capitalize text-gray-600">{{ $bar['label'] }}</span><span class="font-bold text-gray-800">{{ number_format($bar['total']) }}</span></div>
                            <div class="h-2 overflow-hidden rounded-full bg-gray-100"><div class="h-full rounded-full" style="width: {{ $bar['width'] }}%; background-color: {{ $loop->even ? '#d7b65d' : '#174b38' }}"></div></div>
                        </div>
                    @empty
                        <p class="py-6 text-center text-sm text-gray-400">No claim requests yet.</p>
                    @endforelse
                </div>
            </article>
        </div>

        <div class="grid gap-5 lg:grid-cols-2">
            <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-bold text-gray-800">Monthly report trends</h3>
                <p class="mt-1 text-xs text-gray-500">Reports submitted over the last six months.</p>
                <div class="mt-5 flex h-40 items-end gap-3 border-b border-gray-100 pb-2">
                    @foreach ($monthlyReportChart as $bar)
                        <div class="flex h-full min-w-0 flex-1 flex-col items-center justify-end gap-2">
                            <span class="text-[10px] font-bold text-gray-500">{{ $bar['total'] }}</span>
                            <div class="w-full max-w-10 rounded-t-md bg-[#28634c]" style="height: {{ max($bar['width'], $bar['total'] > 0 ? 8 : 2) }}%"></div>
                            <span class="text-[10px] text-gray-500">{{ $bar['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </article>

            <article id="reports-by-category" class="scroll-mt-24 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-bold text-gray-800">Reports by category</h3>
                <div class="mt-5 max-h-48 space-y-4 overflow-y-auto pr-1">
                    @forelse ($categoryChart as $bar)
                        <div>
                            <div class="mb-1.5 flex justify-between gap-3 text-xs"><span class="text-gray-600">{{ $bar['label'] }}</span><span class="font-bold text-gray-800">{{ number_format($bar['total']) }}</span></div>
                            <div class="h-2 overflow-hidden rounded-full bg-gray-100"><div class="h-full rounded-full bg-[#174b38]" style="width: {{ $bar['width'] }}%"></div></div>
                        </div>
                    @empty
                        <p class="py-6 text-center text-sm text-gray-400">Category totals will appear when reports are submitted.</p>
                    @endforelse
                </div>
            </article>
        </div>
    </section>

    <section id="pending-actions" class="scroll-mt-24 space-y-4">
        <div>
            <h2 class="text-lg font-bold text-gray-800">Pending actions</h2>
            <p class="mt-1 text-sm text-gray-500">Items that need an administrator's review.</p>
        </div>
        <div class="grid gap-5 lg:grid-cols-2">
            <article id="pending-reports" class="scroll-mt-24 rounded-2xl border border-amber-200 bg-white shadow-sm">
                <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-5 py-4">
                    <h3 class="text-sm font-bold text-gray-800">Reports waiting for approval</h3>
                    <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-800">{{ number_format($pendingReportCount) }}</span>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse ($pendingReports as $report)
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
                    @forelse ($pendingClaims as $claim)
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

    <section id="recent-activity" class="scroll-mt-24 grid gap-5 xl:grid-cols-[1.3fr_.7fr]">
        <article class="rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-5 py-4">
                <h2 class="text-sm font-bold text-gray-800">Recent activity</h2>
                <p class="mt-1 text-xs text-gray-500">New registrations, reports, claims, and review decisions.</p>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse ($recentActivities as $activity)
                    <div class="flex gap-3 px-5 py-3.5">
                        <span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-full bg-[#e8eee9] text-xs font-bold text-[#174b38]" aria-hidden="true">{{ str($activity['type'])->substr(0, 1)->upper() }}</span>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold text-gray-700">{{ $activity['type'] }}</p>
                            <p class="mt-1 break-words text-xs leading-5 text-gray-500">{{ $activity['description'] }}</p>
                        </div>
                        <time class="shrink-0 text-[10px] text-gray-400" datetime="{{ $activity['created_at']->toIso8601String() }}">{{ $activity['created_at']->diffForHumans() }}</time>
                    </div>
                @empty
                    <p class="px-5 py-8 text-center text-sm text-gray-400">Activity will appear here as accounts, reports, and claims are created.</p>
                @endforelse
            </div>
        </article>

        <article class="rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-5 py-4">
                <h2 class="text-sm font-bold text-gray-800">Latest approvals and rejections</h2>
                <p class="mt-1 text-xs text-gray-500">Review decisions recorded in the activity log.</p>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse ($latestReviews as $review)
                    <div class="px-5 py-3.5">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-xs font-bold {{ str_contains(strtolower($review['type']), 'rejected') ? 'text-red-700' : 'text-green-700' }}">{{ $review['type'] }}</span>
                            <time class="text-[10px] text-gray-400" datetime="{{ $review['created_at']->toIso8601String() }}">{{ $review['created_at']->diffForHumans() }}</time>
                        </div>
                        <p class="mt-1 text-xs leading-5 text-gray-500">{{ $review['description'] }}</p>
                    </div>
                @empty
                    <p class="px-5 py-8 text-sm text-gray-400">No approval or rejection activity has been logged yet.</p>
                @endforelse
            </div>
        </article>
    </section>
</div>
@endsection
