@extends('layouts.admin')

@section('title', 'Create Student Account')
@section('page-label', 'Student Management')

@section('content')
<div class="mx-auto max-w-3xl">
    <a href="{{ route('admin.students.index') }}" class="mb-5 inline-flex items-center gap-2 text-sm font-semibold text-[#28634c] hover:text-[#174b38]">
        <span aria-hidden="true">←</span> Back to student accounts
    </a>

    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
        <div class="mb-7">
            <p class="text-xs font-bold uppercase tracking-[.16em] text-[#9a8546]">Administrator</p>
            <h1 class="mt-2 text-2xl font-extrabold text-[#174b38]">Create a student account</h1>
            <p class="mt-2 text-sm leading-6 text-gray-500">Add the student's details and set a temporary password they can use to sign in.</p>
        </div>

        <form method="POST" action="{{ route('admin.students.store') }}" class="space-y-5">
            @csrf

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="name" class="mb-2 block text-sm font-semibold text-gray-700">Full name</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required class="w-full rounded-lg border border-gray-300 px-3.5 py-3 text-sm outline-none transition focus:border-[#28634c] focus:ring-2 focus:ring-[#28634c]/15">
                    @error('name')<p class="mt-1.5 text-xs text-red-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="student_no" class="mb-2 block text-sm font-semibold text-gray-700">Student number</label>
                    <input id="student_no" name="student_no" type="text" value="{{ old('student_no') }}" required class="w-full rounded-lg border border-gray-300 px-3.5 py-3 text-sm outline-none transition focus:border-[#28634c] focus:ring-2 focus:ring-[#28634c]/15">
                    @error('student_no')<p class="mt-1.5 text-xs text-red-700">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="email" class="mb-2 block text-sm font-semibold text-gray-700">Email address</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required class="w-full rounded-lg border border-gray-300 px-3.5 py-3 text-sm outline-none transition focus:border-[#28634c] focus:ring-2 focus:ring-[#28634c]/15">
                    @error('email')<p class="mt-1.5 text-xs text-red-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="department" class="mb-2 block text-sm font-semibold text-gray-700">Department <span class="font-normal text-gray-400">(optional)</span></label>
                    <input id="department" name="department" type="text" value="{{ old('department') }}" class="w-full rounded-lg border border-gray-300 px-3.5 py-3 text-sm outline-none transition focus:border-[#28634c] focus:ring-2 focus:ring-[#28634c]/15">
                    @error('department')<p class="mt-1.5 text-xs text-red-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="course" class="mb-2 block text-sm font-semibold text-gray-700">Course <span class="font-normal text-gray-400">(optional)</span></label>
                    <input id="course" name="course" type="text" value="{{ old('course') }}" class="w-full rounded-lg border border-gray-300 px-3.5 py-3 text-sm outline-none transition focus:border-[#28634c] focus:ring-2 focus:ring-[#28634c]/15">
                    @error('course')<p class="mt-1.5 text-xs text-red-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="year_level" class="mb-2 block text-sm font-semibold text-gray-700">Year level <span class="font-normal text-gray-400">(optional)</span></label>
                    <input id="year_level" name="year_level" type="text" value="{{ old('year_level') }}" placeholder="e.g. 1st Year" class="w-full rounded-lg border border-gray-300 px-3.5 py-3 text-sm outline-none transition focus:border-[#28634c] focus:ring-2 focus:ring-[#28634c]/15">
                    @error('year_level')<p class="mt-1.5 text-xs text-red-700">{{ $message }}</p>@enderror
                </div>
                <div></div>
                <div>
                    <label for="password" class="mb-2 block text-sm font-semibold text-gray-700">Temporary password</label>
                    <input id="password" name="password" type="password" autocomplete="new-password" required class="w-full rounded-lg border border-gray-300 px-3.5 py-3 text-sm outline-none transition focus:border-[#28634c] focus:ring-2 focus:ring-[#28634c]/15">
                    @error('password')<p class="mt-1.5 text-xs text-red-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-gray-700">Confirm password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="w-full rounded-lg border border-gray-300 px-3.5 py-3 text-sm outline-none transition focus:border-[#28634c] focus:ring-2 focus:ring-[#28634c]/15">
                </div>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end">
                <a href="{{ route('admin.students.index') }}" class="rounded-lg border border-gray-300 px-5 py-3 text-center text-sm font-semibold text-gray-600 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="rounded-lg bg-[#174b38] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#28634c]">Create student account</button>
            </div>
        </form>
    </div>
</div>
@endsection
