@extends('layouts.app')

@section('title', 'Student Profile')

@section('content')
<div class="mx-auto max-w-5xl">

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-sm text-gray-500">Student Management / Student Profile</p>
            <h1 class="mt-1 text-2xl font-bold text-gray-800">Student Profile</h1>
            <p class="mt-1 text-sm text-gray-500">View the student's account and academic information.</p>
        </div>

        <a href="{{ route('admin.students.index') }}"
           class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-600 transition hover:bg-gray-50">
            Back to Students
        </a>
    </div>

    @if (session('success'))
        <div class="mb-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

        <div class="bg-[#174b38] px-6 py-7 text-white sm:flex sm:items-center sm:justify-between">
            <div class="flex items-center gap-5">

                @if ($profilePhotoUrl)
                    <img src="{{ $profilePhotoUrl }}"
                         alt="Student profile photo"
                         class="h-20 w-20 rounded-full border-4 border-white/30 object-cover">
                @else
                    <div class="flex h-20 w-20 items-center justify-center rounded-full border-4 border-white/30 bg-white/15 text-2xl font-bold">
                        {{ strtoupper(substr($student->name, 0, 1)) }}
                    </div>
                @endif

                <div>
                    <h2 class="text-xl font-bold">{{ $student->name }}</h2>
                    <p class="mt-1 text-sm text-green-100">
                        {{ $student->student_no ?: 'No student number assigned' }}
                    </p>
                    <p class="mt-1 text-xs text-green-100/80">{{ $student->email }}</p>
                </div>
            </div>

            <div class="mt-5 sm:mt-0">
                <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold
                    {{ $student->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-700' }}">
                    {{ ucfirst($student->status ?? 'Unknown') }}
                </span>
            </div>
        </div>

        <div class="p-6">

            <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h3 class="font-bold text-gray-800">Personal Information</h3>
                    <p class="mt-1 text-xs text-gray-500">Registered student details</p>
                </div>

                <a href="{{ route('admin.students.edit', $student) }}"
                   class="rounded-lg bg-[#174b38] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#28634c]">
                    Edit Profile
                </a>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">

                <div class="rounded-lg bg-gray-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Full Name</p>
                    <p class="mt-2 text-sm font-semibold text-gray-800">{{ $student->name }}</p>
                </div>

                <div class="rounded-lg bg-gray-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Student Number</p>
                    <p class="mt-2 text-sm font-semibold text-gray-800">{{ $student->student_no ?: 'Not provided' }}</p>
                </div>

                <div class="rounded-lg bg-gray-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Email Address</p>
                    <p class="mt-2 break-all text-sm font-semibold text-gray-800">{{ $student->email }}</p>
                </div>

                <div class="rounded-lg bg-gray-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Department</p>
                    <p class="mt-2 text-sm font-semibold text-gray-800">{{ $student->department ?: 'Not provided' }}</p>
                </div>

                <div class="rounded-lg bg-gray-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Course</p>
                    <p class="mt-2 text-sm font-semibold text-gray-800">{{ $student->course ?: 'Not provided' }}</p>
                </div>

                <div class="rounded-lg bg-gray-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Year Level</p>
                    <p class="mt-2 text-sm font-semibold text-gray-800">{{ $student->year_level ?: 'Not provided' }}</p>
                </div>

                <div class="rounded-lg bg-gray-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Account Role</p>
                    <p class="mt-2 text-sm font-semibold text-gray-800">{{ ucfirst($student->role) }}</p>
                </div>

                <div class="rounded-lg bg-gray-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Account Status</p>
                    <p class="mt-2 text-sm font-semibold {{ $student->status === 'active' ? 'text-green-700' : 'text-red-600' }}">
                        {{ ucfirst($student->status ?? 'Unknown') }}
                    </p>
                </div>

                <div class="rounded-lg bg-gray-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Registered Date</p>
                    <p class="mt-2 text-sm font-semibold text-gray-800">
                        {{ $student->created_at?->format('F d, Y') ?? 'Not available' }}
                    </p>
                </div>

                <div class="rounded-lg bg-gray-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Last Updated</p>
                    <p class="mt-2 text-sm font-semibold text-gray-800">
                        {{ $student->updated_at?->format('F d, Y') ?? 'Not available' }}
                    </p>
                </div>

            </div>
        </div>

        <div class="border-t border-gray-100 bg-gray-50 p-6">
            <h3 class="font-bold text-gray-800">Account Actions</h3>
            <p class="mt-1 text-xs text-gray-500">Manage the student's account access.</p>

            <div class="mt-4 flex flex-wrap gap-3">

                <form action="{{ route('admin.students.status', $student) }}" method="POST"
                      onsubmit="return confirm('Are you sure you want to change this student account status?');">
                    @csrf
                    @method('PATCH')

                    <button type="submit"
                            class="rounded-lg border px-4 py-2 text-sm font-semibold transition
                            {{ $student->status === 'active' ? 'border-red-200 bg-white text-red-600 hover:bg-red-50' : 'border-green-200 bg-white text-green-700 hover:bg-green-50' }}">
                        {{ $student->status === 'active' ? 'Deactivate Account' : 'Activate Account' }}
                    </button>
                </form>

            </div>
        </div>

    </div>
</div>
@endsection
