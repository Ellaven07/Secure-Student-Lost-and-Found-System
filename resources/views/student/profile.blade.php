@extends('layouts.student')

@section('title', 'Settings')
@section('page-label', 'Settings')

@section('content')

@php
    $initials = collect(preg_split('/\s+/', trim($student->name)))
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('');

    $isActive = ($student->status ?? 'active') === 'active';
@endphp

<div class="mx-auto max-w-6xl space-y-6">

    <!-- Page heading -->
    <section>
        <h1 class="text-2xl font-extrabold tracking-tight text-[#174b38]">
            Settings
        </h1>
        <p class="mt-1 text-sm text-gray-500">
            Manage your student account information and security settings.
        </p>
    </section>

    <!-- Profile overview -->
    <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="h-1.5 bg-gradient-to-r from-[#174b38] via-[#28634c] to-[#d7b65d]"></div>

        <div class="p-6 sm:p-8">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex min-w-0 items-center gap-5">
                    <div class="grid h-20 w-20 shrink-0 place-items-center overflow-hidden rounded-2xl border border-gray-200 bg-[#e8eee9] text-2xl font-extrabold text-[#174b38] shadow-sm sm:h-24 sm:w-24">
                        @if ($profilePhotoUrl)
                            <img
                                src="{{ $profilePhotoUrl }}"
                                alt="Profile photo of {{ $student->name }}"
                                class="h-full w-full object-cover"
                            >
                        @else
                            {{ $initials ?: 'S' }}
                        @endif
                    </div>

                    <div class="min-w-0">
                        <h2 class="break-words text-xl font-bold leading-tight text-gray-900 sm:text-2xl">
                            {{ $student->name }}
                        </h2>
                        <p class="mt-1.5 text-sm text-gray-500">
                            Student No. <span class="font-medium text-gray-700">{{ $student->student_no ?: 'Not provided' }}</span>
                        </p>
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-[11px] font-semibold {{ $isActive ? 'border-green-100 bg-green-50 text-green-700' : 'border-gray-200 bg-gray-100 text-gray-600' }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $isActive ? 'bg-green-600' : 'bg-gray-500' }}"></span>
                                {{ ucfirst($student->status ?? 'active') }}
                            </span>
                            <span class="rounded-full border border-gray-200 bg-gray-50 px-3 py-1 text-[11px] font-semibold text-gray-600">
                                Student Account
                            </span>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3 sm:min-w-44 sm:text-right">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Member since</p>
                    <p class="mt-1 text-sm font-semibold text-gray-700">
                        {{ $student->created_at?->format('F d, Y') ?? 'Not available' }}
                    </p>
                </div>
            </div>

            <!-- Profile photo upload -->
            <form
                method="POST"
                action="{{ route('student.profile.photo') }}"
                enctype="multipart/form-data"
                class="mt-7 border-t border-gray-100 pt-6"
            >
                @csrf

                <div class="mb-4">
                    <label for="profile_photo" class="block text-sm font-semibold text-gray-800">Update profile photo</label>
                    <p class="mt-1 text-xs text-gray-500">Choose a clear JPG, PNG, or WEBP image for your account.</p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <input
                        id="profile_photo"
                        name="profile_photo"
                        type="file"
                        data-compress-profile-photo
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                        required
                        class="block w-full rounded-xl border border-gray-200 bg-white text-sm text-gray-600 file:mr-4 file:cursor-pointer file:border-0 file:bg-gray-100 file:px-4 file:py-3 file:text-xs file:font-semibold file:text-gray-700 hover:file:bg-gray-200 sm:max-w-lg"
                    >

                    <button
                        type="submit"
                        class="rounded-xl bg-[#174b38] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#28634c] focus:outline-none focus:ring-2 focus:ring-[#28634c] focus:ring-offset-2"
                    >
                        Upload Photo
                    </button>
                </div>

                <p class="mt-2 text-xs text-gray-400" data-photo-compression-message>
                    JPG, JPEG, PNG, or WEBP. Maximum file size: 2 MB. Larger photos are optimized automatically.
                </p>

                @error('profile_photo')
                    <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </form>
        </div>
    </section>

    <!-- Account statistics -->
    <section>
        <div class="mb-4">
            <h2 class="text-base font-extrabold text-gray-800">My Activity</h2>
            <p class="mt-1 text-xs text-gray-500">
                Summary of your reports and claim submissions.
            </p>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold text-gray-500">Lost Reports</p>
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-amber-50 text-lg text-amber-700" aria-hidden="true">⌕</span>
                </div>
                <p class="mt-4 text-3xl font-extrabold text-gray-800">
                    {{ number_format($statistics['lost_reports'] ?? 0) }}
                </p>
                <p class="mt-1 text-xs text-gray-400">Reports submitted as lost</p>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold text-gray-500">Found Reports</p>
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-green-50 text-lg text-green-700" aria-hidden="true">✓</span>
                </div>
                <p class="mt-4 text-3xl font-extrabold text-gray-800">
                    {{ number_format($statistics['found_reports'] ?? 0) }}
                </p>
                <p class="mt-1 text-xs text-gray-400">Reports submitted as found</p>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold text-gray-500">My Claims</p>
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-blue-50 text-lg text-blue-700" aria-hidden="true">▤</span>
                </div>
                <p class="mt-4 text-3xl font-extrabold text-gray-800">
                    {{ number_format($statistics['claims'] ?? 0) }}
                </p>
                <p class="mt-1 text-xs text-gray-400">Claims submitted</p>
            </div>
        </div>
    </section>

    <!-- Personal and academic information -->
    <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-7">
        <div class="mb-6">
            <h2 class="text-base font-extrabold text-gray-800">
                Personal & Academic Information
            </h2>
            <p class="mt-1 text-xs text-gray-500">
                Update the information associated with your student account.
            </p>
        </div>

        <form method="POST" action="{{ route('student.profile.update') }}">
            @csrf
            @method('PUT')

            <div class="grid gap-5 sm:grid-cols-2">

                <div class="sm:col-span-2">
                    <label for="name" class="mb-2 block text-xs font-bold text-gray-700">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name', $student->name) }}"
                        required
                        maxlength="255"
                        autocomplete="name"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none transition focus:border-[#28634c] focus:bg-white focus:ring-2 focus:ring-green-100"
                    >
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="mb-2 block text-xs font-bold text-gray-700">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email', $student->email) }}"
                        required
                        maxlength="255"
                        autocomplete="email"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none transition focus:border-[#28634c] focus:bg-white focus:ring-2 focus:ring-green-100"
                    >
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="student_no" class="mb-2 block text-xs font-bold text-gray-700">
                        Student Number
                    </label>
                    <input
                        id="student_no"
                        name="student_no"
                        type="text"
                        value="{{ old('student_no', $student->student_no) }}"
                        maxlength="255"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none transition focus:border-[#28634c] focus:bg-white focus:ring-2 focus:ring-green-100"
                    >
                    @error('student_no')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="department" class="mb-2 block text-xs font-bold text-gray-700">
                        College / Department
                    </label>
                    <input
                        id="department"
                        name="department"
                        type="text"
                        value="{{ old('department', $student->department) }}"
                        maxlength="255"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none transition focus:border-[#28634c] focus:bg-white focus:ring-2 focus:ring-green-100"
                    >
                    @error('department')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="course" class="mb-2 block text-xs font-bold text-gray-700">
                        Course / Program
                    </label>
                    <input
                        id="course"
                        name="course"
                        type="text"
                        value="{{ old('course', $student->course) }}"
                        maxlength="255"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none transition focus:border-[#28634c] focus:bg-white focus:ring-2 focus:ring-green-100"
                    >
                    @error('course')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="year_level" class="mb-2 block text-xs font-bold text-gray-700">
                        Year Level
                    </label>
                    <input
                        id="year_level"
                        name="year_level"
                        type="text"
                        value="{{ old('year_level', $student->year_level) }}"
                        maxlength="255"
                        placeholder="e.g. 1st Year"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none transition focus:border-[#28634c] focus:bg-white focus:ring-2 focus:ring-green-100"
                    >
                    @error('year_level')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-7 flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end">
                <a
                    href="{{ route('student.profile') }}"
                    class="rounded-xl border border-gray-200 px-5 py-3 text-center text-xs font-bold text-gray-600 transition hover:bg-gray-50"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="rounded-xl bg-[#174b38] px-6 py-3 text-xs font-bold text-white transition hover:bg-[#28634c]"
                >
                    Save Changes
                </button>
            </div>
        </form>
    </section>

    <!-- Password and security -->
    <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-7">
        <div class="mb-6">
            <h2 class="text-base font-extrabold text-gray-800">Password & Security</h2>
            <p class="mt-1 text-xs text-gray-500">
                Change your password to help keep your account secure.
            </p>
        </div>

        <form method="POST" action="{{ route('student.profile.password') }}">
            @csrf
            @method('PUT')

            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="current_password" class="mb-2 block text-xs font-bold text-gray-700">
                        Current Password <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="current_password"
                        name="current_password"
                        type="password"
                        required
                        autocomplete="current-password"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none transition focus:border-[#28634c] focus:bg-white focus:ring-2 focus:ring-green-100 sm:max-w-xl"
                    >
                    @error('current_password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="mb-2 block text-xs font-bold text-gray-700">
                        New Password <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        autocomplete="new-password"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none transition focus:border-[#28634c] focus:bg-white focus:ring-2 focus:ring-green-100"
                    >
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="mb-2 block text-xs font-bold text-gray-700">
                        Confirm New Password <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        required
                        autocomplete="new-password"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none transition focus:border-[#28634c] focus:bg-white focus:ring-2 focus:ring-green-100"
                    >
                </div>
            </div>

            <div class="mt-6 flex justify-end border-t border-gray-100 pt-5">
                <button
                    type="submit"
                    class="rounded-xl bg-[#174b38] px-6 py-3 text-xs font-bold text-white transition hover:bg-[#28634c]"
                >
                    Update Password
                </button>
            </div>
        </form>
    </section>

    <p class="pb-2 text-center text-[11px] text-gray-400">
        Your account information is private and connected to your authenticated student account.
    </p>
</div>

@endsection
