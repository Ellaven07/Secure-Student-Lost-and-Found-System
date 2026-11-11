@extends('layouts.student')

@section('title', 'My Report History')
@section('page-label', 'My Report History')

@section('content')
<div class="mx-auto max-w-6xl space-y-6">
    <section class="flex flex-col gap-4 rounded-2xl bg-[#174b38] px-6 py-7 text-white shadow-sm sm:flex-row sm:items-center sm:justify-between sm:px-8">
        <div>
            <p class="text-xs font-bold uppercase tracking-[.18em] text-[#e2c66f]">Your submissions</p>
            <h1 class="mt-2 text-2xl font-extrabold sm:text-3xl">My Report History</h1>
            <p class="mt-2 text-sm text-green-50/85">Track the lost and found items you have reported.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('student.reports.lost.create') }}" class="rounded-lg bg-white/10 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/20">− Report lost</a>
            <a href="{{ route('student.reports.found.create') }}" class="rounded-lg bg-[#d7b65d] px-4 py-2.5 text-sm font-bold text-[#174b38] transition hover:bg-[#e2c66f]">+ Report found</a>
        </div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-[#e4eae6] bg-white shadow-sm">
        <div class="flex flex-col gap-4 border-b border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div class="flex items-center gap-3">
                <h2 class="font-bold text-[#174b38]">Submitted reports</h2>
                <span class="rounded-full bg-[#edf4ef] px-3 py-1 text-xs font-semibold text-[#28634c]">{{ $reports->total() }} {{ request()->filled('category_id') ? 'matching' : 'total' }}</span>
            </div>
            <form method="GET" action="{{ route('student.reports.history') }}" class="flex flex-col gap-2 sm:flex-row">
                <label for="category_id" class="sr-only">Filter reports by category</label>
                <select id="category_id" name="category_id" class="rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-700 outline-none focus:border-[#56846b] focus:ring-2 focus:ring-[#174b38]/10">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) $selectedCategoryId === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="rounded-lg bg-[#174b38] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#103b2b]">Filter</button>
                @if ($selectedCategoryId !== '')
                    <a href="{{ route('student.reports.history') }}" class="rounded-lg border border-gray-300 px-4 py-2.5 text-center text-sm font-semibold text-gray-600 transition hover:bg-gray-50">Clear</a>
                @endif
            </form>
        </div>
        @if ($reports->isEmpty())
            <div class="px-6 py-14 text-center">
                <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-[#edf4ef] text-2xl text-[#28634c]" aria-hidden="true">⌕</span>
                <h3 class="mt-4 font-bold text-gray-800">{{ $selectedCategoryId !== '' ? 'No reports in this category' : 'No reports yet' }}</h3>
                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500">{{ $selectedCategoryId !== '' ? 'Choose another category or clear the filter to see your other reports.' : 'When you report a lost or found item, its status and details will appear here.' }}</p>
                <div class="mt-5 flex flex-wrap justify-center gap-3">
                    @if ($selectedCategoryId !== '')
                        <a href="{{ route('student.reports.history') }}" class="rounded-lg border border-[#b8cfc0] px-4 py-2.5 text-sm font-semibold text-[#174b38] hover:bg-[#edf4ef]">Clear category filter</a>
                    @endif
                    <a href="{{ route('student.reports.lost.create') }}" class="rounded-lg bg-[#174b38] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#103b2b]">Report lost item</a>
                    <a href="{{ route('student.reports.found.create') }}" class="rounded-lg border border-[#b8cfc0] px-4 py-2.5 text-sm font-semibold text-[#174b38] hover:bg-[#edf4ef]">Report found item</a>
                </div>
            </div>
        @else
            <div class="divide-y divide-gray-100">
                @foreach ($reports as $report)
                    <article class="flex flex-col gap-4 p-5 sm:flex-row sm:items-start sm:p-6">
                        @if ($report->photo_path)
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($report->photo_path) }}" alt="Photo of {{ $report->item_name }}" class="h-20 w-20 shrink-0 rounded-xl border border-gray-100 object-cover">
                        @else
                            <div class="grid h-20 w-20 shrink-0 place-items-center rounded-xl bg-[#edf4ef] text-2xl text-[#28634c]" aria-hidden="true">{{ $report->type === 'lost' ? '−' : '+' }}</div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="font-bold text-gray-800">{{ $report->item_name }}</h3>
                                <span class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide {{ $report->type === 'lost' ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700' }}">{{ $report->type }}</span>
                                <span class="rounded-full bg-gray-100 px-2.5 py-1 text-[10px] font-semibold capitalize text-gray-600">{{ str_replace('_', ' ', $report->status) }}</span>
                            </div>
                            <p class="mt-1 text-sm leading-6 text-gray-500">{{ $report->description }}</p>
                            <div class="mt-3 flex flex-wrap gap-x-5 gap-y-1 text-xs text-gray-500">
                                <span>{{ $report->category_name ?? 'Uncategorized' }}</span>
                                <span>{{ $report->location }}</span>
                                <span>{{ \Illuminate\Support\Carbon::parse($report->date_lost_or_found)->format('M j, Y') }}</span>
                            </div>
                        </div>
                        <div class="flex shrink-0 flex-row items-center gap-2 sm:flex-col sm:items-end">
                            <p class="text-xs text-gray-400">Submitted {{ \Illuminate\Support\Carbon::parse($report->created_at)->format('M j, Y') }}</p>
                            @if ((int) $report->claims_count === 0 && ! in_array(strtolower($report->status), ['claimed', 'closed', 'resolved'], true))
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('student.reports.edit', $report->id) }}" class="rounded-md border border-[#dce9df] px-2.5 py-1.5 text-xs font-semibold text-[#28634c] transition hover:bg-[#edf4ef]">Edit</a>
                                    <form method="POST" action="{{ route('student.reports.destroy', $report->id) }}" data-confirm="Delete this report? This cannot be undone." data-confirm-title="Delete report" data-confirm-label="Delete report" data-confirm-tone="danger">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-md border border-red-200 px-2.5 py-1.5 text-xs font-semibold text-red-700 transition hover:bg-red-50">Delete</button>
                                    </form>
                                </div>
                            @else
                                <span class="text-[11px] text-gray-400">Locked while a claim exists or the item is closed</span>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
            @if ($reports->hasPages())
                <div class="border-t border-gray-100 px-5 py-4 sm:px-6">{{ $reports->links() }}</div>
            @endif
        @endif
    </section>
</div>
@endsection
