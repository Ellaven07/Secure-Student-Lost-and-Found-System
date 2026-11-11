<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StudentDashboardController extends Controller
{
    public function index(): View
    {
        $categories = DB::table('categories')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('student.dashboard', compact('categories'));
    }
}
