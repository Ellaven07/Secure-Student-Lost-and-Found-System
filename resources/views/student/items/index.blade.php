@extends('layouts.student')

@section('title', 'Browse Items')
@section('page-label', 'Browse and Search Items')

@section('content')
<div class="mx-auto max-w-6xl space-y-6">
    <section class="rounded-2xl bg-[#174b38] px-6 py-7 text-white shadow-sm sm:px-8">
        <p class="text-xs font-bold uppercase tracking-[.18em] text-[#e2c66f]">Campus lost &amp; found</p>
        <h1 class="mt-2 text-2xl font-extrabold sm:text-3xl">Browse and Search Items</h1>
        <p class="mt-2 text-sm text-green-50/85">Search verified reports from the CvSU community and see if an item matches what you are looking for.</p>
    </section>

    <form method="GET" action="{{ route('student.items.index') }}" class="grid gap-3 rounded-2xl border border-[#e4eae6] bg-white p-4 shadow-sm sm:grid-cols-2 lg:grid-cols-[minmax(0,1fr)_180px_220px_auto_auto] lg:items-end">
        <div>
            <label for="search" class="mb-1.5 block text-xs font-semibold text-gray-600">Search items</label>
            <input id="search" name="search" type="search" value="{{ $filters['search'] }}" placeholder="Name, details, location..." class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm outline-none focus:border-[#56846b] focus:ring-2 focus:ring-[#174b38]/10">
        </div>
        <div>
            <label for="type" class="mb-1.5 block text-xs font-semibold text-gray-600">Report type</label>
            <select id="type" name="type" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-[#56846b]">
                <option value="">Lost and found</option>
                <option value="lost" @selected($filters['type'] === 'lost')>Lost items</option>
                <option value="found" @selected($filters['type'] === 'found')>Found items</option>
            </select>
        </div>
        <div>
            <label for="category_id" class="mb-1.5 block text-xs font-semibold text-gray-600">Category</label>
            <select id="category_id" name="category_id" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-[#56846b]">
                <option value="">All categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) $filters['category_id'] === (string) $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="rounded-lg bg-[#174b38] px-5 py-2.5 text-sm font-bold text-white transition hover:bg-[#103b2b]">Search</button>
        @if ($filters['search'] !== '' || $filters['type'] !== '' || $filters['category_id'] !== '')
            <a href="{{ route('student.items.index') }}" class="rounded-lg border border-gray-300 px-5 py-2.5 text-center text-sm font-semibold text-gray-600 transition hover:bg-gray-50">Clear</a>
        @endif
    </form>

    <div class="flex items-center justify-between px-1">
        <h2 class="font-bold text-[#174b38]">Verified reports</h2>
        <span class="rounded-full bg-[#edf4ef] px-3 py-1 text-xs font-semibold text-[#28634c]">{{ $items->total() }} items</span>
    </div>

    @if ($items->isEmpty())
        <section class="rounded-2xl border border-[#e4eae6] bg-white px-6 py-14 text-center shadow-sm">
            <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-[#edf4ef] text-[#28634c]" aria-hidden="true"><svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m21 21-4.3-4.3M19 10.5a8.5 8.5 0 1 1-17 0 8.5 8.5 0 0 1 17 0Z" /></svg></span>
            <h3 class="mt-4 font-bold text-gray-800">No matching items found</h3>
            <p class="mt-2 text-sm text-gray-500">Try another search or check back as more reports are verified.</p>
        </section>
    @else
        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($items as $item)
                <a href="{{ route('student.items.show', $item->id) }}" class="group overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:border-[#b8cfc0] hover:shadow-md">
                    @if ($item->photo_path)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($item->photo_path) }}" alt="Photo of {{ $item->item_name }}" class="h-48 w-full bg-[#edf4ef] object-cover">
                    @else
                        <div class="grid h-48 place-items-center bg-[#edf4ef] text-4xl text-[#28634c]" aria-hidden="true">{{ strtolower($item->type) === 'lost' ? '−' : '+' }}</div>
                    @endif
                    <div class="space-y-3 p-5">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide {{ strtolower($item->type) === 'lost' ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700' }}">{{ $item->type }}</span>
                            <span class="text-xs text-gray-500">{{ $item->category_name ?? 'Uncategorized' }}</span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 group-hover:text-[#174b38]">{{ $item->item_name }}</h3>
                        <p class="line-clamp-2 text-sm leading-6 text-gray-500">{{ $item->description }}</p>
                        <div class="flex items-center justify-between border-t border-gray-100 pt-3 text-xs text-gray-500">
                            <span class="truncate">{{ $item->location }}</span>
                            <span class="shrink-0">{{ $item->date_lost_or_found ? \Illuminate\Support\Carbon::parse($item->date_lost_or_found)->format('M j, Y') : 'Date not listed' }}</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        @if ($items->hasPages())
            <div class="rounded-xl border border-gray-200 bg-white px-5 py-4">{{ $items->links() }}</div>
        @endif
    @endif
</div>
@endsection
