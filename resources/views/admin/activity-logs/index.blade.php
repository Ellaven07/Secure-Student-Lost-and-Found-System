@extends('layouts.app')

@section('title', 'Activity Logs')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <section class="rounded-2xl bg-[#174b38] px-6 py-7 text-white shadow-sm sm:px-8">
        <p class="text-xs font-bold uppercase tracking-[.18em] text-[#e2c66f]">Administrator portal</p>
        <h1 class="mt-2 text-2xl font-extrabold sm:text-3xl">Activity Logs</h1>
        <p class="mt-2 text-sm text-green-50/85">Review important actions recorded across the system.</p>
    </section>

    <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="grid gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:grid-cols-2 lg:grid-cols-5">
        <div>
            <label for="user" class="mb-1 block text-xs font-semibold text-gray-600">User</label>
            <input id="user" name="user" value="{{ $filters['user'] ?? '' }}" placeholder="Name or email" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-[#56846b]">
        </div>
        <div>
            <label for="action" class="mb-1 block text-xs font-semibold text-gray-600">Action</label>
            <input id="action" name="action" value="{{ $filters['action'] ?? '' }}" placeholder="Search action" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-[#56846b]">
        </div>
        <div>
            <label for="role" class="mb-1 block text-xs font-semibold text-gray-600">Role</label>
            <select id="role" name="role" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none focus:border-[#56846b]">
                <option value="">All roles</option>
                <option value="admin" @selected(($filters['role'] ?? '') === 'admin')>Admin</option>
                <option value="student" @selected(($filters['role'] ?? '') === 'student')>Student</option>
                <option value="deleted" @selected(($filters['role'] ?? '') === 'deleted')>Deleted account</option>
            </select>
        </div>
        <div>
            <label for="start_date" class="mb-1 block text-xs font-semibold text-gray-600">From</label>
            <input id="start_date" type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-[#56846b]">
        </div>
        <div>
            <label for="end_date" class="mb-1 block text-xs font-semibold text-gray-600">To</label>
            <input id="end_date" type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-[#56846b]">
        </div>
        <div class="flex gap-2 sm:col-span-2 lg:col-span-5">
            <button type="submit" class="rounded-lg bg-[#174b38] px-4 py-2.5 text-sm font-bold text-white hover:bg-[#103b2b]">Apply filters</button>
            <a href="{{ route('admin.activity-logs.index') }}" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-50">Clear</a>
        </div>
    </form>

    <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4 sm:px-6">
            <h2 class="font-bold text-[#174b38]">Recorded activity</h2>
            <span class="rounded-full bg-[#edf4ef] px-3 py-1 text-xs font-semibold text-[#28634c]">{{ number_format($logs->total()) }} entries</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-left text-sm">
                <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                    <tr><th class="px-5 py-3">Log ID</th><th class="px-5 py-3">User</th><th class="px-5 py-3">Role</th><th class="px-5 py-3">Action and details</th><th class="px-5 py-3">IP address</th><th class="px-5 py-3">Date and time</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($logs as $log)
                        <tr class="align-top">
                            <td class="whitespace-nowrap px-5 py-4 font-semibold text-gray-500">#{{ $log->id }}</td>
                            <td class="px-5 py-4 font-semibold text-gray-800">{{ $log->user_name ?? 'Deleted account' }}</td>
                            <td class="whitespace-nowrap px-5 py-4"><span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold capitalize text-gray-600">{{ $log->user_role ?? 'Deleted' }}</span></td>
                            <td class="min-w-64 px-5 py-4"><p class="font-semibold text-gray-800">{{ $log->action }}</p><p class="mt-1 whitespace-pre-line text-xs leading-5 text-gray-500">{{ $log->details ?: 'No additional details.' }}</p></td>
                            <td class="whitespace-nowrap px-5 py-4 text-gray-600">{{ $log->ip_address ?: '—' }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-gray-600">{{ \Illuminate\Support\Carbon::parse($log->created_at)->format('M j, Y g:i A') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-12 text-center text-sm text-gray-500">No activity logs match these filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($logs->hasPages())<div class="border-t border-gray-100 px-5 py-4 sm:px-6">{{ $logs->links() }}</div>@endif
    </section>
</div>
@endsection
