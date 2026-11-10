@extends('layouts.app')

@section('title', 'Add Student Account')

@section('content')
<div class="mx-auto max-w-5xl">

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-sm text-gray-500">Student Management / Add Student</p>
            <h1 class="mt-1 text-2xl font-bold text-gray-800">Create Student Account</h1>
            <p class="mt-1 text-sm text-gray-500">Register a new student in the system.</p>
        </div>

        <a href="{{ route('admin.students.index') }}"
           class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-600 transition hover:bg-gray-50">
            Back to Students
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="mb-2 font-bold">Please correct the following errors:</p>
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.students.store') }}" method="POST"
          class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        @csrf

        <div class="border-b border-gray-100 px-6 py-5">
            <h2 class="font-bold text-gray-800">Student Information</h2>
            <p class="mt-1 text-xs text-gray-500">Enter the student's personal and academic details.</p>
        </div>

        <div class="grid gap-5 p-6 md:grid-cols-2">

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">Student Number</label>
                <input type="text" name="student_no" value="{{ old('student_no') }}"
                       placeholder="Enter student number"
                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-[#28634c] focus:ring-2 focus:ring-green-100">
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">Full Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       placeholder="Enter full name"
                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-[#28634c] focus:ring-2 focus:ring-green-100">
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">Email Address <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       placeholder="student@example.com"
                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-[#28634c] focus:ring-2 focus:ring-green-100">
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">Department</label>
                <input type="text" name="department" value="{{ old('department') }}"
                       placeholder="Enter department"
                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-[#28634c] focus:ring-2 focus:ring-green-100">
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">Course</label>
                <input type="text" name="course" value="{{ old('course') }}"
                       placeholder="Enter course"
                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-[#28634c] focus:ring-2 focus:ring-green-100">
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">Year Level</label>
                <select name="year_level"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm outline-none focus:border-[#28634c] focus:ring-2 focus:ring-green-100">
                    <option value="">Select year level</option>
                    @foreach (['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year'] as $year)
                        <option value="{{ $year }}" @selected(old('year_level') === $year)>{{ $year }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">Account Status <span class="text-red-500">*</span></label>
                <select name="status" required
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm outline-none focus:border-[#28634c] focus:ring-2 focus:ring-green-100">
                    <option value="active" @selected(old('status', 'active') === 'active')>Active</option>
                    <option value="inactive" @selected(old('status') === 'inactive')>Inactive</option>
                </select>
            </div>

        </div>

        <div class="border-t border-gray-100 px-6 py-5">
            <h2 class="font-bold text-gray-800">Account Password</h2>
            <p class="mt-1 text-xs text-gray-500">The student can sign in with this email and password while the account is active.</p>
        </div>

        <div class="grid gap-5 px-6 pb-6 md:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">Password <span class="text-red-500">*</span></label>
                <input type="password" name="password" required minlength="8"
                       placeholder="At least 8 characters"
                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-[#28634c] focus:ring-2 focus:ring-green-100">
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">Confirm Password <span class="text-red-500">*</span></label>
                <input type="password" name="password_confirmation" required minlength="8"
                       placeholder="Re-enter password"
                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-[#28634c] focus:ring-2 focus:ring-green-100">
            </div>
        </div>

        <div class="flex flex-wrap justify-end gap-3 border-t border-gray-100 bg-gray-50 px-6 py-4">
            <a href="{{ route('admin.students.index') }}"
               class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-100">
                Cancel
            </a>
            <button type="submit"
                    class="rounded-lg bg-[#174b38] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#28634c]">
                Create Student Account
            </button>
        </div>
    </form>
</div>
@endsection
