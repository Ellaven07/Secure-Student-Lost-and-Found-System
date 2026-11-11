@extends('layouts.app')

@section('title', 'Generate and Print Reports')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <section class="rounded-2xl bg-[#174b38] px-6 py-7 text-white shadow-sm sm:px-8">
        <p class="text-xs font-bold uppercase tracking-[.18em] text-[#e2c66f]">Administrator portal</p>
        <h1 class="mt-2 text-2xl font-extrabold sm:text-3xl">Generate and Print Reports</h1>
        <p class="mt-2 text-sm text-green-50/85">Build a report from lost and found records, review the summary, then print or export it.</p>
    </section>

    @if ($errors->any())<div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>@endif

    <form method="GET" action="{{ route('admin.generate-reports.index') }}" class="grid gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:grid-cols-2 lg:grid-cols-4">
        <div><label for="start_date" class="mb-1 block text-xs font-semibold text-gray-600">Start date</label><input id="start_date" type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"></div>
        <div><label for="end_date" class="mb-1 block text-xs font-semibold text-gray-600">End date</label><input id="end_date" type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"></div>
        <div>
            <label for="report_type" class="mb-1 block text-xs font-semibold text-gray-600">Report type</label>
            <select id="report_type" name="report_type" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm">
                @foreach (['all' => 'All reports', 'lost' => 'Lost reports', 'found' => 'Found reports', 'category' => 'Reports by category', 'department' => 'Reports by department'] as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['report_type'] ?? 'all') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="department" class="mb-1 block text-xs font-semibold text-gray-600">Department</label>
            <select id="department" name="department" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm">
                <option value="">All departments</option>
                @foreach ($departments as $department)<option value="{{ $department }}" @selected(($filters['department'] ?? '') === $department)>{{ $department }}</option>@endforeach
            </select>
        </div>
        <div>
            <label for="category" class="mb-1 block text-xs font-semibold text-gray-600">Category</label>
            <select id="category" name="category" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm">
                <option value="">All categories</option>
                @foreach ($categories as $category)<option value="{{ $category->id }}" @selected((string) ($filters['category'] ?? '') === (string) $category->id)>{{ $category->name }}</option>@endforeach
            </select>
        </div>
        <div>
            <label for="approval_status" class="mb-1 block text-xs font-semibold text-gray-600">Approval status</label>
            <select id="approval_status" name="approval_status" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm">
                <option value="">All approval statuses</option>
                @foreach (['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $value => $label)<option value="{{ $value }}" @selected(($filters['approval_status'] ?? '') === $value)>{{ $label }}</option>@endforeach
            </select>
        </div>
        <div>
            <label for="item_status" class="mb-1 block text-xs font-semibold text-gray-600">Item status</label>
            <select id="item_status" name="item_status" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm">
                <option value="">All item statuses</option>
                @foreach (['open' => 'Open', 'claimed' => 'Claimed', 'returned' => 'Returned', 'rejected' => 'Rejected'] as $value => $label)<option value="{{ $value }}" @selected(($filters['item_status'] ?? '') === $value)>{{ $label }}</option>@endforeach
            </select>
        </div>
        <div class="flex items-end gap-2 sm:col-span-2 lg:col-span-4">
            <button type="submit" class="rounded-lg bg-[#174b38] px-4 py-2.5 text-sm font-bold text-white hover:bg-[#103b2b]">Generate report</button>
            <a href="{{ route('admin.generate-reports.index') }}" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-50">Clear</a>
        </div>
    </form>

    <section id="print-report" class="space-y-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-7">
        <header class="flex flex-wrap items-start justify-between gap-4 border-b border-gray-100 pb-5">
            <div><p class="text-xs font-bold uppercase tracking-[.15em] text-[#846e2b]">CvSU Lost &amp; Found</p><h2 class="mt-1 text-xl font-extrabold text-[#174b38]">{{ ucfirst($filters['report_type'] ?? 'all') }} Report</h2><p class="mt-1 text-sm text-gray-500">Generated {{ $generatedAt->format('M j, Y g:i A') }} by {{ $adminName }}</p>
                @if (!empty($filters['start_date']) || !empty($filters['end_date']))<p class="mt-1 text-xs text-gray-500">Date range: {{ $filters['start_date'] ?? 'Any time' }} to {{ $filters['end_date'] ?? 'Present' }}</p>@endif
            </div>
            <div class="flex gap-2 print:hidden">
                <button type="button" onclick="window.print()" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Print / Save PDF</button>
                <a href="{{ route('admin.generate-reports.export', request()->query()) }}" class="rounded-lg bg-[#174b38] px-4 py-2 text-sm font-bold text-white hover:bg-[#103b2b]">Export CSV</a>
            </div>
        </header>

        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
            @foreach ([['Lost reports', $summary['lost']], ['Found reports', $summary['found']], ['Approved', $summary['approved']], ['Rejected', $summary['rejected']], ['Pending', $summary['pending']], ['Total claims', $summary['claims']], ['Approved claims', $summary['approved_claims']], ['Rejected claims', $summary['rejected_claims']], ['Claimed items', $summary['claimed']], ['Returned items', $summary['returned']]] as [$label, $total])
                <article class="rounded-xl border border-gray-100 bg-[#f8faf8] p-4"><p class="text-xs font-medium text-gray-500">{{ $label }}</p><p class="mt-1 text-2xl font-extrabold text-[#174b38]">{{ number_format($total) }}</p></article>
            @endforeach
        </div>

        <div class="grid gap-5 lg:grid-cols-2">
            @foreach ([['Reports by category', $categoryStats], ['Reports by department', $departmentStats]] as [$heading, $stats])
                <section class="rounded-xl border border-gray-100 p-4"><h3 class="font-bold text-gray-800">{{ $heading }}</h3><div class="mt-3 divide-y divide-gray-100">@forelse ($stats as $stat)<div class="flex justify-between gap-3 py-2 text-sm"><span class="text-gray-600">{{ $stat->label }}</span><span class="font-bold text-[#174b38]">{{ number_format($stat->total) }}</span></div>@empty<p class="py-4 text-sm text-gray-500">No records found.</p>@endforelse</div></section>
            @endforeach
        </div>

        <section><div class="mb-3 flex items-center justify-between"><h3 class="font-bold text-gray-800">Detailed report data</h3><span class="text-xs text-gray-500">Showing up to 500 records</span></div>
            <div class="overflow-x-auto rounded-xl border border-gray-100"><table class="min-w-full divide-y divide-gray-100 text-left text-sm">
                <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500"><tr><th class="px-4 py-3">Item</th><th class="px-4 py-3">Type / Category</th><th class="px-4 py-3">Reporter / Department</th><th class="px-4 py-3">Location</th><th class="px-4 py-3">Approval</th><th class="px-4 py-3">Item status</th><th class="px-4 py-3">Submitted</th></tr></thead>
                <tbody class="divide-y divide-gray-100">@forelse ($reports as $report)<tr><td class="whitespace-nowrap px-4 py-3 font-semibold text-gray-800">{{ $report->item_name }}</td><td class="whitespace-nowrap px-4 py-3 text-gray-600"><span class="capitalize">{{ strtolower($report->type) }}</span> / {{ $report->category_name ?? 'Uncategorized' }}</td><td class="whitespace-nowrap px-4 py-3 text-gray-600">{{ $report->reporter_name }}<span class="block text-xs text-gray-400">{{ $report->department ?? 'Unspecified' }}</span></td><td class="px-4 py-3 text-gray-600">{{ $report->location ?? '—' }}</td><td class="px-4 py-3 text-gray-600">{{ $report->is_verified ? 'Approved' : (strtolower($report->status) === 'rejected' ? 'Rejected' : 'Pending') }}</td><td class="px-4 py-3 capitalize text-gray-600">{{ $report->status }}</td><td class="whitespace-nowrap px-4 py-3 text-gray-600">{{ \Illuminate\Support\Carbon::parse($report->created_at)->format('M j, Y') }}</td></tr>@empty<tr><td colspan="7" class="px-4 py-10 text-center text-gray-500">No report records match these filters.</td></tr>@endforelse</tbody>
            </table></div>
        </section>
    </section>
</div>
<style>@media print { aside, header, form, .print\:hidden { display: none !important; } body { background: white !important; } #print-report { border: 0; box-shadow: none; } }</style>
@endsection
