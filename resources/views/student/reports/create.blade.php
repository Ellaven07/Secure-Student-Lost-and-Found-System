@extends('layouts.student')

@section('title', $type === 'lost' ? 'Report Lost Item' : 'Report Found Item')
@section('page-label', $type === 'lost' ? 'Report Lost Item' : 'Report Found Item')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <section class="overflow-hidden rounded-2xl bg-[#174b38] px-6 py-7 text-white shadow-sm sm:px-8">
        <p class="text-xs font-bold uppercase tracking-[.18em] text-[#e2c66f]">Student report</p>
        <h1 class="mt-2 text-2xl font-extrabold sm:text-3xl">{{ $type === 'lost' ? 'Report a Lost Item' : 'Report a Found Item' }}</h1>
        <p class="mt-2 max-w-2xl text-sm leading-6 text-green-50/85">{{ $type === 'lost' ? 'Tell us what you lost and where you last saw it. The Lost & Found team will review your report.' : 'Help return an item to its owner. Share where you found it and a clear description.' }}</p>
    </section>

    <form method="POST" action="{{ route('student.reports.'.$type.'.store') }}" enctype="multipart/form-data" class="space-y-6 rounded-2xl border border-[#e4eae6] bg-white p-5 shadow-sm sm:p-8">
        @csrf
        <div class="grid gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label for="item_name" class="mb-2 block text-sm font-semibold text-gray-700">Item name <span class="text-red-600">*</span></label>
                <input id="item_name" name="item_name" value="{{ old('item_name') }}" required maxlength="150" placeholder="e.g. Black wireless earbuds" class="w-full rounded-lg border border-gray-300 px-3.5 py-3 text-sm outline-none transition focus:border-[#56846b] focus:ring-2 focus:ring-[#174b38]/10">
                @error('item_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="category_id" class="mb-2 block text-sm font-semibold text-gray-700">Category</label>
                <select id="category_id" name="category_id" class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-3 text-sm outline-none focus:border-[#56846b] focus:ring-2 focus:ring-[#174b38]/10">
                    <option value="">Choose a category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                @if ($categories->isEmpty())<p class="mt-1 text-xs text-amber-700">No categories yet. You can still submit your report.</p>@endif
                @error('category_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="date_lost_or_found" class="mb-2 block text-sm font-semibold text-gray-700">Date {{ $type }} <span class="text-red-600">*</span></label>
                <input id="date_lost_or_found" type="date" name="date_lost_or_found" value="{{ old('date_lost_or_found') }}" max="{{ now()->toDateString() }}" required class="w-full rounded-lg border border-gray-300 px-3.5 py-3 text-sm outline-none focus:border-[#56846b] focus:ring-2 focus:ring-[#174b38]/10">
                @error('date_lost_or_found')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2">
                <label for="location" class="mb-2 block text-sm font-semibold text-gray-700">{{ $type === 'lost' ? 'Last seen location' : 'Found location' }} <span class="text-red-600">*</span></label>
                <input id="location" name="location" value="{{ old('location') }}" required maxlength="255" placeholder="Building, room, or area on campus" class="w-full rounded-lg border border-gray-300 px-3.5 py-3 text-sm outline-none focus:border-[#56846b] focus:ring-2 focus:ring-[#174b38]/10">
                @error('location')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2">
                <label for="description" class="mb-2 block text-sm font-semibold text-gray-700">Description <span class="text-red-600">*</span></label>
                <textarea id="description" name="description" rows="5" required maxlength="5000" placeholder="Describe the item's color, brand, identifying details, or what was inside." class="w-full resize-y rounded-lg border border-gray-300 px-3.5 py-3 text-sm outline-none focus:border-[#56846b] focus:ring-2 focus:ring-[#174b38]/10">{{ old('description') }}</textarea>
                @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2">
                <label for="photo" class="mb-2 block text-sm font-semibold text-gray-700">Photo <span class="font-normal text-gray-400">({{ $type === 'found' ? 'required' : 'optional' }}, JPG/PNG/WEBP up to 5 MB)</span></label>
                <input id="photo" type="file" name="photo" accept="image/jpeg,image/png,image/webp" @required($type === 'found') class="block w-full rounded-lg border border-gray-300 text-sm text-gray-600 file:mr-4 file:border-0 file:bg-[#edf4ef] file:px-4 file:py-3 file:text-sm file:font-semibold file:text-[#174b38] hover:file:bg-[#e1ebe4]">
                @error('photo')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-between">
            <a href="{{ route('student.reports.history') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-5 py-3 text-sm font-semibold text-gray-600 transition hover:bg-gray-50">My report history</a>
            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-[#174b38] px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#103b2b] focus:outline-none focus:ring-2 focus:ring-[#56846b] focus:ring-offset-2">Submit {{ ucfirst($type) }} Report</button>
        </div>
    </form>
</div>
@endsection
