
<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\StudentAccountController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('login.store');

    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->middleware('throttle:6,1')->name('password.email');

    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:6,1')->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'role:admin', 'frontend-preview'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
        ->name('admin.dashboard');
    Route::get('/admin/dashboard/export', [AdminDashboardController::class, 'export'])
        ->name('admin.dashboard.export');

    Route::get('/admin/students/create', [StudentAccountController::class, 'create'])
        ->name('admin.students.create');
    Route::post('/admin/students', [StudentAccountController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('admin.students.store');

    Route::get('/admin/students', [StudentController::class, 'index'])
        ->name('admin.students.index');

    Route::view('/admin/categories', 'admin.categories')
        ->name('admin.categories');
});

Route::middleware(['auth', 'role:student', 'frontend-preview'])->group(function () {
    Route::view('/student/dashboard', 'student.dashboard')
        ->name('student.dashboard');

    Route::view('/student/profile', 'student.profile')
        ->name('student.profile');

    Route::view('/student/notifications', 'student.notifications')
        ->name('student.notifications');
});
