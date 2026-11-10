<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\SupabaseStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class StudentProfileController extends Controller
{
    /**
     * Display the authenticated student's profile.
     */
    public function show(SupabaseStorageService $photoStorage): View
    {
        $student = auth()->user();

        $statistics = [
            'lost_reports' => DB::table('items')
                ->where('user_id', $student->id)
                ->where('type', 'lost')
                ->count(),

            'found_reports' => DB::table('items')
                ->where('user_id', $student->id)
                ->where('type', 'found')
                ->count(),

            'claims' => DB::table('claims')
                ->where('claimant_id', $student->id)
                ->count(),
        ];

        return view('student.profile', [
            'student' => $student,
            'statistics' => $statistics,
            'profilePhotoUrl' => $photoStorage->url($student->profile_photo_path),
        ]);
    }

    /**
     * Update the student's basic account information.
     */
    public function update(Request $request): RedirectResponse
    {
        $student = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email,'.$student->id,
            ],
            'student_no' => [
                'nullable',
                'string',
                'max:255',
                'unique:users,student_no,'.$student->id,
            ],
            'department' => ['nullable', 'string', 'max:255'],
            'course' => ['nullable', 'string', 'max:255'],
            'year_level' => ['nullable', 'string', 'max:255'],
        ]);

        $student->update($validated);

        return redirect()
            ->route('student.profile')
            ->with('success', 'Your profile information has been updated successfully.');
    }

    /**
     * Upload or replace the student's profile photo.
     */
    public function updatePhoto(Request $request, SupabaseStorageService $photoStorage): RedirectResponse
    {
        $request->validate([
            'profile_photo' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        $student = $request->user();

        if (! $photoStorage->isConfigured()) {
            $missingSettings = implode(', ', $photoStorage->missingConfiguration());

            return redirect()
                ->route('student.profile')
                ->with('error', "Shared photo storage is missing server setting(s): {$missingSettings}. Update the .env used by this app, run php artisan config:clear, and restart the server.");
        }

        $newPhotoPath = $photoStorage->upload($request->file('profile_photo'), $student->id);

        if (! $newPhotoPath) {
            return redirect()
                ->route('student.profile')
                ->with('error', 'The profile photo could not be uploaded. Please try again.');
        }

        $oldPhotoPath = $student->profile_photo_path;

        $student->update([
            'profile_photo_path' => $newPhotoPath,
        ]);

        $photoStorage->delete($oldPhotoPath);

        return redirect()
            ->route('student.profile')
            ->with('success', 'Your profile photo has been updated successfully.');
    }

    /**
     * Change the student's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'confirmed',
                Password::defaults(),
            ],
        ]);

        $student = $request->user();

        $student->update([
            'password' => $validated['password'],
        ]);

        $request->session()->regenerate();

        return redirect()
            ->route('student.profile')
            ->with('success', 'Your password has been changed successfully.');
    }
}
