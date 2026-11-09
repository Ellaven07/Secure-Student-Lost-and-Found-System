<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

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

        $students = User::query()
            ->where('role', 'student')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('student_no', 'like', "%{$search}%")
                        ->orWhere('course', 'like', "%{$search}%");
                });
            })
            ->when($request->status === 'active', function ($query) {
                $query->where('status', 'active');
            })
            ->when($request->status === 'inactive', function ($query) {
                $query->where('status', '!=', 'active');
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.students.index', compact(
            'totalStudents',
            'activeStudents',
            'inactiveStudents',
            'students'
        ));
    }
}
