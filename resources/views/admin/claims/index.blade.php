@extends('layouts.app')

@section('title', 'Review Claim Requests')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <section class="rounded-2xl bg-[#174b38] px-6 py-7 text-white shadow-sm sm:px-8">
        <p class="text-xs font-bold uppercase tracking-[.18em] text-[#e2c66f]">Administrator portal</p>
        <h1 class="mt-2 text-2xl font-extrabold sm:text-3xl">Review Claim Requests</h1>
        <p class="mt-2 text-sm text-green-50/85">Check the claimant’s proof, then approve or reject the request with a note.</p>
    </section>

    @if ($errors->any())<div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>@endif

    <section class="grid gap-4 sm:grid-cols-3">
        <a href="{{ route('admin.claims.index', ['status' => 'pending']) }}" class="rounded-xl border {{ $selectedStatus === 'pending' ? 'border-[#56846b] ring-2 ring-[#174b38]/10' : 'border-gray-200' }} bg-white p-5 shadow-sm"><p class="text-sm text-gray-500">Pending</p><p class="mt-2 text-2xl font-extrabold text-[#174b38]">{{ number_format($pendingCount) }}</p></a>
        <a href="{{ route('admin.claims.index', ['status' => 'approved']) }}" class="rounded-xl border {{ $selectedStatus === 'approved' ? 'border-[#56846b] ring-2 ring-[#174b38]/10' : 'border-gray-200' }} bg-white p-5 shadow-sm"><p class="text-sm text-gray-500">Approved</p><p class="mt-2 text-2xl font-extrabold text-[#174b38]">{{ number_format($approvedCount) }}</p></a>
        <a href="{{ route('admin.claims.index', ['status' => 'rejected']) }}" class="rounded-xl border {{ $selectedStatus === 'rejected' ? 'border-[#56846b] ring-2 ring-[#174b38]/10' : 'border-gray-200' }} bg-white p-5 shadow-sm"><p class="text-sm text-gray-500">Rejected</p><p class="mt-2 text-2xl font-extrabold text-[#174b38]">{{ number_format($rejectedCount) }}</p></a>
    </section>

    <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4 sm:px-6"><h2 class="font-bold text-[#174b38]">{{ ucfirst($selectedStatus) }} requests</h2><span class="rounded-full bg-[#edf4ef] px-3 py-1 text-xs font-semibold text-[#28634c]">{{ $claims->total() }} total</span></div>
        @forelse ($claims as $claim)
            <article class="space-y-5 border-b border-gray-100 p-5 last:border-0 sm:p-6">
                <div class="flex flex-col gap-4 sm:flex-row">
                    @if ($claim->photo_path)<img src="{{ app(\App\Services\SupabaseStorageService::class)->url($claim->photo_path) }}" alt="Photo of {{ $claim->item_name }}" class="h-24 w-full rounded-xl bg-[#edf4ef] object-cover sm:w-32">@else<div class="grid h-24 w-full shrink-0 place-items-center rounded-xl bg-[#edf4ef] text-3xl text-[#28634c] sm:w-32" aria-hidden="true">+</div>@endif
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2"><h3 class="text-lg font-bold text-gray-800">{{ $claim->item_name }}</h3><span class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide {{ strtolower($claim->status) === 'approved' ? 'bg-emerald-50 text-emerald-700' : (strtolower($claim->status) === 'rejected' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700') }}">{{ $claim->status }}</span></div>
                        <p class="mt-1 text-sm text-gray-500">{{ $claim->category_name ?? 'Uncategorized' }} · {{ $claim->location }} · Reported by {{ $claim->reporter_name }}</p>
                        <p class="mt-2 text-xs text-gray-500">Claimant: <span class="font-semibold text-gray-700">{{ $claim->claimant_name }}</span> ({{ $claim->claimant_email }}) · Submitted {{ \Illuminate\Support\Carbon::parse($claim->created_at)->format('M j, Y g:i A') }}</p>
                    </div>
                </div>
                <div class="rounded-xl bg-[#f6f8f6] p-4"><p class="text-xs font-bold uppercase tracking-wide text-[#846e2b]">Ownership proof</p><p class="mt-2 whitespace-pre-line text-sm leading-6 text-gray-700">{{ $claim->proof_description }}</p></div>
                @if ($claim->remarks && strtolower($claim->status) !== 'pending')<p class="text-sm text-gray-500"><span class="font-semibold">Review note:</span> {{ $claim->remarks }}</p>@endif
                @if (strtolower($claim->status) === 'pending')
                    <div class="grid gap-4 lg:grid-cols-2">
                        <form method="POST" action="{{ route('admin.claims.approve', $claim->id) }}" class="space-y-2 rounded-xl border border-emerald-200 bg-emerald-50/50 p-4" data-confirm="Approve this claim? The item will be marked claimed and other pending claims will be declined." data-confirm-title="Approve claim request" data-confirm-label="Approve claim">
                            @csrf
                            @method('PATCH')
                            <label for="approve-remarks-{{ $claim->id }}" class="block text-xs font-semibold text-gray-600">Note to claimant <span class="font-normal text-gray-400">(optional)</span></label>
                            <textarea id="approve-remarks-{{ $claim->id }}" name="remarks" maxlength="2000" rows="2" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none focus:border-[#56846b]" placeholder="Add pickup instructions or other information."></textarea>
                            <button type="submit" class="w-full rounded-lg bg-[#174b38] px-4 py-2.5 text-sm font-bold text-white hover:bg-[#103b2b]">Approve claim</button>
                        </form>
                        <form method="POST" action="{{ route('admin.claims.reject', $claim->id) }}" class="space-y-2 rounded-xl border border-red-200 bg-red-50/50 p-4" data-confirm="Reject this claim and send the note to the student?" data-confirm-title="Reject claim request" data-confirm-label="Reject claim" data-confirm-tone="danger">
                            @csrf
                            @method('PATCH')
                            <label for="reject-remarks-{{ $claim->id }}" class="block text-xs font-semibold text-gray-600">Reason for rejection <span class="text-red-600">*</span></label>
                            <textarea id="reject-remarks-{{ $claim->id }}" name="remarks" required maxlength="2000" rows="2" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none focus:border-red-400" placeholder="Explain what could not be verified."></textarea>
                            <button type="submit" class="w-full rounded-lg bg-red-700 px-4 py-2.5 text-sm font-bold text-white hover:bg-red-800">Reject claim</button>
                        </form>
                    </div>
                @endif
            </article>
        @empty
            <div class="px-6 py-14 text-center"><h3 class="font-bold text-gray-800">No {{ $selectedStatus }} claim requests</h3><p class="mt-2 text-sm text-gray-500">Claim requests in this status will appear here.</p></div>
        @endforelse
        @if ($claims->hasPages())<div class="border-t border-gray-100 px-5 py-4 sm:px-6">{{ $claims->links() }}</div>@endif
    </section>
</div>
@endsection
