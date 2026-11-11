@extends('layouts.app')

@section('title', 'Review Item Reports')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <section class="rounded-2xl bg-[#174b38] px-6 py-7 text-white shadow-sm sm:px-8">
        <p class="text-xs font-bold uppercase tracking-[.18em] text-[#e2c66f]">Administrator portal</p>
        <h1 class="mt-2 text-2xl font-extrabold sm:text-3xl">Review Item Reports</h1>
        <p class="mt-2 text-sm text-green-50/85">Verify reports before they appear in student Browse Items, or reject reports that need correction.</p>
    </section>

    @if ($errors->any())<div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>@endif

    <section class="grid gap-4 sm:grid-cols-3">
        <a href="{{ route('admin.reports.index', ['status' => 'pending']) }}" class="rounded-xl border {{ $selectedStatus === 'pending' ? 'border-[#56846b] ring-2 ring-[#174b38]/10' : 'border-gray-200' }} bg-white p-5 shadow-sm"><p class="text-sm text-gray-500">Awaiting review</p><p class="mt-2 text-2xl font-extrabold text-[#174b38]">{{ number_format($pendingCount) }}</p></a>
        <a href="{{ route('admin.reports.index', ['status' => 'verified']) }}" class="rounded-xl border {{ $selectedStatus === 'verified' ? 'border-[#56846b] ring-2 ring-[#174b38]/10' : 'border-gray-200' }} bg-white p-5 shadow-sm"><p class="text-sm text-gray-500">Verified</p><p class="mt-2 text-2xl font-extrabold text-[#174b38]">{{ number_format($verifiedCount) }}</p></a>
        <a href="{{ route('admin.reports.index', ['status' => 'rejected']) }}" class="rounded-xl border {{ $selectedStatus === 'rejected' ? 'border-[#56846b] ring-2 ring-[#174b38]/10' : 'border-gray-200' }} bg-white p-5 shadow-sm"><p class="text-sm text-gray-500">Rejected</p><p class="mt-2 text-2xl font-extrabold text-[#174b38]">{{ number_format($rejectedCount) }}</p></a>
    </section>

    <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4 sm:px-6"><h2 class="font-bold text-[#174b38]">{{ ucfirst($selectedStatus) }} reports</h2><span class="rounded-full bg-[#edf4ef] px-3 py-1 text-xs font-semibold text-[#28634c]">{{ $reports->total() }} total</span></div>
        @forelse ($reports as $report)
            <article class="flex flex-col gap-5 border-b border-gray-100 p-5 last:border-0 sm:p-6 lg:flex-row">
                @if ($report->photo_path)<img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($report->photo_path) }}" alt="Photo of {{ $report->item_name }}" class="h-28 w-full rounded-xl bg-[#edf4ef] object-cover sm:w-36">@else<div class="grid h-28 w-full shrink-0 place-items-center rounded-xl bg-[#edf4ef] text-3xl text-[#28634c] sm:w-36" aria-hidden="true">{{ strtolower($report->type) === 'lost' ? '−' : '+' }}</div>@endif
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2"><span class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide {{ strtolower($report->type) === 'lost' ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700' }}">{{ $report->type }}</span><span class="text-xs text-gray-500">{{ $report->category_name ?? 'Uncategorized' }}</span><span class="text-xs text-gray-400">{{ $report->status }}</span></div>
                    <h3 class="mt-2 text-lg font-bold text-gray-800">{{ $report->item_name }}</h3>
                    <p class="mt-1 whitespace-pre-line text-sm leading-6 text-gray-600">{{ $report->description ?: 'No description provided.' }}</p>
                    <p class="mt-3 text-xs leading-5 text-gray-500">{{ $report->location ?: 'Location not provided' }} · {{ $report->date_lost_or_found ? \Illuminate\Support\Carbon::parse($report->date_lost_or_found)->format('M j, Y') : 'Date not provided' }}</p>
                    <p class="mt-1 text-xs text-gray-400">Submitted by {{ $report->student_name }} ({{ $report->student_email }}) · {{ \Illuminate\Support\Carbon::parse($report->created_at)->format('M j, Y g:i A') }}</p>
                </div>
                @if (! $report->is_verified && strtolower($report->status) !== 'rejected')
                    <div class="flex shrink-0 flex-col gap-2 lg:w-52">
                        <form method="POST" action="{{ route('admin.reports.verify', $report->id) }}" data-confirm="Verify this report and make it visible in Browse Items?" data-confirm-title="Verify item report" data-confirm-label="Verify report">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full rounded-lg bg-[#174b38] px-4 py-2.5 text-sm font-bold text-white transition hover:bg-[#103b2b]">Verify report</button>
                        </form>
                        <details class="rounded-lg border border-red-200 bg-red-50 p-3">
                            <summary class="cursor-pointer text-center text-sm font-semibold text-red-700">Reject report</summary>
                            <form method="POST" action="{{ route('admin.reports.reject', $report->id) }}" class="mt-3 space-y-2" data-confirm="Reject this report and send the note to the student?" data-confirm-title="Reject item report" data-confirm-label="Reject report" data-confirm-tone="danger">
                                @csrf
                                @method('PATCH')
                                <label for="reason-{{ $report->id }}" class="block text-xs font-semibold text-gray-600">Reason for student</label>
                                <textarea id="reason-{{ $report->id }}" name="reason" required maxlength="1000" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs outline-none focus:border-red-400" placeholder="Explain why this report was not approved."></textarea>
                                <button type="submit" class="w-full rounded-lg bg-red-700 px-3 py-2 text-xs font-bold text-white hover:bg-red-800">Confirm rejection</button>
                            </form>
                        </details>
                    </div>
                @else
                    <span class="h-fit rounded-full px-3 py-1.5 text-xs font-bold {{ $report->is_verified ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">{{ $report->is_verified ? 'Verified' : 'Rejected' }}</span>
                @endif
            </article>
        @empty
            <div class="px-6 py-14 text-center"><h3 class="font-bold text-gray-800">No {{ $selectedStatus }} reports</h3><p class="mt-2 text-sm text-gray-500">Reports in this status will appear here.</p></div>
        @endforelse
        @if ($reports->hasPages())<div class="border-t border-gray-100 px-5 py-4 sm:px-6">{{ $reports->links() }}</div>@endif
    </section>
</div>
@endsection
