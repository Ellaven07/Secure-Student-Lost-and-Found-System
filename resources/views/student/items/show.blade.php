@extends(auth()->user()->isVisitor() ? 'layouts.visitor' : 'layouts.student')

@section('title', 'Item Details')
@section('page-label', 'Item Details')

@section('content')
@php
    $browseRoutePrefix = $isVisitor ? 'visitor' : 'student';
@endphp

<div class="mx-auto max-w-4xl space-y-5">
    <a href="{{ route($browseRoutePrefix.'.items.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[#28634c] hover:text-[#174b38]">← Back to browse items</a>
    <article class="overflow-hidden rounded-2xl border border-[#e4eae6] bg-white shadow-sm">
        @if ($item->photo_path)
            <img src="{{ app(\App\Services\SupabaseStorageService::class)->url($item->photo_path) }}" alt="Photo of {{ $item->item_name }}" class="max-h-[440px] w-full bg-[#edf4ef] object-contain">
        @else
            <div class="grid h-56 place-items-center bg-[#edf4ef] text-5xl text-[#28634c]" aria-hidden="true">{{ strtolower($item->type) === 'lost' ? '−' : '+' }}</div>
        @endif
        <div class="space-y-6 p-6 sm:p-8">
            <div class="flex flex-wrap items-center gap-3">
                <span class="rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide {{ strtolower($item->type) === 'lost' ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700' }}">{{ $item->type }} item</span>
                <span class="text-sm text-gray-500">{{ $item->category_name ?? 'Uncategorized' }}</span>
                <span class="text-sm text-gray-400">·</span>
                <span class="text-sm text-gray-500">Reported by {{ $item->reporter_name }}</span>
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-[#174b38] sm:text-3xl">{{ $item->item_name }}</h1>
                <p class="mt-4 whitespace-pre-line text-sm leading-7 text-gray-600">{{ $item->description }}</p>
            </div>
            <dl class="grid gap-4 rounded-xl bg-[#f6f8f6] p-5 sm:grid-cols-2">
                <div><dt class="text-xs font-semibold uppercase tracking-wide text-gray-400">{{ strtolower($item->type) === 'lost' ? 'Last seen location' : 'Found at' }}</dt><dd class="mt-1 text-sm font-semibold text-gray-700">{{ $item->location ?: 'Not provided' }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-wide text-gray-400">Date {{ strtolower($item->type) }}</dt><dd class="mt-1 text-sm font-semibold text-gray-700">{{ $item->date_lost_or_found ? \Illuminate\Support\Carbon::parse($item->date_lost_or_found)->format('F j, Y') : 'Not provided' }}</dd></div>
            </dl>
            @if ($isVisitor)
                <div class="rounded-xl border border-[#dce9df] bg-[#edf4ef] px-4 py-3 text-sm text-[#174b38]">Visitor access is limited to browsing items.</div>
            @elseif ($canClaim)
                <a href="{{ route('student.claims.create', $item->id) }}" class="inline-flex w-full items-center justify-center rounded-lg bg-[#174b38] px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#103b2b] sm:w-auto">Submit a claim request</a>
            @elseif ($hasExistingClaim)
                <div class="rounded-xl border border-[#dce9df] bg-[#edf4ef] px-4 py-3 text-sm text-[#174b38]">You already have a pending or approved claim for this item. <a href="{{ route('student.claims.index') }}" class="font-bold underline">View claim status</a></div>
            @elseif (strtolower($item->type) === 'found' && (int) $item->user_id === (int) auth()->id())
                <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-600">You submitted this found item report, so you cannot claim it.</div>
            @elseif (strtolower($item->type) === 'found')
                <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-600">{{ strtolower($item->status) === 'open' ? 'This item already has an approved claim.' : 'This item report is no longer open for claims.' }}</div>
            @endif
        </div>
    </article>
</div>
@endsection
