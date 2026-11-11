@extends('layouts.student')

@section('title', 'Submit Claim Request')
@section('page-label', 'Submit Claim Request')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <section class="rounded-2xl bg-[#174b38] px-6 py-7 text-white shadow-sm sm:px-8">
        <p class="text-xs font-bold uppercase tracking-[.18em] text-[#e2c66f]">Item recovery</p>
        <h1 class="mt-2 text-2xl font-extrabold sm:text-3xl">Submit a Claim Request</h1>
        <p class="mt-2 text-sm text-green-50/85">Describe details that help verify this item belongs to you. The administrator will review your request.</p>
    </section>

    <section class="flex gap-4 rounded-2xl border border-[#e4eae6] bg-white p-5 shadow-sm">
        @if ($item->photo_path)
            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($item->photo_path) }}" alt="Photo of {{ $item->item_name }}" class="h-20 w-20 shrink-0 rounded-xl object-cover">
        @else
            <div class="grid h-20 w-20 shrink-0 place-items-center rounded-xl bg-[#edf4ef] text-2xl text-[#28634c]" aria-hidden="true">+</div>
        @endif
        <div class="min-w-0"><p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Found item</p><h2 class="mt-1 truncate font-bold text-[#174b38]">{{ $item->item_name }}</h2><p class="mt-1 text-sm text-gray-500">{{ $item->category_name ?? 'Uncategorized' }} · {{ $item->location }}</p></div>
    </section>

    <form method="POST" action="{{ route('student.claims.store', $item->id) }}" class="space-y-5 rounded-2xl border border-[#e4eae6] bg-white p-5 shadow-sm sm:p-7">
        @csrf
        <div>
            <label for="proof_description" class="mb-2 block text-sm font-semibold text-gray-700">How can you prove this item is yours? <span class="text-red-600">*</span></label>
            <p class="mb-3 text-xs leading-5 text-gray-500">Include identifying details only the owner would know, such as contents, marks, serial details, or where you last had it. Avoid sharing sensitive passwords or full payment card numbers.</p>
            <textarea id="proof_description" name="proof_description" rows="7" required minlength="20" maxlength="5000" placeholder="Describe identifying details to help the administrator verify your claim..." class="w-full resize-y rounded-lg border border-gray-300 px-3.5 py-3 text-sm outline-none focus:border-[#56846b] focus:ring-2 focus:ring-[#174b38]/10">{{ old('proof_description') }}</textarea>
            @error('proof_description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-between">
            <a href="{{ route('student.items.show', $item->id) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-5 py-3 text-sm font-semibold text-gray-600 hover:bg-gray-50">Back to item</a>
            <button type="submit" class="rounded-lg bg-[#174b38] px-6 py-3 text-sm font-bold text-white transition hover:bg-[#103b2b]">Send claim for review</button>
        </div>
    </form>
</div>
@endsection
