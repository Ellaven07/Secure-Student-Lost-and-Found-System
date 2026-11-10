<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $studentQuery = User::query()->where('role', 'student');

        $totalStudents = (clone $studentQuery)->count();

        $activeStudents = (clone $studentQuery)
            ->where('status', 'active')
            ->count();

        $inactiveStudents = (clone $studentQuery)
            ->where('status', '!=', 'active')
            ->count();

        $students = (clone $studentQuery)
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('student_no', 'like', "%{$search}%")
                        ->orWhere('course', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('department'), fn ($query) => $query->where('department', $request->department)
            )
            ->when($request->filled('course'), fn ($query) => $query->where('course', $request->course)
            )
            ->when($request->filled('year_level'), fn ($query) => $query->where('year_level', $request->year_level)
            )
            ->when($request->status === 'active', fn ($query) => $query->where('status', 'active')
            )
            ->when($request->status === 'inactive', fn ($query) => $query->where('status', '!=', 'active')
            )
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $departments = (clone $studentQuery)
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->distinct()
            ->orderBy('department')
            ->pluck('department');

        $courses = (clone $studentQuery)
            ->whereNotNull('course')
            ->where('course', '!=', '')
            ->distinct()
            ->orderBy('course')
            ->pluck('course');

        $yearLevels = (clone $studentQuery)
            ->whereNotNull('year_level')
            ->where('year_level', '!=', '')
            ->distinct()
            ->orderBy('year_level')
            ->pluck('year_level');

        return view('admin.students.index', compact(
            'totalStudents',
            'activeStudents',
            'inactiveStudents',
            'students',
            'departments',
            'courses',
            'yearLevels'
        ));
    }

    public function create()
    {
        return view('admin.students.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_no' => ['nullable', 'string', 'max:50', 'unique:users,student_no'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'department' => ['nullable', 'string', 'max:255'],
            'course' => ['nullable', 'string', 'max:255'],
            'year_level' => ['nullable', 'string', 'max:50'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $validated['role'] = 'student';

        $student = User::create($validated);

        $this->createNotification(
            $student->id,
            'Your student account is ready',
            'An administrator created your student account. You can now sign in to the system.',
            'success'
        );

        $this->notifyAdministrators(
            'New student account',
            $student->name.' ('.$student->email.') was added to Student Management.',
            'info'
        );

        $this->logActivity(
            'Created student account',
            'Created student account for '.$student->name.'.'
        );

        return redirect()
            ->route('admin.students.index')
            ->with('success', $student->isActive()
                ? 'Student account created. The student can sign in with the email and password you entered.'
                : 'Student account created as inactive. Activate it before the student can sign in.');
    }

    public function show(User $student)
    {
        $this->ensureStudent($student);

        return view('admin.students.show', compact('student'));
    }

    public function edit(User $student)
    {
        $this->ensureStudent($student);

        return view('admin.students.edit', compact('student'));
    }

    public function update(Request $request, User $student)
    {
        $this->ensureStudent($student);

        $validated = $request->validate([
            'student_no' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('users', 'student_no')->ignore($student->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($student->id),
            ],
            'department' => ['nullable', 'string', 'max:255'],
            'course' => ['nullable', 'string', 'max:255'],
            'year_level' => ['nullable', 'string', 'max:50'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('profile_photo')) {
            if ($student->profile_photo_path) {
                Storage::disk('public')->delete($student->profile_photo_path);
            }

            $validated['profile_photo_path'] = $request
                ->file('profile_photo')
                ->store('student-profiles', 'public');
        }

        unset($validated['profile_photo']);

        $student->update($validated);

        $this->createNotification(
            $student->id,
            'Your account information was updated',
            'An administrator updated your student account information.',
            'info'
        );

        $this->logActivity(
            'Updated student account',
            'Updated student account for '.$student->name.'.'
        );

        return redirect()
            ->route('admin.students.show', $student)
            ->with('success', 'Student account updated successfully.');
    }

    public function destroy(User $student): RedirectResponse
    {
        $this->ensureStudent($student);

        $studentName = $student->name;
        $studentEmail = $student->email;

        $deleted = DB::transaction(function () use ($student, $studentEmail): bool {
            $hasReports = DB::table('items')
                ->where('user_id', $student->id)
                ->exists();
            $hasClaims = DB::table('claims')
                ->where('claimant_id', $student->id)
                ->exists();

            if ($hasReports || $hasClaims) {
                return false;
            }

            DB::table('sessions')
                ->where('user_id', $student->id)
                ->delete();

            DB::table('password_reset_tokens')
                ->where('email', $studentEmail)
                ->delete();

            $student->delete();

            return true;
        });

        if (! $deleted) {
            return back()->with('error', 'This account cannot be deleted because it has submitted reports or claim requests.');
        }

        $this->logActivity(
            'Deleted student account',
            'Deleted student account for '.$studentName.'.'
        );

        return redirect()
            ->route('admin.students.index')
            ->with('success', 'Student account deleted successfully.');
    }

    public function toggleStatus(User $student)
    {
        $this->ensureStudent($student);

        $student->status = $student->status === 'active'
            ? 'inactive'
            : 'active';

        $student->save();

        $this->createNotification(
            $student->id,
            'Your account status changed',
            'An administrator changed your account status to '.$student->status.'.',
            $student->status === 'active' ? 'success' : 'warning'
        );

        $this->logActivity(
            'Changed student status',
            'Changed '.$student->name.' status to '.$student->status.'.'
        );

        return back()->with('success', 'Student status updated successfully.');
    }

    public function resetPassword(Request $request, User $student)
    {
        $this->ensureStudent($student);

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $student->password = $validated['password'];
        $student->save();

        $this->createNotification(
            $student->id,
            'Your password was reset',
            'An administrator reset your password. Contact the office if you did not expect this change.',
            'warning'
        );

        $this->logActivity(
            'Reset student password',
            'Reset password for '.$student->name.'.'
        );

        return back()->with('success', 'Student password reset successfully.');
    }

    private function ensureStudent(User $student)
    {
        abort_unless($student->role === 'student', 404);
    }

    private function notifyAdministrators(string $title, string $message, string $type): void
    {
        User::query()
            ->where('role', 'admin')
            ->pluck('id')
            ->each(fn (int $adminId) => $this->createNotification($adminId, $title, $message, $type));
    }

    private function createNotification(int $userId, string $title, string $message, string $type): void
    {
        DB::table('notifications')->insert([
            'id' => (string) Str::uuid(),
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function logActivity(string $action, string $details)
    {
        if (! Schema::hasTable('activity_logs')) {
            return;
        }

        DB::table('activity_logs')->insert([
            'user_id' => Auth::id(),
            'action' => $action,
            'details' => $details,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
