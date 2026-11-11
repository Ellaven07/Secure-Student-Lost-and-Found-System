<div>
    <!-- Do what you can, with what you have, where you are. - Theodore Roosevelt -->
</div>
@extends('layouts.student')

@section('title', 'Edit Item Report')
@section('page-label', 'Edit Item Report')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <section class="overflow-hidden rounded-2xl bg-[#174b38] px-6 py-7 text-white shadow-sm sm:px-8">
        <p class="text-xs font-bold uppercase tracking-[.18em] text-[#e2c66f]">Update your report</p>
        <h1 class="mt-2 text-2xl font-extrabold sm:text-3xl">Edit {{ ucfirst($report->type) }} Item Report</h1>
        <p class="mt-2 max-w-2xl text-sm leading-6 text-green-50/85">After saving, the report will return to admin review before it appears in Browse Items again.</p>
    </section>

    <form method="POST" action="{{ route('student.reports.update', $report->id) }}" enctype="multipart/form-data" class="space-y-6 rounded-2xl border border-[#e4eae6] bg-white p-5 shadow-sm sm:p-8">
        @csrf
        @method('PUT')
        <div class="grid gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label for="item_name" class="mb-2 block text-sm font-semibold text-gray-700">Item name <span class="text-red-600">*</span></label>
                <input id="item_name" name="item_name" value="{{ old('item_name', $report->item_name) }}" required maxlength="150" class="w-full rounded-lg border border-gray-300 px-3.5 py-3 text-sm outline-none focus:border-[#56846b] focus:ring-2 focus:ring-[#174b38]/10">
                @error('item_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="category_id" class="mb-2 block text-sm font-semibold text-gray-700">Category</label>
                <select id="category_id" name="category_id" class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-3 text-sm outline-none focus:border-[#56846b]">
                    <option value="">Choose a category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) old('category_id', $report->category_id) === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="date_lost_or_found" class="mb-2 block text-sm font-semibold text-gray-700">Date {{ $report->type }} <span class="text-red-600">*</span></label>
                <input id="date_lost_or_found" type="date" name="date_lost_or_found" value="{{ old('date_lost_or_found', $report->date_lost_or_found) }}" max="{{ now()->toDateString() }}" required class="w-full rounded-lg border border-gray-300 px-3.5 py-3 text-sm outline-none focus:border-[#56846b]">
                @error('date_lost_or_found')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2">
                <label for="location" class="mb-2 block text-sm font-semibold text-gray-700">{{ strtolower($report->type) === 'lost' ? 'Last seen location' : 'Found location' }} <span class="text-red-600">*</span></label>
                <input id="location" name="location" value="{{ old('location', $report->location) }}" required maxlength="255" class="w-full rounded-lg border border-gray-300 px-3.5 py-3 text-sm outline-none focus:border-[#56846b]">
                @error('location')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2">
                <label for="description" class="mb-2 block text-sm font-semibold text-gray-700">Description <span class="text-red-600">*</span></label>
                <textarea id="description" name="description" rows="5" required maxlength="5000" class="w-full resize-y rounded-lg border border-gray-300 px-3.5 py-3 text-sm outline-none focus:border-[#56846b]">{{ old('description', $report->description) }}</textarea>
                @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            @if ($report->photo_path)
                <div class="sm:col-span-2">
                    <p class="mb-2 text-sm font-semibold text-gray-700">Current photo</p>
                    <div class="flex flex-wrap items-center gap-4">
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($report->photo_path) }}" alt="Current photo of {{ $report->item_name }}" class="h-24 w-24 rounded-xl border border-gray-100 object-cover">
                        <label class="flex items-center gap-2 text-sm text-gray-600"><input type="checkbox" name="remove_photo" value="1" @checked(old('remove_photo')) class="rounded border-gray-300 text-[#174b38] focus:ring-[#174b38]"> Remove current photo</label>
                    </div>
                </div>
            @endif
            <div class="sm:col-span-2">
                <label for="photo" class="mb-2 block text-sm font-semibold text-gray-700">{{ $report->photo_path ? 'Replace photo' : 'Add a photo' }} <span class="font-normal text-gray-400">(optional, JPG/PNG/WEBP up to 5 MB)</span></label>
                <input id="photo" type="file" name="photo" accept="image/jpeg,image/png,image/webp" class="block w-full rounded-lg border border-gray-300 text-sm text-gray-600 file:mr-4 file:border-0 file:bg-[#edf4ef] file:px-4 file:py-3 file:text-sm file:font-semibold file:text-[#174b38] hover:file:bg-[#e1ebe4]">
                @error('photo')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-between">
            <a href="{{ route('student.reports.history') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-5 py-3 text-sm font-semibold text-gray-600 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="rounded-lg bg-[#174b38] px-6 py-3 text-sm font-bold text-white transition hover:bg-[#103b2b]">Save changes</button>
        </div>
    </form>
</div>
@endsection
